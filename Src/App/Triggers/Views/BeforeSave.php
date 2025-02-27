<?php 

namespace App\Triggers\views;

use App\Core\BaseTrigger;
use App\Core\Gctypes\Ctype;

class BeforeSave extends BaseTrigger {

    public function __construct(){
        parent::__construct();
    }

    public function index($data, $is_update = false){
        
        foreach($data->tables as $table){
            if($table->type == "main_table"){
                $table->data->id = get_machine_name($table->data->id);
                $table->data->name = _trim($table->data->name);
            }
        }

    }
}
