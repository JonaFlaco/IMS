<?php

namespace App\Core\Gctypes;

use App\Core\Application;

class DbStructureGenerator {
    
    private $coreModel;
    private $app;

    private $ctype_obj;
    private $fields;
    private $ignore_required_constraint;

    private $ignore_ctypes_sync_id = ["request_tracker", "error_log", "ctypes_logs", "crons_logs", "notifications","users_login_logs"];

    public function __construct($ctype_id) {
        $this->app = Application::getInstance();
        $this->coreModel = $this->app->coreModel;
        
        $this->ctype_obj = (new Ctype)->load($ctype_id);

        $this->fields =  $this->ctype_obj->getFields();

        if($this->ctype_obj->is_field_collection && _strlen($this->ctype_obj->parent_ctype_id) > 0) {
            $parentCtype = (new Ctype)->load($this->ctype_obj->parent_ctype_id);
    
        }
    }

    
    public function generate() {

        //$return_value = "BEGIN TRY\n";
        
        $return_value = $this->fixRequiredCtypeOrphanColumn();

        $return_value .= $this->generateBlankTableHeader();
        $return_value .= $this->generateBlankTable();
        $return_value .= $this->generateRemoveConstraintsHeader();
        $return_value .= $this->generateRemoveConstraints();
        $return_value .= $this->generateColumnsHeader();
        $return_value .= $this->generateColumns();
        $return_value .= $this->generateAutoCode();
        $return_value .= $this->generateConstraints();
        $return_value .= $this->generateTriggers();
        
        // $return_value .= "END TRY BEGIN CATCH\n";
        
        // $return_value .= $this->generateConstraintsAction(null, null, false);
        
        // $return_value .= $this->generateErrorHandler();

        // $return_value .= "END CATCH\n";

        return $return_value;
  
    }


    private function fixRequiredCtypeOrphanColumn() 
    {
        
        $result = "";

        $data = $this->coreModel->getCtypeOrphanColumnsData($this->ctype_obj->id);

        foreach($data as $item) {
            if($item->is_required) {
                $result .= $item->allow_null_script . "\n";
            }
        }

        return $result;
    }
    

    private function generateErrorHandler() {
        return "
        declare @msg nvarchar(max) = ERROR_MESSAGE()
		declare @svr int = ERROR_SEVERITY()
		declare @state int = ERROR_STATE()
		
		RAISERROR(@msg, @svr, @state)\n";
    }


    private function generateAutoCode(){
        
        return "EXEC dbo.core_create_auto_generate_code_trigger '" . $this->ctype_obj->id . "'\n";
    }

    private function generateFieldDefinition($field){
        $return_value = "";

        if($this->ignore_required_constraint || !empty($field->required_condition) || !empty($field->dependencies)){
            $field->is_required = false;
        }

        $return_value .= $this->generateFieldDataType($field) . ($field->is_required == true ? " NOT NULL" : " NULL");

        return $return_value;
    }

