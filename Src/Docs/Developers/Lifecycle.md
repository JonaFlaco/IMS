
### IMS Documentation
<br>

#### Life Cycle

<br>

##### Any request received by the webserver, it will be processed in two ways:
- If they request is downloadable file (ex: jpg, png, pdf) and the file is not restricted then the webserver returns the file.
- Otherwise webserver direct the request to `\public\index.php` and index.php will handle the request.

<br>
##### When a request received by `\public\index.php`, then index.php will do the below steps:

- Load vendor libraries
- Create a new instance of `Application`
    - Check if `.env` file exist
    - Check if required `php extensions are loaded`
    - Load `SMTP settings`.
    - Create instance of required classes
    - If the user is logged in, check if his `secret key` is valid or not.
- Run the Application
    - Call router to handle the request.
        - Router will cross check with below options in sequence
            - Check if the app is in `Maintenance Mode`.
            - If the request is pointing to an `Extracted Controller`.
            - If the request is pointing to a `Controller`.
            - If the request is `Survey`.
            - If the request is `Page`.
            - If the request is pointing to `Gctype`.
            - If the request is pointing to `Modules`.
            - If the request is pointing to `Custom URL`.
            - If the request is pointing to `Documentation`.
        - If any of above option is true, the the router send the request to that class.
        - If the request is none of above, then the router return `Error 404`.
- In case any error happens while creating instance of Application or while running the Application all the exceptions will be handled by ErrorHandler class.

