<?php 

namespace App\Triggers\crons;

use App\Core\BaseTrigger;
class BeforeDelete extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index($id,$data){

        $file =  EXT_ROOT_DIR . "\\Crons\\" . toPascalCase($data->id) . ".php"; 
        
        if(is_file($file)){   
            unlink($file);
        }
        
    }
}