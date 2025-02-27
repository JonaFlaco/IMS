<?php 

namespace App\InternalApi;

use App\Exceptions\ForbiddenException;
use App\Core\BaseInternalApi;
use App\Core\Gctypes\Ctype;
use App\Exceptions\MissingDataFromRequesterException;

class UpdateStatus extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        if(empty($id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
        }

        $this->app->csrfProtection->check();

        $data = $this->app->request->POST();
        
        $ctype_id = null;
        if(isset($data["ctype_id"]) && _strlen($data["ctype_id"]) > 0) {
            $ctype_id = $data["ctype_id"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Content-Type is empty");
        }
            
        $confirmed = false;
        if(isset($data["confirmed"]) && _strlen($data["confirmed"]) > 0)
            $confirmed = boolval($data["confirmed"]);
        
        $to_status = null;
        if(isset($data["to_status"]) && _strlen($data["to_status"]) > 0)
            $to_status = $data["to_status"];

        $reasons = null;
        if(isset($data["reasons"]) && _strlen($data["reasons"]) > 0)
            $reasons = $data["reasons"];

        if(empty($to_status)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Status is empty");
        }

        $justification = null;
        if(isset($data["justification"]) && _strlen($data["justification"]) > 0)
            $justification = $data["justification"];

        $actual_date = null;
        if(isset($data["actual_date"]) && _strlen($data["actual_date"]) > 0)
            $actual_date = $data["actual_date"];

        $ctype_obj = (new Ctype)->load($ctype_id);
        
        if(!in_array($ctype_obj->status_id, [82]) && $this->app->user->isNotAdmin()) {
            throw new ForbiddenException("This Contnet-Type is not accessible");
        }
        
        if(!isset($ctype_obj)){
            throw new \App\Exceptions\NotFoundException("Content-Type not found");
        }

        
        $status_list = $this->coreModel->getStatus(null, $ctype_obj->id);

        $data_from_db = $this->coreModel->nodeModel($ctype_obj->id)
            ->id($id)
            ->loadFirstOrFail();

        if(isset($ctype_obj->governorate_field_name) && _strlen($ctype_obj->governorate_field_name && $this->app->user->isAdmin() != true)){
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

        
        if(!isset($ctype_obj->status_workflow_tempalate) || _strlen($ctype_obj->status_workflow_tempalate) == 0){
            throw new \App\Exceptions\CriticalException("Status Workflow is not defined for this Content-Type");
        }
        
        if(isset($data_from_db->{$ctype_obj->form_type_field_name})){
            $ft = $this->coreModel->getFormType($data_from_db->{$ctype_obj->form_type_field_name});
        }
        
        $wf_id_obj = $this->coreModel->getStatusWorkflowId($ctype_obj->status_workflow_tempalate, isset($ft) ? $ft->name : "");
        
        if(!isset($wf_id_obj)){
            throw new \App\Exceptions\CriticalException("Status Workflow is not defined for this Content-Type");
        }

        
        $status_name = "";
        $status_style = "";
        $found_status_obj = null;
        foreach($status_list as $status_obj){
            if($to_status == $status_obj->id){
                $status_name = $status_obj->name;
                $status_style = $status_obj->style;
                $found_status_obj = $status_obj;
            }
        }
        
        $work_flow_data = $this->coreModel->nodeModel("status_workflow_templates")
            ->id($wf_id_obj->id)
            ->loadFirstOrFail();

        
        $workflow = $work_flow_data->items;
        
        $found_flow = null;
        foreach($workflow as $flow){
            foreach($flow->status_from_id as $status_from_id){
                if($status_from_id->value == $data_from_db->status_id){

                    $user_roles = _explode(",", $this->app->user->getRoles());
                    $flow_roles = array_map(function ($o) {
                                return $o->value;
                            }, $flow->roles
                        );

                    if(count(array_intersect($user_roles, $flow_roles)) === 0)
                        continue;
                    
                    foreach($flow->status_to_id as $status){

                        if($status->value == $to_status){
                            $found_flow = $flow;  
                            break;
                        }
                    }
                    
                    if($found_flow){
                        break;
                    }
                }
            }

            if($found_flow){
                break;
            }
        }
  
        if($found_flow == null)
            throw new \App\Exceptions\IlegalUserActionException("Unable to change status to $status_name or you don't have permission");
        
    
        //Run app base trigger
        $classToRun = '\App\Triggers\Base\BeforeUpdateStatus';
        if(class_exists($classToRun)){
            $classObj = new $classToRun();

            if(method_exists($classObj, "index")){
                
                $classObj->ctypeObj = $ctype_obj;

                $external_fun_result = $classObj->index($id, $data_from_db->status_id, $to_status, $justification, $confirmed);
                if($external_fun_result != array() && isset($external_fun_result["status"]) && $external_fun_result["status"] == "error"){
                    $this->app->response->returnFailed($external_fun_result["message"]);
                }
                if($confirmed != true && $external_fun_result != array() && isset($external_fun_result["status"]) && $external_fun_result["status"] == "warning"){
                    $this->app->response->returnWarning($external_fun_result["message"]);
                }
            } 
        }

        //Run ext base trigger
        $classToRun = '\Ext\Triggers\Base\BeforeUpdateStatus';
        if(class_exists($classToRun)){
            $classObj = new $classToRun();

            if(method_exists($classObj, "index")){
                
                $classObj->ctypeObj = $ctype_obj;

                $external_fun_result = $classObj->index($id,$data_from_db->status_id, $to_status, $justification, $confirmed);

                if($external_fun_result != array() && isset($external_fun_result["status"]) && $external_fun_result["status"] == "error"){
                    $this->app->response->returnFailed($external_fun_result["message"]);
                }
                if($confirmed != true && $external_fun_result != array() && isset($external_fun_result["status"]) && $external_fun_result["status"] == "warning"){
                    $this->app->response->returnWarning($external_fun_result["message"]);
                }
            } 
        }

        $className = toPascalCase($ctype_obj->id);
            
        if($ctype_obj->is_system_object) {
            $classToRun = sprintf('\App\Triggers\%s\BeforeUpdateStatus', $className);
            if(class_exists($classToRun)){
                
                $classObj = new $classToRun();

                if(method_exists($classObj, "index")){

                    $classObj->ctypeObj = $ctype_obj;

                    $external_fun_result = $classObj->index($id,$data_from_db->status_id, $to_status, $justification, $confirmed);
                    
                    if($external_fun_result != array() && isset($external_fun_result["status"]) && $external_fun_result["status"] == "error"){
                        $this->app->response->returnFailed($external_fun_result["message"]);
                    }
                    if($confirmed != true && $external_fun_result != array() && isset($external_fun_result["status"]) && $external_fun_result["status"] == "warning"){
                        $this->app->response->returnWarning($external_fun_result["message"]);
                    }
                }
            }
        }

        $classToRun = sprintf('\Ext\Triggers\%s\BeforeUpdateStatus', $className);
        if(class_exists($classToRun)){
            
            $classObj = new $classToRun();

            if(method_exists($classObj, "index")){

                $classObj->ctypeObj = $ctype_obj;

                $external_fun_result = $classObj->index($id,$data_from_db->status_id, $to_status, $justification, $confirmed);
                
                if($external_fun_result != array() && isset($external_fun_result["status"]) && $external_fun_result["status"] == "error"){
                    $this->app->response->returnFailed($external_fun_result["message"]);
                }
                if($confirmed != true && $external_fun_result != array() && isset($external_fun_result["status"]) && $external_fun_result["status"] == "warning"){
                    $this->app->response->returnWarning($external_fun_result["message"]);
                }
            }
        }
        
        
        $this->coreModel->updateCtypeStatus($ctype_obj->id, $id, $to_status, $this->app->user->getId(),$justification, $reasons, $actual_date, $found_status_obj->actual_date_field_name);

        //Run app base trigger
        $classToRun = '\App\Triggers\Base\AfterUpdateStatus';
        if(class_exists($classToRun)){
            $classObj = new $classToRun();

            if(method_exists($classObj, "index")){
                
                $classObj->ctypeObj = $ctype_obj;

                $external_fun_result = $classObj->index($id,$data_from_db->status_id, $to_status,$justification);

                if(isset($external_fun_result) && _strlen($external_fun_result) > 0){
                    $this->app->response->returnFailed($external_fun_result);
                }
            } 
        }

        //Run ext base trigger
        $classToRun = '\Ext\Triggers\Base\AfterUpdateStatus';
        if(class_exists($classToRun)){
            $classObj = new $classToRun();

            if(method_exists($classObj, "index")){
                
                $classObj->ctypeObj = $ctype_obj;

                $external_fun_result = $classObj->index($id,$data_from_db->status_id, $to_status,$justification);

                if(isset($external_fun_result) && _strlen($external_fun_result) > 0){
                    $this->app->response->returnFailed($external_fun_result);
                }
            } 
        }
        

        $className = toPascalCase($ctype_obj->id);
            
        if($ctype_obj->is_system_object) {
            $classToRun = sprintf('\App\Triggers\%s\AfterUpdateStatus', $className);
            if(class_exists($classToRun)){
                
                $classObj = new $classToRun();

                if(method_exists($classObj, "index")){

                    $classObj->ctypeObj = $ctype_obj;

                    $external_fun_result = $classObj->index($id,$data_from_db->status_id, $to_status,$justification);

                    if(isset($external_fun_result) && _strlen($external_fun_result) > 0){
                        $this->app->response->returnFailed($external_fun_result);
                    }
                }
            }
        }

        $classToRun = sprintf('\Ext\Triggers\%s\AfterUpdateStatus', $className);
        if(class_exists($classToRun)){
            $classObj = new $classToRun();

            if(method_exists($classObj, "index")){

                $classObj->ctypeObj = $ctype_obj;

                $external_fun_result = $classObj->index($id,$data_from_db->status_id, $to_status,$justification);

                if(isset($external_fun_result) && _strlen($external_fun_result) > 0){
                    $this->app->response->returnFailed($external_fun_result);
                }
            }
        }

        
        $statusDetail = $this->coreModel->getCurrentStatus($ctype_obj->id, $id);
        
        $result = (object) [
            "status" => "success",
            "result" => (object) [
                "id" => $statusDetail->current_status_id,
                "name" => $statusDetail->current_status_name,
                "style" => $statusDetail->style,
            ],
        ];

        echo json_encode($result);
        
    }
}
