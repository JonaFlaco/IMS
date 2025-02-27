<?php 

/*
 * This controller handles modules requests
 */

namespace App\Core;

use App\Core\Controller;
use App\Core\Application;
use App\Exceptions\ForbiddenException;

class Modules extends Controller{

    public function __construct($module){
        parent::__construct();

        $this->module = $module;
        //check current user is logged in
        $this->app->user->checkAuthentication();
        
    }

    


        
    /**
     * index
     *
     * @return void
     *
     * Main function
     */
    public function index(){

        //get module items
        $items = $this->coreModel->get_module_items($this->module->id);

        if(sizeof($items) == 0 && !Application::getInstance()->user->isAdmin()){
            throw new ForbiddenException();
        }
        
        //check if module has custom template
        $file =  APP_ROOT_DIR . "/Views/ModulesHomepage/" . toPascalCase($this->module->id) . ".php";
        if(!is_file($file)){
            $file =  EXT_ROOT_DIR . "/Views/ModulesHomepage/" . toPascalCase($this->module->id) . ".php"; 
        }
        
        //return the module
        $data = [
            'title' => $this->module->name,
            'items' => $items,
            'nodeData' =>$this->module
        ];

        
        if(is_file($file)){
            require_once $file;
            exit;
        }
        
        $this->renderView('system/module',$data);

    }
}
