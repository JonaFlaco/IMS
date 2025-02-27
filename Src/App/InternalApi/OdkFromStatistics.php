<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\CriticalException;
use App\Exceptions\ForbiddenException;

class OdkFromStatistics extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();

        if($this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
    }

    public function index($id, $params = []){
        
        if(_strlen($id) == 0)
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");

        $cronObj = $this->coreModel->nodeModel("crons")
            ->id($id)
            ->loadFirst();

        $formResult = \App\Core\Crons\ODK::getStatistics($cronObj);

        if(isset($formResult) && property_exists($formResult,"error_message") && isset($formResult->error_message)) {
            throw new CriticalException($formResult->error_message);
        }

        $result = (object)[
            "status" => "success",
            "result" => (object)[
                "all_records" => isset($formResult) ? $formResult->all_records : 0,
                "pending_records" => isset($formResult) ? $formResult->pending_records : 0,
                "size_kb" => isset($formResult) ? $formResult->size_kb : 0
            ]
        ];

        return_json($result);

    }


}
