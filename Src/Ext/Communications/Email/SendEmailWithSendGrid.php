<?php

namespace Ext\Communications\Email;

use App\Core\Application;
use App\Exceptions\SendEmailFailureException;
use App\Models\CTypeLog;
use App\Core\Communications\Email\IEmailHandler;

class SendEmailWithSendGrid implements IEmailHandler {

    private $coreModel;
    
    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;
    }

    /*
     * This methods sends the SMS
     */
    public function send($id, $to, $subject, $body, $cc, $bcc, $attachments, $template_id, $params) {
        
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(Application::getInstance()->settings->get('MAIL_sendmail_from'), Application::getInstance()->settings->get('MAIL_sendmail_from_name'));
        if(!empty($subject))
            $email->setSubject($subject);
        
        foreach(preg_split('/[,;]/', $to) as $item)
        {
            if(empty($item))
                continue;
            $email->addTo($item, $item);
        }

        foreach(preg_split('/[,;]/', $cc) as $item)
        {
            if(empty($item))
                continue;
            $email->addCc($item, $item);
        }

        foreach(preg_split('/[,;]/', $bcc) as $item)
        {
            if(empty($item))
                continue;
            $email->addBcc($item, $item);
        }

        if(!empty($body)) {
            $email->addContent("text/html", $body);
        } else {
            $email->setTemplateId($template_id);
            $email->addDynamicTemplateDatas((array)json_decode($params));
            
        }
    
        //$email->setReplyTo($to);

        foreach(_explode("\n", $attachments) as $file) {

            if(empty($file)) {
                continue;
            }

            if(is_file($file)){ 
                $fileEncoded = base64_encode(file_get_contents($file));
                $fileName = basename($file);
                $email->addAttachment($fileEncoded, null, $fileName);
            } else {
                echo "Attachment not found: {$file}";
                exit;
            }

        }

        $sendgrid = new \SendGrid(Application::getInstance()->env->get("SENDGRID_API_KEY"), ["verify_ssl" => false]);
        
        try {

            $response = $sendgrid->send($email);

            Application::getInstance()->coreModel->flagEmailAsSent($id, get_class($this));
            
            (new CTypeLog("emails"))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getSystemUserId())
                ->setTitle("Email Sent")
                ->setJustification("Email Sent")
                ->setGroupNam("notification_email")
                ->save();
            return true;
            
        } catch (\Exception $e) {
            
            $exc = new SendEmailFailureException($e, null, null, null, null);
            \App\Core\ErrorHandler::handle($exc);
            
            (new CTypeLog("emails"))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getSystemUserId())
                ->setTitle("Error sending email")
                ->setJustification("Error sending email")
                ->setGroupNam("notification_email")
                ->save();
            return false;
        }

    }

    
}
