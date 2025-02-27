<?php

namespace Ext\Communications\Email;

use App\Core\Application;
use App\Exceptions\SendEmailFailureException;
use App\Helpers\MiscHelper;
use App\Models\CTypeLog;
use App\Core\Communications\Email\IEmailHandler;

class DefaultEmailSender implements IEmailHandler {

    private $coreModel;
    
    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;
    }

    /*
     * This methods sends the SMS
     */
    public function send($id, $to, $subject, $body, $cc, $bcc, $attachments, $template_id, $params) {
        
        $this->loadMailSettings();

        $fromEmail = Application::getInstance()->settings->get('MAIL_sendmail_from');
        $fromName = Application::getInstance()->settings->get('APP_TITLE');


        $returnpath = "";

        $subject = \App\Helpers\MiscHelper::removeNewline($subject);

        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers[] = 'To: ' . \App\Helpers\MiscHelper::removeNewline($to);
        $headers[] = "From: {$fromName} <{$fromEmail}>";
        
        
        if(isset($cc) && _strlen($cc) > 0){
            $headers[] = 'CC: ' . \App\Helpers\MiscHelper::removeNewline($cc);
        }

        if(isset($bcc) && _strlen($bcc) > 0){
            $headers[] = 'BCC: ' . \App\Helpers\MiscHelper::removeNewline($bcc);
        }

        if(empty($attachments)){
            $headers[] = 'Content-Type: text/html; charset=UTF-8';

            $headers = implode("\r\n", $headers);
        } else {
            $headers[] = 'Content-Type: multipart/mixed;';

            // Boundary  
            $semi_rand = sprintf("%s_%s", 
                time(), 
                MiscHelper::randomString(5)
            );

            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  

            // Headers for attachment  
            $headers[] = " boundary=\"{$mime_boundary}\"";

            $headers = implode("\r\n", $headers);
            
            $body = [
                "--{$mime_boundary}",
                "Content-Type: text/html; charset=UTF-8",
                "Content-Transfer-Encoding: 7bit",
                "",
                $body,
            ];
            
            foreach(_explode("\n", $attachments) as $file) {

                if(empty($file)) {
                    continue;
                }

                // Preparing attachment 
                if(!empty($file)){ 
                    if(is_file($file)){ 
                        
                        $body[] = "--{$mime_boundary}";

                        $fp =    @fopen($file,"rb"); 
                        $data =  @fread($fp,filesize($file)); 
                
                        @fclose($fp); 
                        
                        $body[] = "Content-Type: application/octet-stream; name=\"".basename($file) . "\"";
                        $body[] = "Content-Description: ". basename($file);
                        $body[] = "Content-Disposition: attachment;";
                        $body[] = "filename=\"" . basename($file) . "\"; size=" . filesize($file) . ";";
                        $body[] = "Content-Transfer-Encoding: base64";
                        $body[] = "";
                        $body[] = chunk_split(base64_encode($data));

                    } else {
                        echo "Attachment not found: {$file}";
                        exit;
                    }
                }

            }

            $body[] .= "--{$mime_boundary}--"; 

            $body = implode("\r\n", $body);
            

            $returnpath = "-f" . $fromEmail; 
            
        }



        try {
            
            if(mail($to,$subject,$body, $headers, $returnpath)){

                Application::getInstance()->coreModel->flagEmailAsSent($id, get_class($this));

                (new CTypeLog("emails"))
                    ->setContentId($id)
                    ->setUserId(Application::getInstance()->user->getSystemUserId())
                    ->setTitle("Email Sent")
                    ->setJustification("Email Sent")
                    ->setGroupNam("notification_email")
                    ->save();
                return true;
            } else {
                
                (new CTypeLog("emails"))
                    ->setContentId($id)
                    ->setUserId(Application::getInstance()->user->getSystemUserId())
                    ->setTitle("Error sending email")
                    ->setJustification("Error sending email")
                    ->setGroupNam("notification_email")
                    ->save();
                return false;
            }
                    
        } catch (\Exception $exc){
            $exc = new SendEmailFailureException($exc->getMessage(), $exc->getCode(), $exc->getFile(), $exc->getLine(), $exc->getTrace());
            \App\Core\ErrorHandler::handle($exc);
            return false;
        }
        
        
    }

    
    //This method loaded mail settings
    private function loadMailSettings() {
        
        //Mail Config
        ini_set('SMTP', Application::getInstance()->settings->get("MAIL_SMTP"));
        ini_set('smtp_port', Application::getInstance()->settings->get("MAIL_smtp_port"));
        ini_set('sendmail_from', Application::getInstance()->settings->get("MAIL_sendmail_from"));
        ini_set('username', Application::getInstance()->settings->get("MAIL_username"));
        ini_set('password', Application::getInstance()->settings->get("MAIL_password"));
   }
    
}