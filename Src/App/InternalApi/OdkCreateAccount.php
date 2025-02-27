<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;

class odkCreateAccount extends BaseInternalApi {

    public function __construct(){
        parent::__construct();

        if($this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
    }

    public function index($id, $params = []){
        
        $data = $this->app->request->POST();
        
        $user_id = isset($data['user_id']) ? $data['user_id'] : null;
        $odk_id = isset($data['odk_id']) ? $data['odk_id'] : null;
        
        if(empty($odk_id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Odk ID is not supplied");
        }

        $odkModel = new \App\Models\OdkModel($odk_id);
        
        $newpwd = $odkModel->createAccount($user_id);
        
        $result = (object)[
            "status" => "success",
            "message" => "ODK account created successfuly",
            "newpwd" => $newpwd
        ];

        return_json($result);

    }
}
