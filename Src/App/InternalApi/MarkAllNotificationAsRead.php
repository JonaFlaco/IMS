<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;

class MarkAllNotificationAsRead extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();

        $this->app->csrfProtection->check();
    }

    public function index($id, $params = []){

        $notifications = $this->coreModel->getNotifications(null,true);
        foreach($notifications as $obj){
            $this->coreModel->markNotificationAsRead($obj->id);
        }

        $this->app->response->returnSuccess("All notifications marked as read successfuly");
    }
}
