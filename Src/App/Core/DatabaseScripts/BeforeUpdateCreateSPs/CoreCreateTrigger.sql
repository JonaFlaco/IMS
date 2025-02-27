CREATE OR ALTER PROCEDURE [dbo].[core_create_trigger] 
    @ctypeName nvarchar(1000)
AS
BEGIN
    SET NOCOUNT ON;

    declare @qry varchar(max)
                
    IF OBJECT_ID(N'dbo.' + @ctypeName + '_update_dates', N'TR') IS NOT NULL  
    begin
        set @qry = 'DROP TRIGGER dbo.' + @ctypeName + '_update_dates' 

        exec (@qry)
                    
    end


    set @qry = '

    CREATE TRIGGER dbo.' + @ctypeName + '_update_dates
    ON  ' + @ctypeName + '
    AFTER INSERT,UPDATE
    AS 
    BEGIN
        SET NOCOUNT ON;
        IF EXISTS(SELECT * FROM INSERTED)  AND EXISTS(SELECT * FROM DELETED) 
        BEGIN 
            update  x -- updated
                set last_update_date = getdate()
            from ' + @ctypeName + ' x
            left join inserted on inserted.id = x.id
            where inserted.id is not null
        END 
        ELSE IF EXISTS(SELECT * FROM INSERTED)  AND NOT EXISTS(SELECT * FROM DELETED) 
        BEGIN 
            update  x -- inserted
                set created_date = getdate(),
                    token = newid()
            from ' + @ctypeName + ' x
            left join inserted on inserted.id = x.id
            where inserted.id is not null
        END 

    END
    '
    exec (@qry)

END;