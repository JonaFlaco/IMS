<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;

class GetCronLog extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $data = $this->coreModel->getCronLog($id);

        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);
    }
}
