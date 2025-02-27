<?php

namespace App\Crons;

use App\Core\Application;
use App\Core\Communications\EmailService;
use App\Core\Crons\BaseCron;
use App\Core\Crons\BaseSyncOdkForm;
use App\Exceptions\CriticalException;

class SysCheckOdkFormSizes extends BaseCron
{

    public function run()
    {

        $max_size = Application::getInstance()->settings->get("sys_odk_form_max_size_in_mb", 4000);
        $send_email_to = Application::getInstance()->settings->get("sys_admin_group_email");

        if (empty($send_email_to)) {
            throw new CriticalException("Admin email list is empty");
        }

        $items = Application::getInstance()->coreModel->getCronsTasks('sync_odk_form', 'job_sync_odk_forms', true);

        $forms = [];

        foreach ($items as $item) {
            if ($item->size > intval($max_size) && ($item->download_allowed || $item->submission_allowed)) {
                $forms[] = $item;
            }
        }

        if (sizeof($forms) == 0) {
            return;
        }

        $forms_list = "<ul>";
        foreach ($forms as $form) {

            $forms_list .= sprintf("<li>Form: <strong>%s</strong></li>", $form->name);
            $forms_list .= sprintf("<li>Machine Name: %s", $form->id);
            $forms_list .= sprintf("<li>Version: %s", $form->version);
            $forms_list .= sprintf("<li>Size: <strong>%sMB</strong>", intval($form->size));
            $forms_list .= sprintf("<li>Allow Download: %s", ($form->download_allowed ? "Yes" : "No"));
            $forms_list .= sprintf("<li>Allow Submission: %s", ($form->submission_allowed ? "Yes" : "No"));
            $forms_list .= sprintf("<li>Created User: %s", $form->created_user);
            $forms_list .= sprintf("<li>Is Custom: %s", ($form->is_custom ? "Yes" : "No"));
            $forms_list .= sprintf("<li>Created Date: %s", $form->created_date_humanify);
            $forms_list .= sprintf("<li>Last Submission: %s", $form->last_submission_date_humanify);
            $forms_list .= "</ul>";
        }

        $forms_list .= "</ul>";

        $body = file_get_contents(APP_EMAIL_TEMPLATE_FOLDER . '\\CheckOdkFormSizes.html', true);
        $body = _str_replace("{{forms_list}}", $forms_list, $body);
        $body = _str_replace("{{apptitle}}", Application::getInstance()->settings->get('APP_TITLE'), $body);

        $attachments = LOGO_FULL_PATH;

        (new EmailService($send_email_to, "ODK Forms Status", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setAttachments($attachments)
            ->sendNow();

        Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s form(s) found", sizeof($forms)));
    }
}
