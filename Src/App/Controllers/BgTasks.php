<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;

class BgTasks extends Controller {

    public function __construct(){
        parent::__construct();
        
    }
    


    public function run($task_id = null) {
        
        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_TOO_LONG);
        
        if(empty($task_id)) {
            $tasks = $this->coreModel->nodeModel("bg_tasks")
                ->fields(["id"])
                ->where("status_id = 1")
                ->OrderBy("id")
                ->load();
            foreach($tasks as $task) {
                (new \App\Core\BgTask($task->id))->run();    
            }
        } else  {
            (new \App\Core\BgTask($task_id))->run();
        }

        $this->app->response->returnSuccess();
    }

}

