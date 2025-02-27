<?php

namespace App\Core\Gctypes;

use App\Core\Application;
use App\Models\NodeModel;

class DbExportDataSql {
    
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



        $query .= "SET NOCOUNT ON;\n";
        $query .= "DECLARE @parent_ctype_id bigint = null\n\n";



        $query .= $this->generate_field_types();
        $query .= $this->generate_ctypes_categories();
        $query .= $this->generate_field_type_appearances();

        $ctypes_array1 = ["ctypes", "status_list","status_workflow_templates","form_types","views","crons","users","roles","units","positions","menu","settings","widgets","dashboards","custom_url","documents","surveys","modules","db_connection_strings","documentations", "file_extension_types","crons_groups"];
        foreach($ctypes_array1 as $item) {
            $query .= $this->insertSimple($item, true);
        }
        
        $ctypes_array2 = ["field_types","crons_log_types","genders","text_align_types","filter_operators","notification_types","crons_types"];
        foreach($ctypes_array2 as $item) {
            $query .= $this->insertSimple($item, false);
        }


        $query .= $this->generate_dyn("crons_types", false);
        $query .= $this->generate_dyn("genders", false);
        $query .= $this->generate_dyn("text_align_types", false);
        $query .= $this->generate_dyn("notification_types", false);
        $query .= $this->generate_dyn("filter_operators", false);
        $query .= $this->generate_dyn("crons_log_types", false);

        $query .= $this->generate_dyn("status_list", true);
        $query .= $this->generate_dyn("roles", true);
        $query .= $this->generate_dyn("units", true);
        $query .= $this->generate_dyn("positions", true);
        $query .= $this->generate_dyn("settings", true);
        $query .= $this->generate_dyn("custom_url", true);
        $query .= $this->generate_dyn("file_extension_types", true);
        $query .= $this->generate_dyn("crons_groups", true);
        $query .= $this->generate_dyn("db_connection_strings", true);

