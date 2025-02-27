<?php

namespace App\Models\Sub;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;
use App\Exceptions\CtypeValidationException;
use App\Models\CTypeLog;
use \PDO;

class SubNodeSave
{

    static private $coreModel;

    public static function main($data, $settings = array())
    {
        self::$coreModel = Application::getInstance()->coreModel;

        $settings = array_change_key_case($settings, CASE_LOWER);

        if (!isset($data)) {
            throw new \App\Exceptions\CriticalException("Data object provided to save is empty");
        }

        $ctype_id = null;
        if (isset($data) && !empty($data->sett_ctype_id)) {

            $ctype_id = $data->sett_ctype_id;
        } else {

            throw new \App\Exceptions\MissingDataFromRequesterException("Content-Type name is empty");
        }

        $justification = null;
        if ($settings != null && isset($settings['justification'])) {
            $justification = $settings['justification'];
        }

        $token = null;
        if ($settings != null && isset($settings['token'])) {
            $token = $settings['token'];
        }

        $json = (new \App\Models\Sub\NodeJsonToOjbect())->main($ctype_id, $data, $justification, $token);

        $data = json_decode($json);

        return self::legacy($data, $settings);
    }


    public static function legacy($data, $settings = array())
    {
        /*
                $user_id = null, 
                $dont_add_log = false, 
                $dont_validate = false,
                $dont_clean_input = false, 
                $ignore_sql_begin_trans = false,
                $ignore_pre_save = false,
                $ignore_post_save = false
            */

        $settings = array_change_key_case($settings, CASE_LOWER);

        self::$coreModel = new \App\Models\CoreModel;


        if (!isset($data)) {
            throw new \App\Exceptions\CriticalException("Data object provided to save is empty");
        }

        $user_id = null;
        if ($settings != null && isset($settings['user_id'])) {
            $user_id = $settings['user_id'];
        }

        $dont_add_log = null;
        if ($settings != null && isset($settings['dont_add_log'])) {
            $dont_add_log = $settings['dont_add_log'];
        }

        $dont_validate = null;
        if ($settings != null && isset($settings['dont_validate'])) {
            $dont_validate = $settings['dont_validate'];
        }

        $dont_clean_input = null;
        if ($settings != null && isset($settings['dont_clean_input'])) {
            $dont_clean_input = $settings['dont_clean_input'];
        }

        $ignore_sql_begin_trans = null;
        if ($settings != null && isset($settings['ignore_sql_begin_trans'])) {
            $ignore_sql_begin_trans = $settings['ignore_sql_begin_trans'];
        }

        $ignore_pre_save = null;
        if ($settings != null && isset($settings['ignore_pre_save'])) {
            $ignore_pre_save = $settings['ignore_pre_save'];
        }

        $ignore_post_save = null;
        if ($settings != null && isset($settings['ignore_post_save'])) {
            $ignore_post_save = $settings['ignore_post_save'];
        }

        if ($user_id == null && Application::getInstance()->user->getId() === null) {

            $user_id = Application::getInstance()->user->getGuestUserId();
        }

        $id = null;
        
        if (isset($data->tables[0]->data->id) && !empty($data->tables[0]->data->id)) {
            $id = $data->tables[0]->data->id;
        }

        $sett_is_update = isset($id);
        if (isset($data->sett_is_update)) {
            $sett_is_update = $data->sett_is_update;
        }

        if(isset($sett_is_update))
            $isUpdate = $sett_is_update;
        else if(isset($id)) {
            $isUpdate = true;
        } else {
            $isUpdate = false;
        }

        $parent_id = "";
        $return_value = "";


        $ctypeObj = (new Ctype)->load($data->tables[0]->id);

        $justification = "";
        if (isset($data->{'justification'}) && !empty($data->{'justification'})) {
            $justification = $data->justification;
        }

        if ($dont_add_log != true && $isUpdate == true && $ctypeObj->justification_for_edit_is_required == true && _strlen(_trim($data->justification)) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Justification is required, but not provided");
        }

        if (_strlen($justification) > 500) {
            throw new \App\Exceptions\CriticalException("Justification is too long");
        }

        if ($isUpdate == true) {
            $error = self::$coreModel->RecordExistsOrTokenChanged($ctypeObj, $id, (isset($data->token) ? $data->token : null));
            if (!empty($error)) {
                throw new \App\Exceptions\RecordNotExistOrTokenChangedException($error);
            }
        }

        //filter user input
        if ($dont_clean_input != true) {
            //$data = filter_data_before_save($data);
        }


        foreach ($data->tables as $tbl) {
            if ($tbl->type == "main_table") {

                if ($ctypeObj != null && $ignore_pre_save != true) {

                    //Run app base trigger
                    $classToRun = '\App\Triggers\Base\BeforeSave';
                    if (class_exists($classToRun)) {
                        $classObj = new $classToRun();

                        if (method_exists($classObj, "index")) {

                            $classObj->ctypeObj = $ctypeObj;

                            $classObj->index($data, $isUpdate);
                        }
                    }

                    //Run ext base trigger
                    // if ($ctypeObj->is_system_object != true) {
                    $classToRun = '\Ext\Triggers\Base\BeforeSave';
                    if (class_exists($classToRun)) {
                        $classObj = new $classToRun();

                        if (method_exists($classObj, "index")) {

                            $classObj->ctypeObj = $ctypeObj;

                            $classObj->index($data, $isUpdate);
                        }
                    }
                    // }

                    //Run ctype custom trigger
                    $className = toPascalCase($ctypeObj->id);

                    if($ctypeObj->is_system_object) {
                        $classToRun = sprintf('\App\Triggers\%s\BeforeSave', $className);
                        if (class_exists($classToRun)) {

                            $classObj = new $classToRun();

                            if (method_exists($classObj, "index")) {

                                $classObj->ctypeObj = $ctypeObj;

                                $classObj->index($data, $isUpdate);
                            }
                        }
                    }

                    $classToRun = sprintf('\Ext\Triggers\%s\BeforeSave', $className);
                    if (class_exists($classToRun)) {

                        $classObj = new $classToRun();

                        if (method_exists($classObj, "index")) {

                            $classObj->ctypeObj = $ctypeObj;

                            $classObj->index($data, $isUpdate);
                        }
                    }

                }
            }
        }

        if ($dont_validate != true) {

            $validation_result = gimport_validate($ctypeObj, $data);

            if (!empty($validation_result)) {
                throw new CtypeValidationException($ctypeObj->id . ":" . $validation_result);
            }

            $validation_fn_name = "gimport_validate_$ctypeObj->id";
            $validation_result = "";
            if (function_exists($validation_fn_name)) {
                $validation_result = $validation_fn_name($ctypeObj, $data);
                if (!empty($validation_result)) {
                    throw new CtypeValidationException($ctypeObj->id . ":" . $validation_result);
                }
            }
        }

        if ($ignore_sql_begin_trans != true) {
            self::$coreModel->db->beginTransaction();
        }

        foreach ($data->tables as $tbl) {
            
            $tbl->primary_column_type = $ctypeObj->primary_column_type;

            if ($tbl->type == "main_table") {

                $parent_id = null;
                if ($ctypeObj->is_field_collection && property_exists($tbl->data, "parent_id")) {
                    $parent_id = $tbl->data->parent_id;
                }

                if ($isUpdate ) {
                    $parent_id =  self::updateAction($tbl, $user_id, $parent_id);
                    
                    if (!isset($return_value) || _strlen($return_value) == 0)
                        $return_value = $parent_id;
                } else {
                    $parent_id =  self::saveAction($tbl, $parent_id, $isUpdate, $user_id, $justification);
                    
                    $return_value = $parent_id;
                }
            } else {
                self::saveAction($tbl, $parent_id, true, $user_id, $justification);
            }
        }

        if ($ignore_sql_begin_trans != true) {
            self::$coreModel->db->commit();
        }

        if (!$dont_add_log) {
            
            (new CTypeLog($ctypeObj->id))
                ->setContentId($return_value)
                ->setUserId((!isset($user_id) ? Application::getInstance()->user->getId() : $user_id))
                ->setJustification($justification)
                ->setTitle("Record is " . ($isUpdate ? "updated" : "added"))
                ->setGroupNam(($isUpdate ? "edit" : "add"))
                ->save();
        }

        if ($ctypeObj != null && $ignore_post_save != true) {

            //Run app base trigger
            $classToRun = '\App\Triggers\Base\AfterSave';
            if (class_exists($classToRun)) {
                $classObj = new $classToRun();

                if (method_exists($classObj, "index")) {

                    $classObj->ctypeObj = $ctypeObj;

                    $classObj->index(intval($return_value), $data, $isUpdate);
                }
            }

            //Run ext base trigger
            // if ($ctypeObj->is_system_object != true) {
            $classToRun = '\Ext\Triggers\Base\AfterSave';
            if (class_exists($classToRun)) {
                $classObj = new $classToRun();

                if (method_exists($classObj, "index")) {

                    $classObj->ctypeObj = $ctypeObj;

                    $classObj->index(intval($return_value), $data, $isUpdate);
                }
            }
            
            $className = toPascalCase($ctypeObj->id);

            if($ctypeObj->is_system_object) {
                $classToRun = sprintf('\App\Triggers\%s\AfterSave', $className);
                if (class_exists($classToRun)) {

                    $classObj = new $classToRun();

                    if (method_exists($classObj, "index")) {

                        $classObj->ctypeObj = $ctypeObj;

                        $classObj->index($return_value, $data, $isUpdate);
                    }
                }
            }

            $classToRun = sprintf('\Ext\Triggers\%s\AfterSave', $className);
            if (class_exists($classToRun)) {

                $classObj = new $classToRun();

                if (method_exists($classObj, "index")) {

                    $classObj->ctypeObj = $ctypeObj;

                    $classObj->index($return_value, $data, $isUpdate);
                }
            }

        }

        
        return $return_value;
    }


