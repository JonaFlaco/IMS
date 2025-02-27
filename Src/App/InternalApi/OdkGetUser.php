<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;

class odkGetUser extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
    }

    public function index($username, $params = []){

                    
        if($this->app->user->isAdmin() != true && $username  != Application::getInstance()->user->getName()) {
            throw new ForbiddenException();
        }
        
        $odkDbs = $this->coreModel->getAllOdkDatabases();
        $is_admin = Application::getInstance()->user->isAdmin();

        $data = [];
        
        foreach($odkDbs as $odk) {

            $odkModel = new \App\Models\OdkModel($odk->id);

            $user = $odkModel->getUser($username);

            if(!empty($user) || $is_admin) {
                $data[] = (object)[
                    "odk_id" => $odk->id,
                    "odk_name" => $odk->name,
                    "user" => $user
                ];
            }
        }

        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);

    }
}
