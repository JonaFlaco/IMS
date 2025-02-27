<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;

class OdkFromDashboardGetOrphanForms extends BaseInternalApi {

    public function __construct(){
        parent::__construct();

        if($this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
    }

    public function index($id, $params = []){
        
        $list = \App\Core\Crons\ODK::getOrphanForms();

        $result = (object)[
            "status" => "success",
            "result" => $list
        ];

        return_json($result);
        
    }
}
