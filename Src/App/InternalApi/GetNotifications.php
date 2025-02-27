<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;

class GetNotifications extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $notifications = $this->coreModel->getNotificationsSummary();

        $return_value = "";
        if($notifications == null || $notifications == array()){ 
            $return_value .= "<h5 class='m-0 text-center'>No Notification Found!</h5>";
        } else {

            $current_date = null;
            $today = $date = date('l, d F Y');
            $yesterday = date('l, d F Y',strtotime("-1 days"));

            foreach($notifications as $notification){
                $date = date_format(date_create($notification->created_date),"l, d F Y");
                if($current_date != $date){
                    $current_date = $date;
                    
                    if($current_date == $today){
                        $date_str = "Today";
                    } else if ($current_date == $yesterday){
                        $date_str = "Yesterday";
                    } else {
                        $date_str = $current_date;
                    }
                    $return_value .= "<h5 class=\"mt-2 mb-0 ms-2 me-2 pb-1 text-center border-bottom border-other\">$date_str</h5>";
                }

                $return_value .= "
                <a href=\"javascript:void(0);\" class=\"dropdown-item notify-item\">
                    <div class=\"notify-icon\">
                        <img src=\"" . $notification->user_profile_picture . "\" class=\"img-fluid rounded-circle\" alt=\"\" />
                    </div>
                    <p class=\"notify-details\"> ";
                        $return_value .= (!empty($notification->icon) ? "<span style=\"display: inline !important;\" class=\"text-$notification->background_color\"><i class=\"$notification->icon\"></i></span>" : "");
                        $return_value .= $notification->user_full_name;
                        $return_value .= ($notification->cnt > 1 ? "    •    $notification->cnt Notifications" : "");
                        $return_value .= ($notification->is_seen != true ? "    •    <span class=\"text-info\" style=\"display: inline !important;\">New</span>" : "");
                        $return_value .= "<span style=\"display: inline !important;\" class=\"float-end\">" . \App\Helpers\DateHelper::humanify( strtotime($notification->created_date)) . "</span></p>
                    
                    <p class=\"text-muted mb-0 user-msg\">" .$notification->message . "</p></a>";
            }
        }

        echo $return_value;
    }
}
