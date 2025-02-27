<?php

use App\Core\Application;
use App\Core\Gctypes\Ctype;

function ctypes_generate_delete_tsql_code($id){
        
        $coreModel = Application::getInstance()->coreModel;
        $ctype_obj = (new Ctype)->load($id);
        $fields = $ctype_obj->getFields();
        
    
        $return_value = "/*
";
        $return_value .= ctypes_generate_delete_tsql_code_action($ctype_obj,$coreModel, $fields);
    
        $return_value .= "
*/
";
        return $return_value;
    }

    function ctypes_generate_delete_tsql_code_action($ctype_obj, $coreModel, $fields, $is_fc = false){
        $return_value = "";
        
        $return_value .= "
-- DELETE FIELDS IN TABLE " . $ctype_obj->id . "
IF (EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'dbo' AND  TABLE_NAME = '" . $ctype_obj->id . "')) 
BEGIN
";

        foreach($fields as $key => $field){
            
            if($field->is_system_field == true || $field->field_type_id == "button" || $field->field_type_id == "note")
                continue;

            if($field->is_unique == true){
                $return_value .= "    IF((SELECT count(*) FROM sys.objects WHERE type_desc LIKE 'UNIQUE_CONSTRAINT' AND OBJECT_NAME(OBJECT_ID)='UC_" . $field->ctype_id . "_$field->name') > 0) begin" . 
        " 
        ALTER TABLE [dbo].[" . $field->ctype_id . "] DROP CONSTRAINT  UC_" . $field->ctype_id . "_$field->name " . 
        " 
    end 
    if((SELECT count(*) FROM sys.indexes WHERE name='UC_" . $field->ctype_id . "_$field->name' AND object_id = OBJECT_ID('dbo." . $field->ctype_id . "')) > 0) begin" . 
    " 
        DROP INDEX  [dbo].[" . $field->ctype_id . "].UC_" . $field->ctype_id . "_$field->name " . 
        " 
    end 
";
            }

            if(($field->field_type_id == "relation" && $field->is_multi != true) || $field->field_type_id == "text" || $field->field_type_id == "date" || $field->field_type_id == "number" || $field->field_type_id == "decimal" || $field->field_type_id == "boolean"){
                if($field->field_type_id == "relation" && $field->is_multi != true){
                    $return_value .= "    IF (EXISTS(SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'FK_" . $field->ctype_id . "_" . $field->name . "') AND type in (N'F'))) BEGIN ALTER TABLE [dbo].[" . $field->ctype_id . "] DROP CONSTRAINT FK_" . $field->ctype_id . "_" . $field->name . " END              
";
                }

            $return_value .= "    IF (COL_LENGTH('dbo." . $field->ctype_id . "', '$field->name') IS NOT NULL) BEGIN ALTER TABLE [dbo].[" . $field->ctype_id . "] DROP COLUMN $field->name END
";
            }

        }

        // Image Single
        foreach($fields as $key => $field){
            
            if($field->is_system_field == true || $field->field_type_id == "button" || $field->field_type_id == "note")
                continue;

            if($field->field_type_id == "media" && $field->is_multi != true){

            $return_value .= "    IF (COL_LENGTH('dbo." . $field->ctype_id . "', '" . $field->name . "_name') IS NOT NULL) BEGIN ALTER TABLE [dbo].[" . $field->ctype_id . "] DROP COLUMN " . $field->name . "_name END
    IF (COL_LENGTH('dbo." . $field->ctype_id . "', '" . $field->name . "_extension') IS NOT NULL) BEGIN ALTER TABLE [dbo].[" . $field->ctype_id . "] DROP COLUMN " . $field->name . "_extension END
    IF (COL_LENGTH('dbo." . $field->ctype_id . "', '" . $field->name . "_type') IS NOT NULL) BEGIN ALTER TABLE [dbo].[" . $field->ctype_id . "] DROP COLUMN " . $field->name . "_type END
    IF (COL_LENGTH('dbo." . $field->ctype_id . "', '" . $field->name . "_size') IS NOT NULL) BEGIN ALTER TABLE [dbo].[" . $field->ctype_id . "] DROP COLUMN " . $field->name . "_size END
    IF (COL_LENGTH('dbo." . $field->ctype_id . "', '" . $field->name . "_original_name') IS NOT NULL) BEGIN ALTER TABLE [dbo].[" . $field->ctype_id . "] DROP COLUMN " . $field->name . "_original_name END
";
            }

        }

        // Image and Combobox Multi
        foreach($fields as $key => $field){
            
            if($field->is_system_field == true || $field->field_type_id == "button" || $field->field_type_id == "note")
                continue;

            if(($field->field_type_id == "relation" || $field->field_type_id == "media")  && $field->is_multi == true){
                $return_value .= "    IF (EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'dbo' AND  TABLE_NAME = '" . $ctype_obj->id . "_" . $field->name . "')) BEGIN DROP TABLE " . $ctype_obj->id . "_" . $field->name . " END
";
            } 
        }
        

        //FC
        foreach($fields as $key => $field){
            
            if($field->is_system_field == true || $field->field_type_id == "button" || $field->field_type_id == "note")
                continue;

            if ($field->field_type_id == "field_collection"){
                $fields_fc = $field->getFields();
                $ctype_obj_fc = (new Ctype)->load($field->data_source_id);
            
                $return_value .= ctypes_generate_delete_tsql_code_action($ctype_obj_fc,$coreModel, $fields_fc,true);
            } 

        }

        $return_value .= "
    -- DROP TABLE " . $field->ctype_id . "
    DROP TABLE " . $field->ctype_id . "

