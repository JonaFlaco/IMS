<?php

namespace Ext\Triggers\EdfEoi;

use App\Core\BaseTrigger;
use App\Core\Communications\EmailService;


class AfterUpdateStatus extends BaseTrigger
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $from_status_id, $to_status_id, $step = 0, $total_steps = 0, $path = null, $justification = "")
    {
        $edfEoi = $this->coreModel->nodeModel("edf_eoi")->fields(['person_applying','code','password','aplicant_email'])->id($id)->loadFirstOrFail();

        if ($to_status_id == 2)
            $this->sendApprovedEmailNotification($edfEoi);

        if ($to_status_id == 3)
            $this->sendRejectedEmailNotification($edfEoi);

        if ($to_status_id == 86)
            $this->sendWaitlistedEmailNotification($edfEoi);
    }

    function sendApprovedEmailNotification($edfEoi)
    {
        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'EdfEoiNotificationApproved.html', true);

        $body = _str_replace("{{title}}", "¡El estado de su solicitud ha cambiado!", $body);
        $body = _str_replace("{{applicant_name}}", $edfEoi->person_applying, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{username}}", $edfEoi->code, $body);
        $body = _str_replace("{{password}}", $edfEoi->password, $body);
        $body = _str_replace("{{link}}", "https://edf_ecuador.iom.int/en/track-application", $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($edfEoi->aplicant_email, "¡El estado de su solicitud ha cambiado!", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("edf_eoi")
            ->setRecordId($edfEoi->id)
            ->setAttachments($attachments)
            ->sendNow();
    }

    function sendRejectedEmailNotification($edfEoi)
    {
        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'EdfEoiNotificationRejected.html', true);

        $body = _str_replace("{{title}}", "El estado de su solicitud ha cambiado", $body);
        $body = _str_replace("{{applicant_name}}", $edfEoi->person_applying, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{username}}", $edfEoi->code, $body);
        $body = _str_replace("{{password}}", $edfEoi->password, $body);
        $body = _str_replace("{{link}}", "https://edf_ecuador.iom.int/en/track-application", $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($edfEoi->aplicant_email, "El estado de su solicitud ha cambiado", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("edf_eoi")
            ->setRecordId($edfEoi->id)
            ->setAttachments($attachments)
            ->sendNow();
    }

    function sendWaitlistedEmailNotification($edfEoi)
    {
        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'EdfEoiNotificationWaitlisted.html', true);

        $body = _str_replace("{{title}}", "El estado de su solicitud ha cambiado", $body);
        $body = _str_replace("{{applicant_name}}", $edfEoi->person_applying, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{username}}", $edfEoi->code, $body);
        $body = _str_replace("{{password}}", $edfEoi->password, $body);
        $body = _str_replace("{{link}}", "https://edf_ecuador.iom.int/en/track-application", $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($edfEoi->aplicant_email, "El estado de su solicitud ha cambiado", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("edf_eoi")
            ->setRecordId($edfEoi->id)
            ->setAttachments($attachments)
            ->sendNow();
    }
}
