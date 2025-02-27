<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;

class OdkFormGetInCompleteForms extends BaseInternalApi {
    
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
            ->loadFirstOrFail();
            
        $formResult = \App\Core\Crons\ODK::getIncompleteForms($cronObj);

        $result = (object)[
            "status" => "success",
            "result" => $formResult
        ];

        return_json($result);

    }
}
