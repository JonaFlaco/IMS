<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;

class Home extends Controller {

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

        $data = [
            'title' => 'Home'
        ];

        $this->app->view->renderView('index',$data);
    }
}

