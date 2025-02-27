<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;

class SystemUpdateExport extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
    }

    
    

    public function index($name, $params = []){
        
        $data = array();

        $cmd = ""; 
        if(isset($params['cmd'])){
            $cmd = _strtolower($params['cmd']);
        }

        if($cmd == "get_ctypes") {

            $data = array();

            $ctypesItems = $this->coreModel->nodeModel("ctypes")
                ->where("isnull(m.is_field_collection,0) = 0")
                ->load();

            foreach($ctypesItems as $item) {
                
                $obj = new \stdClass();

                $obj->id = $item->id;
                $obj->name = $item->name;
                $obj->icon = "/assets/app/images/icons/" . ($item->icon ?? "archive.png");
                $obj->is_field_collection = $item->is_field_collection;
                $obj->module = $item->module_id_display;
                $obj->module_id = $item->module_id;
                $obj->last_update_date = $item->last_update_date;
                $obj->last_updated_user_name = $item->updated_user_id_display;
                $obj->created_user_name = $item->created_user_id_display;
                $obj->exported = $this->CtypeExistInOutput($item->name);
                $obj->selected = false;
                $obj->status_id = 0;
                $obj->export_data_status_id = 0;
                $obj->exportedRecordsCount = $this->getRecordsCount($item->name);
                $data[] = $obj;
            }

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
            exit;
        
        } else if ($cmd == "export_ctype") {
            
            $data = (new \App\Core\SystemUpdate\DataExport($name, false))->main();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
            
        } else if ($cmd == "remove_ctype") {

            (new \App\Core\SystemUpdate\DataExport($name, true))->removeCtype();

            $this->app->response->returnSuccess();
        } else if ($cmd == "export_ctype_data") {
            
            $data = (new \App\Core\SystemUpdate\DataExport($name, true))->main();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "remove_ctype_data") {
            
            (new \App\Core\SystemUpdate\DataExport($name, true))->removeExportedData();
            $this->app->response->returnSuccess();
        }
        
    }

    private function CtypeExistInOutput($name) {
        
        return file_exists(SYSTEM_UPDATE_OUTPUT_DIR . DS . "ctypes" . DS . $name . ".zip");
    }

    private function getRecordsCount($ctypeName){
        $count = 0;

        if($ctypeName == "ctypes") {
            return 0;
        }
        
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