    private static function SaveAction($tbl, $parent_id = "", $isUpdate = false, $user_id = null, $justification = null)
    {

        if ($tbl->type == "file") {

            if ($isUpdate) {
                //Delete old Data
                self::$coreModel->db->query("DELETE FROM $tbl->id WHERE parent_id = :parent_id");
                self::$coreModel->db->bind(":parent_id", $parent_id);
                self::$coreModel->db->execute();
            }

            foreach ($tbl->data as $file) {
                self::$coreModel->db->query("INSERT INTO " . $tbl->id . " (name, size, type, original_name, extension, parent_id) VALUES(:name, :size, :type, :original_name, :extension, :parent_id )");
                // Bind values
                self::$coreModel->db->bind(":parent_id", $parent_id);
                self::$coreModel->db->bind(":size", $file->size);
                self::$coreModel->db->bind(":type", $file->type);
                self::$coreModel->db->bind(":name", $file->name);
                self::$coreModel->db->bind(":extension", $file->extension);
                self::$coreModel->db->bind(":original_name", $file->original_name);

                //Execute
                if (!self::$coreModel->db->execute()) {

                    return false;
                }
            }
        } else if ($tbl->type == "subtable") {

            if ($isUpdate) {
                //Delete old Data
                self::$coreModel->db->query("DELETE FROM $tbl->id WHERE parent_id = :parent_id");
                self::$coreModel->db->bind(":parent_id", $parent_id);
                self::$coreModel->db->execute();
            }

            if (isset($tbl->data)) {

                foreach ($tbl->data as $itm) {
                    $clean_value = _str_replace("'", "", $itm);
                    foreach (_explode(",", $clean_value) as $itm_clean) {

                        if (isset($itm_clean) && _strlen($itm_clean) > 0) {

                            self::$coreModel->db->query("INSERT INTO " . $tbl->id . " (parent_id, value_id) VALUES(:parent_id, :value_id)");
                            // Bind values
                            self::$coreModel->db->bind(":parent_id", $parent_id);
                            self::$coreModel->db->bind(":value_id", $itm_clean);

                            //Execute
                            if (!self::$coreModel->db->execute()) {

                                return false;
                            }
                        }
                    }
                }
            }
        } else if ($tbl->type == "field_collection") {

            $fc_id = "";

            $fc_ids = "";

            foreach ($tbl->data->data->tables as $tbl2) {
                if ($tbl2->type == "main_table") {
                    
                    // if( _strlen($tbl2->data->id) != 36)
                    //     $tbl2->data->id = null;

                    if (isset($tbl2->data->id) && _strlen($tbl2->data->id) > 0) {
                        if (isset($fc_ids) && _strlen($fc_ids) > 0)
                            $fc_ids .= ",";
                        $fc_ids .= "'" . $tbl2->data->id . "'";
                    }
                }
            }

            
            $fc_ids_in_db  = null;
            
            
            self::$coreModel->db->query("DELETE FROM " . $tbl->id . " WHERE parent_id = :parent_id " . (isset($fc_ids) && _strlen($fc_ids) > 0 ? " and id not in ($fc_ids)" : ""));
            self::$coreModel->db->bind(":parent_id", $parent_id);
            self::$coreModel->db->execute();

            self::$coreModel->db->query("SELECT id FROM " . $tbl->id . " WHERE parent_id = :parent_id");
            self::$coreModel->db->bind(":parent_id", $parent_id);
            $fc_ids_in_db = self::$coreModel->db->resultSet();

            foreach ($tbl->data->data->tables as $tbl2) {

                $tbl2->primary_column_type = $tbl->primary_column_type;

                if ($tbl2->type == "main_table") {

                    $found = false;
                    if (isset($fc_ids_in_db) && $fc_ids_in_db != array()) {
                        foreach ($fc_ids_in_db as $idInDb) {
                            if (isset($tbl2->data->id) && _strtolower($idInDb->id) == _strtolower($tbl2->data->id))
                                $found = true;
                        }
                    }

                    if ($found == true) {
                        $fc_id = self::updateAction($tbl2, $user_id, $parent_id);
                    } else {
                        $fc_id = self::SaveAction($tbl2, $parent_id, $isUpdate, $user_id, $justification);
                    }
                } else if ($tbl2->type == "subtable") {
                    self::SaveAction($tbl2, $fc_id, $isUpdate, $user_id, $justification);
                } else if ($tbl2->type == "file") {
                    self::SaveAction($tbl2, $fc_id, $isUpdate, $user_id, $justification);
                }
            }
        } else {

            $fields = (new CtypeField)->loadByCtypeId($tbl->id);

            foreach ($fields as $field) {

                if (($field->field_type_id == "relation" && $field->is_multi == true) || $field->field_type_id == "button" || $field->field_type_id == "note" || $field->field_type_id == "component" || in_array($field->name, Application::getInstance()->globalVar->get('ignore_fields_on_insert'))) // || $field->is_hidden == true)
                    continue;

                foreach ($tbl->data as $key => $fld) {

                    if ($key == $field->name && $key == "id" && empty($fld)) {
                        //ignore Id field if empty on insert
                        continue;
                    } else if ($field->field_type_id == "media" && $field->is_multi != true) {

                        if ($key == $field->name . "_name") {
                            array_push($fields, (object)array("found" => true, "name" => $field->name . "_name", "value" => $fld, "field_type_id" => "text"));
                        }

                        if ($key == $field->name . "_type") {
                            array_push($fields, (object)array("found" => true, "name" => $field->name . "_type", "value" => $fld, "field_type_id" => "text"));
                        }

                        if ($key == $field->name . "_original_name") {
                            array_push($fields, (object)array("found" => true, "name" => $field->name . "_original_name", "value" => $fld, "field_type_id" => "text"));
                        }

                        if ($key == $field->name . "_extension") {
                            array_push($fields, (object)array("found" => true, "name" => $field->name . "_extension", "value" => $fld, "field_type_id" => "text"));
                        }

                        if ($key == $field->name . "_size") {
                            array_push($fields, (object)array("found" => true, "name" => $field->name . "_size", "value" => $fld, "field_type_id" => "number"));
                        }
                    } else if ($key == $field->name) {

                        $field->found = true;

                        if ($field->allow_basic_html_tags == true) {
                            _strip_tags($fld, BASIC_HTML_TAGS);
                        } else {
                            //$fld = filter_var($fld, FILTER_SANITIZE_STRIPPED);
                        }

                        // $fld = _str_replace("[RQTE]","'",$fld);
                        // $fld = _str_replace("[RDQTE]",'"',$fld);
                        // $fld = _str_replace("[NL]","\N",$fld);
                        $field->value = $fld;
                    }
                }
            }

            $query_1 = "";
            $query_2 = "";

            foreach ($fields as $field) {


                if ((isset($field->found) && $field->found) || $field->name == "parent_id") {

                    if ($field->field_type_id != "field_collection" && ($field->field_type_id != "relation" || $field->is_multi != true)) {

                        if (_strlen($query_1) > 0)
                            $query_1 .= ",";

                        if (_strlen($query_2) > 0)
                            $query_2 .= ",";

                        $query_1 .= $field->name;
                        if ($field->field_type_id == "date") {
                            $query_2 .= "convert(datetime,:$field->name,103)";
                        } else {
                            $query_2 .= ":" . $field->name;
                        }
                    }
                } else {
                    if (isset($field->default_value) && _strlen($field->default_value) > 0) {

                        if ($field->default_value == "[TODAY]") {
                            $field->default_value = _str_replace("[TODAY]", date('d-m-Y'), $field->default_value);
                        }

                        if (_strlen($query_1) > 0)
                            $query_1 .= ",";

                        if (_strlen($query_2) > 0)
                            $query_2 .= ",";

                        $query_1 .= $field->name;

                        if ($field->field_type_id == "date") {
                            $query_2 .= "convert(datetime,:$field->name,103)";
                        } else {
                            $query_2 .= ":" . $field->name;
                        }
                    }
                }
            }

            if ($parent_id == null) {

                $query_1 .= (_strlen($query_1) > 0 ? "," : "") . "created_user_id";
                $query_2 .= (_strlen($query_2) > 0 ? "," : "") . (!isset($user_id) ? (Application::getInstance()->user->getId() ?? "null") : $user_id);
            }

            $qry = "INSERT INTO " . $tbl->id . " ($query_1) VALUES ($query_2)";

            // if($tbl->primary_column_type == "uniqueidentifier") {
                $qry = "SET NOCOUNT ON; DECLARE @tmp TABLE ( id sql_variant ) \n";
                $qry .= "INSERT INTO " . $tbl->id . " ($query_1) OUTPUT INSERTED.id INTO @tmp VALUES ($query_2) \n";
                $qry .= "SELECT * FROM @tmp \n";
            // }
            
            self::$coreModel->db->query($qry);
            
            // Bind values
            foreach ($fields as $field) {

                
                if (isset($field->found) && $field->found == true) {

                    if (isset($parent_id) && $field->name == "parent_id") {
                        self::$coreModel->db->bind(":$field->name", $parent_id);
                    } else {
                        self::$coreModel->db->bind(":$field->name", _trim($field->value), isset($field->value) && _trim($field->value) != "null" ? null : PDO::PARAM_NULL);
                    }
                } else {
                    if (isset($field->default_value) && _strlen($field->default_value) > 0) {
                        self::$coreModel->db->bind(":$field->name", _trim($field->default_value), isset($field->default_value) && _trim($field->default_value) != "null" ? null : PDO::PARAM_NULL);
                    }
                }
            }
            
            //Execute
            // if($tbl->primary_column_type == "uniqueidentifier") {
                $res = self::$coreModel->db->resultSingle();
            // } else {
            //     $res = self::$coreModel->db->execute();
            // }

            if (empty($res))
                return "ERROR";
            
            
                // if($tbl->primary_column_type == "uniqueidentifier") {
            return $res->id;
            // } else {
            //     return self::$coreModel->db->lastInsertId();
            // }
            
        }
    }


