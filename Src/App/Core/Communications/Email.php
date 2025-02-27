<?php

/*
 * Email class, can be used to send email or schedule it to send it later
 */

namespace App\Core\Communications;

use App\Core\Application;
use App\Core\Communications\Email\IEmailHandler;
use App\Core\Gctypes\Ctype;
use App\Exceptions\SendEmailFailureException;
use App\Helpers\MiscHelper;

class Email {

    private $coreModel;
    private IEmailHandler $emailHandler;

    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;

        $emailSenderClassName = '\Ext\Communications\Email\\' . Application::getInstance()->settings->get("mail_provider_class", "SendEmailWithPhpMailer");
        $this->emailHandler = new $emailSenderClassName;
    }

    /*
     * Send method: prepares the request and send it to sendAction
     */
    public function Send(int $id = null) : ?string{

        $emails = $this->coreModel->nodeModel("emails")
            ->limit(500)
            ->id($id)
            ->where("m.status_id = 95")
            ->where("(planned_send_date is null or convert(date,planned_send_date,103) >= convert(date, getdate(), 103))")
            ->load();

        $found = 0;

        foreach($emails as $itm){
 
            if($itm->status_id == 95 &&
                (!isset($itm->planned_send_date) || date_format(date_create($itm->planned_send_date), "d-m-Y") <= date("d-m-Y"))
            ){

                $itm = $this->cleanParameters($itm);

                $isInvalid = false;
                foreach(_explode(",", $itm->email_to) as $e) {
                    if (!filter_var(_trim($e), FILTER_VALIDATE_EMAIL)) {
                        echo "Invalid email format $e";
                        Application::getInstance()->coreModel->flagEmailAsHasError($itm->id);
                        $isInvalid = true;
                        break;
                    }
                }

                if ($isInvalid) {
                    continue;
                }

                if ($this->sendAction($itm->id, $itm->subject, $itm->body, $itm->email_to, $itm->email_cc, $itm->email_bcc, $itm->attachments, $itm->template_id, $itm->params) == true) {
                    $found++;
                } else {
                    //Failed
                }
            }
        }

        return $found;
    }

    private function cleanParameters($itm) {
        
        $itm->email_to  = array_map(function($v) {
            $v = trim($v);
            $v = _strtolower($v);
            $v = _str_replace(";", ",", $v);
            return $v;
        }, preg_split('/;|,/', $itm->email_to ?? ""));

        $itm->email_cc  = array_map(function($v) {
            $v = trim($v);
            $v = _strtolower($v);
            $v = _str_replace(";", ",", $v);
            return $v;
        }, preg_split('/;|,/', $itm->email_cc ?? ""));
        
        $itm->email_bcc  = array_map(function($v) {
            $v = trim($v);
            $v = _strtolower($v);
            $v = _str_replace(";", ",", $v);
            return $v;
        }, preg_split('/;|,/', $itm->email_bcc ?? ""));
        
        $itm->email_to = array_filter(array_unique($itm->email_to));
        $itm->email_cc = array_filter(array_unique($itm->email_cc));
        $itm->email_bcc = array_filter(array_unique($itm->email_bcc));

        $itm->email_cc = array_diff(array_unique($itm->email_cc),  $itm->email_to);
        $itm->email_bcc = array_diff(array_unique($itm->email_bcc), array_merge($itm->email_to, $itm->email_cc));

        $itm->email_to = implode(',', $itm->email_to);
        $itm->email_cc = implode(',', $itm->email_cc);
        $itm->email_bcc = implode(',', $itm->email_bcc);

        return $itm;

    }

    /*
     * This method responsible to send the email
     */
    private function sendAction($id, $subject, $body, $to, $cc = null, $bcc = null, $attachments = null, $template_id = null, $params = null)
    {

        if (Application::getInstance()->settings->get('send_notifications') != 1) {
            return true;
        }

        $attachments = _str_replace("[PUBLIC_DIR]", PUBLIC_DIR_FULL, $attachments);


        return $this->emailHandler->send($id, $to, $subject, $body, $cc, $bcc, $attachments, $template_id, $params);
    }
}
