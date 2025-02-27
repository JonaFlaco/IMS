<?php

namespace App\Crons;

use App\Core\Application;
use App\Core\Communications\EmailService;
use App\Core\Crons\BaseCron;
use App\Core\Crons\BaseSyncOdkForm;
use App\Exceptions\CriticalException;

class SysEmailCtypesLog extends BaseCron
{

    public function run()
    {

        $items = Application::getInstance()->coreModel->getCtypeLogsSummary();

        if (sizeof($items) == 0) {
            return;
        }

        $date = date("D, Y-m-d", strtotime("today"));

        $body = file_get_contents(APP_EMAIL_TEMPLATE_FOLDER . '\\CtypesLogsReport.html', true);

        $tr_detail = "";
		
		$count = 0;
		if(empty($items)) {
			$tr_detail .= "<i>No error found</i>";
		} else {
				
			$tr_detail .= "<table style=\"border: 1px solid black;\">";
			$tr_detail .= "<tr style=\"border-bottom: 1px solid;\"><th>Content-Type</th><th>Title</th><th style=\"margin: 0px 5px 0px 5px !important\">Total</th></tr>";
            $last = null;
			foreach($items as $item) {
                $count += $item->total;
                
                
                $tr_detail .= "<tr style=\"border-bottom: 1px solid;\">";

                $same = $last == $item->ctype_name;
                if(!$same) {
                    $last = $item->ctype_name;
                    $tr_detail .= "<td rowspan=\"$item->items_count\">$item->ctype_name</td>";
                }

                $tr_detail .= "<td>$item->title</td><td style=\"margin: 0px 5px 0px 5px !important\">$item->total</td></tr>";	

			}
			$tr_detail .= "</table>";

		}
			
		$body = _str_replace("{{date}}",$date, $body);
		$body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
		$body = _str_replace("{{tr_detail}}",$tr_detail, $body);
		
		$body = _str_replace("{{title}}", "$date | Content-Types Log Report", $body);
		$body = _str_replace("{{preheader}}", "Content-Types Log Report", $body);
		$attachments = LOGO_FULL_PATH;

		$email = Application::getInstance()->settings->get("sys_admin_group_email");
		

		(new EmailService($email, "$date | Content-Type Log Report", $body))
			->setCtypeId("error_log")
			->setAttachments($attachments)
			->sendNow();

        Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s total count", $count));

    }
}
