<?php

use App\Core\Application;

Application::getInstance()->globalVar->set('PAGINATION_PAGE_SIZE_ARRAY', array(
    10,25,50,100,250,500,1000,2000,3000
));

Application::getInstance()->globalVar->set('ALLOWED_FIELD_TYPES_TO_EXPORT', array(
    "text","relation","date","media","number","decimal","boolean"
));

Application::getInstance()->globalVar->set('OPERATORS_NOT_REQUIRE_VALUE', array(
    'text_is_empty','text_is_not_empty','number_empty','number_not_empty','date_empty','date_not_empty','relation_empty','relation_not_empty','date_tomorrow','date_today','date_yesterday','date_next_week','date_this_week','date_last_week','date_next_month','date_this_month','date_last_month','date_next_quarter','date_this_quarter','date_last_quarter','date_year_next_year','date_year_this_year','date_last_year'
));


Application::getInstance()->globalVar->set('ALLOW_FILE_TYPES', array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc','xlsx', 'docx'));

Application::getInstance()->globalVar->set('ignore_fields_on_insert', array('last_date_date', 'last_heartbeat','updated_user_id','created_user_id'));
Application::getInstance()->globalVar->set('ignore_fields_on_update', array('created_date', 'last_date_date', 'last_heartbeat','updated_user_id','created_user_id'));
    

Application::getInstance()->globalVar->set('CACHED_FIELDS', array());
Application::getInstance()->globalVar->set('CACHED_CTYPES', array());
Application::getInstance()->globalVar->set('CACHED_SETTINGS', array());
Application::getInstance()->globalVar->set('CACHED_KEYWORDS', array());

\PhpOffice\PhpWord\Settings::setTempDir(TEMP_DIR);

Application::getInstance()->globalVar->set('fileCleanupExcludeList', [".","..","recycle_bin","system_user_profile_picture.png","default_profile_pic_anonymous.png","default_profile_pic_female.png","default_profile_pic_male.png"]);


$path = ROOT_DIR . DS . "release.json";
$data = json_decode(file_get_contents($path));
Application::getInstance()->globalVar->set('version', $data->version);
