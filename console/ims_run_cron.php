<?php

use App\Core\Application;

function run_run_cron($args) {

    $cron_id = $args['arguments'][1];

    $batch_size = sizeof($args['arguments']) > 1 && !empty($args['arguments'][2]) ? $args['arguments'][2] : 1;
       
    (new \App\Actions\RunCron())->index($cron_id, ["batch_size" => $batch_size]);

}
