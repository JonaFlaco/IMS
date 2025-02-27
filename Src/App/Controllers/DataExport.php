<?php 

/*
 * This controller handles dataexport requests which meant to export, modify and import it again later on
 */

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Response;

class Dataexport extends Controller {

    public function __construct(){
        parent::__construct();
        //Set execution timeout to long time so it will not timeout if the excel file is big
        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_LONG);
        
        //Check if use is logged in or not
        $this->app->user->checkAuthentication();
        
    }

    


    
    /**
     * export
     *
     * @param  string $ctype_id
     * @param  array $params
     * @return void
     *
     * Export function
     */
    public function export($ctypeId, $params){

        $ctypeObj = $this->app->coreModel->nodeModel("ctypes")->id($ctypeId)->fields(["id", "name"])->loadFirst();

        (new \App\Core\BgTask())
            ->setName(sprintf("Export %s", $ctypeObj->name))
            ->setMainValue($ctypeId)
            ->setActionName("CoreCtypeExport")
            ->setPostData(Application::getInstance()->request->POST())
            ->addToQueue();
            
        Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;

    }

    


        
    /**
     * ExportIndividual
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * Export individual record
     */
    public function ExportIndividual($id, $params){

        //Send the data to its class
        \App\Libraries\DataExportIndividual::main($id, $params);

    }

}