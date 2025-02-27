<?php

namespace App\Core\Gctypes;

use App\Core\Application;
use App\Models\NodeModel;

class DbExportDataSql2 {
    
    private $coreModel;
    private $app;
    private $ctypes_with_hardcoded_ids = [];
    private $include_ext;

    public function __construct($include_ext) {
        $this->app = Application::getInstance();
        $this->coreModel = $this->app->coreModel;
        $this->include_ext = $include_ext;
    }


    public function generate() {

        $query = "";

        $ctypes = $this->coreModel->nodeModel("ctypes")
            ->fields(["id", "name"])
            ->where("isnull(m.is_system_object,0) = 1")
            ->where("isnull(m.is_field_collection,0) = 0")
            ->load();

        $exclude_ctypes = ["home_user_widgets","bg_tasks","survey_credentials", "ctypes_logs","crons_logs","password_reset_requests","notifications","error_log","emails","sms","users_login_logs","request_tracker","sec_ip_address"];

        foreach($ctypes as $item) {
            if(in_array($item->id, $exclude_ctypes))
                continue;
            

            $ctypeObj = (new Ctype())->load($item->id);
            $query .= $this->generate_dyn($ctypeObj);


        }

        return $query;
    }

    private function generate_dyn($ctypeObj) {
        $d = [];
        $fields = (new CtypeField)->loadByCtypeId($ctypeObj->id);
        $query = "\n----------------------\n-- $ctypeObj->id data\n----------------------\n";

        $d[$ctypeObj->id] = ""; 
        $d[$ctypeObj->id] .= "DELETE FROM $ctypeObj->id\n";

        if(empty($ctypeObj->primary_column_type) || $ctypeObj->primary_column_type == "bigint")
            $d[$ctypeObj->id] .= "SET IDENTITY_INSERT $ctypeObj->id ON\n";
        foreach($fields as $field) {
            if($field->field_type_id == "field_collection") {
                $d["{$ctypeObj->id}_{$field->name}"] = "DELETE FROM {$ctypeObj->id}_{$field->name}\n";

                if(empty($field->ctype_primary_column_type) || $field->ctype_primary_column_type == "bigint")
                    $d["{$ctypeObj->id}_{$field->name}"] .= "SET IDENTITY_INSERT {$ctypeObj->id}_{$field->name} ON\n";

                foreach($field->getFields() as $fc) {
                    if ($fc->field_type_id == "media" && $fc->is_multi) {
                        $d["{$ctypeObj->id}_{$field->name}_{$fc->name}"] = "DELETE FROM {$ctypeObj->id}_{$field->name}_{$fc->name}\n";
                    } else if ($fc->field_type_id == "relation" && $fc->is_multi) {
                        $d["{$ctypeObj->id}_{$field->name}_{$fc->name}"] = "DELETE FROM {$ctypeObj->id}_{$field->name}_{$fc->name}\n";
                    }        
                }
            } else if ($field->field_type_id == "media" && $field->is_multi) {
                $d["{$ctypeObj->id}_{$field->name}"] = "DELETE FROM {$ctypeObj->id}_{$field->name}\n";
            } else if ($field->field_type_id == "relation" && $field->is_multi) {
                $d["{$ctypeObj->id}_{$field->name}"] = "DELETE FROM {$ctypeObj->id}_{$field->name}\n";
            }
        }


        $list = (new NodeModel($ctypeObj->id))->load();

        foreach($list as $data) {

            $d["{$ctypeObj->id}"] .= "INSERT INTO $ctypeObj->id (id, token";
            foreach($fields as $field) {
                if(!isset($data->{$field->name}) || $field->is_system_field || !in_array($field->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $field->is_multi) {
                    continue;
                }

                $d["{$ctypeObj->id}"] .= ",";

                if($field->field_type_id == "media") {
                    $d["{$ctypeObj->id}"] .= $field->name . "_name," . $field->name . "_size," . $field->name . "_extension," . $field->name . "_type," . $field->name . "_original_name";
                } else {
                    $d["{$ctypeObj->id}"] .= $field->name;
                }


            }
            $d["{$ctypeObj->id}"] .= ") values (" . escape_query($data->id, "text", false) . "," . escape_query($data->token, "text", true);

            foreach($fields as $field) {
                if(!isset($data->{$field->name}) || $field->is_system_field || !in_array($field->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $field->is_multi) {
                    continue;
                }
                $d["{$ctypeObj->id}"] .= ",";

                if($field->data_source_value_column_is_text) {
                    $d["{$ctypeObj->id}"] .= escape_query($data->{$field->name}, "text", true);
                } else if($field->field_type_id == "relation" && $field->data_source_value_column_is_text != true) {
                    //if(in_array($field->data_source_table_name, $this->ctypes_with_hardcoded_ids)) {
                        $d["{$ctypeObj->id}"] .= empty($data->{$field->name}) ? "null" : "'" . $data->{$field->name} . "'";
                    //} else {
                    //    $query .= _strlen($data->{$field->name . "_display"}) == 0 ? "null" : escape_query($data->{$field->name}, 1, true);
                    //}
                } else if ($field->field_type_id == "media") {
                    $d["{$ctypeObj->id}"] .= escape_query($data->{$field->name . "_name"}, "text", true) . "," . escape_query($data->{$field->name . "_size"}, "number") . "," . escape_query($data->{$field->name . "_extension"}, "text", true) . "," . escape_query($data->{$field->name . "_type"}, "text", true) . "," . escape_query($data->{$field->name . "_original_name"}, "text", true);
                } else {
                    $d["{$ctypeObj->id}"] .= escape_query($data->{$field->name}, $field->field_type_id, true);
                }

            }
            $d["{$ctypeObj->id}"] .= ")\n";
        

            foreach($fields as $field) {
                if($field->field_type_id == "relation" && $field->is_multi) {
                    //$query .= "DELETE FROM {$ctypeObj->id}_{$field->name} WHERE parent_id = (select id from {$ctypeObj->id} WHERE token = " . escape_query($data->token, 1, true) . ")\n";

                    foreach($data->{$field->name} as $sub) {
                        if($field->data_source_value_column_is_text) {
                            $d["{$ctypeObj->id}_{$field->name}"] .= "INSERT INTO {$ctypeObj->id}_{$field->name} (parent_id,value_id) VALUES (" . escape_query($data->id, "text", false) . ", " . escape_query($sub->name, 1, true) . ")\n";
                        } else {
                            $d["{$ctypeObj->id}_{$field->name}"] .= "INSERT INTO {$ctypeObj->id}_{$field->name} (parent_id,value_id) VALUES (" . escape_query($data->id, 6, false) . ", " . escape_query($sub->value, 6, false) . ")\n";
                        }
                    }


                } else if ($field->field_type_id == "media" && $field->is_multi) {
                    //$query .= "DELETE FROM {$ctypeObj->id}_{$field->name} WHERE parent_id = (select id from {$ctypeObj->id} WHERE token = " . escape_query($data->token, 1, true) . ")\n";

                    foreach($data->{$field->name} as $sub) {
                        // $query .= "IF NOT EXISTS (SELECT * FROM {$ctypeObj->id}_{$field->name} WHERE token = " . escape_query($sub->token, 1, true) . ") BEGIN ";
                        $d["{$ctypeObj->id}_{$field->name}"] .= "INSERT INTO {$ctypeObj->id}_{$field->name} (parent_id, name, size, extension, original_name, type, token) VALUES (" . escape_query($data->id, 6, false) . ", " . escape_query($sub->name, 1, true) . ", " . escape_query($sub->size, 6) . "," . escape_query($sub->extension, 1, true) . "," . escape_query($sub->type, 1, true) . "," . escape_query($sub->token, 1, true) . ")\n";
                        //$query .= "END ELSE BEGIN ";
                        // $query .= "UPDATE {$ctypeObj->id}_{$field->name} SET parent_id = (select id from {$ctypeObj->id} WHERE token = " . escape_query($sub->token, 1, true) . "), name = " . escape_query($sub->name, 1, true) . ", size = " . escape_query($sub->size, 6, true) . ", extension = " . escape_query($sub->extension, 1, true) . ", original_name = " . escape_query($sub->original_name, 1, true) . ", type = " . escape_query($sub->type, 1, true) . " WHERE token = " . escape_query($sub->token, 1, true) . " ";
                        // $query .= "END";
                    }

                }
            }

            $has_fc = false;
            foreach($fields as $field) {
                if($field->field_type_id == "field_collection") {
                    $has_fc = true;
                }
            }
            
            // if($has_fc) {
            //     $query .= "set @parent_ctype_id = (select id from $ctypeObj->id where $field_name = " . escape_query($data->{$field_name}, 1, true) . ")\n";
            // }
            
            foreach($fields as $field) {
                if($field->field_type_id == "field_collection") {
                    
                    $fcFields = $field->getFields();

                    $tokens = [];
                    foreach($data->{$field->name} as $item) {
                        $tokens[] = $item->token;
                    }

                    
                    // $query .= "DELETE FROM {$ctypeObj->id}_{$field->name} WHERE @parent_ctype_id is not null and parent_id = @parent_ctype_id " . (sizeof($tokens) > 0 ? " and (token is null or token not in ('" . implode("','", $tokens) . "'))" : "") . "\n";

                    foreach($data->{$field->name} as $item) {
                        
                        $d["{$ctypeObj->id}_{$field->name}"] .= "INSERT INTO {$ctypeObj->id}_{$field->name} (id, token, parent_id";
                        foreach($fcFields as $fc) {
                            if(!isset($item->{$fc->name}) || $fc->is_system_field || !in_array($fc->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $fc->is_multi) {
                                continue;
                            }
                            $d["{$ctypeObj->id}_{$field->name}"] .= ",";

                            if($fc->field_type_id == "media") {
                                $d["{$ctypeObj->id}_{$field->name}"] .= $fc->name . "_name," . $fc->name . "_size," . $fc->name . "_extension," . $fc->name . "_type," . $fc->name . "_original_name";
                            } else {
                                $d["{$ctypeObj->id}_{$field->name}"] .= $fc->name;
                            }
                            
                        }
                        $d["{$ctypeObj->id}_{$field->name}"] .= ") values (" . escape_query($item->id, 6, false) . "," . escape_query($item->token, 1, true) . "," . escape_query($item->parent_id, 6, false);
                        
                        foreach($fcFields as $fc) {
                            if(!isset($item->{$fc->name}) || $fc->is_system_field || !in_array($fc->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $fc->is_multi) {
                                continue;
                            }

                            $d["{$ctypeObj->id}_{$field->name}"] .= ",";

                            if($fc->is_multi != true && $fc->data_source_value_column_is_text) {
                                $d["{$ctypeObj->id}_{$field->name}"] .= escape_query($item->{$fc->name}, 1, true);
                            } else if($fc->field_type_id == "relation" && $fc->data_source_value_column_is_text != true) {
                                //if(in_array($fc->data_source_table_name, $this->ctypes_with_hardcoded_ids)) {
                                    $d["{$ctypeObj->id}_{$field->name}"] .= empty($item->{$fc->name}) ? "null" : "'" . $item->{$fc->name} . "'";
                                //} else {
                                //    $query .= _strlen($item->{$fc->name . "_display"}) == 0 ? "null" : escape_query($item->{$fc->name}, 1, true);
                                //}
                            } else if ($fc->field_type_id == "media") {
                                $d["{$ctypeObj->id}_{$field->name}"] .= escape_query($item->{$fc->name . "_name"}, 1, true) . "," . escape_query($item->{$fc->name . "_size"}, 6) . "," . escape_query($item->{$fc->name . "_extension"}, 1, true) . "," . escape_query($item->{$fc->name . "_type"}, 1, true) . "," . escape_query($item->{$fc->name . "_original_name"}, 1, true);
                            } else {
                                $d["{$ctypeObj->id}_{$field->name}"] .= escape_query($item->{$fc->name}, $fc->field_type_id, true);
                            }

                        }
                        $d["{$ctypeObj->id}_{$field->name}"] .= ")\n";

                        foreach($fcFields as $fc) {
                            if($fc->field_type_id == "relation" && $fc->is_multi) {
                                //$query .= "DELETE FROM {$ctypeObj->id}_{$field->name}_{$fc->name} WHERE parent_id = (select id from {$ctypeObj->id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . ")\n";

                                foreach($item->{$fc->name} as $sub) {
                                    if($fc->data_source_value_column_is_text) {
                                        $d["{$ctypeObj->id}_{$field->name}_{$fc->name}"] .= "INSERT INTO {$ctypeObj->id}_{$field->name}_{$fc->name} (parent_id,value_id) VALUES (" . escape_query($item->id, 6, false) . ", " . escape_query($sub->name, 1, true) . ")\n";
                                    } else {
                                        $d["{$ctypeObj->id}_{$field->name}_{$fc->name}"] .= "INSERT INTO {$ctypeObj->id}_{$field->name}_{$fc->name} (parent_id,value_id) VALUES (" . escape_query($item->id, 6, false) . ", " . escape_query($sub->value, 6, false) . ")\n";
                                    }
                                }


                            } else if ($fc->field_type_id == "media" && $fc->is_multi) {
                                // $query .= "DELETE FROM {$ctypeObj->id}_{$field->name}_{$fc->name} WHERE parent_id = (select id from {$ctypeObj->id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . ")\n";

                                // foreach($item->{$fc->name} as $sub) {
                                //     $query .= "IF NOT EXISTS (SELECT * FROM {$ctypeObj->id}_{$field->name}_{$fc->name} WHERE token = " . escape_query($sub->token, 1, true) . ") BEGIN ";
                                //     $query .= "INSERT INTO {$ctypeObj->id}_{$field->name}_{$fc->name} (parent_id, name, size, extension, original_name, type, token) VALUES ((select id from {$ctypeObj->id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . "), " . escape_query($sub->name, 1, true) . ", " . escape_query($sub->size, 6) . "," . escape_query($sub->extension, 1, true) . "," . escape_query($sub->type, 1, true) . "," . escape_query($sub->token, 1, true) . ") ";
                                //     $query .= "END ELSE BEGIN ";
                                //     $query .= "UPDATE {$ctypeObj->id}_{$field->name}_{$fc->name} SET parent_id = (select id from {$ctypeObj->id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . "), name = " . escape_query($sub->name, 1, true) . ", size = " . escape_query($sub->size, 6, true) . ", extension = " . escape_query($sub->extension, 1, true) . ", original_name = " . escape_query($sub->original_name, 1, true) . ", type = " . escape_query($sub->type, 1, true) . " WHERE token = " . escape_query($sub->token, 1, true) . " ";
                                //     $query .= "END";
                                // }

                            }

                        }

                    }

                }
            }

        }

        if(empty($ctypeObj->primary_column_type) || $ctypeObj->primary_column_type == "bigint")
            $d[$ctypeObj->id] .= "SET IDENTITY_INSERT $ctypeObj->id OFF\n";

        foreach($fields as $field) {
            if($field->field_type_id == "field_collection") {
                if(empty($field->ctype_primary_column_type) || $field->ctype_primary_column_type == "bigint")
                    $d["{$ctypeObj->id}_{$field->name}"] .= "SET IDENTITY_INSERT {$ctypeObj->id}_{$field->name} OFF\n";
            }
        }

        
        foreach($d as $q) {
            $query .= $q;
        }

        return $query;
    }
}
