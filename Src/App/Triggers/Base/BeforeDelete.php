<?php 

namespace App\Triggers\Base;

use App\Core\BaseTrigger;
use App\Exceptions\ForbiddenException;

class BeforeDelete extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index($id,$data){
        
        if(isset($data->{"is_system_object"}) && $data->is_system_object == true){
            throw new ForbiddenException("You can't delete system objects");
        }
        
    }
}