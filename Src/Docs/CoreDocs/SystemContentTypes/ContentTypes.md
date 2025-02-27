#### Content-Types

Content-Types is used to create new Content-Types, you can think about it as `tables` in database but in a very easier way. In `Content-Types` you can define below properties to Content-Types:
- **Name (name)**: It is name of the Content-Type (E.g. students, nationalities, sub_districts). name field will be translated into table name and it will be used as machine name of the Content-Type that's why name is very crucial.
    - Field Type: Text
    - Only below chars can be used:
        - Alphabet (a-z)
        - Numbers (0-9)
        - underscore (_)
    - Should be lowercase.
    - Unique.
    - Required
- **Title (title)**: It is title of the Content-Type (E.g. Students, Nationalities, Sub Districts). title will be friendly name of the Content-Type and will be displayed to the end user.
    - Field Type: Text
    - Unique
    - Required
- **View (view_id)**: You can create custom view and link it with the Content-Type so when you go to list of the Content-Type the view will show.
    - Field Type: Combobox (Link to views shows title saves id)
- **Category (category_id)**: Content-Type has three categories (Content-Type, Field-Collection, Lookup-Table)
    - [Click here for more detail][ctypes_categories]
    - Combobox (Link to ctype_categories shows name saves id)
    - Required
- **Parent Content-Type (parent_ctype_id)**: This will show only if category is `Field-Collection` since Field-Collections are sub tables it will help to determine the parent Content-Type while defining the Field-Collection. it will help later while developing.
    - Field Type: Combobox (Link to ctypes shows title saves id)
- **Icon (icon)**: It is icon for the Content-Type which will be shown in Module homepage and can be shown in more places in future.
    - The icon should be located at `/assets/app/images/icons`.
    - Field Type: text
- **Module (module_id)**: It is like grouping multiple Content-Types together so it will be easier for users to see all relevant Content-Types at the same place.
    - Field Type: Combobox (Link to modules shows title saves id)
- **Redirect After Save (redirect_after_save)**: You can specify a link so anytime any record of this Content-Type saves the system will redirect the user to the specified link.
    - Field Type: Text
    - Should be a valid URL
- **Use Generic Status (use_generic_status)**: This is a flag to let the system know if this Content-Type uses generic status or not, this will help the system while generating UI.
    - Field Type: Boolean
- **Status Workflow (status_workflow_tempalate)**: Specify status workflow for the Content-Type.
    - Field Type: Text
- **Governorate Field Name (governorate_field_name)**: If the Content-Type has governorate field you can specify the governorate field name here so it will filter the data out for the users automatically. (E.g. You have data for all governorates of iraq in the Content-Type. When a user that based on his profile he covers only two governorates opens the Content-Type he will see data for only the two governorate that he covers).
    - Field Type: Text
- **Programme Field Name (programme_field_name)**: If the Content-Type has programme field you can specify the programme field name here so it will filter the data out for the users automatically. (E.g. You have data for all programme of iraq in the Content-Type. When a user that based on his profile he covers only two programme opens the Content-Type he will see data for only the two programme that he covers).
    - Field Type: Text
- **Unit Field Name (unit_field_name)**: If the Content-Type has unit field you can specify the unit field name here so it will filter the data out for the users automatically. (E.g. You have data for all unit of iraq in the Content-Type. When a user that based on his profile he covers only two unit opens the Content-Type he will see data for only the two unit that he covers).
    - Field Type: Text
- **Form Type Field Name (form_type_field_name)**: If the Content-Type has form type field you can specify the form type field name here so it will filter the data out for the users automatically. (E.g. You have data for all form type of iraq in the Content-Type. When a user that based on his profile he covers only two form type opens the Content-Type he will see data for only the two form type that he covers).
    - Field Type: Textaccess the Content-Type by writing the Content-Type name in the webaddress (E.g. localhost/students), if you wisht to disable that you can tick this checkbox.
    - Field Type: Boolean
- **System Obj (is_system_object)**: It is a flag to determine if a Content-Type is sytem object or not. this will help the system to distinguish between system objects and non-system objects.
    - Field Type: Boolean
- **Create Revision (create_revision)**: It is falg to termine if the system should keep revision on save for this Content-Type or not.
- Field Type: Boolean



[ctypes_categories]: /${DOCNAME}/system_content_types/ctype_categories