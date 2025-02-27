<?php

use App\Core\Application;

function run_run_cron_job($args) {

    $job_id = $args['arguments'][1];

    if(empty($job_id)) {
        throw new \App\Exceptions\MissingDataFromRequesterException("Job ID is missing");
    }
        
    (new \App\Actions\RunCronJobs())->index($job_id, []);

}
