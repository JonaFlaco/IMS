<?php

namespace Ext\InternalApi;

use App\Core\Controller;
use App\Exceptions\IlegalUserActionException;
use App\Core\Communications\EmailService;

class CaseWorkerAssignmentUpdate extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $userId = $_POST['selectedUserId']; // get the user id from form
        $ids = $_POST['id']; // get id from the record selected
        $ids_array = explode(',', $ids);

        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'BeneficiaryReAssignament.html', true);

        $body = _str_replace("{{title}}", "Te han reasignado un nuevo caso", $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);

        $casesInfo = "";
        $assignedCasesInfo = "";

        foreach ($ids_array as $id) {
            // Load by ID and update the case 
            $case = $this->coreModel->nodeModel("beneficiaries")
                ->fields(["id", "full_name", "code", "case_worker", "logued_user"])
                ->id($id)
                ->loadFirstOrFail();

            $caseId = $case->id;
            $caseLink = "https://ecuadorims.iom.int/beneficiaries/show/{$caseId}";

            if ($case->case_worker) {
                $assignedCasesInfo .= "<li><a href='{$caseLink}'>Codigo de caso: {$case->code}</a></li>";
            } else {
                // Agregar información del caso al cuerpo del correo
                $casesInfo .= "<li><a href='{$caseLink}'>Codigo de caso: {$case->code}</a></li>";
            }

            $case->case_worker = $userId;
            $users = $this->coreModel->nodeModel("users")
                ->fields(["id", "full_name", "email"])
                ->id($userId)
                ->loadFirstOrFail();

            $cwAdded = $users->full_name;
            $case->status_id = 96;

            // Guardar el caso en la base de datos
            $this->coreModel->node_save($case, ["ignore_post_save" => true, "ignore_pre_save" => true, "justification" => "Actualizó el case worker a $cwAdded"]);
        }

        $body = _str_replace("{{case}}", $casesInfo, $body);
        $body = _str_replace("{{casesInfo}}", $assignedCasesInfo, $body);
        $body = _str_replace("{{cwname}}", $users->full_name, $body);

        $attachments = LOGO_FULL_PATH;

        //Send email to the user's email
        (new EmailService($users->email, "Reasignación de caso", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("beneficiaries")
            ->setRecordId($case->id)
            ->setAttachments($attachments)
            ->sendNow();

        return_json(['status' => 'success', 'message' => 'Case worker actualizado correctamente']);
    }
}
