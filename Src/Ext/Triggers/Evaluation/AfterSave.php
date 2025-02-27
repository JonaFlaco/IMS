<?php

namespace Ext\Triggers\Evaluation;

use App\Core\BaseTrigger;
use DateTime;
use App\Core\Communications\EmailService;



class AfterSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $data, $is_update = false)
    {

        $evaluation = $this->coreModel->nodeModel("evaluation")
            ->id($id)
            ->loadFirstOrFail();

        $justi = $this->sendEmail($evaluation);
        if(!$justi){
            $justi = "_________";
        }

        $evaluation->score = $this->calculateScore($evaluation, $data);

        $this->coreModel->node_save($evaluation, ["ignore_post_save" => true, "ignore_pre_save" => true, "justification" => $justi]);
    }

    function calculateScore($item, $data)
    {
        $id = $item->beneficiary_id;
        $case = $this->coreModel->nodeModel("beneficiaries")
            ->id($id)
            ->loadFirstOrFail();

        $score = 0;
        //sexo Femenino = 1
        if ($item->sex_id  == 2)
            $score += 1;
        //suma 2 puntos por mujer adulta cabeza de hogar y suma 3 puntos por una sola vez si existe algún NNA en estado de gestación o lactancia
        if ($item->is_pregnant == 1)
            $score += 2;

        $familiarPregnant = 0;
        foreach ($item->family_health as $familiar) {
            foreach ($case->family_information as $member) {
                $birthdate = new DateTime($member->birthdate);
                $today = new DateTime();
                $age = $birthdate->diff($today)->y;
            }
            //cuenta nna en periodo de lactancia
            if ($familiar->family_is_pregnant == 1 && $age >= 10 &&  $age < 18)
                $familiarPregnant += 1;
        }
        //si hay 1 o mas nna en periodo de lactancia 3 pts
        if ($familiarPregnant && $familiarPregnant >= 1)
            $score += 3;

        // Pregunta que se despliega para cada miembro del núcleo familiar.
        // Se suma el puntaje por una sola vez cuando algno de los miembros de la familia marca "SI".
        // Se mantiene el puntaje: SI=2 puntos, NO=0 puntos
        if ($item->is_health_problem  == 1)
            $score += 2;

        //para cada miembro del núcleo familiar.Se suma el puntaje por una sola vez cuando uno o varios miembros de la familia marca "SI".
        //Se mantiene el puntaje SI=2 puntos, NO=0 puntos
        if ($item->is_disability == 1)
            $score += 2;

        if ($item->is_health_problem  == 0 || $item->is_health_problem  == 2 || $item->is_disability == 0 || $item->is_disability == 2) {
            $fam_heath_pro = 0;
            $fam_disability = 0;
            foreach ($item->family_health as $familiar) {
                if ($familiar->family_medical_need)
                    $fam_heath_pro += 1;

                if ($familiar->family_disability == 1)
                    $fam_disability += 1;
            }
            if ($fam_heath_pro > 0)
                $score += 2;

            if ($fam_disability > 0)
                $score += 2;
        }

        //conteo de NNA
        if ($case->family_information) {
            $minorsNumber = 0;
            $adultsNumber = 0;
            foreach ($case->family_information as $member) {
                $birthdate = new DateTime($member->birthdate);
                $today = new DateTime();
                $age = $birthdate->diff($today)->y;

                if ($age < 18) {
                    //conteo de menores
                    $minorsNumber += 1;
                }
                if ($age >= 18) {
                    //conteo de adultos
                    $adultsNumber += 1;
                }
            }
            //El grupo familiar tiene dependientes NNA? si = 1 
            if ($item->have_dependent_children == 1 && $minorsNumber < 2)
                $score += 1;
            //El grupo familiar tiene dependientes NNA? 2 NNA o más = 2 puntos 
            if ($item->have_dependent_children == 1 && $minorsNumber >= 2)
                $score += 2;

            //GF de hogar solo y 2 nna o mas 
            if ($minorsNumber > 1 && $adultsNumber < 1 && $item->alone_dependents == 1)
                $score += 2;

            //GF de hogar solo y 1 nna 
            if ($minorsNumber == 1 && $adultsNumber < 1 && $item->alone_dependents == 1)
                $score += 1;
        }


        // Solicitante de Protección internacional 1 
        // Visa de Protección internacional 1
        // Situación migratoria irregular 1 
        // Apátrida 2 
        $migratory_sit = false;
        if ($item->current_migratory_situation == 1) {
            $score += 1;
            $migratory_sit = true;
        }
        if ($item->current_migratory_situation == 2) {
            $score += 1;
            $migratory_sit = true;
        }
        if ($item->current_migratory_situation == 3) {
            $score += 1;
            $migratory_sit = true;
        }
        if ($item->current_migratory_situation == 4) {
            $score += 2;
            $migratory_sit = true;
        }
        if ($item->current_migratory_situation == 5 || $item->current_migratory_situation  == 6)
            $migratory_sit = false;
        //Pregunta que debe desplegarse por cada miembro del núcleo familiar.
        //Se suma el puntaje solamente una vez 
        $fam_migratory = $item->family_migrant_status;
        $fam_mig_score = 0;
        $fam_mig_ap = false;
        foreach ($fam_migratory as $familiar) {
            if ($familiar->family_migratory_situation == 1 && $migratory_sit != true)
                $fam_mig_score += 1;

            if ($familiar->family_migratory_situation == 2 && $migratory_sit != true)
                $fam_mig_score += 1;

            if ($familiar->family_migratory_situation == 3 && $migratory_sit != true)
                $fam_mig_score += 1;

            if ($familiar->family_migratory_situation == 4 && !$migratory_sit) {
                $fam_mig_score += 2;
                $fam_mig_ap = true;
            }
            if ($fam_mig_score > 2 && $fam_mig_ap)
                $fam_mig_score = 2;

            if ($fam_mig_score > 2 && !$fam_mig_ap)
                $fam_mig_score = 1;
        }
        if ($fam_mig_score)
            $score += $fam_mig_score;
        //Existe un miembro en el Grupo Familiar mayor de 60 años si = 1
        if ($case->family_information) {
            foreach ($case->family_information as $member) {
                $birthdate = new DateTime($member->birthdate);
                $today = new DateTime();
                $age = $birthdate->diff($today)->y;

                if ($age > 60) {
                    $score += 1;
                    break; //Stops the loop once a elder 60 is found
                }
            }
        }
        //¿Algún miembro del Grupo Familiar es un NNA separado? si = 4
        if ($item->is_separated_chil == 1)
            $score += 4;
        //¿Algún miembro del Grupo Familiar es un NNA no acompañado? si = 5
        if ($item->is_unaccompanied_child == 1)
            $score += 5;
        //¿Se trata de una mujer sola? si = 1 
        if (!$case->family_information && $case->gender_id == 2)
            $score += 1;

        //Sumará puntaje por una sola vez cuando alguno de los miembros de la familia marca "NO".
        //¿Está asistiendo a la escuela? NO=1 punto.
        $childNotAttendSchool = 0;
        foreach ($item->family_education as $familiar) {

            if ($familiar->is_assisting_school == 0)
                $childNotAttendSchool += 1;
        }

        if ($childNotAttendSchool > 0)
            $score += 1;


        //2 comidas al día 2=2 puntos
        if ($item->diary_meals == 2)
            $score += 3;
        //1 o menos =4 puntos
        if ($item->diary_meals == 3)
            $score += 4;

        //¿Cuál es su condición de vivienda?
        //Situación de Calle
        if ($item->type_housing == 8)
            $score += 4;
        //Hostal pagado por beneficiario
        if ($item->type_housing == 7)
            $score += 3;
        //Prestado a cambio de un servicio
        if ($item->type_housing == 5)
            $score += 3;
        //Acogido temporalmente por familiares, amigos, conocidos
        if ($item->type_housing == 4)
            $score += 2;
        //Albergue temporal
        if ($item->type_housing == 3)
            $score += 3;
        //Arriendo compartido
        if ($item->type_housing == 2)
            $score += 2;
        //Arriendo independiente
        if ($item->type_housing == 1)
            $score += 1;
        //Asistencia en renta por organizaciones
        if ($item->type_housing == 9)
            $score += 1;

        //¿La vivienda cuenta con baterias sanitarias con cerraduras? NO=1
        if ($item->sanitary_batteries == 0)
            $score += 1;

        //¿Tiene un cuarto exclusivo para cocinar? N0=1
        if ($item->is_cooking_room == 0)
            $score += 1;
        //Ha recibido notificaciones de desalojo si=2
        if ($item->is_risk_eviction == 1)
            $score += 2;


        // ¿Qué actividades realizan en el grupo familiar para solventar sus gastos? “trabajador/a sexual” que sumará 3
        foreach ($item->cover_expenses as $expense) {
            // “trabajador/a sexual” = 3 pto
            if ($expense->value == 6) {
                $score += 3;
            }
        }

        //¿La vivienda cuenta con servicios básicos? NO=2
        if ($item->has_basic_services == 0)
            $score += 2;

        //¿Se encuentra al día en el pago del arriendo? NO=2
        if ($item->has_rent_payment_delay == 0)
            $score += 2;

        //Pertenece a la población LGBTIQ SI=1
        if ($item->is_lgbtiq == 1)
            $score += 1;

        //¿Cuántas personas existen en el grupo familiar menores de 15 años que realicen actividades remuneradas? 
        //SI=5;
        if ($item->children_working > 0)
            $score += 5;
        //¿En el Grupo Familiar existe una o más personas víctimas de violencia basada en género o en riesgo de serlo?
        if ($item->is_people_vbg == 1)
            $score += 5;
        //¿En el Grupo Familiar existe una o más personas víctimas de trata de personas o en riesgo de serlo?
        if ($item->is_people_trafficking  == 1)
            $score += 5;
        //¿En el Grupo Familiar existe una o mas personas víctimas de tráfico ilícito de migrantes o en riesgo de serlo?
        if ($item->is_migrante_smuggling  == 1)
            $score += 5;
        //¿Cuenta con un nivel de estudio terminado? NO=2
        if ($item->education_level  == 0)
            $score += 2;

        //¿Cuál es su último nivel de estudio terminado? solamente sumará el puntaje a la respuesta de la cabeza de hogar.
        if ($item->last_study  == 1)
            $score += 2;

        if ($item->last_study  == 2)
            $score += 1;

        //¿Aproximado de los ingresos mensuales del grupo familiar?
        //puntaje en base a la cantidad de dinero por miembro 
        $totalMembers = count($case->family_information) + 1;
        $moneyMember = $item->estimated_money / $totalMembers;
        if ($moneyMember <= 50)
            $score += 3;

        if ($moneyMember > 50 && $moneyMember <= 150)
            $score += 2;

        if ($moneyMember > 150 && $moneyMember <= 250)
            $score += 1;

        return $score;
    }
    function sendEmail($evaluation)
    {
        if ($evaluation->protection_issues == 1) {
            $case = $this->coreModel->nodeModel("beneficiaries")
                ->id($evaluation->beneficiary_id)
                ->loadFirstOrFail();

            $caseProvince = $case->province;
            $amorProvinces = [24, 8, 7, 18, 2];
            if (in_array($caseProvince, $amorProvinces)) {
                //carga punto focal de proteccion en base a rol
                $puntoFocalQuitoEmail = $this->coreModel->nodeModel("users")
                    ->fields(["email", "full_name"])
                    ->where("m.id IN(SELECT parent_id FROM users_roles WHERE value_id = 'focal_point_protection_quito')")
                    ->loadFirstOrFail();
                $protection_fp_email = $puntoFocalQuitoEmail->email;
                $protection_fp_name = $puntoFocalQuitoEmail->full_name;

                //cambia el cw por el fp de proteccion
                $case->case_worker = $puntoFocalQuitoEmail->id;
                $justification = "Asignó el caso al punto focal de protección $protection_fp_name";
            }

            $mantaProvinces = [15,19];
            if (in_array($caseProvince, $mantaProvinces)) {
                //carga punto focal de proteccion en base a rol
                $puntoFocalMantaEmail = $this->coreModel->nodeModel("users")
                    ->fields(["email", "full_name"])
                    ->where("m.id IN(SELECT parent_id FROM users_roles WHERE value_id = 'focal_point_protection_manta')")
                    ->loadFirstOrFail();
                $protection_fp_email = $puntoFocalMantaEmail->email;
                $protection_fp_name = $puntoFocalMantaEmail->full_name;

                //cambia el cw por el fp de proteccion
                $case->case_worker = $puntoFocalMantaEmail->id;
                $justification = "Asignó el caso al punto focal de protección $protection_fp_name";
            }

            $guayaquilProvinces = [10,17,9];
            if (in_array($caseProvince, $guayaquilProvinces)) {
                //carga punto focal de proteccion en base a rol
                $puntoFocalGuayaquilEmail = $this->coreModel->nodeModel("users")
                    ->fields(["email","full_name"])
                    ->where("m.id IN(SELECT parent_id FROM users_roles WHERE value_id = 'focal_point_protection_guayaquil')")
                    ->loadFirstOrFail();
                $protection_fp_email = $puntoFocalGuayaquilEmail->email;
                $protection_fp_name = $puntoFocalGuayaquilEmail->full_name;

               //cambia el cw por el fp de proteccion
                $case->case_worker = $puntoFocalGuayaquilEmail->id;
                $justification = "Asignó el caso al punto focal de protección $protection_fp_name";
            }

            $lagoProvinces = [16,22,11,20];
            if (in_array($caseProvince, $lagoProvinces)) {
                //carga punto focal de proteccion en base a rol
                $puntoFocalLagoEmail = $this->coreModel->nodeModel("users")
                    ->fields(["email","full_name"])
                    ->where("m.id IN(SELECT parent_id FROM users_roles WHERE value_id = 'focal_point_protection_lago')")
                    ->loadFirstOrFail();
                $protection_fp_email = $puntoFocalLagoEmail->email;
                $protection_fp_name = $puntoFocalLagoEmail->full_name;

               //cambia el cw por el fp de proteccion
                $case->case_worker = $puntoFocalLagoEmail->id;
                $justification = "Asignó el caso al punto focal de protección $protection_fp_name";
            }

            $tulcanProvinces = [5,6,13];
            if (in_array($caseProvince, $tulcanProvinces)) {
                //carga punto focal de proteccion en base a rol
                $puntoFocalTucanEmail = $this->coreModel->nodeModel("users")
                    ->fields(["email","full_name"])
                    ->where("m.id IN(SELECT parent_id FROM users_roles WHERE value_id = 'focal_point_protection_tulcan')")
                    ->loadFirstOrFail();
                $protection_fp_email = $puntoFocalTucanEmail->email;
                $protection_fp_name = $puntoFocalTucanEmail->full_name;

               //cambia el cw por el fp de proteccion
                $case->case_worker = $puntoFocalTucanEmail->id;
                $justification = "Asignó el caso al punto focal de protección $protection_fp_name";
            }

            $huaquillasProvinces = [4];
            if (in_array($caseProvince, $huaquillasProvinces)) {
                //carga punto focal de proteccion en base a rol
                $puntoFocalHuaquillasEmail = $this->coreModel->nodeModel("users")
                    ->fields(["email","full_name"])
                    ->where("m.id IN(SELECT parent_id FROM users_roles WHERE value_id = 'focal_point_protection_huaquillas')")
                    ->loadFirstOrFail();
                $protection_fp_email = $puntoFocalHuaquillasEmail->email;
                $protection_fp_name = $puntoFocalHuaquillasEmail->full_name;

               //cambia el cw por el fp de proteccion
                $case->case_worker = $puntoFocalHuaquillasEmail->id;
                $justification = "Asignó el caso al punto focal de protección $protection_fp_name";
            }

            $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'EvaluationAfterSubmission.html', true);
            $caseLink = "https://ecuadorims.iom.int/beneficiaries/show/{$case->id}";
            $caseInfo = "<li><a href='{$caseLink}'>Codigo de caso: {$case->code}</a></li>";
            $evaluationLink = "https://ecuadorims.iom.int/evaluation/edit/{$evaluation->id}";
            $evaluationInfo = "<li><a href='{$evaluationLink}'>Codigo de evaluación: {$evaluation->code}</a></li>";


            $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
            $body = _str_replace("{{fpname}}", $protection_fp_name, $body);
            $body = _str_replace("{{case}}", $caseInfo, $body);
            $body = _str_replace("{{caseEvaluation}}", $evaluationInfo, $body);

            $attachments = LOGO_FULL_PATH;

            //Send email to the user's email
            (new EmailService($protection_fp_email, "Nuevo caso de Protección", $body))
                ->setUserId($this->app->user->getSystemUserId())
                ->setCtypeId("evaluation")
                ->setRecordId($evaluation->id)
                ->setAttachments($attachments)
                ->sendNow();

            return $justification;
        }
    }
}
