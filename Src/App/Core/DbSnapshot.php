<?php

namespace App\Core;

use App\Core\Controller;
use App\Core\DAL\MSSQLDatabase;
use App\Core\Gctypes\DbStructureGenerator;
use App\Models\NodeModel;

class DbSnapshot
{

    private $app;
    private $base;
    private $ctypes;
    private $include_ext;

    static private $BEFORE_UPDATE_NAME = "01 BEFORE_UPDATE";
    static private $CREATE_TABLES_NAME = "02 CREATE_TABLES";
    static private $REMOVE_CONSTRAINTS_NAME = "03 REMOVE_CONSTRAINTS";
    static private $ADD_COLUMNS_NAME = "04 ADD_COLUMNS";
    static private $INSERT_DATE_NAME = "05 INSERT_DATA";
    static private $CREATE_CONSTRAINTS_NAME = "06 CREATE_CONSTRAINTS";
    static private $CREATE_TRIGGERS_NAME = "07 CREATE_TRIGGERS";
    static private $AFTER_UPDATE_NAME = "08 AFTER_UPDATE";

    public function __construct($include_ext = false, $base)
    {
        $this->app = Application::getInstance();

        if (empty($base)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Release name empty");
        }

        $this->base = $base;
        $this->include_ext = $include_ext;


        $start = microtime(true);
        $this->output("> Loading ctypes...");


        // $this->ctypes = (new NodeModel("ctypes"))
        //     ->where("isnull(m.is_field_collection,0) = 0 " . ($this->include_ext ? "" : "and isnull(m.is_system_object,0) = 1" ))
        //     ->fields(["id", "name", "title", "is_system_object", "is_field_collection", "category_id"])
        //     ->load();

        $this->ctypes = $this->app->coreModel->getCtypesByLastUpdated($this->include_ext, null); // '2021-10-26 10:47:12.303'
        
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");
    }

    private function output($value)
    {
        if (php_sapi_name() == "cli") {
            echo $value;
        }
    }

    public function take()
    {


        $start = microtime(true);
        $this->output("> Initialize dir...");
        $this->initializeDir();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");

        $start = microtime(true);
        $this->output("> Geneate before script...");
        $this->generateBeforeScript();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");

        $start = microtime(true);
        $this->output("> geneate create tables...");
        $this->generateCreateTables();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");

        $start = microtime(true);
        $this->output("> geneate remove constraints...");
        $this->generateRemoveConstraints();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");

        $start = microtime(true);
        $this->output("> geneate columns...");
        $this->generateColumns();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");

        $start = microtime(true);
        $this->output("> geneate insert records...");
        $this->generateInsertRecords();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");

        $start = microtime(true);
        $this->output("> geneate constraints...");
        $this->generateConstraints();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");

        $start = microtime(true);
        $this->output("> geneate triggers...");
        $this->generateTriggers();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");

        $start = microtime(true);
        $this->output("> geneate after script...");
        $this->generateAfterScript();
        $this->output("\e[92mSuccess (Elapsed time: " . sprintf('%0.2fs', microtime(true) - $start) . ", Memory: " . round(memory_get_usage() / 1048576, 2) . '' . " MB)\e[39m\n");
    }


    private function initializeDir()
    {

        if (file_exists($this->base)) {
            del_dir($this->base);
        }

        if (!file_exists($this->base)) {
            mkdir($this->base, 0777, true);
        }
    }

    private function getAfterUpdateScript()
    {
        return "
        CREATE OR ALTER FUNCTION [dbo].[core_FN_GetOicUsers] 
        (	
            @UserId bigint,
            @returnTheUser bit = 1
        )
        RETURNS TABLE 
        AS
        RETURN 
        (
            select @userId as id where @returnTheUser = 1 union 
            select created_user_id as id
            from oic
            where user_id = @userId and isnull(is_disabled,0) = 0 and convert(date, oic.date_from, 103) <= convert(date, getdate(), 103) and convert(date, oic.date_to, 103) >= convert(date, getdate(), 103)
        )
        
        ";
    }

    private function writeToFile($file, $content)
    {

        file_put_contents($this->base . $file . ".sql", $content, FILE_APPEND | LOCK_EX);
    }

    private function generateBeforeScript()
    {

        if (!is_dir($this->base . self::$BEFORE_UPDATE_NAME)) {
            mkdir($this->base . self::$BEFORE_UPDATE_NAME, 0777, true);
        }

        $this->recusiveCopy(APP_ROOT_DIR . DS . "Core" . DS . "DatabaseScripts" . DS . "Before_update_Create_SPs", $this->base . self::$BEFORE_UPDATE_NAME);
    }

