<?php 

namespace App\Triggers\users;

use App\Core\Application;
use App\Core\BaseTrigger;
use App\Exceptions\IlegalUserActionException;

class BeforeSave extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(&$data, $is_update = false){
        
        foreach($data->tables as $table){
            if($table->id == "users_roles") {
                if(!in_array(AUTHENTICATED_USER_ROLE_ID,$table->data)){
                    array_push($table->data, AUTHENTICATED_USER_ROLE_ID);
                }
            }
        }
        
        if(!$is_update)
        {

            $item = $data->tables[0]->data;
            
            if(_strpos($item->email, "@") == false)
                throw new IlegalUserActionException("Invalid email address");
            
            $default_email_domain = Application::getInstance()->env->get("DEFAULT_EMAIL_DOMAIN_NAME");

            if(empty($default_email_domain)) {
                $item->name = $item->email;
            } else {

                $username = _explode("@", $item->email)[0];
                $domain = _explode("@", $item->email)[1];
                
                if(_strtolower($domain) == _strtolower(Application::getInstance()->env->get("DEFAULT_EMAIL_DOMAIN_NAME")))
                    $item->name = $username;
                else
                    $item->name = _str_replace("@", "", $item->email);

            }

        }
    }
}
