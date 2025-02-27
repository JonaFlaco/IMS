<?php

/*
 * SMS class, responsible to send SMS
 */

namespace App\Core\Communications;

use App\Core\Application;
use App\Core\Communications\Sms\ISmsHandler;
use App\Core\Gctypes\Ctype;
use App\Exceptions\CriticalException;

class Sms {

    private $coreModel;
    private ISmsHandler $smsHandler;

    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;

        $sms_provider_class = Application::getInstance()->settings->get("sms_provider_class", "");

        if(!empty($sms_provider_class)) {

            $smsSenderClassName = '\Ext\Communications\SMS\\' . $sms_provider_class;
        
            $this->smsHandler = new $smsSenderClassName;
        }
    }

    public function send($id = null, $params = array()) : ?string {
        
        if(empty(Application::getInstance()->settings->get("sms_provider_class", "")) || Application::getInstance()->settings->get('send_notifications') != 1){
            return true;
        }
        
        $batch_size = intval(Application::getInstance()->settings->get('SMS_BATCH_SIZE'));
        
        if(empty($batch_size)) {
            $batch_size = 1000;
        }

        $sms = $this->coreModel->nodeModel("sms")
            ->limit(intval($batch_size))
            ->id($id)
            ->where("m.status_id = 95")
            ->load();

        $found = 0;

        foreach($sms as $itm){

            if($itm->status_id == 95 && 
                (!isset($itm->planned_send_date) || date_format(date_create($itm->planned_send_date),"d-m-Y") <= date("d-m-Y"))
            ){ 
                
                if($this->smsHandler->send($itm->id, $itm->send_to, $itm->body)){
                    $found++;
                } else {
                    echo "Sending sms to " . $itm->send_to . " failed<br>";
                }
            }

        }
        
        return $found;
    }

}