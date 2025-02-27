<?php

use App\Core\Application;
use App\Core\Gctypes\CtypeField;
use App\Exceptions\CtypeValidationException;
use App\Exceptions\IlegalUserActionException;

function gimport_validate_selected($is_not, $org_field,$data, $key, $value, $operator = "="){
        
        if($is_not == false){
            if($operator == "="){
                if(isset($data[$key]) && $data[$key] == $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == "!="){
                if(isset($data[$key]) && $data[$key] != $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == ">"){
                if(isset($data[$key]) && $data[$key] > $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == "<"){
                if(isset($data[$key]) && $data[$key] < $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == "<="){
                if(isset($data[$key]) && $data[$key] <= $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == ">="){
                if(isset($data[$key]) && $data[$key] >= $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                        return true;
                    } else {
                        return false;
                    }
                }

        } else {
            if($operator == "="){
                if((!isset($data[$key]) || $data[$key] != $value) && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == "!="){
                if(isset($data[$key]) && $data[$key] != $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return !true;
                } else {
                    return !false;
                }
            } else if($operator == ">"){
                if(isset($data[$key]) && $data[$key] > $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return !true;
                } else {
                    return !false;
                }
            } else if($operator == "<"){
                if(isset($data[$key]) && $data[$key] < $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return !true;
                } else {
                    return !false;
                }

            } else if($operator == ">="){
                if(isset($data[$key]) && $data[$key] >= $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return !true;
                } else {
                    return !false;
                }

            } else if($operator == "<="){
                if(isset($data[$key]) && $data[$key] <= $value && (!isset($data[$org_field]) || empty($data[$org_field]))){
                    return !true;
                } else {
                    return !false;
                }
            }
        }

    }

    function gimport_validate_v_selected($is_not, $org_field,$data, $key, $value, $operator = "="){
        
        if($is_not == false){
            if($operator == "="){
                if(isset($data[$key]) && $data[$key] == $value){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == "!="){
                if(isset($data[$key]) && $data[$key] != $value){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == ">"){
                if(isset($data[$key]) && $data[$key] > $value){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == "<"){
                if(isset($data[$key]) && $data[$key] < $value){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == "<="){
                if(isset($data[$key]) && $data[$key] <= $value){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == ">="){
                
                if(isset($data[$key]) && $data[$key] >= $value){
                        return true;
                    } else {
                        return false;
                    }
                }

        } else {
            if($operator == "="){
                if(isset($data[$key]) && $data[$key] == $value){
                    return false;
                } else {
                    return true;
                }
            } else if($operator == "!="){
                if(isset($data[$key]) && $data[$key] != $value){
                    return false;
                } else {
                    return true;
                }
            } else if($operator == ">"){
                if(isset($data[$key]) && $data[$key] > $value){
                    return true;
                } else {
                    return false;
                }
            } else if($operator == "<"){
                if(isset($data[$key]) && $data[$key] < $value){
                    return false;
                } else {
                    return true;
                }

            } else if($operator == ">="){
                if(isset($data[$key]) && $data[$key] >= $value){
                    return false;
                } else {
                    return true;
                }

            } else if($operator == "<="){
                if(isset($data[$key]) && $data[$key] <= $value){
                    return false;
                } else {
                    return true;
                }
            }
        }

    }

    function gimport_validate($ctype, $data){
        return;

        foreach($data->tables as $itm){

            $fields_fields = (new CtypeField)->loadByCtypeId($itm->id);
            
            $dataTobeChecked = array();
            if($itm->type == "main_table")
                $dataTobeChecked[] = $itm;
            else if ($itm->type == "fieldcollection")
                $dataTobeChecked = $itm->data->data->tables;

            

            foreach($dataTobeChecked as $records){
                    
                foreach($fields_fields as $field_def){
                    $field_def->found = false;
                    $field_def->has_value = false;

                    foreach($records->data as $key => $value){
                        
                        if($field_def->name == $key){
                            
                            $field_def->found = true;
                            $field_def->has_value = $value != null && _strlen(_trim($value)) > 0;

                            if($field_def->is_required == true && !$field_def->has_value){

                                return "$field_def->title is required, but not supplied.";
                            }

                            if(!empty($value)){
                                
                                if($field_def->field_type_id == "text"){
                                    if (!isset($field_def->str_length)){
                                        $field_def->str_length = TEXT_DEFAULT_LENGTH;
                                    }
        
                                    if(_strtolower($field_def->str_length) != "max" && $field_def->str_length < mb_strlen($value)){
                                        throw (new CtypeValidationException("Value for '$field_def->title' is exeeding field length"))->addExtraDetail("Defined length: " . $field_def->str_length . ", data ength: " . mb_strlen($value));
                                    }
                                    
                                }

                                if($field_def->field_type_id == "number" && !$field_def->name == "id"){
                                    
                                    if(!is_numeric($value) || _strpos($value,".") !== false){
                                        throw (new CtypeValidationException("Value for '$field_def->title' is not a valid number"))->addExtraDetail("Value: $value");
                                    }
                                    
                                }

                                if($field_def->field_type_id == "decimal"){
                                    
                                    if(!is_numeric($value)){
                                        throw (new CtypeValidationException("Value for '$field_def->title' is not a valid decimal"))->addExtraDetail("Value: $value");
                                    }
                                    
                                }

                                if($field_def->field_type_id == "date"){
                                    
                                    $dObj = date_parse($value);
                                    
                                    if(!isset($dObj['year'])){
                                        throw (new CtypeValidationException("Value for '$field_def->title is not a valid date"))->addExtraDetail("Value: $value");
                                    }
                        
                                    $year = date('Y',strtotime($value));

                                    if($year < 1900 || $year > 2100){
                                        throw (new CtypeValidationException("Value for '$field_def->title' is not a valid date or out of range"))->addExtraDetail("Value: $value");
                                    }
                                }

                                if($field_def->field_type_id == "boolean"){

                                    $value = _strtolower($value);
                                    
                                    if(!empty($value) || $value == "0" || $value == "1"){
                                        //Valid
                                    } else {
                                        throw (new CtypeValidationException("Value for '$field_def->title' is not a valid boolean"))->addExtraDetail("Value: $value");
                                    }
                                }

                            }

                        }
                        //break;
                    }
                   
                    if($field_def->found != true && !empty($field_def->required_condition)){
                        
                        $record_has_data = false;
                        $record_value = null;
                        $field_name_to_be_passed = $field_def->name;

                        //TODO: fix required condition
                        continue;
                        
                        if($field_def->field_type_id == "relation" && $field_def->is_multi == true){
                            
                            foreach($data->tables as $tbl){
                                if($record_has_data == true)
                                    break;

                                if($tbl->type == "subtable" && $tbl->name == $itm->name . "_" . $field_def->name){
                                    if($tbl->data == null)
										continue;
									
                                    foreach($tbl->data as $d){
                                        
                                        if($record_has_data == true)
                                            break;
                                            
                                        if(!empty($d)){
                                            $record_has_data = true;
                                        }
                                    }
                                }
                            }
                        } else if($field_def->field_type_id == "media" && $field_def->is_multi == true){
                        
                            foreach($data->tables as $tbl){
                                if($record_has_data == true)
                                    break;

                                
                                if($tbl->type == "file" && $tbl->name == $itm->name . "_" . $field_def->name){
                                    if($tbl->data == array()){
                                        throw new CtypeValidationException("$field_def->title is required but not supplied");
                                        break;
                                    }
                                    
                                }
                            }
                            
                        } else {
                            if($field_def->field_type_id == "media" && $field_def->is_multi != true){
                                $field_name_to_be_passed = $field_def->name . "_name";
                            }

                            $record_value = (array)$records->data;
                        }

                        if($record_has_data == true){
                            continue;
                            $record_value = "-EMPTY-";
                        }

                        $condition = $field_def->required_condition;
                        $condition = "return ($condition);";
                        
                        $condition = _str_replace("selected(self,","selected(",$condition);
                        $condition = _str_replace("selected(","gimport_validate_selected(false,\$field_name_to_be_passed,\$record_value,",$condition);
                        $condition = _str_replace("!gimport_validate_selected(false,","gimport_validate_selected(true,",$condition);
                        $condition = _str_replace(" and"," && ",$condition);
                        $condition = _str_replace(" or"," || ",$condition);
                        $condition = _str_replace(")and",")&&",$condition);
                        $condition = _str_replace(")or",")||",$condition);

                        // if(eval($condition) == true){
                            
                        //     $r = "";
                        //     $r .= "<br>$field_def->title is required but not supplied..";
                        //     return $r;
                        // } 

                    }
                    
                    

                }
                
            }

            foreach($fields_fields as $field_def) {
                if($field_def->is_required && !$field_def->has_value)
                    throw new IlegalUserActionException($field_def->title . " is required but empty");
            }
        }
    
        $v = gimport_validate_v($ctype, $data);
        if(!empty($v))
            return $v;

        return "";
    }

    function gimport_validate_v($ctype, $data){
        foreach($data->tables as $itm){

            $fields_fields = (new CtypeField)->loadByCtypeId($itm->id);
            
            $dataTobeChecked = array();
            if($itm->type == "main_table")
                $dataTobeChecked[] = $itm;
            else if ($itm->type == "fieldcollection")
                $dataTobeChecked = $itm->data->data->tables;

            

            foreach($dataTobeChecked as $records){
                    
                foreach($fields_fields as $field_def){
                    $field_def->found = false;
                    $field_def->has_value = false;

                    foreach($records->data as $key => $value){
                        
                        if($field_def->name == $key){
                            $field_def->found = true;
                            $field_def->has_value = $value != null && _strlen(_trim($value)) > 0;

                            if($field_def->is_required == true && !$field_def->has_value){
                                return "$key is required, but not supplied.";
                            }

                            
                            if(!empty($value)){
                                
                                if($field_def->field_type_id == "text"){
                                    if (!isset($field_def->str_length)){
                                        $field_def->str_length = TEXT_DEFAULT_LENGTH;
                                    }
        
                                    if(_strtolower($field_def->str_length) != "max" && $field_def->str_length < mb_strlen($value)){
                                        throw (new CtypeValidationException("Value for '$field_def->title' is exeeding field length"))->addExtraDetail("Defined length: " . $field_def->str_length . ", data ength: " . mb_strlen($value));
                                    }
                                    
                                }

                                if($field_def->field_type_id == "number" && !$field_def->name == "id"){
                                    
                                    if(!is_numeric($value) || _strpos($value,".") !== false){
                                        throw (new CtypeValidationException("Value for '$field_def->title' is not a valid number"))->addExtraDetail("Value: $value");
                                    }
                                    
                                }

                                if($field_def->field_type_id == "decimal"){
                                    
                                    if(!is_numeric($value)){
                                        throw (new CtypeValidationException("Value for '$field_def->title' is not a valid decimal"))->addExtraDetail("Value: $value");
                                    }
                                    
                                }

                                if($field_def->field_type_id == "date"){
                                    
                                    $dObj = date_parse($value);
                                    
                                    if(!isset($dObj['year'])){
                                        throw new CtypeValidationException("Value for '$field_def->title is not a valid date");
                                    }
                        
                                    $year = date('Y',strtotime($value));

                                    if($year < 1900 || $year > 2100){
                                        throw (new CtypeValidationException("Value for '$field_def->title' is not a valid date or out of range"))->addExtraDetail("Value: $value");
                                    }
                                }

                                if($field_def->field_type_id == "boolean"){

                                    $value = _strtolower($value);
                                    
                                    if(!empty($value) || $value == "0" || $value == "1"){
                                        //Valid
                                    } else {
                                        throw new CtypeValidationException("Value for '$field_def->title' is not a valid boolean");
                                    }
                                }

                            }

                        }
                        break;
                    }

                    if($field_def->found != true && !empty($field_def->validation_condition)){
                        
                        $record_has_data = false;
                        $record_value = null;
                        $field_name_to_be_passed = $field_def->name;

                        if($field_def->field_type_id == "relation" && $field_def->is_multi == true){
                            
                            foreach($data->tables as $tbl){
                                if($record_has_data == true)
                                    break;

                                if($tbl->type == "subtable" && $tbl->name == $itm->name . "_" . $field_def->name){
                                    
                                    foreach($tbl->data as $d){
                                        
                                    }
                                }
                            }
                        } else if($field_def->field_type_id == "media" && $field_def->is_multi == true){
                        
                            foreach($data->tables as $tbl){
                                if($record_has_data == true)
                                    break;

                                
                                if($tbl->type == "file" && $tbl->name == $itm->name . "_" . $field_def->name){
                                    if($tbl->data == array()){
                                        throw new CtypeValidationException("$field_def->title is required but not supplied","simple");
                                        break;
                                    }
                                    
                                }
                            }
                            
                        } else {
                            if($field_def->field_type_id == "media" && $field_def->is_multi != true){
                                $field_name_to_be_passed = $field_def->name . "_name";
                            }

                            $record_value = (array)$records->data;
                        }

                        // if($record_has_data == true){
                        //     continue;
                        //     $record_value = "-EMPTY-";
                        // }

                        
                        $condition = $field_def->validation_condition;
                        $condition = "return ($condition);";
                        
                        $condition = _str_replace("selected(self,","selected(",$condition);
                        $condition = _str_replace("selected(","gimport_validate_v_selected(false,\$field_name_to_be_passed,\$record_value,",$condition);
                        $condition = _str_replace("!gimport_validate_selected(false,","gimport_validate_selected(true,",$condition);
                        $condition = _str_replace(" and"," && ",$condition);
                        $condition = _str_replace(" or"," || ",$condition);
                        $condition = _str_replace(")and",")&&",$condition);
                        $condition = _str_replace(")or",")||",$condition);

                        // if(eval($condition) == false){
                            
                        //     $r = "";
                        //     if(!empty($field_def->validation_message)){
                        //         $r .= "<br>$field_def->title: $field_def->validation_message";
                        //     } else {
                        //         $r .= "<br>$field_def->title: value is not valid";
                        //     }
                            
                        //     return $r;
                        // } 

                    }
                    
                    

                }
                
            }

            foreach($fields_fields as $field_def) {
                if($field_def->is_required && !$field_def->has_value)
                    throw new IlegalUserActionException($field_def->title . " is required but empty");
            }
        }
    

        return "";
    }
    