<?php 

/*
 * Home controller
 */
namespace App\BgTaskHandlers;

use App\Core\Application;
use App\Core\BgTaskHandlers;

class CoreGviewsExport extends BgTaskHandlers {

    private \App\Core\BgTask $task;

    public function __construct($task){
        parent::__construct();

        $this->task = $task;
        
    }
    


    public function run() {
        
        //load the view object based on the id provided
        $view_data = $this->coreModel->nodeModel("views")
            ->id($this->task->getMainValue())
            ->loadFirstOrFail();
        
        $_POST = $this->task->getPostData();

        $fileName = (new \App\Core\Gviews\Export($this->task->getMainValue(), ["is_bg_task" => true]))->main();
        
        return $fileName;

    }

    public function afterCompletion() {
        
    }
}

