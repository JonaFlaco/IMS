<?php

/*
 * This is base class for syncing ODK Form, contains some helper classes
 */

namespace App\Core\Crons;

use App\Core\Application;
use App\Models\NodeModel;
use Exception;

class ODK {


    static public function getStatistics($cron) {

        if($cron->type_id != "sync_odk_form")
            return;
            
        if(_strlen($cron->db_connection_string_id) == 0)
            return null;

        try {

            $connection_string_obj = Application::getInstance()->coreModel->nodeModel("db_connection_strings")
                ->id($cron->db_connection_string_id)
                ->loadFirstOrDefault();
            
            if(!isset($connection_string_obj)){
                return null;
            }
            
            $dbObj = new \App\Core\DAL\MySqlDatabase($connection_string_obj->name,$connection_string_obj->host,$connection_string_obj->db_name,$connection_string_obj->username,$connection_string_obj->password,$connection_string_obj->port);    
            
            $tableName = $cron->is_custom && _strlen($cron->odk_form_main_table_name) > 0 ? $cron->odk_form_main_table_name : "{$cron->id}_core";

            $extraJoin = null;
            if($cron->is_custom && _strlen($cron->odk_form_main_table_name) > 0) {
                $extraJoin = "left join {$cron->id}_core main ON main._URI = $tableName._TOP_LEVEL_AURI ";
            }
            $qry = "select 
                (SELECT count(*) from {$tableName} $extraJoin WHERE _IS_COMPLETE = 1 AND {$tableName}._URI NOT in (select _URI from odk_synced_records where source_table = N'{$cron->id}_core')) as pending_records,
                (SELECT count(*) from {$tableName} $extraJoin WHERE _IS_COMPLETE = 0) as incomplete_records, 
                (SELECT count(*) from {$tableName}) as all_records,
                (SELECT _CREATION_DATE from {$tableName} ORDER BY _CREATION_DATE DESC LIMIT 1) as last_submission_date,
                (SELECT sum(ROUND(DATA_LENGTH + INDEX_LENGTH) / 1024) FROM information_schema.TABLES WHERE TABLE_NAME like '{$cron->id}_%' AND TABLE_SCHEMA = '$connection_string_obj->db_name') as size_kb,
                (select CREATE_TIME FROM information_schema.TABLES WHERE TABLE_NAME = '{$cron->id}_core' AND TABLE_SCHEMA = '$connection_string_obj->db_name') as created_date,
                (SELECT r.IS_SUBMISSION_ALLOWED FROM _form_info_submission_association r WHERE r.SUBMISSION_FORM_ID = '{$cron->id}') AS submission_allowed,
                (SELECT fi.IS_DOWNLOAD_ALLOWED FROM _form_info f left join _form_info_fileset fi ON fi._PARENT_AURI = f._URI WHERE f.FORM_ID = '{$cron->id}') AS download_allowed,
                (SELECT f._CREATOR_URI_USER FROM _form_info f WHERE f.FORM_ID = '{$cron->id}') AS created_user
            ";

            $dbObj->query($qry);
            
            return $dbObj->resultSingle();  
        } catch(Exception $exc) {
            return (object)[
                "pending_records" => null,
                "incomplete_records" => null,
                "all_records" => null,
                "last_submission_date" => null,
                "size_kb" => null,
                "created_date" => null,
                "submission_allowed" => null,
                "download_allowed" => null,
                "created_user" => null,
                "error_message" => $exc->getMessage()
            ];
        }

    }

    static public function getIncompleteForms($cron) {

        if(_strlen($cron->db_connection_string_id) == 0)
            return null;

        $connection_string_obj = Application::getInstance()->coreModel->nodeModel("db_connection_strings")
            ->id($cron->db_connection_string_id)
            ->loadFirstOrDefault();
        
        if(!isset($connection_string_obj)){
            return null;
        }
        
        $dbObj = new \App\Core\DAL\MySqlDatabase($connection_string_obj->name,$connection_string_obj->host,$connection_string_obj->db_name,$connection_string_obj->username,$connection_string_obj->password,$connection_string_obj->port);    
        
        $tableName = $cron->is_custom && _strlen($cron->odk_form_main_table_name) > 0 ? $cron->odk_form_main_table_name : "{$cron->id}_core";
        
        $extraJoin = null;
        if($cron->is_custom && _strlen($cron->odk_form_main_table_name) > 0) {
            $extraJoin = "left join {$tableName} sub ON {$cron->id}_core._URI = sub._TOP_LEVEL_AURI ";
        }
        $qry = "
        SELECT 
            {$cron->id}_core._URI as id, 
            {$cron->id}_core._CREATOR_URI_USER as created_user_id,
            {$cron->id}_core._CREATION_DATE as created_date
        from {$cron->id}_core 
        $extraJoin
        WHERE _is_COMPLETE = 0";

        $dbObj->query($qry);
        
        return $dbObj->resultSet();  

    }

    static public function getOrphanForms() {

        $odkDbs = NodeModel::new("db_connection_strings")
            ->where("m.category='odk'")
            ->where("isnull(m.is_active,0) = 1")
            ->load();

        $result = [];

        $odkFormList = NodeModel::new("crons")
        ->fields(["id", "name", "status_id", "title", "type_id"])
        ->load();
        
        foreach($odkDbs as $odkDb) {
            
            $dbObj = new \App\Core\DAL\MySqlDatabase($odkDb->name,$odkDb->host,$odkDb->db_name,$odkDb->username,$odkDb->password,$odkDb->port);    
            
            $qry = "
            SELECT
                f.form_id as name,
                fi.FORM_NAME as title,
                r.IS_SUBMISSION_ALLOWED allow_submission,
                fi.IS_DOWNLOAD_ALLOWED as allow_download,
                f._CREATOR_URI_USER as created_user,
                f._CREATION_DATE as created_date
            FROM _form_info f
            LEFT JOIN _form_info_submission_association r ON r.SUBMISSION_FORM_ID = f.FORM_ID
            LEFT JOIN _form_info_fileset fi ON fi._PARENT_AURI = f._URI
            ORDER BY f.form_id
            ";
            
            $dbObj->query($qry);
        
            foreach($dbObj->resultSet() as $res) {

                $obj = get_object_in_array_of_objects($odkFormList, "id", $res->name);
                
                $res->server = $odkDb->name;

                $status = "";
                if($obj == null)
                    $status = "Cron not found";
                elseif($obj->status_id == 82)
                    continue;
                else
                    $status = "Cron disabled";

                $res->status = $status;
                $result[] = $res;

            }

        }

        return $result;

    }

}

