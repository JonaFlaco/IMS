#### Step by step guide how to update BMDS to Nov 2020 Update
- Open error_log `/ctypes/edit/53` ctype and add below fields
    - add column (Name: `code`, Data type: `text`)
    - add column (Name: `line`, Data type: `number`)
- Update PHP Version to 7.4
    - Download `PHP 7.4` and put it on your computer.
    - Download `Sql Server driver` to PHP 7.4 and put it inside `/php/extensions`
    - Make sure in `IIS/Handler Mapping/PHP via FastCGI` is pointing to php-cgi.php inside PHP 7.4
    - In IIS open `Advanced Settings` of the app and change `Physical Path` to `/public`.
    - Run the app and see if works, otherwise means there is an error and needs to be fixed before going to next step.
- Make sure IIS user has permission to create folder on wwwroot folder.
- Merge the update 'Nov 2020 Update' branch with master
- Open CMD inside `wwwroot` and run `composer dump-autoload`
- Open Settings `/settings` and open `SETT_IS_LIVE_PLATFORM` then change the name to `IS_LIVE_PLATFORM`

