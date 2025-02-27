<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;

class odkChangePassword extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $data = $this->app->request->POST();
        
        $username = isset($data['username']) ? $data['username'] : null;
        $current_password = isset($data['current_password']) ? $data['current_password'] : null;
        $new_password = isset($data['new_password']) ? $data['new_password'] : null;
        $new_password2 = isset($data['new_password2']) ? $data['new_password2'] : null;
        $odk_id = isset($data['odk_id']) ? $data['odk_id'] : null;

        if($this->app->user->isAdmin() != true && $username  != Application::getInstance()->user->getName()) {
            throw new ForbiddenException();
        }

        if(empty($odk_id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Odk ID is not supplied");
        }

        $odkModel = new \App\Models\OdkModel($odk_id);

        if(_strlen($new_password) < 8) {
            throw new \App\Exceptions\PasswordOperationException("Password must be at least 8 chars long");
        }

        if($new_password != $new_password2) {
            throw new \App\Exceptions\PasswordOperationException("Passwords do not match");
        }

        if(!$odkModel->verifyCurrentPassword($username, $current_password)) {
            throw new ForbiddenException("Current password is invalid");
        }

        $odkModel->changeUserPassword($username, $new_password);

        $result = (object)[
            "status" => "success",
            "message" => "ODK User password updated successfuly",
        ];

        return_json($result);

    }
}
