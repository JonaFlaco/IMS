<?php 

/**
 * This class is starting point to sync one crons or all scheduled crons. 
 * This class will receive request from user or scheduled task and send it to gcron from libraries to process it
 */

namespace App\Actions;

use App\Core\Controller;
use App\Core\Response;

class RunCronJobs extends Controller {
    
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
        
        // Get scheduled crons from database
        $crons = $this->coreModel->nodeModel("crons")
            ->where("m.status_id = 82")
            ->where("m.job_id = :id")
            ->bindValue(":id", $id)
            ->loadFc(false)
            ->load();
        
        //Loop throw them one by one
        foreach($crons as $cron){
             
            //Set batch size
            $batchSize = $cron->batch_size;

            if(empty($batchSize))
                $batchSize = 1;
            
            $this->syncForm($cron->id, $batchSize);

        }

    }


    private function syncForm($cronId, $batchSize) {
        
        if($this->app->request->isCli()) {

            $ims_file = ROOT_DIR . DS . "console" . DS . "ims.php";
            
            $command = sprintf("php %s run_cron %s %d", $ims_file, $cronId, $batchSize);
            
            $output = null;
            $retval = null;

            exec($command, $output, $retval);
            
            echo "Run cron #$cronId: [" . implode(' - ',$output) . "]\n";
            
        } else {
                
            (new \App\Actions\RunCron())->index($cronId, ["batch_size" => $batchSize]);

        }
    }
}
