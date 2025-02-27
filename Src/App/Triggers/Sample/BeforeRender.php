<?php 

namespace App\Triggers\sample;

use App\Core\BaseTrigger;
use App\Core\Gctypes\CtypesHelper;

class BeforeRender extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index($type, $id, $params){
        
        if($type == CtypesHelper::$TYPE_ADD) {

        } else if ($type == CtypesHelper::$TYPE_EDIT) {

        } else if( $type == CtypesHelper::$TYPE_DELETE) {

        } else if ($type == CtypesHelper::$TYPE_SHOW) {
            
        } else if ($type == CtypesHelper::$TYPE_VIEW) {
            
        }
        
    }
}
