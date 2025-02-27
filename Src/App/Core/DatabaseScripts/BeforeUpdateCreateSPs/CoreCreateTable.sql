CREATE OR ALTER PROCEDURE [dbo].[core_create_table]
    @table_name varchar(1000),
	@primary_col_def varchar(1000) = null
AS
BEGIN
    SET NOCOUNT ON;

	declare @sql nvarchar(max)

	if(@primary_col_def is null or @primary_col_def = '')
		set @sql = 'IF OBJECT_ID(''dbo.' + @table_name + ''') IS NULL BEGIN CREATE TABLE [dbo].[' + @table_name + '] (id BIGINT IDENTITY(1,1) PRIMARY KEY) END';
	else
		set @sql = 'IF OBJECT_ID(''dbo.' + @table_name + ''') IS NULL BEGIN CREATE TABLE [dbo].[' + @table_name + '] (id ' + @primary_col_def + ' PRIMARY KEY) END';


    EXEC sp_executesql @sql;
END;

