<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;

class GetCtypeBasicInfo extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $data = $this->coreModel->nodeModel("ctypes")
            ->id($id)
            ->loadFirstOrFail();

        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);
    }
}
