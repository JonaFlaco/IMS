<?php 

/*
 * Home controller
 */
namespace App\BgTaskHandlers;

use App\Core\BgTaskHandlers;

class CoreGimport extends BgTaskHandlers {

    private \App\Core\BgTask $task;

    public function __construct($task){
        parent::__construct();

        $this->task = $task;
        
    }
    


    public function run() {
        
        // if(!$this->ask->hasInputFile()) {
        //     throw new \App\Exceptions\MissingDataFromRequesterException("Input file missing");
        // }

        // $file_full_path = UPLOAD_DIR_FULL . DS . "bg_tasks" . DS . $this->task->getInputFileName();

        
        // if(!file_exists($file_full_path)) {
        //     throw new \App\Exceptions\FileNotFoundException("File not found");
        // }

        // (new \App\Controllers\Dataimport())->import($file_full_path);

    }

    public function afterCompletion() {
        
    }
}

