<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;

class Reports extends Controller {

    public function __construct(){
        parent::__construct();

        $this->app->user->checkAuthentication();
        
    }
    
    /**
     * index
     *
     * @return void
     *
     * Home function
     */
    public function index(){

        $items = $this->coreModel->nodeModel("dashboards")->load();

        $this->app->view->renderView('reports/index',["items" => $items]);
    }
}

