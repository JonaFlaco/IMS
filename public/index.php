<?php

/* 
 * Any request to the app will be directed to this file
 * Thie file is responsible to run the app and if there an exception is will handle it and send it to ErrorHandler
 */

//Load vendor libraries
require '../vendor/autoload.php';

try {
    
    //Create new instance of Application, then run it
    $app = \App\Core\Application::getInstance()->prepare()->run();

} catch (PDOException $exc){
    App\Core\ErrorHandler::handle($exc);
} catch (Exception $exc){
    App\Core\ErrorHandler::handle($exc);
} catch (Throwable $exc) {
    App\Core\ErrorHandler::handle($exc);
}
