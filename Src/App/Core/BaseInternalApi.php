<?php

/**
 * Base class for internal api
 */

namespace App\Core;

use App\Models\CoreModel;

class BaseInternalApi extends Controller {

    public CoreModel $coreModel;
    public Application $app;
    
    public function __construct() {
        parent::__construct();

        $this->app = Application::getInstance();
        $this->coreModel = CoreModel::getInstance();
        

        Application::getInstance()->response->setResponseFormat(Response::$FORMAT_JSON);

        //Check if the user is logged
        $this->app->user->checkAuthentication(true);
    
        
    }
    
}
