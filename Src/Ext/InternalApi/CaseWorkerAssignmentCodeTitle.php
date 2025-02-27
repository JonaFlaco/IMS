<?php
namespace Ext\InternalApi;

use App\Core\Controller;

class CaseWorkerAssignmentCodeTitle extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Verifica si el usuario está autenticado o en local
        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        if (isset($_GET['id'])) {
            $ids = explode(',', $_GET['id']);
            $caseCodes = [];
            $assignedCaseCodes = [];
            $assignedCaseWorwerCodes = [];

            foreach ($ids as $id) {
                $case = $this->coreModel->nodeModel("beneficiaries")
                    ->fields(["code", "case_worker"])
                    ->id($id)
                    ->loadFirstOrFail();

                $caseCodes[] = $case->code;
                if($case->case_worker){
                    $assignedCaseCodes[] = $case->code;
                    $assignedCaseWorwerCodes[] = $case->case_worker_display;
                }
            }

            $userId = $_GET['selectedUserId']; // get the user id from form
            $users = $this->coreModel->nodeModel("users")
                ->fields(["id", "full_name"])
                ->id($userId)
                ->loadFirstOrFail();

            $cwAdded = $users->full_name;

            return_json(['codes' => $caseCodes, 'assignedcodes' => $assignedCaseCodes,'assignedcaseworkers' => $assignedCaseWorwerCodes,'cw' => $case->case_worker_display, 'new_cw' => $cwAdded]);
        } else {
            return_json(['error' => 'No se proporcionó un ID válido']);
        }
    }
}
