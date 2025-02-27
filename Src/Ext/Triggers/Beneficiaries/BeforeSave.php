<?php

namespace Ext\Triggers\beneficiaries;

use App\Core\BaseTrigger;
use App\Core\Application;
use App\Exceptions\IlegalUserActionException;
use DateTime;

class BeforeSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($data, $is_update = false)
    {

        $userId =  Application::getInstance()->user->getId();
        $userAdmin =  Application::getInstance()->user->isAdmin();

        $userProvinces = $this->coreModel->nodeModel("users")
            ->where("m.id = :id")
            ->bindValue("id", $userId)
            ->load();
        if ($is_update != false && $userProvinces) {
            $id = $data->tables[0]->data->id;
            $case = $this->coreModel->nodeModel("beneficiaries")
                ->id($id)
                ->loadFirstOrFail();

            //verifica las provinces del usuario
            $provinceExists = false;
            $provinceToCheck = $data->tables[0]->data->province;

            foreach ($userProvinces[0]->provinces as $province) {
                if ($province->value == $provinceToCheck) {
                    $provinceExists = true;
                    break;
                }
            }
            $hasChanges = false;

            function compareValues($value1, $value2, &$hasChanges)
            {
                if ($value1 != $value2) {
                    $hasChanges = true;
                }
            }
            compareValues($case->full_name, $data->tables[0]->data->full_name, $hasChanges);
            compareValues($case->national_id_no, $data->tables[0]->data->national_id_no, $hasChanges);
            compareValues($case->medio_contacto, $data->tables[0]->data->medio_contacto, $hasChanges);
            compareValues($case->nationality_id, $data->tables[0]->data->nationality_id, $hasChanges);
            compareValues($case->national_id_photo_front_original_name, $data->tables[0]->data->national_id_photo_front_original_name, $hasChanges);

            $fechaOriginal = $case->birth_date;
            $fechaNew = $data->tables[0]->data->birth_date;

            $dateTimeOriginal = DateTime::createFromFormat('Y-m-d H:i:s.u', $fechaOriginal);
            $dateTimeNew = DateTime::createFromFormat('d/m/Y H:i:s', $fechaNew);

            if (!$dateTimeOriginal || !$dateTimeNew) {
                $hasChanges = true;
            } else {
                if ($dateTimeOriginal != $dateTimeNew) {
                    $hasChanges = true;
                }
            }

            $familyInfoCase = $case->family_information;
            $familyInfoData = $data->tables[3]->data->data->tables;

            // Comparar la cantidad de familiares registrados
            if (count($familyInfoCase) !== count($familyInfoData)) {
                $hasChanges = true;
            }
            // Función para comparar campos en el array de familiares 
            function compareFamilyObjects($caseObj, $dataObj, &$hasChanges)
            {
                $fieldsToCompare = [
                    'id',
                    'full_name',
                    'parent_id',
                    'relationship',
                    'nationality',
                    'family_national_id',
                    // 'id_photo_family_name',
                    // 'id_photo_family_original_name',
                    // 'id_photo_family_size',
                    // 'id_photo_family_type',
                    // 'id_photo_family_extension'
                ];

                foreach ($fieldsToCompare as $field) {
                    if ($caseObj->$field != $dataObj->data->$field) {
                        $hasChanges = true;
                        return;
                    }
                }

                $birthdateCase = DateTime::createFromFormat('Y-m-d H:i:s.u', $caseObj->birthdate);
                $birthdateData = DateTime::createFromFormat('d/m/Y H:i:s', $dataObj->data->birthdate);

                if (!$birthdateCase || !$birthdateData || $birthdateCase != $birthdateData) {
                    $hasChanges = true;
                }
            }

            $minLength = min(count($familyInfoCase), count($familyInfoData));

            for ($i = 0; $i < $minLength; $i++) {
                compareFamilyObjects($familyInfoCase[$i], $familyInfoData[$i], $hasChanges);
            }

            if ($hasChanges == true && !$provinceExists) {
                throw new IlegalUserActionException("Los cambios no se han guardado porque este caso no pertenece a su sub oficina, solo puede editar los campos provincia y número de teléfono, recuerde que al cambiar la provincia enviará un correo automático notificando al focal point de la provincia seleccionada.");
            }
        }


        //Gets the survey machine name from the HTTP request
        $survey_id = Application::getInstance()->request->getParam("survey_id");

        $fecha_actual = new DateTime();
        if ($data->tables[3]->data->data->tables) {
            foreach ($data->tables[3]->data->data->tables as $familyMember) {
                if (isset($familyMember->data->full_name)) {
                    $familyMember->data->full_name = strtoupper($familyMember->data->full_name);
                }
                $fecha_nacimiento = DateTime::createFromFormat('d/m/Y H:i:s', $familyMember->data->birthdate);
                $calculate = $fecha_actual->diff($fecha_nacimiento);
                $familyMemberAge = $calculate->y;
                $familyRelationship = $familyMember->data->relationship;

                // if ($familyMemberAge < 18 && ($familyRelationship == 1 || $familyRelationship == 2)) {
                //     throw new IlegalUserActionException("No se puede registrar una relación espos@ menor de edad");
                // }

                if ($familyMemberAge < 18 && ($familyRelationship == 7 || $familyRelationship == 8)) {
                    throw new IlegalUserActionException("No se puede registrar una relación abuel@ menor de edad");
                }

                if ($familyMemberAge > 18 && $familyRelationship == 11) {
                    throw new IlegalUserActionException("Para registrar un menor en acogida, debe ser menor de 18 años");
                }

                if ($fecha_nacimiento > $fecha_actual) {
                    throw new IlegalUserActionException("Revise las fechas de nacimiento registradas en los miembros familares");
                }
            }

            $relationships = array_column($data->tables[3]->data->data->tables, 'data');
            $count1 = 0;
            $count2 = 0;

            foreach ($relationships as $relationship) {
                if ($relationship->relationship == 1) {
                    $count1++;
                }
                if ($relationship->relationship == 2) {
                    $count2++;
                }
            }

            if ($count1 > 1 || $count2 > 1 || ($count1 > 0 && $count2 > 0)) {
                throw new IlegalUserActionException("Solo se puede registrar una pareja por aplicante");
            }
        }

        $is_focal_point = false;
        if ($userProvinces) {
            foreach ($userProvinces[0]->roles as $role) {
                if ($role->value === 'bm_focal_point') {
                    $is_focal_point = true;
                    break;
                }
            }
        }
        //solo el focal point puede editar casos sin gestor asignado 
        // if ($is_update != false && !$data->tables[0]->data->case_worker  && $hasChanges == true) {
        //     throw new IlegalUserActionException("Este caso aun no ha sido asignado a un gestor , solo puede editar el telefono o la provincia");
        // }
        //ajusta los permisos de edicion par que el gestor no edite casos de otro gestor y habilita permiso de edicion para el focal point
        // if ($is_update != false && $userId != $data->tables[0]->data->case_worker && !$is_focal_point) {
        //     throw new IlegalUserActionException("Este caso fue asignado a otro gestor");
        // }

        $fecha_nacimiento = DateTime::createFromFormat('d/m/Y H:i:s', $data->tables[0]->data->birth_date);
        $calculate = $fecha_actual->diff($fecha_nacimiento);
        $edad = $calculate->y;

        if ($userId == 10542 || !$userId) {
            if ($edad < 18 || $edad > 100) {
                throw new IlegalUserActionException("En caso de ser menor de edad (menos de 18 años), para registrarse en nuestra base de datos y tener la posibilidad de ser evaluado/a para una posible asistencia, por favor envíe un correo electrónico, indicando sus nombres y un número de contacto a: iomecpi@iom.int El tiempo de espera para ser contactado/a, no es inmediato, debido a la lista de espera que tenemos, agradecemos su comprensión");
            }
        }
        if ($is_update == false && $userId) {
            $data->tables[0]->data->case_worker = $userId;
            $data->tables[0]->data->status_id = 96;
        }
        if ($fecha_nacimiento > $fecha_actual) {
            throw new IlegalUserActionException("Revise la fecha de nacimiento registrada");
        }

        if ($data->tables[0]->data->national_id_no == $data->tables[0]->data->passport_no) {
            throw new IlegalUserActionException("El numero de  cedula esta repetido con  pasaporte");
        }
        if ($data->tables[0]->data->national_id_no == $data->tables[0]->data->birth_certificate_no) {
            throw new IlegalUserActionException("El numero de  cedula esta repetido con  certificado de nacimiento");
        }

        if ($data->tables[0]->data->national_id_no == $data->tables[0]->data->other_id_no) {
            throw new IlegalUserActionException("El numero de  cedula esta repetido con otro documento de identidad");
        }

        if ($data->tables[0]->data->passport_no == $data->tables[0]->data->birth_certificate_no && $data->tables[0]->data->passport_no != '') {
            throw new IlegalUserActionException("El numero de  pasaporte esta repetido con  certificado de nacimiento");
        }
        if ($data->tables[0]->data->passport_no == $data->tables[0]->data->other_id_no && $data->tables[0]->data->passport_no != '') {
            throw new IlegalUserActionException("El numero de  pasaporte esta repetido con otro documento de identidad");
        }

        if ($data->tables[0]->data->birth_certificate_no == $data->tables[0]->data->other_id_no && $data->tables[0]->data->birth_certificate_no != '') {
            throw new IlegalUserActionException("El numero de  certificado de nacimiento esta repetido con otro documento de identidad");
        }

        //agrega numero de adultos
        $adults_number = 0;
        $male_adults = 0;
        $female_adults = 0;
        $male_nna = 0;
        $female_nna = 0;

        foreach ($data->tables[3]->data->data->tables as $member) {
            $birthdate = DateTime::createFromFormat('d/m/Y H:i:s', $member->data->birthdate);
            $age = $birthdate->diff($fecha_actual)->y;

            if ($age >= 18)
                $adults_number += 1;

            if ($age >= 18 && $member->data->gender_id == 1)
                $male_adults += 1;

            if ($age >= 18 && $member->data->gender_id == 2)
                $female_adults += 1;

            if ($age < 18 && $member->data->gender_id == 1)
                $male_nna += 1;

            if ($age < 18 && $member->data->gender_id == 2)
                $female_nna += 1;
        }


        if ($data->tables[0]->data->birth_date) {
            $apBirthdate = DateTime::createFromFormat('d/m/Y H:i:s', $data->tables[0]->data->birth_date);
            $apAge = $apBirthdate->diff($fecha_actual)->y;
            //agrega al aplicante principal como adulto si es mayor de edad
            if ($apAge >= 18)
                $adults_number += 1;

            if ($apAge >= 18 && $data->tables[0]->data->gender_id == 1)
                $male_adults += 1;

            if ($apAge >= 18 && $data->tables[0]->data->gender_id == 2)
                $female_adults += 1;
        }

        if (isset($data->tables[0]->data->full_name)) {
            $data->tables[0]->data->full_name = strtoupper($data->tables[0]->data->full_name);
        }

        if ($adults_number)
            $data->tables[0]->data->adults_number = $adults_number;
        else
            $data->tables[0]->data->adults_number = 0;

        $data->tables[0]->data->male_adults = $male_adults;
        $data->tables[0]->data->female_adults = $female_adults;
        $data->tables[0]->data->male_nna = $male_nna;
        $data->tables[0]->data->female_nna = $female_nna;

        $duplication_result = $this->app->coreModel->nodeModel("beneficiaries")
            ->fields(["code"])
            ->Where("m.status_id not in (3,39) and m.recommended_service_id = :recommended_service_id and (m.full_name  = :full_name or m.national_id_no  = :national_id_no) and m.national_id_no != :no_disponible")
            ->bindValue(":full_name", $data->tables[0]->data->full_name)
            ->bindValue(":national_id_no", $data->tables[0]->data->national_id_no)
            ->bindValue(":recommended_service_id", $data->tables[0]->data->recommended_service_id)
            ->bindValue(":no_disponible", "No disponible")
            ->load();

        if ($is_update) {
            foreach ($duplication_result as $itm) {
                if ($itm->id != $data->tables[0]->data->id)
                    throw new IlegalUserActionException("Nombre o numero de cedula ya fue registrado para este servicio");
            }
        } else if (!$is_update && !empty($duplication_result)) {
            throw new IlegalUserActionException("Nombre o numero de cedula ya fue registrado para este servicio");
        }


        $service = $this->coreModel->nodeModel("services")
            ->fields(['unit_id'])
            ->id($data->tables[0]->data->recommended_service_id)
            ->loadFirstOrFail("Service not exit");

        $data->tables[0]->data->unit_id = $service->unit_id;
    }
}
