<?php

namespace App\Helpers;

use App\Core\Application;

class DbHelper {
    
    public static function getMySQLDbObj($id){
        
        $connection_string_obj = Application::getInstance()->coreModel->nodeModel("db_connection_strings")
            ->id($id)
            ->fields(["name","host","db_name","username","password","","port"])
            ->loadFirstOrDefault();

        if(!isset($connection_string_obj)){
            return null;
        }
            
        $db = new \App\Core\DAL\MySqlDatabase(
                $connection_string_obj->name,
                $connection_string_obj->host,
                $connection_string_obj->db_name,
                $connection_string_obj->username,
                $connection_string_obj->password,
                $connection_string_obj->port);
        return $db;
        
    }

    public static function getMSSQLDbObj($id){

        $connection_string_obj = Application::getInstance()->coreModel->nodeModel("db_connection_strings")
            ->id($id)
            ->fields(["name","host","db_name","username","password","","port"])
            ->loadFirstOrDefault();

        if(!isset($connection_string_obj)){
            return null;
        }
            
        $db = new \App\Core\DAL\MSSQLDatabase(
                $connection_string_obj->name,
                $connection_string_obj->host,
                $connection_string_obj->db_name,
                $connection_string_obj->username,
                $connection_string_obj->password,
                $connection_string_obj->port);
        return $db;
        
    }
}