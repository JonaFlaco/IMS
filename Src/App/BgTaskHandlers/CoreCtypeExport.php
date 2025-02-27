<?php 

/*
 * Home controller
 */
namespace App\BgTaskHandlers;

use App\Core\BgTaskHandlers;

class CoreCtypeExport extends BgTaskHandlers {

    private \App\Core\BgTask $task;
    public function __construct(\App\Core\BgTask $task){
        parent::__construct();

        $this->task = $task;
        
    }
    


    public function run() {
        
        //load the view object based on the id provided
        $ctype_id = $this->task->getMainValue();
        
        $_POST = $this->task->getPostData();

        //Send the request to DataExport
        $fileName = \App\Libraries\DataExport::main($ctype_id, ["is_bg_task" => true]);

        return $fileName;

    }


    public function afterCompletion() {
        
    }

}

