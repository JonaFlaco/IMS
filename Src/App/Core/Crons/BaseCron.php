<?php

/*
 * This is base class for syncing ODK Form, contains some helper classes
 */

namespace App\Core\Crons;

use App\Core\Application;
use App\Exceptions\ForbiddenException;
use App\Models\CTypeLog;

class BaseCron {

    public $coreModel;
    public Application $app;
    public $ukey;
    
    protected $id;
    protected $params;
    
    public function __construct() {
        
        $this->coreModel = Application::getInstance()->coreModel;

        
        if(Application::getInstance()->user->isNotAdmin() && Application::getInstance()->request->isLocal() != true && !Application::getInstance()->request->isCli()){
            throw new ForbiddenException();
        }

        $this->app = Application::getInstance();
    }

    public function index($id, $params = []) {
        
        $this->id = $id;
        $this->params = $params;

        $this->ukey = \App\Helpers\MiscHelper::randomString(25);
    
        Application::getInstance()->coreModel->addCronLog($this->ukey, $id, "started", "Started");

        try {
            
            if($this->run() === false) {
                // Application::getInstance()->coreModel->addCronLog($this->ukey, $id, "failed", "The cron encountered some issues while running"); 
                throw new \App\Exceptions\MissingDataFromRequesterException("Failed! The cron encountered some issues while running");

            } else {
                Application::getInstance()->coreModel->addCronLog($this->ukey, $id, "finished", "Cron ran successfuly");
            }

        } catch(\Exception $exc) {
            Application::getInstance()->coreModel->addCronLog($this->ukey, $id, "failed", $exc->getMessage());
            throw $exc;
        }
        
    }

}