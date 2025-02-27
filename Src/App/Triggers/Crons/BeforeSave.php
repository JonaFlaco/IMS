<?php 

namespace App\Triggers\crons;

use App\Core\BaseTrigger;

class BeforeSave extends BaseTrigger {

    public function __construct(){
        parent::__construct();
    }

    public function index($data, $is_update = false){
        
        $fieldsSize = 0;

        $action_ctype_id = "";

        foreach($data->tables as $table){
            if($table->type == "main_table"){
                
                if(_strlen($action_ctype_id) == 0){
                    $action_ctype_id = $table->data->id;
                }

                $table->data->id = get_machine_name($table->data->id, true);
                $table->data->name = _trim($table->data->name);

                if($table->data->type_id == "custom") {
                    $table->data->is_custom = true;
                }


            }

        }


    }
}
