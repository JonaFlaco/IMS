<?php 

namespace App\Triggers\ctypes;

use App\Core\BaseTrigger;
use App\Core\Gctypes\Ctype;

class BeforeDelete extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index($id,$data){

        if($data->is_field_collection && !empty($data->parent_ctype_id)){
            
            $parent_ctype_obj = (new Ctype)->load($data->parent_ctype_id);

            if($parent_ctype_obj->is_system_object == true){
                throw new \App\Exceptions\ForbiddenException("Unable to delete system object");
            }
        }

        //delete custom_tpl if exist
        if($data->is_system_object == true){
            $file =  APP_ROOT_DIR . "\\Views\\CustomTpls\\" . toPascalCase($data->id) . ".php";
        } else {
            $file =  EXT_ROOT_DIR . "\\Views\\CustomTpls\\" . toPascalCase($data->id) . ".php";
        }
        
        if(is_file($file)){   
            unlink($file);
        }

        //delete custom_edit_tpl if exist
        if($data->is_system_object == true){
            $file =  APP_ROOT_DIR . "\\Views\\CustomEditTpls\\" . toPascalCase($data->id) . ".php";
        } else {
            $file =  EXT_ROOT_DIR . "\\Views\\CustomEditTpls\\" . toPascalCase($data->id) . ".php";
        }
        
        if(is_file($file)){   
            unlink($file);
        }

        $views = $this->coreModel->nodeModel("views")
                ->where("m.ctype_id = :ctype_id")
                ->bindValue(":ctype_id", $id)
                ->load();

        if(sizeof($views) > 0){

            foreach($views as $view){
                $this->coreModel->delete("views", $view->id);
            }
        }

        $crons = $this->coreModel->nodeModel("crons")
                ->where("m.ctype_id = :ctype_id")
                ->bindValue(":ctype_id", $id)
                ->load();

        if(sizeof($crons) > 0){
            
            foreach($crons as $cron){
                $this->coreModel->delete("crons", $cron->id);
            }
        }

        $qry = ctypes_generate_delete_table_tsql_code($id);
        
        $result = $this->coreModel->query_execute($qry);
        
        if($data->is_field_collection != true && !empty($data->id)){
            \App\Helpers\UploadHelper::deleteAllFilesInsideDir(UPLOAD_DIR_FULL . DS . $data->id, true);
        }
    }

}
