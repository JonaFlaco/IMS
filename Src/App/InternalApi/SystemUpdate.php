<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;

class SystemUpdate extends BaseInternalApi {
    
    private $metaData = null;

    private $cached_data_source = array();
    private $generic_preload_list_obj;

    private $ctypesBaseDir;

    public function __construct(){
        parent::__construct();

        $this->generic_preload_list_obj = new \App\InternalApi\GenericPreloadList();
        $this->ctypesBaseDir = SYSTEM_UPDATE_OUTPUT_DIR . DS . "ctypes";
    }

    
    

    public function index($name, $params = []){
        
        $result = array();

        $cmd = ""; 
        if(isset($params['cmd'])){
            $cmd = _strtolower($params['cmd']);
        }

        

        if($cmd == "get_ctypes") {
            $data = $this->getCtypes($name, $params);

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);

        } else if ($cmd == "install_ctype") {
            $this->installCtype($name, $params);

            $this->app->response->returnSuccess();

        } else if ($cmd == "insert_records") {
            
            return $this->insertRecords($name, $params);

        }
        
    }

    private function getCtypes($name, $params){
        
        $ctypesItems = scandir($this->ctypesBaseDir);

        $result = [];

        foreach($ctypesItems as $item) {
            
            if($item == "." || $item == "..") {
                continue;
            }
            
            $zip = new \ZipArchive;
            
            $res = $zip->open($this->ctypesBaseDir . DS . $item);

            if ($res === TRUE) {

                
                $data = json_decode($zip->getFromName("metadata.json"));

                if($data->is_field_collection)
                    continue;

                $installed = false;
                $ctypeObj = (new Ctype)->load($data->name);
                if(isset($ctypeObj)) {
                    $installed = true;
                }


                $dependancies = array();
                foreach(json_decode($zip->getFromName("metadata.json"))->data as $tbl) {
                    
                    foreach($tbl->data as $sub) {

                        if($sub->name == $data->name || object_exist_in_array_of_objects($dependancies,"name", $sub->name) == true)
                            continue;

                        //Don't show Field-Collections
                        if($sub->is_field_collection)
                            continue;

                        $temp = new \stdClass();
                        
                        $temp->title = $sub->title;
                        $temp->name = $sub->name;
                        $temp->is_field_collection = $sub->is_field_collection;
                        $temp->created_user_name = $data->created_user_name;
                        $temp->last_updated_user_name = $data->last_updated_user_name;
                        $obj = (new Ctype)->load($sub->name);
                        $temp->installed = !empty($obj);

                        $dependancies[] = $temp;
                    }
                }

                usort($dependancies, fn($a, $b) => strcmp($a->name, $b->name));

                $result[] = array(
                    "last_update_date" => \App\Helpers\DateHelper::humanify(strtotime($data->last_update_date)),
                    "id" => $data->id,
                    "name" => $data->name,
                    "title" => $data->title,
                    "description" => $data->description,
                    "module" => $data->module,
                    "category" => $data->category,
                    "is_field_collection" => $data->is_field_collection,
                    "icon" => "/assets/app/images/icons/" . ($data->icon ?? "archive.png"),
                    "status_id" => 0,
                    "error_message" => null,
                    "installed" => $installed,
                    "created_user_name" => $data->created_user_name,
                    "last_updated_user_name" => $data->last_updated_user_name,
                    "dependancies" => $dependancies,
                    "exportedRecordsCount" => $this->getRecordsCount($data->name)
                );
                
            } else {
                //echo 'failed, code:' . $res;
                //exit;
            }
            
        }

        return $result;

    }

    private function installCtype($name, $params){
        
        $zip = new \ZipArchive;
            
        $res = $zip->open($this->ctypesBaseDir . DS . $name . ".zip");

        if ($res === TRUE) {
            
            $this->metaData = json_decode($zip->getFromName("metadata.json"));

            foreach($this->metaData->data as $sub) {

                foreach($sub->data as $itm) {
                    if($itm->name == "users") {
                        continue;
                    }

                    //install dep
                }
            }
            
            $data = json_decode($zip->getFromName($name . ".json"));
            
            $viewObj = null;
            if(isset($data->view_id)) {
                $viewObj = json_decode($zip->getFromName("view.json"));
            }

            //check view
            $view_id = $data->view_id;
            $data->view_id = null;
            $data->view_id_display = null;

            $justification  = "Installed throw plugin manager";

            
            $installed = false;
            $ctypeObj = (new Ctype)->load($this->metaData->name);

            
            if(isset($ctypeObj)) {
                $installed = true;
                $data->id = $ctypeObj->id;
                $justification = "Updated throw plugin manager";
            } else {
                $data->id = null;
            }

            $data = $this->processData($data, (new CtypeField)->loadByCtypeId($data->sett_ctype_id));
            
            //TODO: fix permissions 
            $data->field_permissions = array();
            $data->permissions = array();
            
            $this->coreModel->node_save($data, array("justification" => $justification));

            if(isset($viewObj)) {
                
                //check if view exist
                $existObj = $this->coreModel->nodeModel("views")
                    ->id($viewObj->id)
                    ->loadFirstOrDefault();

                if(empty($existObj)){
                   
                    //not exist
                    $viewObj->id = null;
                    $viewObj = $this->processData($viewObj, (new CtypeField)->loadByCtypeId($viewObj->sett_ctype_id));

                    //TODO: fix actions relations
                    $viewObj->actions = array();

                    $view_id = $this->coreModel->node_save($viewObj, array("justification" => $justification));
                    
                    $data->view_id = $view_id;
                    $this->coreModel->node_save($data, array("justification" => "System Update: assign view"));

                } else {
                    //exist
                    
                    $data->view_id = $existObj->id;
                    $this->coreModel->node_save($data, array("justification" => "System Update: assign view*"));

                }
                
            }

        } else {
            throw new \App\Exceptions\NotFoundException("File not found");
        }
        
        
    }

    private function insertRecords($name, $params){

        $count = 0;
        foreach(scandir(SYSTEM_UPDATE_OUTPUT_DIR . DS . $name) as $file) {
            
            if(is_file(SYSTEM_UPDATE_OUTPUT_DIR . DS . $name . DS. $file)) {
                
                $zip = new \ZipArchive;
        
                $res = $zip->open(SYSTEM_UPDATE_OUTPUT_DIR . DS . $name . DS. $file);

                if ($res === TRUE) {
                        
                    $json = $zip->getFromName("metadata.json");

                    $data = json_decode($json);

                    $data->id = null;
                    
                    $data = $this->processData($data, (new CtypeField)->loadByCtypeId($data->sett_ctype_id));
                    
                    $this->coreModel->node_save($data, array("justification" => "Added throw systme update"));
                    
                } else {
                    throw new \App\Exceptions\NotFoundException("File not found");
                }
            }
        }

        return $count;
        
    }

    private function processData($data, $fields){

        foreach($fields as $field) {

            if($field->field_type_id == "field_collection") {
                $fcFields = $field->getFields();
                foreach($data->{$field->name} as $obj) {
                    
                    $obj = $this->processData($obj, $fcFields);
                    $obj->id = null;

                }

            } else if($field->field_type_id == "relation" && $field->data_source_value_column_is_text != true){
                if($field->is_multi == true ) {
                                            
                    $i = 0;
                    foreach(_explode("\n", $data->{$field->name . "_display"}) as $item) {

                        if(!isset($this->cached_data_source[$field->data_source_table_name]) || $field->data_source_table_name == "ctypes"){

                            $p = ["field_id" => $field->id,"return_object" => 1];
                            $result = $this->generic_preload_list_obj->index(null,$p);
                            
                            $this->cached_data_source[$field->data_source_table_name] = $result;
                        }
                        
                        $found_value = null;
                        foreach($this->cached_data_source[$field->data_source_table_name] as $itm){
                            
                            if(_trim(_strtolower($itm->title)) == _trim(_strtolower($item))){
                                $found_value = $itm->id;
                            }
                        }
                        
                        if(!empty($found_value)) {
                            $data->{$field->name}[$i]->value = $found_value;
                        }

                        $i++;
                    }

                    
                } else {
                    
                    $value = $data->{$field->name . "_display"};
                    
                    if(empty($value)){
                        continue;
                    }

                    if(!isset($this->cached_data_source[$field->data_source_table_name]) || $field->data_source_table_name == "ctypes"){

                        $p = ["field_id" => $field->id,"return_object" => 1];
                        $result = $this->generic_preload_list_obj->index(null,$p);
                        
                        $this->cached_data_source[$field->data_source_table_name] = $result;
                    }
                    
                    $found_value = null;
                    foreach($this->cached_data_source[$field->data_source_table_name] as $itm){
                        
                        if(_trim(_strtolower($itm->title)) == _trim(_strtolower($value))){
                            $found_value = $itm->id;
                        }
                    }
                    
                    if(!empty($found_value)) {
                        $data->{$field->name} = $found_value;
                    }

                }
            } else if ($field->field_type_id == "media") {
                                    
                if($field->is_multi) {

                } else {

                    echo $data->{$field->name . "_name"};
                    exit;

                }
            }

        }

        return $data;
    }

    private function getRecordsCount($ctypeName) {
        
        $count = 0;
        if(file_exists(SYSTEM_UPDATE_OUTPUT_DIR . DS . $ctypeName)) {
            foreach(scandir(SYSTEM_UPDATE_OUTPUT_DIR . DS . $ctypeName) as $file) {
                
                if(is_file(SYSTEM_UPDATE_OUTPUT_DIR . DS . $ctypeName . DS. $file)) {
                    $count++;
                }
            }
        }

        return $count;
    
    }
}
