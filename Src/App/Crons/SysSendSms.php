<?php 

namespace App\Crons;

use App\Core\Application;
use App\Core\Crons\BaseCron;

class SysSendSms extends BaseCron {

    public function run()
    {
        
        $result = Application::getInstance()->sms->Send();

        if($result > 0) {
            Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s sms(s) sent", $result));
        }
    }

}