        $query .= $this->generate_dyn("form_types", true);
        $query .= $this->generate_dyn("modules", true);
        $query .= $this->generate_dyn("ctypes", true);
        $query .= $this->generate_dyn("status_workflow_templates", true);
        $query .= $this->generate_dyn("views", true);
        $query .= $this->generate_dyn("crons", true);
        $query .= $this->generate_dyn("users", true);
        $query .= $this->generate_dyn("menu", true);
        $query .= $this->generate_dyn("dashboards", true);
        $query .= $this->generate_dyn("widgets", true);
        $query .= $this->generate_dyn("documents", true);
        $query .= $this->generate_dyn("surveys", true);
        $query .= $this->generate_dyn("documentations", true);
        
        
        return $query;
    }

    private function generate_field_types() {

        $ctype_id = "field_types";

        $this->ctypes_with_hardcoded_ids[] = $ctype_id;

        $query = "\n----------------------\n-- $ctype_id data\n----------------------\n";

        $query .= "DELETE FROM $ctype_id\n";

        $query .= "SET IDENTITY_INSERT $ctype_id ON\n";
        $ids = [];
        foreach((new NodeModel($ctype_id))->load() as $data) {
            $query .= "INSERT INTO $ctype_id (id, name, group_id, icon, description, token) values (" . $data->id . "," . escape_query($data->name, 1, true) . "," . escape_query($data->group_id, 6) . "," . escape_query($data->icon, 1, true) . "," . escape_query($data->description, 1, true) . "," . escape_query($data->token, 1, true) . ")\n";
        }
        $query .= "SET IDENTITY_INSERT $ctype_id OFF\n";

        return $query;
    }

    
    private function generate_ctypes_categories() {

        $ctype_id = "ctypes_categories";

        $this->ctypes_with_hardcoded_ids[] = $ctype_id;

        $query = "\n----------------------\n-- $ctype_id data\n----------------------\n";

        $query .= "DELETE FROM $ctype_id\n";
        $query .= "SET IDENTITY_INSERT $ctype_id ON\n";
        foreach((new NodeModel($ctype_id))->load() as $data) {
            $query .= "INSERT INTO $ctype_id (id, name, token) values (" . $data->id . "," . escape_query($data->name, 1, true) . "," . escape_query($data->token, 1, true) . ")\n";
        }
        $query .= "SET IDENTITY_INSERT $ctype_id OFF\n";

        return $query;
    }

    private function generate_field_type_appearances() {

        $ctype_id = "field_type_appearances";

        $this->ctypes_with_hardcoded_ids[] = $ctype_id;

        $query = "\n----------------------\n-- $ctype_id data\n----------------------\n";

        $query .= "DELETE FROM $ctype_id\n";
        $query .= "SET IDENTITY_INSERT $ctype_id ON\n";
        foreach((new NodeModel($ctype_id))->load() as $data) {
            $query .= "INSERT INTO $ctype_id (id, name, title, field_type_id, description, icon, sort, token) values (" . $data->id . "," . escape_query($data->name, "text", true) . "," . escape_query($data->title, 1, true) . "," . escape_query($data->field_type_id, 6) . "," . escape_query($data->description, 1, true) . "," . escape_query($data->icon, 1, true) . "," . escape_query($data->sort, 6) . "," . escape_query($data->token, "text") . ")\n";
        }
        $query .= "SET IDENTITY_INSERT $ctype_id OFF\n";

        return $query;
    }



    private function generate_dyn($ctype_id, $filter_by_is_system_object = false, $field_name = "name") {
        
        $fields = (new CtypeField)->loadByCtypeId($ctype_id);
        $query = "\n----------------------\n-- $ctype_id data\n----------------------\n";

        $names = [];
        $list = (new NodeModel($ctype_id))->DisplayNameAsTitle(true)->where($filter_by_is_system_object ? "isnull(m.is_system_object,0) = 1" : "")->load();
        foreach($list as $data) {
            
            $query .= "IF NOT EXISTS (SELECT * FROM $ctype_id WHERE $field_name = " . escape_query($data->name, 1, true) . ") BEGIN ";
            $query .= "INSERT INTO $ctype_id (token";
            foreach($fields as $field) {
                if(!isset($data->{$field->name}) || $field->is_system_field || !in_array($field->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $field->is_multi) {
                    continue;
                }

                $query .= ",";

                if($field->field_type_id == "media") {
                    $query .= $field->name . "_name," . $field->name . "_size," . $field->name . "_extension," . $field->name . "_type," . $field->name . "_original_name";
                } else {
                    $query .= $field->name;
                }


            }
            $query .= ") values (" . escape_query($data->token, 1, true);

            foreach($fields as $field) {
                if(!isset($data->{$field->name}) || $field->is_system_field || !in_array($field->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $field->is_multi) {
                    continue;
                }
                $query .= ",";

                if($field->data_source_value_column_is_text) {
                    $query .= escape_query($data->{$field->name}, 1, true);
                } else if($field->field_type_id == "relation" && $field->data_source_value_column_is_text != true) {
                    if(in_array($field->data_source_table_name, $this->ctypes_with_hardcoded_ids)) {
                        $query .= empty($data->{$field->name}) ? "null" : $data->{$field->name};
                    } else {
                        $query .= _strlen($data->{$field->name . "_display"}) == 0 ? "null" : "(SELECT id FROM $field->data_source_table_name WHERE name =" . escape_query($data->{$field->name . "_display"}, 1, true) . ")";
                    }
                } else if ($field->field_type_id == "media") {
                    $query .= escape_query($data->{$field->name . "_name"}, 1, true) . "," . escape_query($data->{$field->name . "_size"}, 6) . "," . escape_query($data->{$field->name . "_extension"}, 1, true) . "," . escape_query($data->{$field->name . "_type"}, 1, true) . "," . escape_query($data->{$field->name . "_original_name"}, 1, true);
                } else {
                    $query .= escape_query($data->{$field->name}, $field->field_type_id, true);
                }

            }
            $query .= ") END ";
        
            $query .= " ELSE IF NOT EXISTS (SELECT * FROM $ctype_id WHERE $field_name = " . escape_query($data->name, 1, true) . " and token = " . escape_query($data->token, 1, true) . ") BEGIN UPDATE $ctype_id SET token = " . escape_query($data->token, 1, true);

            foreach($fields as $field) {
                if($field->is_system_field || !in_array($field->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $field->name == "name" || $field->is_multi) {
                    continue;
                }
                $query .= ",";

                if($field->field_type_id == "media") {
                    $query .= $field->name . "_name = " . escape_query($data->{$field->name . "_name"}, 1, true) . ", " . $field->name . "_size = " . escape_query($data->{$field->name . "_size"}, 6) . ", " . $field->name . "_extension = " . escape_query($data->{$field->name . "_extension"}, 1, true) . ", " . $field->name . "_type = " . escape_query($data->{$field->name . "_type"}, 1, true) . ", " . $field->name . "_original_name = " . escape_query($data->{$field->name . "_original_name"}, 1, true);
                } else {
                        
                    $query .= $field->name . " = ";
                    
                    if($field->is_multi != true && $field->data_source_value_column_is_text) {
                        $query .= escape_query($data->{$field->name}, 1, true);
                    } else if($field->field_type_id == "relation" && $field->data_source_value_column_is_text != true) {
                        if(in_array($field->data_source_table_name, $this->ctypes_with_hardcoded_ids)) {
                            $query .= empty($data->{$field->name}) ? "null" : $data->{$field->name};
                        } else {
                            $query .= _strlen($data->{$field->name . "_display"}) == 0 ? "null" : "(SELECT id FROM $field->data_source_table_name WHERE name =" . escape_query($data->{$field->name . "_display"}, 1, true) . ")";
                        }
                    } else {
                        $query .= escape_query($data->{$field->name}, $field->field_type_id, true);
                    }
                }
            }


            $query .= " WHERE name = " . escape_query($data->name, 1, true) . " END\n";


            foreach($fields as $field) {
                if($field->field_type_id == "relation" && $field->is_multi) {
                    $query .= "DELETE FROM {$ctype_id}_{$field->name} WHERE parent_id = (select id from {$ctype_id} WHERE token = " . escape_query($data->token, 1, true) . ")\n";

                    foreach($data->{$field->name} as $sub) {
                        if($field->data_source_value_column_is_text) {
                            $query .= "INSERT INTO {$ctype_id}_{$field->name} (parent_id,value_id) VALUES ((select id from {$ctype_id} WHERE token = " . escape_query($data->token, 1, true) . "), " . escape_query($sub->name, 1, true) . ")\n";
                        } else {
                            $query .= "INSERT INTO {$ctype_id}_{$field->name} (parent_id,value_id) VALUES ((select id from {$ctype_id} WHERE token = " . escape_query($data->token, 1, true) . "), (select id from $field->data_source_table_name where name = " . escape_query($sub->name, 1, true) . "))\n";
                        }
                    }


                } else if ($field->field_type_id == "media" && $field->is_multi) {
                    $query .= "DELETE FROM {$ctype_id}_{$field->name} WHERE parent_id = (select id from {$ctype_id} WHERE token = " . escape_query($data->token, 1, true) . ")\n";

                    foreach($data->{$field->name} as $sub) {
                        $query .= "IF NOT EXISTS (SELECT * FROM {$ctype_id}_{$field->name} WHERE token = " . escape_query($sub->token, 1, true) . ") BEGIN ";
                        $query .= "INSERT INTO {$ctype_id}_{$field->name} (parent_id, name, size, extension, original_name, type, token) VALUES ((select id from {$ctype_id} WHERE token = " . escape_query($data->token, 1, true) . "), " . escape_query($sub->name, 1, true) . ", " . escape_query($sub->size, 6) . "," . escape_query($sub->extension, 1, true) . "," . escape_query($sub->type, 1, true) . "," . escape_query($sub->token, 1, true) . ") ";
                        $query .= "END ELSE BEGIN ";
                        $query .= "UPDATE {$ctype_id}_{$field->name} SET parent_id = (select id from {$ctype_id} WHERE token = " . escape_query($sub->token, 1, true) . "), name = " . escape_query($sub->name, 1, true) . ", size = " . escape_query($sub->size, 6, true) . ", extension = " . escape_query($sub->extension, 1, true) . ", original_name = " . escape_query($sub->original_name, 1, true) . ", type = " . escape_query($sub->type, 1, true) . " WHERE token = " . escape_query($sub->token, 1, true) . " ";
                        $query .= "END";
                    }

                }
            }

            $has_fc = false;
            foreach($fields as $field) {
                if($field->field_type_id == "field_collection") {
                    $has_fc = true;
                }
            }
            
            if($has_fc) {
                $query .= "set @parent_ctype_id = (select id from $ctype_id where name = " . escape_query($data->name, 1, true) . ")\n";
            }
            
            foreach($fields as $field) {
                if($field->field_type_id == "field_collection") {
                    
                    $fcFields = $field->getFields();

                    $tokens = [];
                    foreach($data->{$field->name} as $item) {
                        $tokens[] = $item->token;
                    }

                    
                    $query .= "DELETE FROM {$ctype_id}_{$field->name} WHERE @parent_ctype_id is not null and parent_id = @parent_ctype_id " . (sizeof($tokens) > 0 ? " and (token is null or token not in ('" . implode("','", $tokens) . "'))" : "") . "\n";

                    foreach($data->{$field->name} as $item) {
                        $query .= "IF NOT EXISTS (SELECT * FROM {$ctype_id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . ") BEGIN ";
                        $query .= "INSERT INTO {$ctype_id}_{$field->name} (token, parent_id";
                        foreach($fcFields as $fc) {
                            if(!isset($item->{$fc->name}) || $fc->is_system_field || !in_array($fc->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $fc->is_multi) {
                                continue;
                            }
                            $query .= ",";

                            if($fc->field_type_id == "media") {
                                $query .= $fc->name . "_name," . $fc->name . "_size," . $fc->name . "_extension," . $fc->name . "_type," . $fc->name . "_original_name";
                            } else {
                                $query .= $fc->name;
                            }
                            
                        }
                        $query .= ") values (" . escape_query($item->token, 1, true) . ",@parent_ctype_id";
                        
                        foreach($fcFields as $fc) {
                            if(!isset($item->{$fc->name}) || $fc->is_system_field || !in_array($fc->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $fc->is_multi) {
                                continue;
                            }

                            $query .= ",";

                            if($fc->is_multi != true && $fc->data_source_value_column_is_text) {
                                $query .= escape_query($item->{$fc->name}, 1, true);
                            } else if($fc->field_type_id == "relation" && $fc->data_source_value_column_is_text != true) {
                                if(in_array($fc->data_source_table_name, $this->ctypes_with_hardcoded_ids)) {
                                    $query .= empty($item->{$fc->name}) ? "null" : $item->{$fc->name};
                                } else {
                                    $query .= _strlen($item->{$fc->name . "_display"}) == 0 ? "null" : "(SELECT id FROM $fc->data_source_table_name WHERE name =" . escape_query($item->{$fc->name . "_display"}, 1, true) . ")";
                                }
                            } else if ($fc->field_type_id == "media") {
                                $query .= escape_query($item->{$fc->name . "_name"}, 1, true) . "," . escape_query($item->{$fc->name . "_size"}, 6) . "," . escape_query($item->{$fc->name . "_extension"}, 1, true) . "," . escape_query($item->{$fc->name . "_type"}, 1, true) . "," . escape_query($item->{$fc->name . "_original_name"}, 1, true);
                            } else {
                                $query .= escape_query($item->{$fc->name}, $fc->field_type_id, true);
                            }

                        }
                        $query .= ") END ";

                        $query .= " ELSE BEGIN UPDATE {$ctype_id}_{$field->name} SET token = " . escape_query($item->token, 1, true) . ",parent_id = @parent_ctype_id";
        
                        foreach($fcFields as $fc) {
                            if($fc->is_system_field || !in_array($fc->field_type_id,["text","relation","date","media","number","decimal","boolean"]) || $fc->name == "name" || $fc->is_multi) {
                                continue;
                            }

                            $query .= ",";

                            if($field->field_type_id == "media") {
                                $query .= $fc->name . "_name = " . escape_query($item->{$fc->name . "_name"}, 1, true) . ", " . $fc->name . "_size = " . escape_query($item->{$fc->name . "_size"}, 6) . ", " . $fc->name . "_extension = " . escape_query($item->{$fc->name . "_extension"}, 1, true) . ", " . $fc->name . "_type = " . escape_query($item->{$fc->name . "_type"}, 1, true) . ", " . $fc->name . "_original_name = " . escape_query($item->{$fc->name . "_original_name"}, 1, true);
                            } else {
                                $query .= $fc->name . " = ";
                                
                                if($fc->is_multi != true && $fc->data_source_value_column_is_text) {
                                    $query .= escape_query($item->{$fc->name}, 1, true);
                                } else if($fc->field_type_id == "relation" && $fc->data_source_value_column_is_text != true) {
                                    if(in_array($fc->data_source_table_name, $this->ctypes_with_hardcoded_ids)) {
                                        $query .= empty($item->{$fc->name}) ? "null" : $item->{$fc->name};
                                    } else {
                                        $query .= _strlen($item->{$fc->name . "_display"}) == 0 ? "null" : "(SELECT id FROM $fc->data_source_table_name WHERE name =" . escape_query($item->{$fc->name . "_display"}, 1, true) . ")";
                                    }
                                } else {
                                    $query .= escape_query($item->{$fc->name}, $fc->field_type_id, true);
                                }
                            }
                        }
        
        
                        $query .= " WHERE token = " . escape_query($item->token, 1, true) . " END\n";
        

                        foreach($fcFields as $fc) {
                            if($fc->field_type_id == "relation" && $fc->is_multi) {
                                $query .= "DELETE FROM {$ctype_id}_{$field->name}_{$fc->name} WHERE parent_id = (select id from {$ctype_id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . ")\n";

                                foreach($item->{$fc->name} as $sub) {
                                    if($fc->data_source_value_column_is_text) {
                                        $query .= "INSERT INTO {$ctype_id}_{$field->name}_{$fc->name} (parent_id,value_id) VALUES ((select id from {$ctype_id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . "), " . escape_query($sub->name, 1, true) . ")\n";
                                    } else {
                                        $query .= "INSERT INTO {$ctype_id}_{$field->name}_{$fc->name} (parent_id,value_id) VALUES ((select id from {$ctype_id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . "), (select id from $fc->data_source_table_name where name = " . escape_query($sub->name, 1, true) . "))\n";
                                    }
                                }


                            } else if ($fc->field_type_id == "media" && $fc->is_multi) {
                                $query .= "DELETE FROM {$ctype_id}_{$field->name}_{$fc->name} WHERE parent_id = (select id from {$ctype_id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . ")\n";

                                foreach($item->{$fc->name} as $sub) {
                                    $query .= "IF NOT EXISTS (SELECT * FROM {$ctype_id}_{$field->name}_{$fc->name} WHERE token = " . escape_query($sub->token, 1, true) . ") BEGIN ";
                                    $query .= "INSERT INTO {$ctype_id}_{$field->name}_{$fc->name} (parent_id, name, size, extension, original_name, type, token) VALUES ((select id from {$ctype_id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . "), " . escape_query($sub->name, 1, true) . ", " . escape_query($sub->size, 6) . "," . escape_query($sub->extension, 1, true) . "," . escape_query($sub->type, 1, true) . "," . escape_query($sub->token, 1, true) . ") ";
                                    $query .= "END ELSE BEGIN ";
                                    $query .= "UPDATE {$ctype_id}_{$field->name}_{$fc->name} SET parent_id = (select id from {$ctype_id}_{$field->name} WHERE token = " . escape_query($item->token, 1, true) . "), name = " . escape_query($sub->name, 1, true) . ", size = " . escape_query($sub->size, 6, true) . ", extension = " . escape_query($sub->extension, 1, true) . ", original_name = " . escape_query($sub->original_name, 1, true) . ", type = " . escape_query($sub->type, 1, true) . " WHERE token = " . escape_query($sub->token, 1, true) . " ";
                                    $query .= "END";
                                }

                            }
                        }

                    }

                }
            }

            

            
            if($filter_by_is_system_object != true || $data->is_system_object) {
                $names[] = $data->{$field_name};
            }
        }

        if(sizeof($names) > 0) {
            $query .= "DELETE FROM $ctype_id WHERE name not in (";
            $i = 0;
            foreach($names as $itm) {
                if($i > 0) {
                    $query .= ",";
                }
                $query .= escape_query($itm, 1, true);

                $i++;
            }

            $query .= ") " . ($filter_by_is_system_object ? " and isnull(is_system_object,0) = 1" : "") . "\n";
        } else if($filter_by_is_system_object) {
            $query .= " DELETE FROM $ctype_id WHERE isnull(is_system_object,0) = 1\n";
        }


        return $query;
    }
    private function insertSimple($ctype_id, $filter_by_is_system_object = false, $field_name = "id") {
        
        $query = "";

        $list = (new NodeModel($ctype_id))
                    // ->fields(["id", "name"])
                    ->where(($filter_by_is_system_object && !$this->include_ext) ? "isnull(m.is_system_object,0) = 1" : "")
                    ->load();
        
        foreach($list as $data) {
            $query .= "IF NOT EXISTS (SELECT * FROM $ctype_id WHERE $field_name = " . escape_query($data->{$field_name}, 1, true) . ") BEGIN INSERT INTO $ctype_id ($field_name) values (" . escape_query($data->{$field_name}, 1, true) . ") END\n";
        }

        return $query;
    }

}