    private static function UpdateAction($tbl, $user_id, $parent_id = null)
    {

        $id = "";

        $fields = (new CtypeField)->loadByCtypeId($tbl->id);

        foreach ($fields as $field) {
            if (
                ($field->field_type_id == "relation" && $field->is_multi == true) ||
                $field->field_type_id == "button" ||
                $field->field_type_id == "note" ||
                $field->field_type_id == "component" ||
                in_array($field->name, Application::getInstance()->globalVar->get('ignore_fields_on_update'))
            )
                continue;

            if ($field->name == "token")
                continue;

            foreach ($tbl->data as $key => $fld) {

                if ($field->field_type_id == "media" && $field->is_multi != true) {

                    if ($key == $field->name . "_name") {
                        array_push($fields, (object)array("found" => true, "name" => $field->name . "_name", "value" => $fld, "field_type_id" => "text"));
                    }

                    if ($key == $field->name . "_type") {
                        array_push($fields, (object)array("found" => true, "name" => $field->name . "_type", "value" => $fld, "field_type_id" => "text"));
                    }

                    if ($key == $field->name . "_original_name") {
                        array_push($fields, (object)array("found" => true, "name" => $field->name . "_original_name", "value" => $fld, "field_type_id" => "text"));
                    }

                    if ($key == $field->name . "_extension") {
                        array_push($fields, (object)array("found" => true, "name" => $field->name . "_extension", "value" => $fld, "field_type_id" => "text"));
                    }

                    if ($key == $field->name . "_size") {
                        array_push($fields, (object)array("found" => true, "name" => $field->name . "_size", "value" => $fld, "field_type_id" => "number"));
                    }
                } else if ($key == $field->name) {
                    $field->found = true;

                    if ($field->allow_basic_html_tags == true) {
                        _strip_tags($fld, BASIC_HTML_TAGS);
                    } else {
                        //$fld = filter_var($fld, FILTER_SANITIZE_STRIPPED);
                    }

                    // $fld = _str_replace("[RQTE]","'",$fld);
                    // $fld = _str_replace("[RDQTE]",'"',$fld);
                    // $fld = _str_replace("[NL]","\n",$fld);

                    $field->value = $fld;
                }
            }
        }


        $query_1 = "";

        foreach ($fields as $field) {

            if (!isset($field->found) || !$field->found || $field->name == "id" || $field->name == "parent_id")
                continue;


            if ($field->field_type_id != "field_collection" || ($field->field_type_id == "relation" && $field->is_multi != true)) {
                if (_strlen($query_1) > 0)
                    $query_1 .= ",";

                if ($field->field_type_id == "date" && _strlen($field->value) > 0 && _strtolower($field->value) != "null") {
                    $query_1 .= " $field->name = convert(datetime,:$field->name,103) ";
                } else {

                    if (_strlen($field->value) == 0 || _strtolower($field->value) == "null")
                        $query_1 .= " $field->name = null ";
                    else
                        $query_1 .= " $field->name = :$field->name ";
                }
            }
        }

        if ($parent_id == null) {
            if (!empty($query_1)) {
                $query_1 .= ",";
            }
            $query_1 .= " updated_user_id = :updated_user_id_sys";
        } else {
            if (!empty($query_1)) {
                $query_1 .= ",";
            }
            $query_1 .= " parent_id = :parent_id";
        }


        self::$coreModel->db->query("UPDATE $tbl->id SET token = newid() " . (!empty($query_1) ? "," : "") . " $query_1 WHERE id =:id");

        if ($parent_id == null) {
            self::$coreModel->db->bind(":updated_user_id_sys", (empty($user_id) ? Application::getInstance()->user->getId() : $user_id));
        }


        //Bind values
        foreach ($fields as $field) {

            if (isset($field->found) && $field->found && _strlen($field->value) > 0 && _strtolower($field->value) != "null") {

                self::$coreModel->db->bind(":$field->name", (_trim($field->value) == "null" ? null : _trim($field->value)));

                if ($field->name == "id") {
                    $id = $field->value;
                }
            } else if ($field->name == "parent_id" && isset($parent_id) && _strlen($parent_id) > 0) {

                self::$coreModel->db->bind(":$field->name", $parent_id);
            }
        }

        //Execute
        if (!self::$coreModel->db->execute())
            return "ERROR";
        else
            return $id;
    }
}
