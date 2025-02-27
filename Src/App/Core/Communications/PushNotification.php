<?php

/*
 * PushNotification, this class responsible to send notification to users
 */


namespace App\Core\Communications;

use App\Core\Application;
use App\Core\Gctypes\Ctype;

class PushNotification {

    private $coreModel;

    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;
    }

    /*
     * This method addes notification to database then it will send it to users
     */
    public function add($message,$from_user_id,  $users = null, $roles = null, $ctype_id = null, $record_id = null, $type_id = null, $is_admin_notification = null){

        if($users == null){
            $users = array();
        }
        
        if(isset($roles) && $roles != array()){

            foreach($roles as $role){
                foreach($this->coreModel->getUsersBasedOnRole("'" . $role . "'") as $u){
                    array_push($users, $u->id);
                }
            }
        }
        $users = array_unique($users);
        
        $data = new \stdClass();
        $data->sett_ctype_id = "notifications";
        $data->title = "Notifications";
        $data->message = $message;
        $data->ctype_id = $ctype_id;
        $data->record_id = $record_id;
        $data->type_id = $type_id;
        $data->is_admin_notification = $is_admin_notification;
        $data->from_user_id = (!empty($from_user_id) ? $from_user_id : \App\Core\Application::getInstance()->user->getId());
        $data->to_users = $users;
            
        $this->coreModel->node_save($data);

        
        // foreach($users as $user){

        //     if(\App\Core\Application::getInstance()->globalVar->get("SETTING_SEND_PUSH_NOTIFICATION") == 1){
        //         // try{
        //         //     $redis = new Redis();    
        //         //     $redis->pconnect('localhost',6379);

        //         //     $msg =  array("to_user" => $user, "data" => array("username" => \SessionHelper::get('user_name'),"ctype_id" => $ctype_id, "record_id" => $record_id, "user_full_name" => \SessionHelper::get('user_full_name'), "profile_picture" => \SessionHelper::get('user_profile_picture'), "title" => "Notification", "message" => $message));
        //         //     $redis->publish('channel01', json_encode($msg)); // send message to channel 1.  

        //         //     $redis->close();

        //         // } catch (Exception $e) {
        //         //         //throw $th;
        //         // }
        //     }
        // }
    }

}