<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use Exception;

class ChangeAccountIsActive extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();

        if(Application::getInstance()->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
    }

    public function index($id, $params = []){
        
        if(!isset($params["new_value"])) {
            throw new Exception("New value is empty");
        }

        if(empty($id)) {
            throw new Exception("Id is empty");
        }

        $new_value = false;
        
        if($params["new_value"] == "1" || $params["new_value"] == true )
            $new_value = true;
        
        $this->coreModel->nodeModel("users")->id($id)->loadFirst();

        Application::getInstance()->userModel->changeAccountIsActive($id, $new_value);


        $this->app->response->returnSuccess();

    }
}
