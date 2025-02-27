<?php

namespace App\Crons;

use App\Core\Application;
use App\Core\Communications\EmailService;
use App\Core\Crons\BaseCron;
use App\Core\Crons\BaseSyncOdkForm;
use App\Exceptions\CriticalException;

class SysEmailErrorLogReport extends BaseCron
{

    public function run()
    {

        $items = Application::getInstance()->coreModel->getErrorLogNotification();

        if (sizeof($items) == 0) {
            return;
        }

        $date = date("D, Y-m-d", strtotime("today"));

        $body = file_get_contents(APP_EMAIL_TEMPLATE_FOLDER . '\\ErrorLogReport.html', true);

        $tr_detail = "";
		
		$errorCount = 0;
		if(empty($items)) {
			$tr_detail .= "<i>No error found</i>";
		} else {
				
			$tr_detail .= "<table style=\"border: 1px solid black;\">";
			$tr_detail .= "<tr style=\"border-bottom: 1px solid;\"><th>Title</th><th style=\"margin: 0px 5px 0px 5px !important\">Today</th><th style=\"margin: 0px 5px 0px 5px !important\">Yesterday</th></tr>";
			foreach($items as $item) {
				$errorCount += intval($item->value_today);
                $tr_detail .= "<tr style=\"border-bottom: 1px solid;\"><td>$item->title</td><td style=\"margin: 0px 5px 0px 5px !important\">$item->value_today</td><td style=\"margin: 0px 5px 0px 5px !important\">$item->value_yesterday</td></tr>";	

			}
			$tr_detail .= "</table>";

		}
			
		$body = _str_replace("{{date}}",$date, $body);
		$body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
		$body = _str_replace("{{tr_detail}}",$tr_detail, $body);
		
		$body = _str_replace("{{title}}", "$date | IMS Team Daily Activity Report", $body);
		$body = _str_replace("{{preheader}}", "IMS Team Daily Activity Report", $body);
		$attachments = LOGO_FULL_PATH;

		$email = Application::getInstance()->settings->get("sys_admin_group_email");
		
		(new EmailService($email, "$date | Error Log Report", $body))
			->setCtypeId("error_log")
			->setAttachments($attachments)
			->sendNow();

        Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s error found", $errorCount));

    }
}
