<?php 

namespace App\Crons;

use App\Core\Application;
use App\Core\Crons\BaseCron;

class SysSendEmail extends BaseCron {

    public function run()
    {
       
        $result = Application::getInstance()->email->Send();   

        if($result > 0) {
            Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s email(s) sent", $result));
        }
    }

}
