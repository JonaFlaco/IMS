<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;

class GetCtypesLog extends BaseInternalApi {

    public function __construct(){
        parent::__construct();

    }
    

    public function index($id, $params = []){
    
        $ctype_id = "";
        if(isset($params["ctype_id"]) && _strlen($params["ctype_id"]) > 0)
            $ctype_id = $params["ctype_id"];

        $load_all_records = true;
        if(isset($params["load_all_records"]))
            $load_all_records = $params["load_all_records"];

        $data = $this->coreModel->getCtypesLog($ctype_id, $id, null,$load_all_records);

        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);
    }
}
