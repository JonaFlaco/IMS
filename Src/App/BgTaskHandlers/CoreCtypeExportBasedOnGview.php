<?php 

/*
 * Home controller
 */
namespace App\BgTaskHandlers;

use App\Core\BgTaskHandlers;

class CoreCtypeExportBasedOnGview extends BgTaskHandlers {

    private \App\Core\BgTask $task;
    public function __construct(\App\Core\BgTask $task){
        parent::__construct();

        $this->task = $task;
        
    }
    


    public function run() {
       
        //load the view object based on the id provided
        $view_data = $this->coreModel->nodeModel("views")
            ->id($this->task->getMainValue())
            ->loadFirstOrFail();
        
        $_POST = $this->task->getPostData();

        //based on the filters we received in $_POST we ask to get ids of the records to export and put it inside $_POST
        $_POST["selected_ids"] = \App\Models\Sub\LoadViewData::getIds($view_data, $_POST);
        
        //Send the request to DataExport
        $fileName = \App\Libraries\DataExport::main($view_data->ctype_id, ["is_bg_task" => true]);

        return $fileName;

    }


    public function afterCompletion() {
        
    }

}