    private function recusiveCopy($src, $dst)
    {

        if (_strlen($dst) == 0 || _strlen($src) == 0) {
            return;
        }
        if (is_file($src)) {

            if (!file_exists(dirname($dst))) {
                mkdir(dirname($dst), 0777, true);
            }

            copy($src, $dst);
            return;
        }

        $dir = opendir($src);

        if (!file_exists($dst)) {
            mkdir($dst, 0777, true);
        }

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recusiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {

                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }


    private function generateAfterScript()
    {

        $this->writeToFile(self::$AFTER_UPDATE_NAME, $this->getAfterUpdateScript());
    }


    private function generateCreateTables()
    {

        $this->writeToFile(self::$CREATE_TABLES_NAME, DbStructureGenerator::generateBlankTableHeader());
        $this->writeToFile(self::$ADD_COLUMNS_NAME, DbStructureGenerator::generateColumnsHeader());
        $this->writeToFile(self::$CREATE_CONSTRAINTS_NAME, "SET NOCOUNT ON;\n");

        foreach ($this->ctypes as $item) {

            $x = (new DbStructureGenerator($item->id));

            $this->writeToFile(self::$CREATE_TABLES_NAME, $x->generateBlankTable());
            $this->writeToFile(self::$ADD_COLUMNS_NAME, $x->generateColumns(false));

            $this->writeToFile(self::$CREATE_CONSTRAINTS_NAME, $x->generateConstraints(true));

            Application::getInstance()->cache->clear();
        }
    }

    private function generateRemoveConstraints()
    {

        $query = "SET NOCOUNT ON;\n";

        // $qry = "select case when f.name is null then c.name else c.name + '_' + f.name end as name from ctypes c \n";
        // $qry .= "left join ctypes p on p.id = c.parent_ctype_id left join ctypes_fields f on f.parent_id = c.id and f.field_type_id in (2,5) and isnull(is_multi,0) = 1 \n";
        // if($this->include_ext != true) {
        //     $qry .= "where (isnull(c.is_system_object,0) = 1 or (isnull(c.is_field_collection,0) = 1 and isnull(p.is_system_object,0) = 1))\n";
        // }
        // $qry .= "union\n";
        // $qry .= "select c.name from ctypes c left join ctypes p on p.id = c.parent_ctype_id \n";

        // if($this->include_ext != true) {
        //     $qry .= "where (isnull(c.is_system_object,0) = 1 or (isnull(c.is_field_collection,0) = 1 and isnull(p.is_system_object,0) = 1))\n";
        // }

        // $this->app->coreModel->db->query($qry);

        // $query  .= "declare @tbl table (id bigint identity, name nvarchar(255))\n";
        // foreach($this->app->coreModel->db->resultSet() as $item) {
        //     $query .= "INSERT INTO @tbl (name) VALUES ('$item->name')\n";
        // }

        $query .= "DECLARE @sqlRemoveConstraint nvarchar(max) = ''\n";

        $query .= "SET @sqlRemoveConstraint = (";
        $query .= "SELECT STRING_AGG(cast('ALTER TABLE ' + QUOTENAME(OBJECT_SCHEMA_NAME(parent_object_id)) + '.' + QUOTENAME(OBJECT_NAME(parent_object_id)) + ' NOCHECK CONSTRAINT ' + QUOTENAME(name) + ';' as nvarchar(max)),' ') FROM sys.foreign_keys WHERE SCHEMA_NAME(schema_id) = 'dbo')\n";
        $query .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $query .= "SET @sqlRemoveConstraint = (";
        $query .= "SELECT STRING_AGG(cast('ALTER INDEX ' + i.name + ' ON dbo.' + o.name + ' DISABLE;' as nvarchar(max)),' ')\n";
        $query .= "FROM sys.indexes i\n";
        $query .= "LEFT JOIN sys.index_columns ic ON i.index_id = ic.index_id AND i.object_id = ic.object_id\n";
        $query .= "left join sys.objects o on o.object_id = ic.object_id\n";
        $query .= "WHERE i.is_unique = 1 and i.type_desc = 'NONCLUSTERED' and o.type = 'U')\n";
        $query .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $query .= "SET @sqlRemoveConstraint = (";
        $query .= "SELECT STRING_AGG(cast('ALTER TABLE ' + s.name + '.' + t.name + ' DROP CONSTRAINT ' + d.name + ';' as nvarchar(max)),' ') from sys.all_columns c join sys.tables t on t.object_id = c.object_id join sys.schemas s on s.schema_id = t.schema_id join sys.default_constraints d on c.default_object_id = d.object_id WHERE c.name != 'id' AND s.name = 'dbo')\n";
        $query .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $query .= "SET @sqlRemoveConstraint = (";
        $query .= "SELECT STRING_AGG(cast('DROP INDEX ' + QUOTENAME(OBJECT_SCHEMA_NAME(object_id)) + '.' + QUOTENAME(OBJECT_NAME(object_id)) + '.' + name + ';' as nvarchar(max)),' ')  FROM SYS.INDEXES WHERE OBJECT_SCHEMA_NAME(Object_id) = 'dbo' and type_desc = 'NONCLUSTERED' AND (is_unique = 0 or filter_definition is not null));\n";
        $query .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $query .= "SET @sqlRemoveConstraint = (";
        $query .= "SELECT STRING_AGG(cast('ALTER TABLE ' + QUOTENAME(OBJECT_SCHEMA_NAME(object_id)) + '.' + QUOTENAME(OBJECT_NAME(object_id)) + ' DROP CONSTRAINT [' + name + '];' as nvarchar(max)),' ')  FROM SYS.INDEXES WHERE OBJECT_SCHEMA_NAME(Object_id) = 'dbo' and type_desc = 'NONCLUSTERED' AND is_unique = 1 and filter_definition is null);\n";
        $query .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $query .= "SET @sqlRemoveConstraint = (";
        $query .= "SELECT STRING_AGG(cast('DROP TRIGGER ' + QUOTENAME(OBJECT_SCHEMA_NAME(t.object_id)) + N'.' + QUOTENAME(t.name) + N'; ' + NCHAR(13) as nvarchar(max)),' ') FROM sys.triggers AS t WHERE t.is_ms_shipped = 0 AND t.parent_class_desc = N'OBJECT_OR_COLUMN' AND OBJECT_SCHEMA_NAME(Object_id) = 'dbo');\n";
        $query .= "EXEC sp_executesql @sqlRemoveConstraint;\n";

        $this->writeToFile(self::$REMOVE_CONSTRAINTS_NAME, $query);
    }

    private function generateColumns()
    {
    }

    private function generateInsertRecords()
    {

        $this->writeToFile(self::$INSERT_DATE_NAME, $this->beforeInsertData());

        $this->writeToFile(self::$INSERT_DATE_NAME, (new \App\Core\Gctypes\DbExportDataSql2($this->include_ext))->generate());

        $this->writeToFile(self::$INSERT_DATE_NAME, $this->afterInsertData());
    }

    private function beforeInsertData()
    {
        $return_value = "";

        $return_value .= "
        declare @i int = 0;
        declare @pageSize int = 400
        declare @totalRecords int = (select count(*) from sysobjects fk, sysobjects t where fk.type='F' and fk.parent_obj=t.id)
        DECLARE @disable_fk_temp varchar(max) = '';

        while ((@i * @pageSize) < @totalRecords)
        begin

            set @disable_fk_temp = ''

            SELECT 
                @disable_fk_temp += cast('ALTER TABLE ' + t.name + ' NOCHECK CONSTRAINT ' + fk.name + ';' as varchar(4000))
            from sysobjects fk, sysobjects t where fk.type='F' and fk.parent_obj=t.id
            ORDER BY t.name OFFSET (@i * @pagesize) ROWS FETCH NEXT @pagesize ROWS ONLY 
            
            print len(@disable_fk_temp)
            
            --print @disable_fk_temp

            EXECUTE(@disable_fk_temp);

            set @i = @i + 1

        end

        ";

        $return_value .= "\n\n";
        $return_value .= "UPDATE field_type_appearances SET name = cast(field_type_id as varchar(5)) + '_' + name where SUBSTRING(name,1,IIF(field_type_id < 10, 2,3)) != cast(field_type_id as varchar(5)) + '_'\n";
        $return_value .= "UPDATE custom_url SET name = new_url WHERE name IS NULL\n";

        $return_value .= "update filter_operators set name = code where name is null\n";
        $return_value .= "delete from filter_operators where code is null\n";
        $return_value .= "UPDATE filter_operators SET name = 'date_seperator2' WHERE sort = 15 and field_type_id = 'date' and code = 'date_seperator' and name != 'date_seperator2'\n";
        $return_value .= "UPDATE filter_operators SET name = 'date_seperator3' WHERE sort = 19 and field_type_id = 'date' and code = 'date_seperator' and name != 'date_seperator3'\n";
        $return_value .= "UPDATE filter_operators SET name = 'date_seperator4' WHERE sort = 23 and field_type_id = 'date' and code = 'date_seperator' and name != 'date_seperator4'\n";
        $return_value .= "UPDATE filter_operators SET name = 'date_seperator5' WHERE sort = 27 and field_type_id = 'date' and code = 'date_seperator' and name != 'date_seperator5'\n";
        return $return_value;
    }

    private function afterInsertData()
    {
        $return_value = "";

        $return_value .= "
        set @i = 0;
        set @pageSize = 400
        set @totalRecords = (select count(*) from sysobjects fk, sysobjects t where fk.type='F' and fk.parent_obj=t.id)
        set @disable_fk_temp = '';

        while ((@i * @pageSize) < @totalRecords)
        begin

            set @disable_fk_temp = ''

            SELECT 
                @disable_fk_temp += cast('ALTER TABLE ' + t.name + ' CHECK CONSTRAINT ' + fk.name + ';' as varchar(4000))
            from sysobjects fk, sysobjects t where fk.type='F' and fk.parent_obj=t.id
            ORDER BY t.name OFFSET (@i * @pagesize) ROWS FETCH NEXT @pagesize ROWS ONLY 
            
            print len(@disable_fk_temp)
            
            --print @disable_fk_temp

            EXECUTE(@disable_fk_temp);

            set @i = @i + 1

        end

        ";

        return $return_value;
    }

    private function generateConstraints()
    {
    }

    private function generateTriggers()
    {

        $query = "SET NOCOUNT ON;\n";

        foreach ($this->ctypes as $item) {
            $query .= "exec core_create_trigger '$item->id'\n";
        }
        $this->writeToFile(self::$CREATE_TRIGGERS_NAME, $query);
    }
}
