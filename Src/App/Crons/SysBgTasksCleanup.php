<?php 

namespace App\Crons;

use App\Core\Application;
use App\Core\Crons\BaseCron;
use App\Core\Crons\BaseSyncOdkForm;

class SysBgTasksCleanup extends BaseCron {

    public function run()
    {

        $result = Application::getInstance()->coreModel->cleanUpBgTasks();

        if($result > 0) {
            Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s record(s) deleted", $result));
        }

        

    }

}
