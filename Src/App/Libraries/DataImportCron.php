<?php

/**
 * This class will receive an excel file to import cron
 */

namespace App\Libraries;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Models\CoreModel;

Class DataImportCron {


    private static $coreModel;
    /**
    * main
    *
    * @return void
    *
    * This is the public function, which receives an excel file to import cron.
    */
    public static function main(){

        
            //If it is post request, then process it, otherwise show import interface
            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                self::$coreModel = CoreModel::getInstance();

                $input_file_name = $_FILES['file']['tmp_name'];
                
                if(substr(_strtolower($input_file_name), 0, _strlen(PHP_UPLOAD_TMP_FOLDER)) !== _strtolower(PHP_UPLOAD_TMP_FOLDER)){
                    throw new \App\Exceptions\FileOperationFailedException("Invalid file path");
                }
                
                //Load the excel file
                $obj_php_excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($input_file_name);
                
                //Get settings sheet
                $sheet = $obj_php_excel->getSheetByName("settings"); 

                //find height column and row
                $highest_row = $sheet->gethighestrow(); 
                $highest_column = $sheet->gethighestcolumn();

                //Get header row
                $headings = $sheet->rangeToArray('A1:' . $highest_column . 1,NULL,TRUE,FALSE);

                //Find column index for each property
                $col_form_title_index = null;
                $col_form_name_index = null;
                $col_form_version_index = null;
                $col_form_ctype_index = null;

                foreach($headings[0] as $key => $col){
                    if(_strtolower($col) == _strtolower("form_title"))
                        $col_form_title_index = $key + 1;
                    if(_strtolower($col) == _strtolower("form_id"))
                        $col_form_name_index = $key + 1;
                    if(_strtolower($col) == _strtolower("version"))
                        $col_form_version_index = $key + 1;
                    if(_strtolower($col) == _strtolower("gc:ctypeName"))
                        $col_form_ctype_index = $key + 1;
                }

                //Load cron info
                $form_name = _trim($sheet->getCellByColumnAndRow($col_form_title_index,2)->getFormattedValue());
                $form_id = _trim(_strtolower($sheet->getCellByColumnAndRow($col_form_name_index,2)->getFormattedValue()));
                $form_version = _trim(_strtolower($sheet->getCellByColumnAndRow($col_form_version_index,2)->getFormattedValue()));
                $form_ctype = _trim(_strtolower($sheet->getCellByColumnAndRow($col_form_ctype_index,2)->getFormattedValue()));
                
                //Load Content-Type based on the cron
                $ctype_obj = (new Ctype)->load($form_ctype);
                

                //If the Content-Type not found in system, show error
                if(!isset($ctype_obj) || $ctype_obj == array()){
                    throw new \App\Exceptions\NotFoundException("ERROR! Ctype $form_ctype not found");
                }

                //Create a new object and assign properties to it
                $data = new \stdClass();
                $data->sett_ctype_id = "crons";
                $data->id = $form_id;
                $data->sett_is_update = false;
                $data->name = _trim($form_name);
                $data->version = _trim($form_version);
                $data->ctype_id = _trim($ctype_obj->id);
                $data->group_id = isset($cron_group_obj) ? _trim($cron_group_obj->id) : null;
                $data->type_id = "sync_odk_form";

                //create an array for regular fields which are not inside repeat (Field-Collection)
                $data->fields = array();

                //create an array for fields which are inside repeat (Field-Collection)
                $data->field_collections = array();
                
                //Get survey sheet
                $sheet = $obj_php_excel->getSheetByName("survey"); 

                //find height column and row
                $highest_row = $sheet->gethighestrow(); 
                $highest_column = $sheet->gethighestcolumn();

                //get header row
                $headings = $sheet->rangeToArray('A1:' . $highest_column . 1,NULL,TRUE,FALSE);

                //Find column index for below properties
                $col_gc_field_name_index = null;
                $col_type_index = null;
                $col_name_index = null;
                $col_tag_index = null;

                foreach($headings[0] as $key => $col){
                    if(_strtolower($col) == _strtolower("gc:fieldname"))
                        $col_gc_field_name_index = $key + 1;
                    if(_strtolower($col) == _strtolower("gc:tag"))
                        $col_tag_index = $key + 1;
                    if(_strtolower($col) == _strtolower("type"))
                        $col_type_index = $key + 1;
                    if(_strtolower($col) == _strtolower("name"))
                        $col_name_index = $key + 1;
                }

                //Set allowed field types
                $allowed_field_types = array("text","date","file","integer","geopoint","geotrace","calculate","decimal","decimal","image","select_multiple","select_one", "start","end");

                $fields_array = array();
                $fields_array['main_table'] = array();
                $repeat_fields = array();
                $prefix = "";
                $current_repeat = "";
                $current_fc_name = ""; //repeat name without prefix
                $groups = array();
                $groups_before_repeat = array();

                $last_image_multi_repeat_name = null;

                //Loop throw rows one by one
                for ($row = 1; $row <= $highest_row; $row++){
                
                    //Get type
                    $type = _trim(_strtolower($sheet->getCellByColumnAndRow($col_type_index,$row)->getFormattedValue()));
                    
                    $type = strtolower($type);

                    if($type == "begin repeat")
                        $type = "begin_repeat";
                    elseif($type == "end repeat")
                        $type = "end_repeat";
                    elseif($type == "begin group")
                        $type = "begin_group";
                    elseif($type == "end group")
                        $type = "end_group";
                    
                    $type_full = $type;
                    
                    
                    if(isset($type) && _strlen($type) > 0){
                        $type = _explode(" ", $type)[0];
                    }

                    //Get tag, gc_field_name and name
                    $tag = _trim(_strtolower($sheet->getCellByColumnAndRow($col_tag_index,$row)->getFormattedValue()));
                    $gc_field_name = _trim(_strtolower($sheet->getCellByColumnAndRow($col_gc_field_name_index,$row)->getFormattedValue()));
                    $name = _trim(_strtolower($sheet->getCellByColumnAndRow($col_name_index,$row)->getFormattedValue()));
                    
                    //with each begin group put the group name inside $groups array
                    if($type_full == "begin_group"){
                        array_push($groups, $name);
                    }

                    //with each end group pop the group name inside $groups array
                    if($type_full == "end_group"){
                        array_pop($groups);
                    }

                    $prefix = "";
                    //loop throw groups and create prefix for the field
                    foreach($groups as $group){
                        $prefix .= $group . "_";   
                    }

                    //If beginning of repeat and the repeat is not for image multi
                    if($type_full == "begin_repeat" && $tag != "image_repeat"){
                        
                        $current_fc_name = $name;

                        if(sizeof($groups) > 0) {
                            $name = implode("_" ,$groups) . "_" . $name;
                        }

                        $groups_before_repeat = $groups;
                        $groups = [];

                        $current_repeat = $name;
                        // if($name != "info_household_fc" && $name != "identify_info") {
                        //     echo $name . "\n";
                        //     print_r($groups);exit;
                        // }
                    }

                    //If beginning of repeat and the repeat is for image multi
                    if($type_full == "begin_repeat" && $tag == "image_repeat"){
                        $last_image_multi_repeat_name = $name;
                    }

                    //If end of repeat
                    if($type_full == "end_repeat" && $last_image_multi_repeat_name == null){
                        $fields_array[$current_repeat] = $repeat_fields;
                        $current_repeat = "";
                        $repeat_fields = array();

                        $groups = $groups_before_repeat;
                    }

                    if($type_full == "end_repeat" && $last_image_multi_repeat_name != null){
                        $last_image_multi_repeat_name = null;

                        $groups = $groups_before_repeat;
                    }

                    //If type and name and gc_field_name all have values and type exist inside allowed_field_types
                    if(!empty($gc_field_name) && !empty($name) && !empty($type) && in_array($type, $allowed_field_types)){
                        
                        //Check if it is inside repeat
                        if(!empty($current_repeat)){

                            $image_multi_repeat_name = $last_image_multi_repeat_name;

                            if(!empty($image_multi_repeat_name)) {
                                $tag = "image_repeat";
                            }

                            //push it to array
                            array_push($repeat_fields, array("name" => $prefix . $name, "type" => $type, "gc_field_name" => $gc_field_name,"tag" => $tag, "image_multi_repeat_name" => $image_multi_repeat_name, "gc_fc_name" => $current_fc_name));

                        //If it is not inside repeat
                        } else { 

                            //push it to array
                            array_push($fields_array['main_table'], array("name" => $prefix . $name, "type" => $type, "gc_field_name" => $gc_field_name,"tag" => $tag, "gc_fc_name" => $current_fc_name));
                        }
                        
                    }
                
                }

                
                //Loop throw fields array and put it into data object
                foreach($fields_array as $key => $fields){
                    
                    //Loop throw fields one by one
                    foreach($fields as $field){
                        
                        $obj = new \stdClass();
                        $obj->odk_name = _trim($field['name']);
                        $obj->gc_name = _trim($field['gc_field_name']);
                        $obj->data_type = _trim($field['type']);
                        $obj->tag = _trim($field['tag']);
                        $obj->gc_fc_name = _trim($field['gc_fc_name']);

                        if($key == 'main_table'){
                            
                            //push the object into fields array
                            $data->fields[] = $obj;

                        } else {

                            //set repeat name and image_multi_repeat name
                            $obj->repeat_name = $key;
                            $obj->image_multi_repeat_name = (isset($field['image_multi_repeat_name']) ? $field['image_multi_repeat_name'] : "");

                            //push the object into Field-Collections array
                            $data->field_collections[] = $obj;

                        }
                    
                    }
                }
                
                //Send the data objec to save
                $id = self::$coreModel->node_save($data);
                
                 Application::getInstance()->session->flash('flash_success', 'Cron Imported Successfuly');
                 
                 $data = array ("title" => "Import Generic Cron");
                 Application::getInstance()->view->renderView("admin/GenericImport/ImportGCron", $data);
    
            } else {
                
                $data = array ("title" => "Import Generic Cron");
                Application::getInstance()->view->renderView("admin/GenericImport/ImportGCron", $data);
                
            }
        
    }

}