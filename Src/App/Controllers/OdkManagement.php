<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Exceptions\ForbiddenException;
use App\Models\OdkModel;

class OdkManagement extends Controller {

    private $OdkModel;

    public function __construct(){
        parent::__construct();

        $this->app->user->checkAuthentication();
        $this->OdkModel = new OdkModel();
    }
    
    
    public function index(){

        $data = [
            'title' => 'ODK Management',
        ];

        
        //$this->app->view->renderView('help/index',$data);
        die("ODK Management");
    }

    public function users(){

        $odkDbList = $this->coreModel->getAllOdkDatabases();

        $data = [
            'title' => 'ODK Users',
            'odkDbList' => $odkDbList
        ];

        
        $this->app->view->renderView('odk/users',$data);
    }
}

