CREATE OR ALTER PROCEDURE [dbo].[core_create_col]
    @table_name varchar(1000),
    @column_name varchar(1000),
    @data_type varchar(1000),
	@is_required bit = 0,
    @default_value varchar(1000) = null
AS
BEGIN
    SET NOCOUNT ON;

    declare @sql nvarchar(max)
    
    declare @cur_data_type varchar(1000)
	declare @field_exist bit = 0
	declare @allowNullStr nvarchar(250) = case when @is_required = 1 then 'NOT NULL' else 'NULL' end
	declare @cur_is_required bit = 0

	select 
		@field_exist = 1,
		@cur_is_required = case when IS_NULLABLE = 'YES' then 0 else 1 end, 
		@cur_data_type = data_type + 
			case when CHARACTER_MAXIMUM_LENGTH is not null then '(' + cast(CHARACTER_MAXIMUM_LENGTH as varchar(100)) + ')' else '' end +
			case when data_type = 'decimal' then '(' + cast(NUMERIC_PRECISION as varchar(100)) + ',' + cast(NUMERIC_SCALE as varchar(100)) + ')' else '' end
	from INFORMATION_SCHEMA.COLUMNS where table_name = @table_name and COLUMN_NAME = @column_name

	if(@field_exist = 0) -- Add
		set @sql = 'ALTER TABLE dbo.' + @table_name + ' ADD ' + @column_name + ' ' + @data_type + ' ' + @allowNullStr + ' ' + case when len(@default_value) > 0 then 'DEFAULT ' + @default_value else '' END 
	else if (@cur_data_type != @data_type or @cur_is_required  != @is_required ) -- Update
		set @sql = 'ALTER TABLE dbo.' + @table_name + ' ALTER column ' + @column_name + ' ' + @data_type + ' ' + @allowNullStr
	
    EXEC sp_executesql @sql;
END;