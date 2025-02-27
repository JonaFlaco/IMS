<?php 

/**
 * This class is starting point to sync one crons or all scheduled crons. 
 * This class will receive request from user or scheduled task and send it to gcron from libraries to process it
 */

// namespace App\Externalapi;

// use App\Core\BaseExternalApi;
// use App\Core\Application;

// class RunCron extends BaseExternalApi {
    
//     public function __construct(){
//         parent::__construct();
        
//         $this->userObj = Application::getInstance()->user->LoginUsingBasicAuth();
        
//     }

//     /**
//         * index
//         *
//         * @param  string $id
//         * @param  array $params
//         * @return void
//         */
//     public function index($id, $params){
        
//         //Check if requester want to sync one specific gron
//         if(empty($id)){
//             throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
//         }
            
//         (new \App\Core\Crons\RunCron($id, $params))->run();

//         return $this->app->response->returnSuccess();
//     }
    
// }
