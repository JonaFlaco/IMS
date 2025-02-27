<?php 

namespace App\InternalApi;

use App\Core\Application;
use App\Core\Response;
use App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use App\Helpers\DateHelper;

class BgTasksRun extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){

        $data = Application::getInstance()->coreModel->nodeModel("bg_tasks")
            ->fields(["status_id","created_user_id"])
            ->id($id)
            ->loadFirst();
        
        if($data->created_user_id != Application::getInstance()->user->getId()) {
            throw new ForbiddenException("Unable change background task of other users");
        }
        
        if(in_array($data->status_id, [1,73])) {
            \App\Core\BgTask::execInBg($id);
            Application::getInstance()->response->returnSuccess();
        } else {
            throw new \App\Exceptions\IlegalUserActionException("Can not retry if task is not failed");
        }

    }

}