    private function getStrLength($value) 
    {
        if(_strlen($value) == 0 || intval($value) > 4000 || ($value != "max" && intval($value) == 0))
            $value = TEXT_DEFAULT_LENGTH;
        
            return $value;
    }
    private function generateFieldDataType($field){
        $return_value = "";

        $str_length = $this->getStrLength($field->str_length);

        if($field->field_type_id == "relation" && $field->data_source_value_column_is_text) {
            return "NVARCHAR($str_length)";
        }

        if(isset($field->data_type_id)) {
            if(in_array($field->data_type_id, ["nvarchar", "varchar", "char", "nchar"]))
                return $field->data_type_id . "(" . $this->getStrLength($field->str_length) . ")";
            else
                return $field->data_type_id;
        }

        switch ($field->field_type_id){
            case "text": //Text
                    return "NVARCHAR($str_length)";
                    
                break;
            case "relation": //ComboBox
                if($field->is_multi != true && $field->data_source_value_column_is_text != true) {
                    
                    if($field->data_type_id == "uniqueidentifier")
                        return "UNIQUEIDENTIFIER";
                    else
                        return "BIGINT";
                } else if($field->is_multi != true && $field->data_source_value_column_is_text == true) {
                    return "NVARCHAR(" . TEXT_DEFAULT_LENGTH . ")";
                } else if($field->is_multi && $field->data_source_value_column_is_text != true) {
                    if($field->data_type_id == "uniqueidentifier")
                        return "UNIQUEIDENTIFIER";
                    else
                        return "BIGINT";
                }
                break;
            case "field_collection": //FieldCollection
                break;
            case "date": //Date
                return "DATETIME";
            break;
            case "media": //Attachment
                return "NVARCHAR(" . TEXT_DEFAULT_LENGTH . ")";
                break;
            case "number": //Number
                return "BIGINT";
                break;
            case "decimal": //Decimal
                return "DECIMAL(25,6)";
                break;
            case "boolean": //Boolean
                return "BIT";
                break;
            case "button": //Button
                break; 
        }

        return $return_value;
    }


    public static function generateBlankTableHeader() {
        $return_value = "SET NOCOUNT ON;\n";
        return $return_value;
    }
    
    public function generateBlankTable(){
        $return_value = "";
        
        $return_value = $this->generateBlankTableAction(null, false);
        
        return $return_value;
    }

    private function generateBlankTableAction($fields = null){
        
        if($fields == null) {
            $fields = $this->fields;
        }

        $return_value = "";
    
        foreach($fields as $field){
            
            $data_type = "BIGINT IDENTITY(1,1)";
            
            if(isset($field->data_type_id)) {
                if ($field->data_type_id == "uniqueidentifier")
                    $data_type = "UNIQUEIDENTIFIER DEFAULT NEWID()";
                else if($field->data_type_id == "varchar")
                    $data_type = $field->data_type_id . "(" . $this->getStrLength($field->str_length) . ")";
                else if ($field->data_type_id == "bigint")
                    $data_type = "BIGINT IDENTITY(1,1)";
                else if ($field->data_type_id == "int")
                    $data_type = "int IDENTITY(1,1)";
                else if ($field->data_type_id == "smallint")
                    $data_type = "smallint IDENTITY(1,1)";
                else if ($field->data_type_id == "tinyint")
                    $data_type = "tinyint IDENTITY(1,1)";
                else
                    $data_type = $field->data_type_id;
            }

            
            if(_strtolower($field->name) == _strtolower("id")) { // Main Table
                $return_value .= "EXEC dbo.core_create_table '" . $field->ctype_id . "', '" . $data_type . "'\n";
            } else if($field->field_type_id == "field_collection"){ // Field-Collection
                $return_value .=  $this->generateBlankTableAction($field->getFields());
            } else if ($field->field_type_id == "relation" && $field->is_multi == true){ //Cbx Multi
                $return_value .= "EXEC dbo.core_create_table '" . $field->ctype_id . "_" . $field->name . "','UNIQUEIDENTIFIER DEFAULT NEWID()'\n";
            } else if($field->field_type_id == "media" && $field->is_multi == true){ //Attachment Multi
                $return_value .= "EXEC dbo.core_create_table '" . $field->ctype_id . "_" . $field->name . "'\n";
            }
            
        }

        return $return_value;
        
    }

    public static function generateColumnsHeader() {
        $return_value = "SET NOCOUNT ON;\n";
        // $return_value .= "declare @temp_table_columns table (id int identity, table_name varchar(250), column_name nvarchar(250))\n";
        // $return_value .= "insert into @temp_table_columns (table_name, column_name) select table_name, column_name from INFORMATION_SCHEMA.COLUMNS\n";
        return $return_value;
    }

