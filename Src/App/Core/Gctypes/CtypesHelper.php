<?php

/*
 * This class is responsible to render Add and Edit interface
 * It breaks down the process into sub components to make it easier to maintaine.
 */

namespace App\Core\Gctypes;

use App\Core\Application;

class CtypesHelper {

    public static string $TYPE_ADD = "add";
    public static string $TYPE_EDIT = "edit";
    public static string $TYPE_SHOW = "show";
    public static string $TYPE_DELETE = "delete";
    public static string $TYPE_VIEW = "view";

    public static function checkExtraConditions($type, $ctypeObj, $id = null, $params = null) {

        //Check if we have filterApi for this
        $className = toPascalCase($ctypeObj->id);
             
        if($ctypeObj->is_system_object) {
            $classToRun = sprintf('\App\Triggers\Base\BeforeRender', $className);
            if(class_exists($classToRun)){
                
                $classObj = new $classToRun();

                $classObj->index($type, $id, $params);
                
            }
        }
        
        $classToRun = sprintf('\Ext\Triggers\Base\BeforeRender', $className);
        if(class_exists($classToRun)){
            
            $classObj = new $classToRun();

            $classObj->index($type, $id, $params);
            
        }

        if($ctypeObj->is_system_object) {
            $classToRun = sprintf('\App\Triggers\%s\BeforeRender', $className);
            if(class_exists($classToRun)){
                
                $classObj = new $classToRun();

                $classObj->index($type, $id, $params);
                
            }
        }
        
        $classToRun = sprintf('\Ext\Triggers\%s\BeforeRender', $className);
        if(class_exists($classToRun)){
            
            $classObj = new $classToRun();

            $classObj->index($type, $id, $params);
            
        }

    }

    public function getFields($is_add_mode = true, $ctype_id = null, $field_id = null, $field_name = null, $use_cache = true) : array {

        $fields = Application::getInstance()->coreModel->getFields($ctype_id, $field_id, $field_name,$is_add_mode, $use_cache);

        foreach($fields as $field) {

            if(intval($field->size) <= 0 || intval($field->size) > 3 ) {
                $field->size = 1;
            }

            $field->size = (12 / $field->size);
         
            $field->is_hidden_updated = $field->is_hidden;

            if($is_add_mode != true && $field->is_hidden_updated_edit == true) {
                $field->is_hidden_updated = true;
            }

            if($is_add_mode && $field->is_hidden_updated_add == true) {
                $field->is_hidden_updated = true;
            }

            $field->select2_async = false;
            if($field->appearance_id == "2_select2_async") {
                $field->appearance_id = "2_select2";
                $field->select2_async = true;
            }
            
        }

        return $fields;
        
    }

    public function getComboboxOptions($field) : array {
        
        if(empty($field->data_source_from_string)){
           
            return Application::getInstance()->coreModel->getPreloadList($field->data_source_table_name,$field->data_source_value_column, $field->data_source_display_column, array("original_ctype_id" => $field->parent_id, "fixed_where_condition" => $field->data_source_fixed_where_condition, "sort_field_name" => $field->data_source_sort_column, "field_id" => $field->id));
            
        } else {
            
            return json_decode($field->data_source_from_string);

        }

    }
    
}
