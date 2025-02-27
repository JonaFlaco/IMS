<?php

namespace Ext\Communications\Email;

use App\Core\Application;
use App\Exceptions\SendEmailFailureException;
use App\Helpers\MiscHelper;
use App\Models\CTypeLog;
use PHPMailer\PHPMailer\PHPMailer;
use App\Core\Communications\Email\IEmailHandler;

class SendEmailWithPhpMailer implements IEmailHandler {

    private $coreModel;
    
    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;
    }

    /*
     * This methods sends the SMS
     */
    public function send($id, $to, $subject, $body, $cc, $bcc, $attachments, $template_id, $params) {
        
        
        $mail = $this->loadMailSettings();
        
        $mail->SMTPDebug  = 0;

        $fromEmail = Application::getInstance()->settings->get('MAIL_sendmail_from');
        $fromName = Application::getInstance()->settings->get('APP_TITLE');

        $mail->From = $fromEmail;
        $mail->FromName = $fromName;

        $mail->addReplyTo($fromEmail);

        foreach(preg_split('/[,;]/', $to) as $item)
            $mail->addAddress($item);

        if(!empty($cc))
            foreach(preg_split('/[,;]/', $cc) as $item)
                $mail->addCC($item);

        if(!empty($bcc))
            foreach(preg_split('/[,;]/', $bcc) as $item)
                $mail->addBCC($item);
        
        $mail->addReplyTo($to);

		$mail->addCustomHeader( 'In-Reply-To', '<' . $fromEmail . '>' );

        $mail->Subject = $subject;
        
        $mail->Body = $body;

        $mail->isHTML(true);

        foreach(_explode("\n", $attachments) as $file) {

            if(empty($file)) {
                continue;
            }

            if(is_file($file)){ 
                $mail->addAttachment($file);
            } else {
                echo "Attachment not found: {$file}";
                exit;
            }

        }



        if($mail->send()){
            Application::getInstance()->coreModel->flagEmailAsSent($id, get_class($this));
            
            (new CTypeLog("emails"))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getSystemUserId())
                ->setTitle("Email Sent")
                ->setJustification("Email Sent")
                ->setGroupNam("notification_email")
                ->save();
            return true;
        }else{
            $exc = new SendEmailFailureException($mail->ErrorInfo, null, null, null, null);
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


    private function loadMailSettings() {
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer();

        $mail->isSMTP();
            
        $mail->Host = Application::getInstance()->settings->get('MAIL_SMTP');
        $mail->SMTPAuth = false;
        $mail->Username = Application::getInstance()->settings->get('MAIL_username');
        $mail->Password = Application::getInstance()->settings->get('MAIL_password');
        $mail->SMTPSecure = 'tls';
        $mail->Port = Application::getInstance()->settings->get('MAIL_smtp_port');

        $mail->CharSet = 'UTF-8';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ];

        return $mail;

    }
    
}