    public function generateColumns($ignore_required_constraint = false){
        $this->ignore_required_constraint = $ignore_required_constraint;
        $return_value = "";
        
        $return_value = $this->generateColumnsAction(null, $this->ctype_obj->id, null, false);
        
        return $return_value;
    }
      
    private function generateColumnsAction($fields, $ctype_id, $is_fc = false){
        
        if($fields == null) {
            $fields = $this->fields;
        }

        $return_value = "";

        if($is_fc) {
            $return_value .= "EXEC dbo.core_create_col '" . $ctype_id . "','token','nvarchar(255)',0,'newid()'\n";

        }
        
    
        // Create/Alter non-relational columns
        foreach($fields as $key => $field){

            if(!empty($field->required_condition) || !empty($field->dependencies)){
                $field->is_required = false;
            }
            
            if(_strtolower($field->name) == _strtolower("id") || 
               ($is_fc && _strtolower($field->name) == _strtolower("token")) || 
                            $field->field_type_id == "button" || 
                            $field->field_type_id == "note" || 
                            $field->field_type_id == "component" || 
                            $field->field_type_id == "field_collection" || 
                            $field->field_type_id == "media" || 
                            ($field->field_type_id == "relation" && $field->is_multi == true))
                            continue;
                
                            if($field->field_type_id == "relation" && $field->name == "parent_id" && $field->is_multi != true) {

                                $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "','parent_id','" . $this->generateFieldDataType($field) . "',1\n";
                                
                            } else {            
                                //$return_value .= "IF NOT EXISTS(SELECT * FROM @temp_table_columns WHERE TABLE_NAME = '" . $prefix . $field->ctype_id . "' AND COLUMN_NAME = '$field->name') BEGIN ALTER TABLE [dbo].[" . $prefix . $field->ctype_id . "] ADD $field->name " . $this->generateFieldDefinition($field) . "END ELSE BEGIN ALTER TABLE [dbo].[" . $prefix . $field->ctype_id . "] ALTER COLUMN $field->name " . $this->generateFieldDefinition($field) . " END\n";
                                $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "','$field->name','" . $this->generateFieldDataType($field) . "'," . ($field->is_required ? "1" : "0") . "\n";
                            }
            
        }
    
        //Create/Alter relational fields/tables
        foreach($fields as $key => $field){

            if( $field->field_type_id == "field_collection"){
                $return_value .=  $this->generateColumnsAction($field->getFields(), $field->ctype_id . "_" . $field->name, true);
            } else if ($field->field_type_id == "relation" && $field->is_multi == true){


                $data_type = $field->ctype_primary_column_type;
                if(in_array($data_type, ["nvarchar", "varchar", "char", "nchar"])) {
                    $data_type = $data_type . "(" . $this->getStrLength($field->ctype_primary_column_length) . ")";
                }

                if(empty($data_type))
                    $data_type = "bigint";

                $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','parent_id','$data_type',1\n";

                $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','value_id','" . ($field->data_source_value_column_is_text == true ? "nvarchar(" . TEXT_DEFAULT_LENGTH . ")" : $this->generateFieldDataType($field)) . "'\n";
                
            } else if($field->field_type_id == "media"){  // File

                if($field->is_multi == true) {
                    
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','name','NVARCHAR(255)'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','size','INT'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','extension','NVARCHAR(255)'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','type','NVARCHAR(255)'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','original_name','NVARCHAR(255)'\n";
                    
                    $data_type_parent = $field->ctype_primary_column_type;
                    if(in_array($data_type_parent, ["nvarchar", "varchar", "char", "nchar"])) {
                        $data_type_parent = $data_type_parent . "(" . $this->getStrLength($field->ctype_primary_column_length) . ")";
                    }

                    if(empty($data_type_parent))
                        $data_type_parent = "bigint";

                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','parent_id','$data_type_parent'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "_" . $field->name . "','token','NVARCHAR(255)',0,'newid()'\n";
                    
                    
                } else {
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "','" . $field->name . "_name','nvarchar(255)'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "','" . $field->name . "_size','int'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "','" . $field->name . "_extension','nvarchar(255)'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "','" . $field->name . "_type','nvarchar(255)'\n";
                    $return_value .= "EXEC dbo.core_create_col '" . $field->ctype_id . "','" . $field->name . "_original_name','nvarchar(255)'\n";
                }
            }
        }
        
        return $return_value;
        
    }

    
    public static function generateRemoveConstraintsHeader() {
        $return_value = "SET NOCOUNT ON;\n";
        $return_value .= "DECLARE @sqlRemoveConstraint NVARCHAR(MAX) = N'';\n";
        
        return $return_value;
    }