END
";
        return $return_value;

    }















    function ctypes_generate_delete_table_tsql_code($id){
        
        $coreModel = new \App\Models\CoreModel;
        $ctype_obj = (new Ctype)->load($id);
        $fields = $ctype_obj->getFields();
        
        
        $return_value = "";

        foreach($fields as $field){
            if($field->field_type_id == "field_collection"){

                $coreModel->delete("ctypes", $field->data_source_id);
            }
        }

        $return_value .= "

        declare @sql nvarchar(max) = (
            SELECT 
                    'alter table ' + sch.name + '.' + tab1.name + ' drop constraint ' + obj.name + '; '
                FROM sys.foreign_key_columns fkc
                INNER JOIN sys.objects obj
                    ON obj.object_id = fkc.constraint_object_id
                INNER JOIN sys.tables tab1
                    ON tab1.object_id = fkc.parent_object_id
                INNER JOIN sys.schemas sch
                    ON tab1.schema_id = sch.schema_id
                INNER JOIN sys.columns col1
                    ON col1.column_id = parent_column_id AND col1.object_id = tab1.object_id
                INNER JOIN sys.tables tab2
                    ON tab2.object_id = fkc.referenced_object_id
                INNER JOIN sys.columns col2
                    ON col2.column_id = referenced_column_id AND col2.object_id = tab2.object_id
                    where
                        tab1.name ='" . $ctype_obj->id . "' or tab2.name = '" . $ctype_obj->id . "'
                for xml path('')
                
        );
            
        
        delete from ctypes_fields where data_source_id = '$ctype_obj->id';
        delete from views_relations where ctype_id = '$ctype_obj->id';
        delete from views_relations where left_ctype_id = '$ctype_obj->id';

        exec sp_executesql @sql;

        

";

        $return_value .= ctypes_generate_delete_table_tsql_code_action($ctype_obj,$coreModel, $fields);
    
        $return_value .= "

";
        return $return_value;
    }

    function ctypes_generate_delete_table_tsql_code_action($ctype_obj, $coreModel, $fields, $is_fc = false){
        $return_value = "";
        
        $return_value .= "
IF (EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'dbo' AND  TABLE_NAME = '" . $ctype_obj->id . "')) 
BEGIN
";

        // Image and Combobox Multi
        foreach($fields as $key => $field){
            
            if($field->is_system_field == true || $field->field_type_id == "button" || $field->field_type_id == "note")
                continue;

            if(($field->field_type_id == "relation" || $field->field_type_id == "media")  && $field->is_multi == true){
                $return_value .= "    IF (EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'dbo' AND  TABLE_NAME = '" . $ctype_obj->id . "_" . $field->name . "')) BEGIN DROP TABLE " . $ctype_obj->id . "_" . $field->name . " END
";
            } 
        }
        

        //FC
        foreach($fields as $key => $field){
            
            if($field->is_system_field == true || $field->field_type_id == "button" || $field->field_type_id == "note")
                continue;

            if ($field->field_type_id == "field_collection"){
                $fields_fc = $field->getFields();
                $ctype_obj_fc = (new Ctype)->load($field->data_source_id);
            
                $return_value .= ctypes_generate_delete_table_tsql_code_action($ctype_obj_fc,$coreModel, $fields_fc,true);
            } 

        }

        $return_value .= "
    -- DROP TABLE " . $field->ctype_id . "
    DROP TABLE " . $field->ctype_id . "

END
";
        return $return_value;

    }