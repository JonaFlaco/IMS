<?php

namespace Ext\Triggers\EdfEoiVerification;

use App\Core\BaseTrigger;
use App\Core\Communications\EmailService;
use App\Exceptions\CriticalException;
use App\Core\Node;


class AfterUpdateStatus extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $from_status_id, $to_status_id, $step = 0, $total_steps = 0, $path = null, $justification = "")
    {
        $edfEoiVerification = $this->coreModel->nodeModel("edf_eoi_verification")
            ->id($id)
            ->loadFirstOrFail();
        $edfEoi = $this->coreModel->nodeModel("edf_eoi")
            ->id($edfEoiVerification->business_id)
            ->loadFirstOrFail();

        if ($to_status_id == 2) {
            $this->sendApprovedEmailNotification($edfEoiVerification, $edfEoi);
            $this->updateEoi($edfEoiVerification, $edfEoi);
            $this->coreModel->node_save($edfEoi, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => false, "justification" => "Actualizó info desde la fase de verificación"]);
        }

        if ($to_status_id == 3){
            $this->sendRejectedEmailNotification($edfEoiVerification, $edfEoi);
            $this->updateEoi($edfEoiVerification, $edfEoi);
            $this->coreModel->node_save($edfEoi, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => false, "justification" => "Actualizó info desde la fase de verificación"]);
        }

        if ($to_status_id == 86)
            $this->sendWaitlistedEmailNotification($edfEoiVerification, $edfEoi);
    }


    function sendApprovedEmailNotification($edfEoiVerification, $edfEoi)
    {
        $node = new Node("survey_credentials");
        $node->name = $edfEoi->code;
        $node->password = $edfEoi->password;
        $node->is_active = 1;
        $node->surveys_display = "Enterprise Development Fund (EDF) - Full Application";
        $node->ctype_id = "edf_eoi_full_application";
        $node->ctype_id_display = "EDF EOI Full Application";
        $node->surveys = ['edf_eoi_application'];
        $node->save();

        if ($edfEoiVerification->correct_aplicant_name)
            $aplicantName = $edfEoiVerification->correct_aplicant_name;
        else
            $aplicantName = $edfEoi->person_applying;

        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . '\\EdfVerNotificationApproved.html', true);

        $body = _str_replace("{{title}}", "¡El estado de su solicitud ha cambiado!", $body);
        $body = _str_replace("{{applicant_name}}", $aplicantName, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{username}}", $edfEoi->code, $body);
        $body = _str_replace("{{password}}", $edfEoi->password, $body);
        $body = _str_replace("{{link}}", "https://edf_ecuador.iom.int/en/track-application", $body);
        $body = _str_replace("{{surveylink}}", "https://ecuadorims.iom.int/surveys/show/edf_eoi_application", $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($edfEoi->aplicant_email, "¡El estado de su solicitud ha cambiado!", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("edf_eoi_verification")
            ->setRecordId($edfEoiVerification->id)
            ->setAttachments($attachments)
            ->sendNow();
    }
    function sendRejectedEmailNotification($edfEoiVerification, $edfEoi)
    {

        if ($edfEoiVerification->correct_aplicant_name)
            $aplicantName = $edfEoiVerification->correct_aplicant_name;
        else
            $aplicantName = $edfEoi->person_applying;

        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . '\\EdfVerNotificationRejected.html', true);

        $body = _str_replace("{{title}}", "El estado de su solicitud ha cambiado", $body);
        $body = _str_replace("{{applicant_name}}", $aplicantName, $body);
        $body = _str_replace("{{newStatus}}", $edfEoiVerification->status_id_display, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($edfEoi->aplicant_email, "El estado de su solicitud ha cambiado", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("edf_eoi_verification")
            ->setRecordId($edfEoiVerification->id)
            ->setAttachments($attachments)
            ->sendNow();
    }
    function sendWaitlistedEmailNotification($edfEoiVerification, $edfEoi)
    {

        if ($edfEoiVerification->correct_aplicant_name)
            $aplicantName = $edfEoiVerification->correct_aplicant_name;
        else
            $aplicantName = $edfEoi->person_applying;

        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . '\\EdfVerNotificationWaitlisted.html', true);

        $body = _str_replace("{{title}}", "El estado de su solicitud ha cambiado", $body);
        $body = _str_replace("{{applicant_name}}", $aplicantName, $body);
        $body = _str_replace("{{newStatus}}", $edfEoiVerification->status_id_display, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($edfEoi->aplicant_email, "El estado de su solicitud ha cambiado", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("edf_eoi_verification")
            ->setRecordId($edfEoiVerification->id)
            ->setAttachments($attachments)
            ->sendNow();
    }
    function updateEoi($verification, $edf)
    {

        if ($verification->correct_business_name)
            $edf->business_name = $verification->correct_business_name;

        if ($verification->correct_legal_name)
            $edf->legal_name = $verification->correct_legal_name;

        if ($verification->correct_aplicant_name)
            $edf->person_applying = $verification->correct_aplicant_name;

        if ($verification->correct_legal_rep_name)
            $edf->full_name_legal_rep = $verification->correct_legal_rep_name;

        if ($verification->correct_gender_rep)
            $edf->gender_legal_rep = $verification->correct_gender_rep;

        if ($verification->correct_ruc)
            $edf->single_taxpayer_registry_number = $verification->correct_ruc;

        if ($verification->relationship_no)
            $edf->relationship = $verification->relationship_no;

        if ($verification->other_specify)
            $edf->specify_relationship = $verification->other_specify;

        if ($verification->correct_sector)
            $edf->sector = $verification->correct_sector;
        
        if ($verification->correct_sub_sector)
            $edf->subsector = $verification->correct_sub_sector;

        if ($verification->correct_years_operation)
            $edf->operation_years_company = $verification->correct_years_operation;

        if ($verification->correct_current_employees)
            $edf->employees_number = $verification->correct_current_employees;

        if ($verification->correct_number_male_employees)
            $edf->male_employees_number = $verification->correct_number_male_employees;

        if ($verification->correct_number_nonbinary_employees)
            $edf->non_binary_employees = $verification->correct_number_nonbinary_employees;

        if ($verification->correct_number_female_employees)
            $edf->female_employees_number = $verification->correct_number_female_employees;

        if ($verification->correct_grant_iom)
            $edf->total_amount__iom = $verification->correct_grant_iom;

        if ($verification->correct_contribution_bsns)
            $edf->contribution_value = $verification->correct_contribution_bsns;

        if ($verification->correct_aditional_staff)
            $edf->additional_staff_need = $verification->correct_aditional_staff;

        return $edf;
    }
}
