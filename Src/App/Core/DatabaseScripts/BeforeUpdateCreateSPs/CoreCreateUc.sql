CREATE OR ALTER PROCEDURE [dbo].[core_create_uc]
    @table_name varchar(1000),
    @column_name varchar(1000)
AS
BEGIN
    SET NOCOUNT ON;

    declare @name nvarchar(1000) = 'UC_' + @table_name + '_' + @column_name
    declare @sql nvarchar(max)

    if(exists(
		SELECT *
        FROM sys.indexes i
        LEFT JOIN sys.index_columns ic ON i.index_id = ic.index_id AND i.object_id = ic.object_id
        left join sys.objects o on o.object_id = ic.object_id
        WHERE i.is_unique = 1 and i.type_desc = 'NONCLUSTERED' and o.type = 'U' and i.name = @name
		))
	begin
	    set @sql = 'ALTER INDEX ' + @name + ' ON [dbo].[' + @table_name + '] REBUILD';
        EXEC sp_executesql @sql;
    END ELSE BEGIN
        set @sql = 'CREATE UNIQUE INDEX ' + @name + ' ON [dbo].[' + @table_name + '](' + @column_name + ') WHERE ' + @column_name + ' IS NOT NULL';
        EXEC sp_executesql @sql;
    END

    
END;