    public function generateRemoveConstraints(){
                
        $return_value = $this->generateRemoveConstraintsAction(null, null, false);
        
        return $return_value;
    }

    private function generateRequiredConstraint($fields = null, $is_fc = false) {
        $return_value = "";

        if($fields == null) { $fields = $this->fields; }

        foreach($fields as $field) {
            
            if(!empty($field->required_condition) || !empty($field->dependencies)){
                $field->is_required = false;
            }
            
            if($field->field_type_id == "field_collection") {
                $return_value .= $this->generateRequiredConstraint($field->getFields(), true);
            }

            if(in_array($field->field_type_id, ["text","relation","date","number","decimal","boolean"]) && $field->is_multi != true && $field->is_required) {
                if($field->name == "token" && in_array($this->ctype_obj->id, $this->ignore_ctypes_sync_id)) {
                    continue;
                }

                $return_value .= "ALTER TABLE [dbo].[$field->ctype_id] ALTER COLUMN $field->name " . $this->generateFieldDefinition($field) . "\n";
            }
        }

        return $return_value;
    }

    private function generateRemoveConstraintsAction($fields, $is_fc = false){

        if($fields == null) {
            $fields = $this->fields;
        }

        $return_value = "";
    
        $table_names = [$this->ctype_obj->id];
        
        foreach($fields as $field){
            
            if($field->field_type_id == "relation" && $field->is_multi) {
                $table_names[] = $this->ctype_obj->id . "_" . $field->name;
            } else if($field->field_type_id == "field_collection") {

                $table_names[] = $this->ctype_obj->id . "_" . $field->name;
                
                foreach($field->getFields() as $fc){

                    if($fc->field_type_id == "relation" && $fc->is_multi) {
                        $table_names[] = $this->ctype_obj->id . "_" . $field->name . "_" . $fc->name;
                    } else if($fc->field_type_id == "media" && $fc->is_multi) {
                        $table_names[] = $this->ctype_obj->id . "_" . $field->name . "_" . $fc->name;
                    }        
                }

            } else if($field->field_type_id == "media" && $field->is_multi) {
                $table_names[] = $this->ctype_obj->id . "_" . $field->name;
            }
        }

        
        $return_value .= "SET @sqlRemoveConstraint = N'';\n";
        $return_value .= "SELECT @sqlRemoveConstraint += N'ALTER TABLE ' + QUOTENAME(OBJECT_SCHEMA_NAME(parent_object_id)) + '.' + QUOTENAME(OBJECT_NAME(parent_object_id)) + ' DROP CONSTRAINT ' + QUOTENAME(name) + ';' FROM sys.foreign_keys WHERE OBJECT_NAME(parent_object_id) in ('" . implode("','", $table_names) . "');\n";
        $return_value .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $return_value .= "SET @sqlRemoveConstraint = N'';\n";
        $return_value .= "SELECT @sqlRemoveConstraint += N'ALTER TABLE ' + QUOTENAME(OBJECT_SCHEMA_NAME(parent_object_id)) + '.' + QUOTENAME(OBJECT_NAME(parent_object_id)) + ' NOCHECK CONSTRAINT ' + QUOTENAME(name) + ';' FROM sys.foreign_keys WHERE OBJECT_NAME(parent_object_id) in ('" . implode("','", $table_names) . "');\n";
        $return_value .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $return_value .= "SET @sqlRemoveConstraint = N'';\n";
        $return_value .= "SELECT @sqlRemoveConstraint += N'ALTER TABLE ' + OBJECT_SCHEMA_NAME(Object_id) + '.' + object_name(parent_object_id) + ' DROP CONSTRAINT ' + object_name(object_id) + ';'  FROM SYS.OBJECTS WHERE type_desc = 'UNIQUE_CONSTRAINT' and OBJECT_SCHEMA_NAME(Object_id) = 'dbo' and object_name(parent_object_id) in ('" . implode("','", $table_names) . "');\n";
        $return_value .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $return_value .= "SET @sqlRemoveConstraint = N'';\n";
        $return_value .= "SELECT @sqlRemoveConstraint += N'ALTER TABLE ' + s.name + '.' + t.name + ' DROP CONSTRAINT ' + d.name + ';' from sys.all_columns c join sys.tables t on t.object_id = c.object_id join sys.schemas s on s.schema_id = t.schema_id join sys.default_constraints d on c.default_object_id = d.object_id WHERE c.name != 'id' AND s.name = 'dbo' and t.name in ('" . implode("','", $table_names) . "')\n";
        $return_value .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $return_value .= "SET @sqlRemoveConstraint = N'';\n";
        $return_value .= "SELECT @sqlRemoveConstraint += N'DROP INDEX ' + QUOTENAME(OBJECT_SCHEMA_NAME(object_id)) + '.' + QUOTENAME(OBJECT_NAME(object_id)) + '.' + name + ';'  FROM SYS.INDEXES WHERE OBJECT_SCHEMA_NAME(Object_id) = 'dbo' and type_desc = 'NONCLUSTERED' and OBJECT_NAME(OBJECT_ID) in ('" . implode("','", $table_names) . "');\n";
        $return_value .= "EXEC sp_executesql @sqlRemoveConstraint;\n";


        return $return_value;
        
    }

