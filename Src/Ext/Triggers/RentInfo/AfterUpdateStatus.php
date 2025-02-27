<?php

namespace Ext\Triggers\RentInfo;

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
        $rentInfo = $this->coreModel->nodeModel("rent_info")
            ->fields(['rent_name', 'rent_phone', 'rent_id_no', 'rent_email'])
            ->id($id)
            ->loadFirstOrFail();

        if ($to_status_id == 2)
            $this->sendApprovedEmailNotification($rentInfo);

        if ($to_status_id == 3)
            $this->sendRejectedEmailNotification($rentInfo);
    }

    function sendApprovedEmailNotification($rentInfo)
    {
        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'RentInfoNotificationApproved.html', true);

        $body = _str_replace("{{title}}", "¡El estado de su perfil ha cambiado!", $body);
        $body = _str_replace("{{applicant_name}}", $rentInfo->rent_name, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{link}}", "https://ecuadorims.iom.int/surveys/show/renter_form", $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($rentInfo->rent_email, "¡El estado de su perfil ha cambiado!", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("rent_info")
            ->setRecordId($rentInfo->id)
            ->setAttachments($attachments)
            ->sendNow();
    }

    function sendRejectedEmailNotification($rentInfo)
    {
        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'RentInfoNotificationRejected.html', true);

        $body = _str_replace("{{title}}", "¡El estado de su perfil ha cambiado!", $body);
        $body = _str_replace("{{applicant_name}}", $rentInfo->rent_name, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{link}}", "https://ecuadorims.iom.int/surveys/show/renter_form", $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($rentInfo->rent_email, "El estado de su solicitud ha cambiado", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("rent_info")
            ->setRecordId($rentInfo->id)
            ->setAttachments($attachments)
            ->sendNow();
    }
}
