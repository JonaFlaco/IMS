<?php 

namespace App\Crons;

use App\Core\Application;
use App\Core\Crons\BaseCron;

class SysFixStuckBgTasks extends BaseCron {

    public function run()
    {
        $result = Application::getInstance()->coreModel->fixStuckBgTasks();

        if($result > 0) {
            Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s task(s) fixed", $result));
        }
    }

}
