<?php 

namespace App\Crons;

use App\Core\Application;
use App\Core\Communications\EmailService;
use App\Core\Crons\BaseCron;
use App\Core\Crons\BaseSyncOdkForm;
use App\Exceptions\CriticalException;
use App\Helpers\MiscHelper;

class SysCronIssuesNotification extends BaseCron {

    public function run()
    {

        $send_email_to = Application::getInstance()->settings->get("sys_admin_group_email");

        if (empty($send_email_to)) {
            throw new CriticalException("Admin email list is empty");
        }

        $data = $this->coreModel->getCronsTasks(null,null,false,'failed');

        if(sizeof($data) == 0)
            return;

        $forms_list = "";

        $forms_list = "<ol>";
        
        foreach ($data as $item) {

            $forms_list .= sprintf("<li>Form: <strong>%s</strong></li>", $item->name);
            $forms_list .= "<ul>";
            $forms_list .= sprintf("<li>Machine Name: %s</li>", $item->id);
            $forms_list .= sprintf("<li>Is Custom: %s</li>", ($item->is_custom ? "Yes" : "No"));
            $forms_list .= sprintf("<li>Group: %s</li>", $item->group_name);
            $forms_list .= sprintf("<li>Job: %s</li>", $item->job_name);
            $forms_list .= sprintf("<li>Type: %s</li>", $item->type_name);
            $forms_list .= sprintf("<li>Last run: %s</li>", $item->last_run_humanify);
            $forms_list .= sprintf("<li>Last Status: <span style=\"color:red\">%s</span></li>", $item->last_run_status_id);
            $forms_list .= sprintf("<li>Started Count: %s</li>", $item->started_count);
            $forms_list .= sprintf("<li>Failed Count: %s</li>", $item->failed_count);
            $forms_list .= sprintf("<li>Data synced Count: %s</li>", $item->data_synced_count);
            
            $forms_list .= "</ul>";
        }

        
        $forms_list .= "</ol>";

        $body = file_get_contents(APP_EMAIL_TEMPLATE_FOLDER . '\\CronIssuesNotification.html', true);
        $body = _str_replace("{{title}}", "Crons with Issues", $body);
        $body = _str_replace("{{forms_list}}", $forms_list, $body);
        $body = _str_replace("{{apptitle}}", Application::getInstance()->settings->get('APP_TITLE'), $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($send_email_to, "IMS: Cron Issues", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setAttachments($attachments)
            ->sendNow();
        
        Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s form(s) found with issues", sizeof($data)));

    }

}
