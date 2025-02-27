<?php

namespace Ext\Triggers\RenterLogin;

use App\Core\BaseTrigger;
use App\Core\Node;
use App\Core\Communications\EmailService;

class AfterSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $data, $is_update = false)
    {
        $renter = $this->coreModel->nodeModel("renter_login")
            ->id($id)
            ->loadFirstOrFail();

        $node = new Node("survey_credentials");
        $node->name = $renter->renter_user;
        $node->password = $renter->renter_password;
        $node->is_active = 1;
        $node->surveys_display = "Formulario para registrar las viviendas y a el Arrendador";
        $node->ctype_id = "rent_info";
        $node->ctype_id_display = "Registro de arrendadores";
        $node->surveys = ['renter_form'];
        $node->save();

        if ($is_update != true)
            $this->sendEmail($renter);
    }
    function sendEmail($renter)
    {
        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'renterAfterSubmission.html', true);

        $body = _str_replace("{{title}}", "¡Su cuenta ha sido creada exitosamente!", $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{applicant_name}}", $renter->renter_user, $body);
        $body = _str_replace("{{username}}", $renter->renter_user, $body);
        $body = _str_replace("{{password}}", $renter->renter_password, $body);
        $body = _str_replace("{{link}}", "https://ecuadorims.iom.int/surveys/show/renter_form", $body);

        $attachments = LOGO_FULL_PATH;

        //Send email to the user's email
        (new EmailService($renter->renter_email, "¡Credenciales de Acceso Red de Arrendadores!", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("renter_login")
            ->setRecordId($renter->id)
            ->setAttachments($attachments)
            ->sendNow();
    }
}