    private function deleteOrphanNodes() {
        $query = "";

        //deleteOrphanFieldCollectionRecords
        foreach($this->fields as $field) {
            if($field->field_type_id == "relation" && $field->is_multi) {
                $query .= "DELETE FROM " . $this->ctype_obj->id . "_" . $field->name . " WHERE parent_id not in (select id from " . $this->ctype_obj->id . ")\n";
            } else if($field->field_type_id == "media" && $field->is_multi) {
                $query .= "DELETE FROM " . $this->ctype_obj->id . "_" . $field->name . " WHERE parent_id not in (select id from " . $this->ctype_obj->id . ")\n";
            } else if ($field->field_type_id == "field_collection") {
                $query .= "DELETE FROM " . $this->ctype_obj->id . "_" . $field->name . " WHERE parent_id is null\n";

                foreach($field->getFields() as $fc) {
                    if($fc->field_type_id == "relation" && $fc->is_multi) {
                        $query .= "DELETE FROM " . $this->ctype_obj->id . "_" . $field->name . "_" . $fc->name . " WHERE parent_id not in (select id from " . $this->ctype_obj->id . "_" . $field->name . ")\n";
                    } else if($fc->field_type_id == "media" && $fc->is_multi) {
                        $query .= "DELETE FROM " . $this->ctype_obj->id . "_" . $field->name . "_" . $fc->name . " WHERE parent_id not in (select id from " . $this->ctype_obj->id . "_" . $field->name . ")\n";
                    }
                }
            }
        }

        return $query;
    }

    private function generateSyncIdIfNull() {

        if(in_array($this->ctype_obj->id, $this->ignore_ctypes_sync_id)) {
            return "";
        }

        $query = "";

        $query .= "UPDATE " . $this->ctype_obj->id . " SET token = newid() WHERE token is null\n";

        foreach($this->fields as $field) {
            if($field->field_type_id == "field_collection") {
                $query .= "UPDATE " . $this->ctype_obj->id . "_" . $field->name . " SET token = newid() WHERE token is null\n";
            }
        }

        return $query;
    }

