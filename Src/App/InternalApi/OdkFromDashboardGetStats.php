<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use stdClass;

class OdkFromDashboardGetStats extends BaseInternalApi {

    public function __construct(){
        parent::__construct();

        if($this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
    }

    public function index($id = null, $params = []){
        
        $limited = null;
        if(isset($params['limited'])) {
            $limited = $params['limited'];
        }


        $data = $this->coreModel->getCronStats($id, $limited);

        $years = [];
        foreach($data as $item) {
            if(!in_array($item->year, $years)) 
                $years[] = $item->year;
        }

        $years = array_unique($years, SORT_NUMERIC);
        usort($years, fn($a, $b) => strcmp($b, $a)); //desc

        $newData = [];
        foreach($years as $year) {
            
            $yearObj = new stdClass();
            $yearObj->name = $year;
            $yearObj->items = [];


            $months = [];
            foreach($data as $item) {
                if(!in_array($item->month, $months)) 
                $months[] = $item->month;
            }
            $months = array_unique($months, SORT_NUMERIC);
            usort($months, fn($a, $b) => strcmp($b, $a)); //desc
            
            foreach($months as $month) {

                $monthObj = new stdClass();
                $monthObj->name = date("M", mktime(0,0,0,$month,1,2011));
                $monthObj->items = [];
                
                $mi = 0;
                foreach($data as $item) {
                    if($item->year == $year && $item->month == $month) {
                        $monthObj->items[] = $item;
                        $mi++;
                    }
                }

                if($mi > 0)
                    $yearObj->items[] = $monthObj;
            }

            $newData[] = $yearObj;
        }

        $result = (object)[
            "status" => "success",
            "result" => $newData
        ];

        return_json($result);
        
    }
}
