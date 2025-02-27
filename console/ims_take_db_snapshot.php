<?php

use App\Core\Application;
use App\Core\Response;

function run_take_db_snapshot($args) {

    $g_before = microtime(true);

    $is_silent = in_array('s', $args['flags']);
    $include_ext = isset($args['options']['include_ext']) && $args['options']['include_ext'] == "true";
    
    process($include_ext, $is_silent);

    $g_after = microtime(true);
    echo "\e[92m> Take database snaphsot finished successfuly! (Elapsed time: " . sprintf('%0.2fs', $g_after - $g_before) . ")\e[39m\n";
    exit;


}

function process($include_ext, $is_silent) {

    $app = Application::getInstance();
    $app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_TOO_LONG);

    $res = null;
    $valid_input = $is_silent;
    $attempt_no = 0;
    while(!$valid_input) {

        $include_ext_str = ($include_ext ? "including EXT" : "excluding EXT");
        echo "> Are you sure you want take db snapshot from \e[93m" . $app->env->get("DB_NAME") . "\e[39m on \e[93m" . $app->env->get("DB_HOST") . "\e[39m (\e[93m$include_ext_str\e[39m)? [Y/N] (default: Y):";
        $res = _trim(fgets(STDIN));

        if(_strlen($res) == 0 || _strtolower($res) == "y" || _strtolower($res) == "n") {
            $valid_input = true;
        } else {

            if($attempt_no > 5) {
                echo "\e[91m> Too many invalid attempt, cancelling the operation\e[39m\n";
                exit;
            }

            echo "\e[91m> Invalid input, please try again\e[39m\n";

            $attempt_no++;
        }
    }


    if(_strlen($res) == 0 || _strtolower($res) == "y") {
    } else {
        echo "\e[91m> Operation aborted by the user\e[39m\n";
        exit;
    }

    echo "> Taking snapshot of (\e[93m" . $app->env->get("DB_NAME") . "\e[39m) on (\e[93m" . $app->env->get("DB_HOST") . ")\e[39m started\n";

    (new App\core\DbSnapshot($include_ext, APP_ROOT_DIR . DS . "Core" . DS . "DbSnapshot" . DS))->take();

    //echo "\e[92mSuccess\e[39m\n";
}

