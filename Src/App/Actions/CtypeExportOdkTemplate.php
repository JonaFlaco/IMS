<?php 

namespace Ext\Actions;

use App\Core\Controller;
use \App\core\Application;
class CtypeExportOdkTemplate extends Controller {
    
    public function __construct(){
        parent::__construct();

        //Check if user is logged in or on local
        $this->app->user->checkAuthentication();
    }

    public function index($id, $params = []){

        if(empty($_POST['ctype_id']))                      
            throw new \App\Exceptions\MissingDataFromRequesterException("Ctype ID is required , but not provided");
        
        $ctype = $this->coreModel->nodeModel("ctypes")
                                ->id($_POST['ctype_id'])
                                ->loadFirstOrDefault();
        (new \App\Core\BgTask())
        ->setName("Export $ctype->name ODK Template")
        ->setActionName("CtypeExportOdkTemplate")
        ->setPostData(Application::getInstance()->request->POST())
        ->addToQueue();
        
        Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;
    }
}