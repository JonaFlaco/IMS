<?php

/**
 * Base class for controllers
 */

namespace App\Core;

use \App\Models\CoreModel;

class Controller {

    protected CoreModel $coreModel;
    protected Application $app;

    public function __construct() {
        $this->app = Application::getInstance();
        $this->coreModel = Application::getInstance()->coreModel;
    }
    
    public function renderView(string $view,array $data = array(), bool $useCache = false) {
        
        Application::getInstance()->view->renderView($view, $data, $useCache);
    }


}