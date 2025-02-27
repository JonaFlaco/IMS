CREATE OR ALTER PROCEDURE [dbo].[core_changed_ctypes] 
	@include_ext bit = 0,
	@date datetime = null
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	;with cte as (
		select
			id, isnull(last_update_date, created_date) as last_update
		from ctypes

		union

		select
			parent_id, isnull(last_update_date, created_date) as last_update
		from ctypes_fields

		union

		select
			parent_id, isnull(last_update_date, created_date) as last_update
		from ctypes_field_permissions

		union

		select
			parent_id, isnull(last_update_date, created_date) as last_update
		from ctypes_field_groups

		union

		select
			parent_id, isnull(last_update_date, created_date) as last_update
		from ctypes_status_settings

	)

	select 
		isnull(p.id, c.id) as id, 
		isnull(p.name,c.name) as name, 
		isnull(isnull(p.is_system_object, c.is_system_object),0) as is_system_object, 
		isnull(isnull(p.is_field_collection,c.is_field_collection),0) as is_field_collection,
		isnull(p.category_id, c.category_id) as category_id
	from cte
	left join ctypes c on c.id = cte.id
	left join ctypes p on p.id = c.parent_ctype_id
	where 
		(@date is null or last_update >= @date) and
		(@include_ext = 1 or isnull(p.is_system_object, c.is_system_object) = 1)
	group by
		isnull(p.id, c.id),
		isnull(p.name,c.name),
		isnull(isnull(p.is_system_object, c.is_system_object),0),
		isnull(isnull(p.is_field_collection,c.is_field_collection),0),
		isnull(p.category_id, c.category_id)
	order by isnull(p.id, c.id)
END
