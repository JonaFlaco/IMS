<?php

/**
 * This class is responsible to retrive data from ODK Aggregate without writing custom code for each form.
 * For that we have 'Crons' table in which field mapping for each form is stored. 
 * This class will get field mapping from 'Crons' and based on that it will retrive data from ODK Aggregate an save it in its Content-Type. 
 */

namespace App\Core\Crons;

use App\Core\Application;
use App\Core\Gctypes\Ctype;

Class RunCron {

    private $id;
    private $params;
    private $cronObj;
    private $coreModel;
    
    public function __construct(string $id,array $params = array()) {
        
        $this->id = $id;
        $this->params = $params;

        $this->batchSize = 1;
        if(isset($params['batch_size']) && intval($params['batch_size']) > 0){
            $this->batchSize = intval($params['batch_size']);
        }

        $this->coreModel = Application::getInstance()->coreModel;
        
        $this->cronObj = $this->coreModel->nodeModel("crons")
            ->id($this->id)
            ->loadFirstOrFail();

        if(!empty($this->cronObj->ctype_id)){
			$this->ctypeObj = (new Ctype)->load($this->cronObj->ctype_id);
		}

    }

    public function run() {

        if(in_array($this->cronObj->status_id, [72,83])) {
            throw new \App\Exceptions\IlegalUserActionException("Unable to run archived or abandoned crons");
        }
        
        //If the cron is custom then send it to its own class otherwise continue.
        if($this->cronObj->is_custom == true){

            //Find the class by namespace
            if($this->cronObj->is_system_object){
                $classToRun = "\\App\\Crons\\" . toPascalCase($this->cronObj->id); 
            } else {
                $classToRun = "\\Ext\\Crons\\" . toPascalCase($this->cronObj->id); 
            }
            
            //Check if the class exist
            if(class_exists($classToRun)){
                
                //If exist  then call it.
                $classObj = new $classToRun($this->cronObj);
                $classObj->cronObj = $this->cronObj;
                
                if(isset($this->ctypeObj)) {
                    $classObj->ctypeObj = $this->ctypeObj;
                }
                
                $classObj->batchSize = $this->batchSize;

                if(method_exists($classObj, "loadDb")) {
                    $classObj->loadDb();
                }
                
                if(!method_exists($classObj, "index")){
                    throw new \App\Exceptions\NotFoundException("Method index not found in class" . $classToRun);
                }
                
                $classObj->index($this->id, $this->params);
            } else {

                //If not exist show error
                throw new \App\Exceptions\NotFoundException("Class $classToRun not found");
            }

            return true;
        } else if($this->cronObj->type_id == "update_odk_pl") { //Update PL

            if(_strlen($this->cronObj->preload_list_name) == 0) {
                throw new \App\Exceptions\MissingDataFromRequesterException("Preloadlist name is required");
            }
            
            (new SyncOdkPreloads($this->cronObj->db_connection_string_id))->index($this->cronObj->id, $this->params);
        } else if($this->cronObj->type_id == "sync_kobo_form") { //Sync KOBO Form
            (new SyncKoboForm($this->cronObj->id, $this->params))->index($this->cronObj->id, $this->params);
        } else {
            
            (new SyncOdkForm($this->cronObj->id, $this->params))->index($this->cronObj->id, $this->params);
        }
        
        
    }

}
