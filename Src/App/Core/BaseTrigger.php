<?php

/**
 * Base class for triggers
 */

namespace App\Core;

use App\Models\CoreModel;

class BaseTrigger {

    public CoreModel $coreModel;
    public object $ctypeObj;
    public Application $app;
    
    public function __construct() {
        
        $this->app = Application::getInstance();
        $this->coreModel = CoreModel::getInstance();

        Application::getInstance()->response->setResponseFormat(Response::$FORMAT_JSON);
    }
    
}
