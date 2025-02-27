<?php 

namespace App\Triggers\ctype_name;

use App\Core\BaseTrigger;

class AfterUpdateStatus extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $from_status_id, $to_status_id, $justification){

    }
}
