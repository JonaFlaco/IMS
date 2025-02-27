<?php 

namespace App\Models\Sub;

use App\Core\Application;
use App\Core\Gctypes\Ctype;

class SubNodeLoad {
    
    public static function Main($ctype_id, $id = null, $settings = array()){

        $coreModel = Application::getInstance()->coreModel;

        $settings = array_change_key_case($settings,CASE_LOWER);

        $where = null;
        if($settings != null && isset($settings['where'])){
            $where = $settings['where'];
        }
        $bind_values = array();
        if($settings != null && isset($settings['bind_values'])){
            $bind_values = $settings['bind_values'];
        }

        $select_fields = array();
        if($settings != null && isset($settings['select_fields'])){
            $select_fields = $settings['select_fields'];
        }

        $load_fc = true;
        if($settings != null && isset($settings['load_fc'])){
            $load_fc = $settings['load_fc'];
        }

        $deep_load = false;
        if($settings != null && isset($settings['deep_load'])){
            $deep_load = $settings['deep_load'];
        }

        $limit = null;
        if($settings != null && isset($settings['limit'])){
            $limit = $settings['limit'];
        }

        $order_by = null;
        if($settings != null && isset($settings['order_by'])){
            $order_by = $settings['order_by'];
        }

        $ctype_obj = (new Ctype)->load($ctype_id);

        $fields = $ctype_obj->getFields();

        $qry = "select " . (!empty($limit) ? " TOP " . $limit : "") . " '$ctype_id' as sett_ctype_id ";
        
        $lang = \App\Core\Application::getInstance()->user->getLangId();
        if(!empty($lang)) {
            $lang = "_" . $lang;
        }
        
        foreach($fields as $field){
            
            if(in_array($field->name, $select_fields) || ($field->name == "id" && $field->is_system_field == true) || $select_fields == array()){
                $field->found = true;
                
            } else {
                $field->found = false;
            }
        }

        if($select_fields == array()){
            $qry .= ",m.* ";
        } else {
            foreach($fields as $field){
                if($field->found == true){
                    $qry .= ",m.$field->name";
                }
            }
        }

        
        foreach($fields as $field){
            
            if($field->found != true){
                continue;
            }
            
            if($field->field_type_id == "relation" && $field->is_multi == true  && $field->data_source_value_column_is_text != true){
                $qry .= ",STUFF((SELECT '\n' + s. " . $field->data_source_display_column . " FROM " . $field->ctype_id . "_" . $field->name . " sf left join $field->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $field->name  . "_display ";
            } else if($field->field_type_id == "relation" && $field->is_multi == true  && $field->data_source_value_column_is_text == true){
                $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $field->ctype_id . "_" . $field->name . " sf WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $field->name  . "_display ";
            } else if($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text != true){
                $qry .= " ," . $field->data_source_table_name . "_" . $field->name . ".$field->data_source_display_column" . " as " . $field->name . "_display ";
            }
        }
        
        $qry .= " from " . "$ctype_id m ";

        foreach($fields as $field){
            if($field->found != true){
                continue;
            }
            if($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text != true){
                $qry .= " LEFT JOIN " . $field->data_source_table_name . " " . $field->data_source_table_name . "_" . $field->name . " ON " . $field->data_source_table_name . "_" . $field->name . ".$field->data_source_value_column = m.$field->name ";
            }
        }

        $where_str = "";

        if(isset($id) && _strlen($id) > 0){
            $where_str .= ($ctype_obj->is_field_collection ? " m.parent_id" : "m.id") . " = :id ";
        }

        if(isset($where_str) && _strlen($where_str) > 0 && isset($where) && _strlen($where) > 0) {
            $where_str .= " AND ";
        }
        
        if(isset($where) && _strlen($where) > 0){
            $where_str .= " $where ";
        }
        

        if(isset($where_str) && _strlen($where_str) > 0){
            $qry .= " Where $where_str ";
        }
        

        $order_by_qry = null;
        if(!empty($order_by)){
            $order_by_qry .= " order by $order_by ";
        } else {

            if($ctype_obj->is_field_collection){
                foreach($fields as $field){
                    if($field->found != true){
                        continue;
                    }
                    if($field->name == "sort"){
                        $order_by_qry .= " order by m.sort ";
                    }
                }
            } else if (!isset($id)){
                $order_by_qry .= " order by " . ($ctype_obj->is_field_collection ? " m.parent_id " : " m.id ");
            }
        }

        $qry .= " " . $order_by_qry;

        $coreModel->db->query($qry);
        
        if(isset($id) && _strlen($id) > 0) {
            $bind_values[":id"] = $id;
        }

        //bindValues for where conditions
        foreach($bind_values as $key => $value) {
            $coreModel->db->bind("{$key}", $value);
        }

        $results_main = $coreModel->db->resultSet();

        //Deep load
        if($deep_load == true){
            foreach($results_main as &$res){
                foreach($fields as $field){
                    if($field->found != true){
                        continue;
                    }
                    if($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text != true){
                        
                        if(!isset( $res->{$field->name}) || _strlen($res->{$field->name}) == 0)
                            continue;

                        $qry = "select m.* ";
                
                        $refFields = $field->getFields();
                        foreach($refFields as $refField){
                            
                            
                            if($refField->field_type_id == "relation" && $refField->is_multi == true  && $refField->data_source_value_column_is_text != true){
                                $qry .= ",STUFF((SELECT '\n' + s.$refField->data_source_display_column FROM " . $refField->ctype_id . "_" . $refField->name . " sf left join $refField->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                            } else if($refField->field_type_id == "relation" && $refField->is_multi == true  && $refField->data_source_value_column_is_text == true){
                                $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $refField->ctype_id . "_" . $refField->name . " sf WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                            } else if($refField->field_type_id == "relation" && $refField->is_multi != true && $refField->data_source_value_column_is_text != true){
                                $qry .= " ," . $refField->data_source_table_name . "_" . $refField->name . ".$refField->data_source_display_column as " . $refField->name . "_display ";
                            }
                        }
                        
                        $qry .= " from $field->data_source_table_name m ";

                        foreach($refFields as $refField){
                            if($refField->field_type_id == "relation" && $refField->is_multi != true && $refField->data_source_value_column_is_text != true){
                                $qry .= " LEFT JOIN " . $refField->data_source_table_name . " " . $refField->data_source_table_name . "_" . $refField->name . " ON " . $refField->data_source_table_name . "_" . $refField->name . ".$refField->data_source_value_column = m.$refField->name ";
                            }
                        }

                        
                        $qry .= " Where m.id = :id ";
                        
                    
                        $coreModel->db->query($qry);
                    
                        $coreModel->db->bind(':id', $res->{$field->name});

                        $sub_array = $coreModel->db->resultSet();
                        
                        $res = (array($res));
                        
                        $res = (object)array_merge( ((array)$res[0]), array( "$field->name" . "_detail" => $sub_array ) );

                    }
                }
            }
        }
        
        $id_fc = "";
        
        foreach($results_main as $res){
            if(_strlen($id_fc) > 0)
                $id_fc .= ",";
            $id_fc .= $res->id;
        }
        
        
        foreach($results_main as &$res){

            $res = $res;
            
            foreach($fields as $field){
                if($field->found != true){
                    continue;
                }
                if($field->field_type_id == "relation" && $field->is_multi == true){ //ComboBox Multi

                    $coreModel->db->query("
                    select value_id as value from " . $field->ctype_id . "_" . $field->name . "
                    where parent_id = :id ");
                    $coreModel->db->bind(':id', $res->id);
                    
                    $res = (array($res));
                    
                    $sub_array = $coreModel->db->resultSet();
                    
                    $res = (object)array_merge( ((array)$res[0]), array( "$field->name" => $sub_array ) );
                    

                } else if($field->field_type_id == "media" && $field->is_multi == true) {
                    
                    $coreModel->db->query("select * from " . $field->ctype_id . "_" . $field->name . "
                    where parent_id = :id");
                    $coreModel->db->bind(':id', $res->id);
                    
                    
                    $res = (array($res));
                    
                    $sub_array = $coreModel->db->resultSet();
                    $res = (object) array_merge( (array)$res[0], array( "$field->name" => $sub_array ) );

                } else if ($field->field_type_id == "field_collection"){ //FieldCollection
                    if($load_fc == false){
                        continue;
                    }
                    $qry = "select fc.* ";
                    
                    $fcFields = $field->getFields();

                    $sort_by_order = false;
                    
                    foreach($fcFields as $fc){
                        
                        if($fc->name == "sort"){
                            $sort_by_order = true;
                        }

                        if($fc->field_type_id == "relation" && $fc->is_multi == true && $fc->data_source_value_column_is_text != true){
                            $qry .= ",STUFF((SELECT '\n' + s.$fc->data_source_display_column FROM " . $fc->ctype_id . "_" . $fc->name . " sf left join $fc->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = fc.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $fc->name  . "_display ";
                        } else if($fc->field_type_id == "relation" && $fc->is_multi == true  && $fc->data_source_value_column_is_text == true){
                            $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $fc->ctype_id . "_" . $fc->name . " sf WHERE sf.parent_id = fc.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $fc->name  . "_display ";
                        } else if($fc->field_type_id == "relation" && $fc->is_multi != true && $fc->data_source_value_column_is_text != true){
                            
                            $qry .= " ," . $fc->data_source_table_name . "_" . $fc->name . ".$fc->data_source_display_column as " . $fc->name . "_display ";
                            
                        }
                    }
                    
                    $qry .= " from " . "$field->data_source_table_name fc ";
                    
                    foreach($field->getFields() as $fc){
                        if($fc->field_type_id == "relation" && $fc->is_multi != true && $fc->data_source_value_column_is_text != true){
                            $qry .= " LEFT JOIN " . $fc->data_source_table_name . " " . $fc->data_source_table_name . "_" . $fc->name . " ON " . $fc->data_source_table_name . "_" . $fc->name . ".$fc->data_source_value_column = fc.$fc->name ";
                        }
                    }

                    
                    $qry .= " where fc.parent_id = :id ";

                    if($sort_by_order == true){
                        $qry .= " order by fc.sort ";
                    }
                    
                    if($sort_by_order != true){
                        $qry .= " order by fc.parent_id ";
                    }

                    $coreModel->db->query($qry);
                    
                    $coreModel->db->bind(':id', $res->id);
                    
                    
                    
                    $res = (array($res));
                    
                    $sub_array = $coreModel->db->resultSet();
                    
                    if($deep_load == true){
                        //Deep loading
                        foreach($sub_array as &$res_sub){
                            foreach($field->getFields() as $fc){
                                if($fc->field_type_id == "relation" && $fc->is_multi != true && $fc->data_source_value_column_is_text != true){
                                    
                                    if(!isset( $res_sub->{$fc->name}) || _strlen($res_sub->{$fc->name}) == 0)
                                        continue;
            
                                    //echo "$field->name: " . $res_sub->{$field->name} . "<br>";
            
                                    $qry = "select m.* ";
                            
                                    $refFields = $fc->getFields();
                                    foreach($refFields as $refField){
                                        
                                        
                                        if($refField->field_type_id == "relation" && $refField->is_multi == true  && $refField->data_source_value_column_is_text != true){
                                            $qry .= ",STUFF((SELECT '\n' + s.$refField->data_source_display_column FROM " . $refField->ctype_id . "_" . $refField->name . " sf left join $refField->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                                        } else if($refField->field_type_id == "relation" && $refField->is_multi == true  && $refField->data_source_value_column_is_text == true){
                                            $qry .= ",STUFF((SELECT '\n' + sf.value_id FROM " . $refField->ctype_id . "_" . $refField->name . " sf WHERE sf.parent_id = m.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as " . $refField->name  . "_display ";
                                        } else if($refField->field_type_id == "relation" && $refField->is_multi != true && $refField->data_source_value_column_is_text != true){
                                            $qry .= " ," . $refField->data_source_table_name . "_" . $refField->name . ".$refField->data_source_display_column as " . $refField->name . "_display ";
                                        }
                                    }
                                    
                                    $qry .= " from $fc->data_source_table_name m ";
            
                                    foreach($refFields as $refField){
                                        if($refField->field_type_id == "relation" && $refField->is_multi != true && $refField->data_source_value_column_is_text != true){
                                            $qry .= " LEFT JOIN " . $refField->data_source_table_name . " " . $refField->data_source_table_name . "_" . $refField->name . " ON " . $refField->data_source_table_name . "_" . $refField->name . ".$refField->data_source_value_column = m.$refField->name ";
                                        }
                                    }
            
                                    //if(isset($where_str) && _strlen($where_str) > 0){
                                        $qry .= " Where m.id = :id ";
                                    //}
                                    
                                    $coreModel->db->query($qry);
                                
                                    $coreModel->db->bind(':id', $res_sub->{$fc->name});
            
                                    $sub_sub_array = $coreModel->db->resultSet();
                                    
                                    $res_sub = (array($res_sub));
                                    
                                    $res_sub = (object)array_merge( ((array)$res_sub[0]), array( "$fc->name" . "_detail" => $sub_sub_array ) );
            
                                }
                            }
                        }
                    }


                    
                    foreach($sub_array as &$res2){

                        foreach($field->getFields() as $fc){
                            if($fc->field_type_id == "relation" && $fc->is_multi == true){
                                $coreModel->db->query("
                                select value_id as value from " . $fc->ctype_id . "_" . $fc->name . "
                                where parent_id = :id ");
                                $coreModel->db->bind(':id', $res2->id);
                                
                                $res2 = (array($res2));
                                
                                $fc_sub_array = $coreModel->db->resultSet();
                                
                                $res2 = (object)array_merge( ((array)$res2[0]), array( "$fc->name" => $fc_sub_array ) );
                            } else if($fc->field_type_id == "media" && $fc->is_multi == true) {
                                //echo "p id: $res->id<br>";
                                $coreModel->db->query("select * from " . $fc->ctype_id . "_" . $fc->name . "
                                where parent_id = :id");
                                $coreModel->db->bind(':id', $res2->id);
                                
                                $res2 = (array($res2));
                                
                                $fc_sub_array = $coreModel->db->resultSet();
                                
                                $res2 = (object)array_merge( ((array)$res2[0]), array( "$fc->name" => $fc_sub_array ) );
                            }            
                        }
                    }

                    $res = (object) array_merge( (array)$res[0], array( "$field->name" => $sub_array ) );
                    
                }
            }
        }

        
        return $results_main;
        

    }
}