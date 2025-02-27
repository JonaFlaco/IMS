<?php 

namespace Ext\Middlewares;

use App\Core\Controller;
use App\Core\Application;

class Sample extends Controller {
    
    public function __construct(){
        parent::__construct();
    }

    public function addExtraConditionToGetData(){

        
            return "where query part";         
        
    }

    
}

