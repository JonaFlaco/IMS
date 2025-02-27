<?php

use App\Core\Application;

function run_run_bg_tasks($args) {

    $task_id = (sizeof($args['arguments']) > 1 ? $args['arguments'][1] : null);
      
    try {

        (new \App\Controllers\BgTasks())->run($task_id);

    } catch (PDOException $exc){
        //App\Core\ErrorHandler::handle($exc->getCode(), $exc->getMessage(), $exc->getFile(), $exc->getLine(), $exc->getTrace(), get_class($exc));
        \App\Core\BgTask::handleError($task_id, $exc);
    } catch (Exception $exc){
        //App\Core\ErrorHandler::handle($exc->getCode(), $exc->getMessage(), $exc->getFile(), $exc->getLine(), $exc->getTrace(), get_class($exc));\
        \App\Core\BgTask::handleError($task_id, $exc);
    } catch (Throwable $exc) {
        //App\Core\ErrorHandler::handle($exc->getCode(), $exc->getMessage(), $exc->getFile(), $exc->getLine(), $exc->getTrace(), get_class($exc));
        \App\Core\BgTask::handleError($task_id, $exc);
    }

    
    
}
