<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Core\Gctypes\Ctype;
use App\Exceptions\ForbiddenException;

class UpdateVerification extends BaseInternalApi {

    public function __construct(){
        parent::__construct();

        $this->app->csrfProtection->check();
    }

    public function index($id, $params = []){
        
        if(!isset($id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
        }

        $ctype_id = "";
        if(isset($params["ctype_id"]) && _strlen($params["ctype_id"]) > 0) {
            $ctype_id = $params["ctype_id"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Content-Type id is missing");
        }

        $value = false;
        if(isset($params["value"]) && _strlen($params["value"]) > 0)
            $value = intval($params["value"]);
        

        $permission_obj = $this->app->user->getCtypePermission($ctype_id);
        
        if(($value == 1 && $permission_obj->allow_verify != 1) || ($value == 0 && $permission_obj->allow_unverify != 1) ){
            if($value == 1){
                throw new ForbiddenException("You don't have permission to verify record");
            } else {
                throw new ForbiddenException("You don't have permission to unverify record");
            }
        }


        $ctype_obj = (new Ctype)->load($ctype_id);

        $data_from_db = $this->coreModel->nodeModel($ctype_obj->id)
                ->id($id)
                ->loadFirstOrFail();

        if(isset($ctype_obj->governorate_field_name) && _strlen($ctype_obj->governorate_field_name && $this->app->user->isAdmin() != true) > 0){
            if( !in_array($data_from_db->{$ctype_obj->governorate_field_name}, _explode(",", $this->coreModel->getUserGovernorates($ctype_id)))){
                throw new ForbiddenException("You don't have permission to this governorate");
            }
        }

        if(isset($ctype_obj->unit_field_name) && _strlen($ctype_obj->unit_field_name) > 0 && $this->app->user->isAdmin() != true){
            if( !in_array($data_from_db->{$ctype_obj->unit_field_name}, _explode(",", $this->coreModel->getUserUnits($ctype_id)))){
                throw new ForbiddenException("You don't have permission to this Unit");
            }
        }

        if(isset($ctype_obj->form_type_field_name) && _strlen($ctype_obj->form_type_field_name) > 0 && $this->app->user->isAdmin() != true){
            if( !in_array($data_from_db->{$ctype_obj->form_type_field_name}, _explode(",", $this->coreModel->getUserFormTypes($ctype_id)))){
                throw new ForbiddenException("You don't have permission to this form type");
            }
        }

        $this->coreModel->verifyRecord($ctype_obj->id, $id, $value);

        if($value == 1)
            $this->app->response->returnSuccess("record verified successfuly");
        else 
            $this->app->response->returnSuccess("record un-verified successfuly");

    }
}
