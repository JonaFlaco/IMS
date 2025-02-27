<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;

class OdkFromDashboardSyncForm extends BaseInternalApi {

    public function __construct(){
        parent::__construct();

        if($this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
    }

    public function index($id, $params = []){
        
        (new \App\Core\Crons\RunCron($id, $params))->run();

        $cron = $this->coreModel->nodeModel("crons")
            ->id($id)
            ->loadFirstOrFail();

        $pendingRecordsInfo = \App\Core\Crons\ODK::getStatistics($cron);

        $all_records = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->all_records : 0);
        $pending_records = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->pending_records : 0);
        $incomplete_records = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->incomplete_records : 0);
        $size = (isset($pendingRecordsInfo) ? $pendingRecordsInfo->size_kb / 1024 : 0);

        $result = (object)[
            "status" => "success",
            "result" => (object)[
                "all_records" => $all_records,
                "pending_records" => $pending_records,
                "incomplete_records" => $incomplete_records,
                "size" => $size
            ]
        ];

        return_json($result);
        
    }
}
