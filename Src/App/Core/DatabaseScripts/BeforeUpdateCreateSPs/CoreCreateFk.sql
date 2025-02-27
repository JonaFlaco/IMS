CREATE OR ALTER PROCEDURE [dbo].[core_create_fk]
    @table1 varchar(1000),
    @col1 varchar(1000),
    @table2 varchar(1000),
    @col2 varchar(1000),
	@rules nvarchar(1000) = null
AS
BEGIN
    SET NOCOUNT ON;

	declare @name nvarchar(255) = 'FK_' + @table1 + '_' + @col1
    declare @sql nvarchar(max)
    
    if(exists(
		SELECT 
			*
		FROM sys.foreign_keys f
		left join sys.objects o on o.object_id = f.parent_object_id
		left join sys.objects oref on oref.object_id = f.referenced_object_id
		WHERE 
			SCHEMA_NAME(f.schema_id) = 'dbo' and 
			f.type = 'F' and
			o.name = @table1 and oref.name = @table2 and 
			f.name = @name
		))
	begin
		set @sql = 'ALTER TABLE [dbo].[' + @table1 + ']  CHECK CONSTRAINT [' + @name + ']';
		
		EXEC sp_executesql @sql;

	END ELSE BEGIN

		set @sql = 'ALTER TABLE [dbo].[' + @table1 + ']  WITH CHECK ADD  CONSTRAINT [' + @name + '] FOREIGN KEY([' + @col1 + ']) REFERENCES [dbo].[' + @table2 + '] ([' + @col2 + ']) ' + isnull(@rules,'') + '
		ALTER TABLE [dbo].[' + @table1 + '] CHECK CONSTRAINT [' + @name + ']'

		EXEC sp_executesql @sql;
	end
END
