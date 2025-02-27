## Deploy

1. Enable IIS
2. Install URL Rewrite
3. Copy IMS to web folder
4. Restore database
5. Copy `\src\.env.example` and paste it at the same location and name it `.env`
5. Fill config file inside `\src\.env`
6. Enable below php extensions
   1. fileinfo
   2. pdo_mysql
   3. pdo_sqlsrv
7. Install composer
   1. Go to website root dir and run:
      1. Run `composer install`
      2. Run `composer dump-autoload`

Make sure the IIS user have permission to create folder inside wwwroot

Make sure in php.ini upload_tmp_dir exist and IIS user has full permission to it

php settings
- post_max_size => 100M
- upload_max_filesize => 100M
- max_input_vars => 50000