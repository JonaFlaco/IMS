<?php 

namespace App\Models\Sub;

use \App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;

class PreloadList {

    private static $coreModel;
    
    public static function get($ctype_id, $data_source_value_column, $data_source_display_column, $settings = array()){
        
        self::$coreModel = Application::getInstance()->coreModel;

        $filter_by_column_name = null;
        if(isset($settings['filter_by_column_name'])){
            $filter_by_column_name = $settings['filter_by_column_name'];
        }

        $filter_value = null;
        if(isset($settings['filter_value'])){
            $filter_value = $settings['filter_value'];
        }

        $fixed_where_condition = null;
        if(isset($settings['fixed_where_condition'])){
            $fixed_where_condition = $settings['fixed_where_condition'];
        }

        $add_all_option = false;
        if(isset($settings['add_all_option'])){
            $add_all_option = $settings['add_all_option'];
        }

        
        $field_id = null;
        if(isset($settings['field_id'])){
            $field_id = $settings['field_id'];
        }

        $sort_field_name = null;
        if(isset($settings['sort_field_name'])){
            $sort_field_name = $settings['sort_field_name'];
        }

        $original_ctype_id = null;
        if(isset($settings['original_ctype_id'])){
            $original_ctype_id = $settings['original_ctype_id'];
        }

        $keyword = null;
        if(isset($settings['keyword'])){
            $keyword = $settings['keyword'];
        }
        

        $ctype_obj = (new Ctype)->load($ctype_id);
        
        $fields = $ctype_obj->getFields();
        $found_trans_column = false;

        $display_column_field_type_is_combobox = false;
        $display_column_display_field_name = null;
        $display_column_value_field_name = null;
        $display_column_ctype_id = null;

        
        foreach($fields as $field){
            if($field->name == $data_source_display_column && $field->field_type_id == "relation"){
                $display_column_field_type_is_combobox = true;
                $display_column_display_field_name = $field->data_source_display_column;
                $display_column_value_field_name = $field->data_source_value_column;
                $display_column_ctype_id = $field->data_source_id;
                break;
            }
        }

        if($display_column_field_type_is_combobox != true){
            foreach($fields as $field){
                if($field->name == $data_source_display_column . "_" . \App\Core\Application::getInstance()->user->getLangId()){
                    $found_trans_column = true;
                    break;
                }
            }
        } else {
                  
            $fields_x = (new CtypeField)->loadByCtypeId($display_column_ctype_id);
            $found_trans_column = false;
            foreach($fields_x as $field){
                if($field->name == $data_source_display_column . "_" . \App\Core\Application::getInstance()->user->getLangId()){
                    $found_trans_column = true;
                    break;
                }
            }
        }

        $where = "";

        if(Application::getInstance()->user->isAuthenticated()){
            if(Application::getInstance()->user->isAuthenticated() && \App\Core\Application::getInstance()->user->isAdmin() != true && isset($ctype_obj->governorate_field_name) && _strlen($ctype_obj->governorate_field_name) > 0 && \App\Core\Application::getInstance()->user->isAdmin() != true){
                $govs = implode(",", Application::getInstance()->user->getUserGovernorates($original_ctype_id, "allow_read"));
                if(!isset($govs) || _strlen($govs) == 0){
                    $govs = "NULL";
                }
                $where .= sprintf(' AND m.%s in (%s) ', $ctype_obj->governorate_field_name, $govs);
            }

            if(\App\Core\Application::getInstance()->user->isAdmin() != true && isset($ctype_obj->unit_field_name) && _strlen($ctype_obj->unit_field_name) > 0 && \App\Core\Application::getInstance()->user->isAdmin() != true){
                $units = implode(",", Application::getInstance()->user->getUserUnits($original_ctype_id, "allow_read"));
                if(!isset($units) || _strlen($units) == 0){
                    $units = "NULL";
                }
                $where .= sprintf(' AND m.%s in (%s) ', $ctype_obj->unit_field_name, $units);
            }


            if(Application::getInstance()->user->isAdmin() != true && isset($ctype_obj->form_type_field_name) && _strlen($ctype_obj->form_type_field_name && \App\Core\Application::getInstance()->user->isAdmin() != true) > 0){
                $form_types = implode(",", Application::getInstance()->user->getUserFormTypes($original_ctype_id, "allow_read"));
                
                if(!isset($form_types) || _strlen($form_types) == 0){
                    $form_types = "NULL";
                }
                $where .= sprintf(' AND m.%s in (%s) ', $ctype_obj->form_type_field_name, $form_types);     
                
            }
        }


        if(!empty($fixed_where_condition)){
            $where .= "AND " . $fixed_where_condition;
        }

        $returnTop = null;
        if(!empty($keyword)){
            $returnTop = "TOP 100";

            $where .= "AND $data_source_display_column like N'%$keyword%'";
        }

        
        
        if(!empty($field_id)) {

            $field = (new CtypeField)->loadById($field_id);

            if(isset($field)) {

                //Extra PL condition
                $className = toPascalCase($field->ctype_id);
                                            
                $classToRun = sprintf('\App\Middlewares\%s', $className);
                if(!class_exists($classToRun)){
                    $classToRun = sprintf('\Ext\Middlewares\%s', $className);
                }
                
                if(class_exists($classToRun)){
                    
                    $classObj = new $classToRun();
                
                    if(method_exists($classObj, "pl_" . $field->name)){
                        $res = $classObj->{"pl_" . $field->name}($filter_value);
                        if(!empty($res))
                            $where .= "AND " . $res;
                    }

                }
            }
        }
        
        $i = 0;

        foreach(_explode(",", $filter_by_column_name) as $itm){
            $j = 0;
            
            if(is_array($filter_value)) {
                foreach($filter_value as $v){

                    $whitelist = '/[^a-zA-Z0-9 -_,]/';
                    $v = preg_replace($whitelist, '', $v);
                    
                    if($i == $j){

                        if(!empty($itm) && !empty($v) && _strtolower($v) != "null"){

                            $filter_field = (new CtypeField)->loadByCtypeIdAndFieldNameOrDefault($ctype_obj->id, $itm);
                            
                            if(isset($filter_field) && _strlen($v) > 0){

                                if(!isset($filter_field->is_multi) || $filter_field->is_multi != true){
                                    $where .= " AND $itm in (";
                                    $m = 0;
                                    foreach(_explode(",", $v) as $item) {
                                        if($m++ > 0)  
                                            $where .= ",";
                                        
                                        $where .= "'$item'";
                                    }
                                    $where .= ") ";
                                } else {
                                    $where .= " AND (select count(*) from " . $ctype_id . "_$itm x where parent_id = $ctype_id.id and value_id in (";
                                    $m = 0;
                                    foreach(_explode(",", $v) as $item) {
                                        if($m++ > 0)  
                                            $where .= ",";

                                        $where .= "'$item'";
                                    }
                                    $where .= ")) > 0 ";
                                }

                            }
                            
                        }
                    }
                    $j++;
                    
                }
            }
            $i++;
        }

        $all_str = ($add_all_option == true ? " SELECT null AS id, ' -All- ' AS name UNION " : "");
        
        if(empty($data_source_display_column)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Data Source display column is required, but not provided - $ctype_id - $field_id");
        }

        $lang = \App\Core\Application::getInstance()->user->getLangId();

        if($display_column_field_type_is_combobox == true){

            $qry = $all_str . " 
                SELECT $returnTop 
                    m.$data_source_value_column as id, " . 
                    ($found_trans_column == true ? " case when isnull(" . $display_column_ctype_id.$display_column_display_field_name . "_" . $lang . ",'') = '' then $display_column_ctype_id.$display_column_display_field_name else " . $display_column_ctype_id.$display_column_display_field_name . "_" . $lang . " end " : $display_column_ctype_id . "." . $display_column_display_field_name ) . " as name 
                FROM $ctype_id m
                left join $display_column_ctype_id on $display_column_ctype_id.$display_column_value_field_name = m.$data_source_display_column 
                WHERE 1=1 $where 
                ORDER BY " . (!empty($sort_field_name) ? $sort_field_name : "id");

        } else {
            $qry = $all_str . " 
                SELECT $returnTop 
                    m.$data_source_value_column as id, " . 
                    ($found_trans_column == true ? " case when isnull(m." . $data_source_display_column . "_" . $lang . ",'') = '' then m.$data_source_display_column else m." . $data_source_display_column . "_" . $lang . " end m." : $data_source_display_column ) . " as name 
                FROM $ctype_id m
                WHERE 1=1 $where 
                ORDER BY " . (!empty($sort_field_name) ? $sort_field_name : "id");
        }
    

        self::$coreModel->db->query($qry);
        
        $results = self::$coreModel->db->resultSet();

        return $results;

    }
}