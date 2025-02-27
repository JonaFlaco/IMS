<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Core\Gctypes\Ctype;
use App\Exceptions\ForbiddenException;

class GetStatusOptions extends BaseInternalApi {
    
    private $ctypeObj;

    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $result = [];
        $ctype_id = "";
        if(isset($params["ctype_id"]) && _strlen($params["ctype_id"]) > 0)
            $ctype_id = $params["ctype_id"];

        $predefinedToStatus = null;
        if(isset($params["to_status"]) && _strlen($params["to_status"]) > 0 && $params["to_status"] != "null")
            $predefinedToStatus = $params["to_status"];

        $this->ctypeObj = (new Ctype)->load($ctype_id);

        if(!in_array($this->ctypeObj->status_id, [82]) && $this->app->user->isNotAdmin()) {
            throw new ForbiddenException("This Contnet-Type is not accessible");
        }

        $status_list = $this->coreModel->getStatus(null, $this->ctypeObj->id);

        $data_from_db = $this->coreModel->nodeModel($this->ctypeObj->id)
            ->id($id)
            ->loadFirstOrFail();

        $this->app->user->checkCtypeExtraPermission($this->ctypeObj, $data_from_db, null);
        
        if(isset($data_from_db->{$this->ctypeObj->form_type_field_name})){
            $ft = $this->coreModel->getFormType($data_from_db->{$this->ctypeObj->form_type_field_name});
        }
        
        if(!isset($this->ctypeObj->status_workflow_tempalate) || _strlen($this->ctypeObj->status_workflow_tempalate) == 0){
            throw new \App\Exceptions\CriticalException("Status Workflow is not defined for this Content-Type");
        }
        
        $wf_id_obj = $this->coreModel->getStatusWorkflowId($this->ctypeObj->status_workflow_tempalate, isset($ft) ? $ft->name : "");
        
        if(!isset($wf_id_obj)){
            throw new \App\Exceptions\CriticalException("Status Workflow is not defined or wrong for this Content-Type");
        }
        
        $statusDetail = $this->coreModel->getCurrentStatus($this->ctypeObj->id, $id);
        
        $currentStatusText = null;
        if(_strlen($predefinedToStatus) == 0) {
            
            $currentStatusText = $statusDetail->current_status_name;

        }

        $work_flow_data = $this->coreModel->nodeModel("status_workflow_templates")
            ->id($wf_id_obj->id)
            ->loadFirstOrFail();

        $workflow = $work_flow_data->items;
        
        foreach($workflow as $flow){
            foreach($flow->status_from_id as $status_from_id){
                if($status_from_id->value == $data_from_db->status_id){

                    foreach($flow->status_to_id as $status){
                        
                        $user_roles = explode(",", $this->app->user->getRoles());
                        $flow_roles = array_map(function ($o) {
                                    return $o->value;
                                }, $flow->roles
                            );

                        if(count(array_intersect($user_roles, $flow_roles)) === 0)
                            continue;
                        
                        foreach($status_list as $status_obj){
                            
                            if($status_obj->id == $status->value && get_object_in_array_of_objects($result, "status_id", $status->value, false) == null){
                                

                                $obj = new \stdClass();
                                $obj->status_id = $status->value;
                                $obj->status_name = $status_obj->name;
                                $obj->is_justification_required = $status_obj->is_justification_required == true;
                                $obj->is_actual_date_required = $status_obj->is_actual_date_required == true;
                                $obj->style = $status_obj->style;
                                $obj->reasons_list = $status_obj->reasons_list;

                                $result[] = $obj;

                            }
                        }

                    }

                }
            }
        }

        if(_strlen($predefinedToStatus) > 0) {
            foreach($result as $item) {
                if($item->status_id == $predefinedToStatus) {
                    
                    $result = (object)[
                        "status" => "success",
                        "currentStatus" => $currentStatusText,
                        "result" => [$item]
                    ];
        
                    return_json($result);
                }
            }

            $result = (object)[
                "status" => "success",
                "currentStatus" => $currentStatusText,
                "result" => []
            ];

            return_json($result);
        }

        $result = (object)[
            "status" => "success",
            "currentStatus" => $currentStatusText,
            "result" => $result
        ];

        return_json($result);
    }
}
