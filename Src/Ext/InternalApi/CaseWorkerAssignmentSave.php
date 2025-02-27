<?php

namespace Ext\InternalApi;

use App\Core\Controller;
use App\Exceptions\IlegalUserActionException;
use App\Core\Communications\EmailService;

class CaseWorkerAssignmentSave extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $userId = $_POST['selectedUserId']; // get the user id from form
        $ids = $_POST['id']; // get ids from the records selected
        $loguedUser = $this->app->user->getId();
        $ids_array = explode(',', $ids);

        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'BeneficiaryAssignament.html', true);
        $body = str_replace("{{title}}", "Te han asignado nuevos casos", $body);
        $body = str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);

        $casesInfo = "";

        foreach ($ids_array as $id) {
            // Load by ID and update the case 
            $case = $this->coreModel->nodeModel("beneficiaries")
                ->fields(["id", "full_name", "code", "case_worker", "logued_user"])
                ->id($id)
                ->loadFirstOrFail();

            if ($case->case_worker) {
                return_json(['status' => 'missed', 'message' => 'Este caso ya tiene asignado un case Worker']);
            }

            $case->case_worker = $userId;
            $case->logued_user = $loguedUser;
            $case->status_id = 96;
            $users = $this->coreModel->nodeModel("users")
                ->fields(["id", "full_name", "email"])
                ->id($userId)
                ->loadFirstOrFail();

            $cwAdded = $users->full_name;

            // Guardar el log del caso
            $this->coreModel->node_save($case, ["ignore_post_save" => true, "ignore_pre_save" => true, "justification" => "Asignó a $cwAdded como case worker"]);
            $caseId = $case->id;
            $caseLink = "https://ecuadorims.iom.int/beneficiaries/show/{$caseId}";
            // Agregar información del caso al cuerpo del correo
            $caseInfo = "<li><a href='{$caseLink}'>Codigo de caso: {$case->code}</a></li>";
            $casesInfo .= $caseInfo;
        }

        // Reemplazar la información del caso en el cuerpo del correo
        $body = str_replace("{{case}}", $casesInfo, $body);
        $body = str_replace("{{cwname}}", $users->full_name, $body);

        $attachments = LOGO_FULL_PATH;

        // Enviar correo al case worker
        (new EmailService($users->email, "Asignación de casos", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("beneficiaries")
            ->setAttachments($attachments)
            ->sendNow();

        return_json(['status' => 'success', 'message' => 'Case worker asignado correctamente']);
    }
}
