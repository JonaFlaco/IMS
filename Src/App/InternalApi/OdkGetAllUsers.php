<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use App\Helpers\DateHelper;

class OdkGetAllUsers extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
        
        if($this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
    }

    public function index($odk_id, $params = []){
        
        if(empty($odk_id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Odk ID is not supplied");
        }
        
        $odkModel = new \App\Models\OdkModel($odk_id);

        $users = $odkModel->getAllUsers();

        $imsUsers = $this->coreModel->nodeModel("users")
            ->fields(["id", "name", "full_name"])
            ->load();

        foreach($users as $item) {

            if(object_exist_in_array_of_objects($imsUsers, "name", $item->name)) {
                $item->has_ims_account = true;
            } else {
                $item->has_ims_account = false;
            }

        }

        $result = (object)[
            "status" => "success",
            "result" => $users
        ];

        return_json($result);

    }
}
