<?php

namespace Ext\Triggers\StoryClinic;

use App\Core\BaseTrigger;
use App\Core\Communications\EmailService;
use App\Core\Application;

class AfterSave extends BaseTrigger
{
    public function __construct()
    {
        parent::__construct();
    }


    public function index($id)
    {
        $storyClinic = $this->coreModel->nodeModel("story_clinic")
            ->id($id)
            ->loadFirstOrFail();

        $hasData = (
            isset($storyClinic->specialist) && count($storyClinic->specialist) > 0
        ) || (
            isset($storyClinic->ecogra) && count($storyClinic->ecogra) > 0
        ) || (
            isset($storyClinic->radiogra) && count($storyClinic->radiogra) > 0
        ) || (
            isset($storyClinic->laboratory) && count($storyClinic->laboratory) > 0
        ) || (
            isset($storyClinic->procedures) && count($storyClinic->procedures) > 0
        );

        if ($hasData) {
            $patientData = $this->getPatientData($storyClinic);
            $this->sendEmailNotification($patientData, $id);
        }
    }

    private function getPatientData($storyClinic)
    {
        $patientData = [];

        if ($storyClinic->beneficiary) {
            $beneficiary = $this->coreModel->nodeModel("beneficiaries")
                ->fields(["id", "code", "full_name"])
                ->where("m.id = :id")
                ->bindValue("id", $storyClinic->beneficiary)
                ->loadFirstOrDefault();

            if ($beneficiary) {
                $patientData[] = [
                    'code' => $beneficiary->code,
                    'full_name' => $beneficiary->full_name,
                ];
            }
        }
        if ($storyClinic->family_code) {
            $familyMembers = $this->coreModel->nodeModel("beneficiaries_family_information")
                ->fields(["code", "full_name"])
                ->where("m.id = :id")
                ->bindValue("id", $storyClinic->family_code)
                ->load();

            foreach ($familyMembers as $familyMember) {
                $patientData[] = [
                    'code' => $familyMember->code,
                    'full_name' => $familyMember->full_name,
                ];
            }
        }

        return $patientData;
    }

    function sendEmailNotification($patientData, $id)
    {
        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'ProvidersAssignament.html', true);

        $tableRows = '';
        foreach ($patientData as $patient) {
            $tableRows .= "<tr><td>{$patient['full_name']}</td><td>{$patient['code']}</td></tr>";
        }
        $table = "<table border='1'><thead><tr><th>Nombre del Paciente</th><th>Código del Paciente</th></tr></thead><tbody>$tableRows</tbody></table>";

        $body = _str_replace("{{title}}", "¡Pacientes!", $body);
        $body = _str_replace("{{patient_data}}", $table, $body);

        $currentUser = Application::getInstance()->user->getId();

        $userProvinces = $this->coreModel->nodeModel("users")
            ->where("m.id = :id")
            ->bindValue("id", $currentUser)
            ->loadFirstOrDefault();

        // Verificar si existen provincias y tomar el primer valor
        $provinceValue = null;
        if (!empty($userProvinces->governorates) && is_array($userProvinces->governorates)) {
            $provinceValue = $userProvinces->governorates[0]->value ?? null;
        }

        //Filtro Quito
        if ($provinceValue == "24") {
            $providerUio = $this->coreModel->nodeModel("users")
                ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'aprofe_quito')")
                ->loadFirstOrDefault();

            if ($providerUio) {
                $body = _str_replace("{{full_name}}", $providerUio->full_name, $body);

                // Enviar el correo
                (new EmailService($providerUio->email, "Pacientes esperando examenes medicos", $body))
                    ->setUserId($currentUser)
                    ->setCtypeId("story_clinic")
                    ->setRecordId($id)
                    ->sendNow();
            }
        }

        //filtro Guayaquil
        if ($provinceValue == "10") {
            $providerGye = $this->coreModel->nodeModel("users")
                ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'aprofe_gye')")
                ->loadFirstOrDefault();

            if ($providerGye) {
                $body = _str_replace("{{full_name}}", $providerGye->full_name, $body);

                // Enviar el correo
                (new EmailService($providerGye->email, "Pacientes esperando examenes medicos", $body))
                    ->setUserId($currentUser)
                    ->setCtypeId("story_clinic")
                    ->setRecordId($id)
                    ->sendNow();
            }
        }

        //filtro sucumbios
        if ($provinceValue == "16") {
            $providerSucumbios = $this->coreModel->nodeModel("users")
                ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'clinica_ema')")
                ->loadFirstOrDefault();

            if ($providerSucumbios) {
                $body = _str_replace("{{full_name}}", $providerSucumbios->full_name, $body);

                // Enviar el correo
                (new EmailService($providerSucumbios->email, "Pacientes esperando examenes medicos", $body))
                    ->setUserId($currentUser)
                    ->setCtypeId("story_clinic")
                    ->setRecordId($id)
                    ->sendNow();
            }
        }

        //filtro manta
        if ($provinceValue == "19") {
            $providerManta = $this->coreModel->nodeModel("users")
                ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'salud_amiga')")
                ->loadFirstOrDefault();

            if ($providerManta) {
                $body = _str_replace("{{full_name}}", $providerManta->full_name, $body);

                // Enviar el correo
                (new EmailService($providerManta->email, "Pacientes esperando examenes medicos", $body))
                    ->setUserId($currentUser)
                    ->setCtypeId("story_clinic")
                    ->setRecordId($id)
                    ->sendNow();
            }
        }
    }
}
