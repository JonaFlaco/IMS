<?php 

namespace App\InternalApi;

use App\Core\Application;
use App\Core\Response;
use App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use App\Helpers\DateHelper;

class BgTasksDelete extends BaseInternalApi {
    
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
        
        if($data->status_id == 22 || $data->status_id == 73 || $data->status_id == 1) {
            Application::getInstance()->coreModel->bg_tasks_delete($id);
            Application::getInstance()->response->returnSuccess();
        }

    }

}
