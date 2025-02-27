<?php 

namespace App\InternalApi;

use App\Core\Application;
use App\Core\Response;
use App\Core\BaseInternalApi;
use App\Helpers\DateHelper;

class BgTasksGet extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($status_id, $params = []){

        $data = Application::getInstance()->coreModel->bg_tasks_get($status_id);
        foreach($data as $item) {
            
            $item->elapsed_time = $this->findElapsedTime($item->elapsed_time_sec);
            
            $item->created_date_humanify = isset($item->start_date) ? DateHelper::humanify(strtotime($item->created_date)) : null;
            $item->start_date_humanify = isset($item->start_date) ? DateHelper::humanify(strtotime($item->start_date)) : null;
            $item->completion_date_humanify = isset($item->completion_date) ? DateHelper::humanify(strtotime($item->completion_date)) : "N/A";
            $item->theme = "secondary";
            $item->icon = " mdi mdi-clock-time-two-outline";
            if($item->status_id == 22) {
                $item->theme = "success";
                $item->icon = " mdi mdi-check-outline";
            } else if($item->status_id == 28) {
                    $item->theme = "info";
                    $item->icon = " mdi-dots-horizontal";
            } else if($item->status_id == 73) {
                $item->theme = "danger";
                $item->icon = " mdi mdi-cancel";
            }
            
            $item->output_file_link = (isset($item->output_file_name) ? "/filedownload?ctype_id=bg_tasks&field_name=output_file&file_name=" . $item->output_file_name : null);

            $item->cancelling = false;
            $item->deleting = false;
        }
        
        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);
    }


    private function findElapsedTime($seconds) : string {

        if($seconds < 0)
            return "N/A";

        $result = "";

        $x = floor($seconds / 86400);
        if($x > 0) {
            $result .= "{$x}d ";
            $seconds -= $x * 86400;
        }

        $x = floor($seconds / 3600);
        if($x) {
            $result .= "{$x}h ";
            $seconds -= $x * 3600;
        }

        $x = floor($seconds / 60);
        if($x > 0) {
            $result .= "{$x}m ";
            $seconds -= $x * 60;
        }

        if($seconds > 0) {
            $result .= "{$seconds}s";
        }

        return $result;
    }

}
