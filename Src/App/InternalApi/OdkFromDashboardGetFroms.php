<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;

class OdkFromDashboardGetFroms extends BaseInternalApi {

    public function __construct(){
        parent::__construct();

        if($this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
    }

    public function index($id, $params = []){
        
        $cron_job_id = null;
        if(isset($params['cron_job_id']))
            $cron_job_id = $params['cron_job_id'];
        
        $type_id = null;
        if(isset($params['type_id']))
            $type_id = $params['type_id'];

        $load_detail = null;
        if(isset($params['load_detail']))
            $load_detail = $params['load_detail'];

        $items = $this->coreModel->getCronsTasks($type_id, $cron_job_id, $load_detail == true);

        $result = (object)[
            "status" => "success",
            "result" => $items
        ];

        return_json($result);

    }
}
