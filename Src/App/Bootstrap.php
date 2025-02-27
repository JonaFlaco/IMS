<?php

//Load Config

use App\Core\Application;

require_once 'config/config.php';

set_error_handler(function($err_severity = "", $err_msg = "", $err_file = "", $err_line = 0, array $err_context = []) {
    throw new \errorexception($err_msg, 0, $err_severity, $err_file, $err_line);
    }, E_ALL ^ (E_DEPRECATED));


define('UPLOAD_DIR_FULL', Application::getInstance()->env->get("attachments_path", PUBLIC_DIR_FULL . DS . "attachments"));//;
define("UPLOAD_DIR", basename(UPLOAD_DIR_FULL));


