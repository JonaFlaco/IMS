<?php 

/**
 * This class is starting point to sync one crons or all scheduled crons. 
 * This class will receive request from user or scheduled task and send it to gcron from libraries to process it
 */

namespace App\Actions;

use App\Core\Controller;
use App\Core\Response;

class RunCron extends Controller {
    
    public function __construct(){
        parent::__construct();

        if($this->app->user->isAdmin() || $this->app->request->isLocal() || $this->app->request->isCli()){
            
            //Since the request might need some time, set execution timeout to long
            $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_TOO_LONG);

        } else {
            throw new \App\Exceptions\ForbiddenException();
        }


    }

    /**
        * index
        *
        * @param  string $id
        * @param  array $params
        * @return void
        */
    public function index($id, $params = []){
        
        //Check if requester want to sync one specific gron
        if(empty($id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
        }
            
        (new \App\Core\Crons\RunCron($id, $params))->run();

        return $this->app->response->returnSuccess();
        
    }

}
