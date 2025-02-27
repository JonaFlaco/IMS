<?php

require 'helper.php';
if(file_exists('./Src/App/Helpers/MiscHelper.php'))
    require './Src/App/Helpers/MiscHelper.php';

//Set Error handling
set_error_handler('error_handler',E_ALL ^ (E_DEPRECATED));

if (!defined('DS')) {
    /**
     * Defines DS as short form of DS.
     */
    define('DS', DIRECTORY_SEPARATOR);
}

try {

    if(php_sapi_name() == "cli") {

        $args = arguments($argv);
        $command = null;
        if(empty($args['commands'])) {
            $command = (empty($args['arguments']) ? null : $args['arguments'][0]);
        } else {
            $command = $args['commands'][0];
        }
        
        if($command != "update_db") {
            //Load vendor libraries
            require dirname(__DIR__) . '/vendor/autoload.php';

            //Create new instance of Application, then run it
            $app = \App\Core\Application::getInstance()->prepare();
        }


        if(empty($command)) { throw new \Exception("Command is missing"); }

        $filePath = dirname(__FILE__) . DS . "ims_" . $command . ".php";

        if(!file_exists($filePath)) {
            $filePath = dirname(dirname(__FILE__)) . DS . "Src" . DS . "Ext" . DS . "Console" . DS . "ims_" . $command . ".php";
            
            if(!file_exists($filePath)) {
                throw new \Exception("Command not found"); 
            }
        }
        
        require $filePath;
        
        $method_name = "run_" . $command;
        $method_name($args);

    } else {
        die("GUI is not supported yet");
    }

} catch (PDOException $exc){
    error_handler(null, $exc->getMessage(), $exc->getFile(), $exc->getLine(), $exc->getTrace(), get_class($exc));
} catch (Exception $exc){
    error_handler(null, $exc->getMessage(), $exc->getFile(), $exc->getLine(), $exc->getTrace(), get_class($exc));
} catch (Throwable $exc) {
    error_handler(null, $exc->getMessage(), $exc->getFile(), $exc->getLine(), $exc->getTrace(), get_class($exc));
}

function error_handler($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
    echo "\e[91mError: " . $err_msg . ", File: " . $err_file . ", Line: " . $err_line . "\e[39m";
    print_r($err_context);
}




?>

