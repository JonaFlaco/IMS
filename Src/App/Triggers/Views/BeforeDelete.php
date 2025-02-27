<?php 

namespace App\Triggers\views;

use App\Core\BaseTrigger;

class BeforeDelete extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index($id,$data){

        //delete GviewExtends if exist
        if($data->is_system_object == true){
            $file =  APP_ROOT_DIR . "\\Views\\GviewExtends\\" . toPascalCase($data->id) . ".html.php";
        } else {
            $file =  EXT_ROOT_DIR . "\\Views\\GviewExtends\\" . toPascalCase($data->id) . ".html.php"; 
        }
        
        if(is_file($file)){   
            unlink($file);
        }
        
        //delete GviewExtends if exist
        if($data->is_system_object == true){
            $file =  APP_ROOT_DIR . "\\Views\\GviewExtends\\" . toPascalCase($data->id) . ".js.php";
        } else {
            $file =  EXT_ROOT_DIR . "\\Views\\GviewExtends\\" . toPascalCase($data->id) . ".js.php"; 
        }
        
        if(is_file($file)){   
            unlink($file);
        }

        //delete GviewExtends if exist
        if($data->is_system_object == true){
            $file =  APP_ROOT_DIR . "\\Views\\GviewExtends\\" . toPascalCase($data->id) . "_result.js.php";
        } else {
            $file =  EXT_ROOT_DIR . "\\Views\\GviewExtends\\" . toPascalCase($data->id) . "_result.js.php"; 
        }
        
        if(is_file($file)){   
            unlink($file);
        }

        //delete GviewExtends if exist
        if($data->is_system_object == true){
            $file =  APP_ROOT_DIR . "\\Views\\GviewExtends\\" . toPascalCase($data->id) . "AfterSuccessFilter.js.php";
        } else {
            $file =  EXT_ROOT_DIR . "\\Views\\GviewExtends\\" . toPascalCase($data->id) . "AfterSuccessFilter.js.php"; 
        }
        
        if(is_file($file)){   
            unlink($file);
        }

        
        $qry = "
        delete from views_actions where download_view_id = '$id'
        ";
        
        $this->coreModel->query_execute($qry);

    }
}