<?php 

namespace App\Triggers\ctypes;

use App\Core\BaseTrigger;
use App\Core\Gctypes\Ctype;
use App\Exceptions\MissingDataFromRequesterException;
use App\Exceptions\CtypeValidationException;

class BeforeSave extends BaseTrigger {

    public function __construct(){
        parent::__construct();
    }

    public function index($data, $is_update = false){
        
        $fieldsSize = 0;

        $action_ctype_id = "";

        foreach($data->tables as $table){
            if($table->type == "main_table"){

                if (_strpos($table->data->id, "_") === 0) {
                    throw new CtypeValidationException("Ctype machine name cannot start with an underscore.");
                }
                
                if(_strlen($action_ctype_id) == 0){
                    $action_ctype_id = $table->data->id;
                }
                
                $table->data->id = get_machine_name($table->data->id, true);
                $table->data->name = _trim($table->data->name);

                if($is_update != true && property_exists($table->data, "is_field_collection") && $table->data->is_field_collection){
                    
                    if(!property_exists($table->data, "parent_ctype_id") || !isset($table->data->parent_ctype_id)) {
                        throw new MissingDataFromRequesterException("Parent Content-Type is missing");
                    }

                    $parent_ctype_obj = $this->coreModel->nodeModel("ctypes")
                        ->id($table->data->parent_ctype_id)
                        ->loadFirstOrFail();

                    
                    $table->data->id = $parent_ctype_obj->id . "_" . $table->data->id;

                    $table->data->category_id = "field_collection"; //Field-Collection

                }

            } else if ($table->type == "subtable"){

                

            } else if ($table->type == "field_collection"){
                
                if($table->id == "ctypes_fields" ){
                    $fieldsSize = sizeof($table->data->data->tables);
                }
                

                $unique_fields = [];
                foreach($table->data->data->tables as $stable){
                    if($stable->type == "main_table"){
                        
                        if($stable->id == "ctypes_fields" ){
                            
                            $stable->data->id = get_machine_name($stable->data->id,true);
                            $stable->data->name = get_machine_name($stable->data->name);
                            $stable->data->title = _trim($stable->data->title);

                            if (_strpos($stable->data->name, "_") === 0) {
                                throw new CtypeValidationException("Field machine name cannot start with an underscore.");
                            }

                            if($stable->data->field_type_id == "text" && _strlen($stable->data->str_length) == 0){
                                if($stable->data->appearance_id == "1_long_text" || $stable->data->appearance_id == "1_rich_text"){
                                    $stable->data->str_length = 4000;
                                }else{
                                    $stable->data->str_length = 255;
                                }
                            }
                            
                            // if($stable->data->field_type_id == "field_collection" && (!isset($stable->data->id) || _strlen($stable->data->id) == 0  || $stable->data->id == 0)){
                                
                            //     $fc_id = (new Ctype)->load($stable->data->data_source_id)->id;
                                
                            //     $stable->data->id = _str_replace($action_ctype_id . "_","",$fc_id);
                            //     echo "> " . $stable->data->id . "\n";

                            // }

                            if(in_array($stable->data->name, $unique_fields)) {
                                throw new \App\Exceptions\IlegalUserActionException("Duplicate field found (" . $stable->data->name . ")");
                            } else {
                                $unique_fields[] = $stable->data->name;
                            }

                        }
        
                    } else if ($stable->type == "subtable"){

                    }
                }

            }

            
        }

        if($fieldsSize == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Fields can not be empty");
            exit;
        }
    }
}