    public function generateConstraints($ignore_required_constraint = false){
        $this->ignore_required_constraint = $ignore_required_constraint;

        $return_value = "";
        
        // $return_value .= $this->generateSyncIdIfNull();

        // $return_value .= $this->deleteOrphanNodes();
        if(!$ignore_required_constraint)
            $return_value .= $this->generateRequiredConstraint();

        $return_value .= $this->generateConstraintsAction(null, null, false);
        
        
        return $return_value;
    }

    private function generateConstraintsAction($fields, $is_fc = false){

        if($fields == null) {
            $fields = $this->fields;
        }

        $return_value = "";
    
        foreach($fields as $key => $field){
            if(_strtolower($field->name) == _strtolower("id") || 
                $field->field_type_id == "button" || 
                $field->field_type_id == "note" || 
                $field->field_type_id == "component" || 
                $field->field_type_id == "field_collection" || 
                $field->field_type_id == "media" || 
                ($field->field_type_id == "relation" && $field->is_multi == true))
                continue;

            if($field->is_unique == true){
                $return_value .= "EXEC dbo.core_create_uc '" . $field->ctype_id . "','$field->name'\n";
            }

            if($field->field_type_id == "relation" && $field->is_multi == false && $field->data_source_value_column_is_text != true && empty($field->data_source_from_string)) {
                $return_value .= "EXEC dbo.core_create_fk '" . $field->ctype_id . "','" . $field->name . "','" . $field->data_source_table_name . "','" . $field->data_source_value_column . "'" . (!empty($field->delete_rule) ? ",'ON DELETE $field->delete_rule'" : "") . "\n";
            }
                        
        }
        
        foreach($fields as $key => $field){
                    
            if( $field->field_type_id == "field_collection"){
                
                $return_value .=  $this->generateConstraintsAction($field->getFields(),true);

                // $return_value .= "ALTER TABLE [dbo].[" . $field->data_source_table_name . "]  WITH CHECK ADD  CONSTRAINT [FK_" . $field->data_source_table_name . "_" . $field->name . "] FOREIGN KEY([parent_id]) REFERENCES [dbo].[" . $prefix . $field->ctype_id . "] ([id])" . " ON DELETE CASCADE ON UPDATE NO ACTION\n";
                // $return_value .= "ALTER TABLE [dbo].[" . $field->data_source_table_name . "] CHECK CONSTRAINT [FK_" . $field->data_source_table_name . "_" . $field->name . "]\n";
        
            } else if ($field->field_type_id == "relation" && $field->is_multi == true){
                            
                if(empty($field->data_source_from_string)){
                    $return_value .= "EXEC dbo.core_create_fk '" . $field->ctype_id . "_" . $field->name . "','parent_id','$field->ctype_id','id','ON DELETE CASCADE'\n";
                }
                
                if($field->data_source_value_column_is_text != true && empty($field->data_source_from_string)){
                    $return_value .= "EXEC dbo.core_create_fk '" . $field->ctype_id . "_" . $field->name . "','value_id','$field->data_source_table_name','id'" . (!empty($field->delete_rule) ? ",'ON DELETE $field->delete_rule'" : "") . "\n";
                }
    
            } else if($field->field_type_id == "media"){  // File
    
                if($field->is_multi == true) {
                    $return_value .= "EXEC dbo.core_create_fk '" . $field->ctype_id . "_" . $field->name . "','parent_id','" . $field->ctype_id . "','id','ON DELETE CASCADE'\n";
                }
            }
        }
    
        return $return_value;
        
    }

    private function generateTriggers() {
        if($this->ctype_obj->is_field_collection) {
            return "";
        }
        
        return "exec core_create_trigger '" . $this->ctype_obj->id . "'\n";
    }

}