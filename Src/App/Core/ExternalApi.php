<?php

/**
 * Base class for controllers
 */

namespace App\Core;

class ExternalApi extends Controller {

    protected $userObj;

    public function __construct($loginUsingBasicAuth = false) {
        parent::__construct();
        
        $this->app->response->setResponseFormat(Response::$FORMAT_JSON);
        
        if($loginUsingBasicAuth) {
            $this->userObj = Application::getInstance()->user->LoginUsingBasicAuth();
        }
        
    }
    
    
}