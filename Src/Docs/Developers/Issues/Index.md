### Issues

Icons
- ☑ Done
- ☐ Pending

#### Bugs/Todos
- ☐ Field-Collection table => preview multiselect selected data
- ☐ Select2 required condition
- ☐ Notify admins on cron error
- ☐ Content-Type data validation before save() is broken
- ☐ Views => record actions will not show if list of records contain only one record (maybe move to popup)
- ☐ Check upload file method (unify)
- ☐ Clean uploaded files
- ☐ Check CSRF token
- ☐ Required condition for groups
- ☐ ODK form sync sometimes it will not sync all images
- ☐ Status workflow => when have multiple records for same status it should give warning
- ☐ System load dashboard
- ☐ Db maintenance
- ☐ Server side errors page sometimes won't render properly.
- ☐ Handle error if happens at early stages of the request
- ☐ Add is published/draft state flag
- ☐ Governorate permission not working with widgets if you don't filter

- ☐ Work on DevOps

- ☐ Clean up extra tables inside db

- ☐ If database connection error happen then don't load left menu items
- ☐ Upload ODK Form => after upload show link to the uplaoded form.
- ☐ Boolean (List and Combobx) Invalid message won't appear

- ☐ ODK _COMPLETED FLAG
- ☐ Check destination after redirect to login
- ☐ add + button on left side system menu
- ☐ Windows auth login for sql server (for local)
- ☐ Gviews => add number formatting for computed fields
- ☐ Datepicker issue with dependencies

- ☐ ODK => already synced form error message
- ☐ ODK Dashboard => load data faster (send one request)

- ☐ Attachment error dialog won't show on error.

- ☐ Uploaded files issues
    - ☐ Missing thumbnails
    - ☐ Orphan Images
    - ☐ High Resolution (Big) Images

- ☑ Attachment error dialog won't show on error.
- ☑ Import cron, image repeat has issue
- ☑ Import from excel, file names get doubled
- ☑ Gviews => Generic Export => Loading will keep visibile after task finished
- ☑ Select All/Unselect All for cbx (list)
- ☑ Uploading Attachment inside FC: While uploading the user should not be able to close FC modal or if they do it should cancel the upload request.
- ☑ Views => Conditional formatting not working on boolean fields
- ☑ Fix fields => datasource component issue
- ☑ Change password have problem
- ☑ Gview loading stuck sometimes
- ☑ Cron Import => image repeat inside fc  
- ☑ Add odk form creation date to odk forms dashboard
- ☑ NodeLoad => issue with Deepload
- ☑ Remove scheduled Crons from ctypes, menu and reset interface
- ☑ ODK dashboard => if a cron has issue show an icon next to it and continue render the dashboard.
- ☑ Run crons inside crons dashboard
- ☑ Redesign Crons Dashboard
- ☑ Gcron sync gsp inside FC
- ☑ Add params to redirect distination
- ☑ Combobox huge data source: add "select2 Async" appearance.
- ☑ Fix get keywords by language
- ☑ Disable revision for ctypes by default
- ☑ Gviews => Filteration Panel => If select Any (operator) it should hide the input.
- ☑ Show odk form size in dashboard and when open the edit interface
- ☑ Add extra condition for PL
- ☑ Image multi if required has problem (Keep says empty)
- ☑ In Ctypes list show no of records for each ctype
- ☑ Pagination buttons not working
- ☑ RunCron should not create connection with ODK unless it is necessory.
- ☑ Change Cron Db from hard coded to retrive it from db
- ☑ Fix image compression
- ☑ If user not found and has session then logout
- ☑ Add trigger for BeforeRender (Add, Edit, Delete, Read, View);
- ☑ Add extra condition to gview get data
- ☑ Fix calculated date issue from sync ODK form.
- ☑ Set extra permission checking on Add/Edit/Show/Delete/View Ctype
- ☑ Set extra permission checking on gview load data
- ☑ Add relation to parent for multi cbx when data source is text
- ☑ Check if database credential is provided
- ☑ Remove revision tables with "Cleaning Stuck Tables" if they ctypes does not have revision enabled anymore.
- ☑ In Ctypes Fields => Reorder does not work.
- ☑ Error 407, authentication required: if not logged in should redirect to login page if response_type is not json
- ☑ On change password interface, enter key not working.
- ☑ Add message parameter on LoadFirstOrFail() or NodeLoad().
- ☑ Clean filter in gdashbords
- ☑ New way to NodeModel (Load Node) with secure where clause
- ☑ Field Permissions => roles should be optional if inverser role
- ☑ Fix email templates and logo
- ☑ dashboards => Widgets => Negative values 
- ☑ dashboards => Save filters in url and add reset button
- ☑ Sort not work in widget dashborad
- ☑ Dependence is not working on group
- ☑ Email FROM name and address add it to config
- ☑ Send email with attachment
- ☑ Gdashboard => reorder filters
- ☑ Gviews => fix conditional formatting (Greater or equal to, Less than or equal to)
- ☑ Add a button to edit Content-Type and view on views
- ☑ Add a button to edit Content-Type on edit/add
- ☑ Add a button in ctypes Content-Type to open the view
- ☑ In Content-Type add/edit add link to eidt the ctype
- ☑ Add component to add buttons to header actions
- ☑ In Content-Type if tab is empty then hide it
- ☑ In Content-Type if group is empty then hide it
- ☑ Gviews => filter default value (select2)
- ☑ GetCtypeBasicInfo gives error on first time open a cbx field inside Content-Types
- ☑ Write docs for components
- ☑ dashboards => filters operator_id default value
- ☑ Write docs for components
- ☑ dashboards => filters operator_id default value
- ☑ dashboards => filter by cascade
- ☑ Add $dependencies to required condition and read only condition

#### Feature Requests
- ☐ Generate ODK excel file from Content-Type
- ☐ Check if session is still valid every x mins
- ☐ Add aggregation (Sum, Avg, Min, Max) to givews columns
- ☐ Simple Git Interface to pull and push and view recent commits
- ☐ Clone Content-Type
- ☐ Export single record on edit/tpl mode
- ☐ Clean stuck fields
- ☐ Givews => Export to excel => choose columns && save as template
- ☐ Gviews => Save fitlers as template
- ☐ Encrypt passwords in settings
- ☐ send email after password reset
- ☐ Write Tests
    - ☐ Unit Tests
    - ☐ Integration Tests
    - ☐ UI Tests (End to end Test)
- ☐ Look into https://github.com/czproject/git-php
- ☐ Scan uploaded file for viurs/malious content
- ☐ Db snapshot (Start without db)
- ☐ Score Interface
- ☐ Create seed for initial data
- ☐ Initial Setup
    - ☐ The only required step before initial setup is having the root folder binding to a webserver.
    - ☐ On loading the page it should check if the app is setup or not, if not the it will load the initial setup wizard
    - ☐ The Wizard
        - ☐ View the requirement
        - ☐ Set up database
        - ☐ Install
        - ☐ Configure App
            - ☐ Site name
            - ☐ Admin username
            - ☐ Admin password
            - ☐ Admin email
            - ☐ Timezone
        - ☐ Redirect to home page with logged in user
- ☐ Add Rich Text Component
- ☐ Add Rich Text appearance for text
- ☐ Add Save, Save and new, Save and go back options to save button
- ☐ Help Center
    - ☑ Design show tpl for Help Posts
    - ☑ Set permission of help_posts in 'show' page
    - ☐ Add user-friendly link to each post

#### WIP
- ☐ System Export/Update
