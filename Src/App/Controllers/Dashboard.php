<?php 
/*
 * This controller handles dashboard requests
 */ 
namespace App\Controllers;
use App\Core\Controller;
class Dashboard extends Controller {
    public function __construct(){
        parent::__construct();
        //check if user is logged in
        $this->app->user->checkAuthentication();
    }
    
    /**
     * index
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * index page, generates dashboard
     */
    public function index($id, $params = array()){
        
        (new \App\Core\Gdashboards\DashboardGenerator($id))->generate();
        
    }
}