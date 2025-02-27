<?php 

/*
 * Home controller
 */
namespace Ext\BgTaskHandlers;

use App\Core\BgTaskHandlers;

class Sample extends BgTaskHandlers {

    private \App\Core\BgTask $task;

    public function __construct($task){
        parent::__construct();

        $this->task = $task;
        
    }
    


    public function run() {
        
    }

    public function afterCompletion() {
        
    }
}

