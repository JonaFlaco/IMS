<?php

/**
 * Base class for controllers
 */

namespace App\Core;

class BaseExternalApi extends Controller {

    protected $userObj;

    public function __construct($loginUsingBasicAuth = false) {
        parent::__construct();
        
        $lang = _strtolower($this->app->request->getParam("lang"));
        if (!empty($lang)) {
            
            $langObj = $this->coreModel->nodeModel("languages")
                ->id($lang)
                ->fields(["name", "direction"])
                ->loadFirstOrDefault();
            
            if (isset($langObj)) {
                $this->app->session->set('user_lang_id', $langObj->id);
                $this->app->session->set('user_lang_direction', $langObj->direction);
                $this->app->session->set('user_lang_name', $langObj->name);
            }
        }

        $this->app->response->setResponseFormat(Response::$FORMAT_JSON);
        
        if($loginUsingBasicAuth) {
            $this->userObj = Application::getInstance()->user->LoginUsingBasicAuth();
        }
        
    }
    
    
}