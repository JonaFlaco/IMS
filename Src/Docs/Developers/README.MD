## Index
* 1. Getting Started
* 1.1 What is IMS
* 1.2 Tech Stack
* 1.3 Installation
* 1.4 Directory Structure

### 1.1 What is IMS
IMS is framework built by IOM Iraq Developers to make every day tasks easier. the key feature of IMS is ability to create and deploy forms without the need of writing single line of code. They idea came after many struggle with Drupal which had similar feature but with time it was getting heavier and heavier. With IMS we made almost all of our user-requests easy and user-friendly interfaces which our developers can create advanced interfaces without writing code or if they wish to customize they can customize the interface and write some custom code to it but the idea is for most of the cases no coding is required.


### 1.2 Tech Stack
* PHP
* SQL Server
* Vue.js
* Bootstrap


### 1.3 Installtion
Our development and production enviroment is Windows, so we run the app using Fast-CGI on IIS. Steps to install the app:

1. Enable IIS
2. Install URL Rewrite for IIS
3. Copy IMS files to the web folder
4. Install SQL Server
5. Restore database
6. Copy `\.env.example` and paste it at the same location and name it `.env`
7. Fill config file inside `\.env`
8. Check below PHP settings inside php.ini
    - post_max_size => 100M
    - upload_max_filesize => 100M
9. Enable below php extensions
   - fileinfo
   - pdo_mysql
   - pdo_sqlsrv
10. Install composer
11. Go to app root dir using cmd and run: `composer install` then run `composer dump-autoload`

**Notes**
- *Make sure the IIS user have permission to create folder inside wwwroot*
- *Make sure in `php.ini` upload_tmp_dir exist and IIS user has full permission to it*



### 1.4 Directory Structure
- **public**: Publis directory which will be accessible by publish users
    - **assets**: All the js, css, imag files
        - **app**: This directory will contain assets related to src\app directory
        - **ext**: This directory will contain assets related to src\ext directory
        - **theme**: This directory contains assets for the theme
    - **uploaded_files**: This directory is the wharehouse for all the uploaded images
- **runtime**: This directory contains some files which the app will create on runtime in order to function
    - **csrf_tokens**: Stores CSRF Tokens as file here
    - **error_log**: Errors will be logged here
    - **php_sessions**: PHP Session will be stored here as file
    - **temp**: This is a temp folder the app uses for various tasks
- **src**
    - **app**
        - **[actions](/${DOCNAME}/directory_structure/src_app_actions)**
        - **apiFilter**
        - **config**
        - **controllers**
        - **Core**
        - **Crons**
        - **exceptions**
        - **externalapi**
        - **helpers**
        - **InternalApi**
        - **libraries**
        - **models**
        - **resources**
        - **triggers**
        - **views**
            - **Components**
            - **custom_dashboards**
            - **custom_edit_tpls**
            - **custom_tpls**
            - **custom_views**
            - **custom_widgets**
            - **gview_extends**
            - **surveys**
    - **docs**
    - **ext**
        - *Same as app dir*
- **tests**




##### Gview conditional formatting:
- between
- greater
- greater_or_equal
- less
- less_than_or_equal

{
    "conditions":[
        {"operator":"equals","value":6, "style":"bg-info text-white"},
        {"operator":"greater_or_equal","value":50,"style":"bg-success text-white"},
        {"operator":"less","value":50, "value_2": 50, "style":"bg-danger text-white"},
        {"operator":"between","value":25, "value_2": 30, "style":"bg-warning text-white"}
    ]
}


### In ctypes fields in required condition and read only condition you can put $dependencies to get same dependency as defined in dependencies field.




### Dashboard Widget Filteration:
In the query we can use /*{filter_name}*/ to replace with the filter value or /*{where}*/ to replace with full where clause.

Note filter_name is name of the filter inside widget => filters.