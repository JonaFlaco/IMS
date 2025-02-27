<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Models\CTypeLog;

class AddCtypesLog extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();

        $this->app->csrfProtection->check();
    }

    public function index($id, $params = []){
        
        $ctype_id = "";
        if(isset($params["ctype_id"]) && _strlen($params["ctype_id"]) > 0)
            $ctype_id = $params["ctype_id"];
        
        if($id == array()){
            $id = null;
        }
        
        $justification = null;
        if(isset($_POST["justification"]) && _strlen($_POST["justification"])){
            $justification = $_POST["justification"];
        }

        $attachments = null;
        if(isset($_POST["attachments"]) && _strlen($_POST["attachments"])){
            $attachments = json_decode($_POST["attachments"]);
        }

        $parent_log_id = null;
        if(isset($params["reply_to_id"]) && _strlen($params["reply_to_id"])){
            $parent_log_id = $params["reply_to_id"];
        }

        //addLogToDb
        (new CTypeLog($ctype_id))
            ->setContentId($id)
            ->setJustification($justification)
            ->setGroupNam("add")
            ->setIsComment(true)
            ->setParentLogId($parent_log_id)
            ->setAttachments($attachments)
            ->save();

        $this->app->response->returnSuccess();
    }
}
