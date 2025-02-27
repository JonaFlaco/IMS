<?php

namespace Ext\Communications\SMS;

use App\Core\Application;
use App\Core\CurlRequest;
use App\Models\CTypeLog;
use App\Core\Communications\SMS\ISmsHandler;

class Twilio implements ISmsHandler {

    private $coreModel;
    
    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;
    }

    /*
     * This methods sends the SMS
     */
    public function send($id, $send_to, $body) {
     
        if(empty($send_to))
            throw new \App\Exceptions\MissingDataFromRequesterException("Send to is empty");

        if(empty($body))
            throw new \App\Exceptions\MissingDataFromRequesterException("Body is empty");

        $data = [
            "To" => $send_to,
            "From" => Application::getInstance()->env->get("TWILIO_SMS_MESSAGING_SERVICE_ID"),
            "Body" => $body
        ];

        $curl = new CurlRequest("Twilio SMS");
        $res = $curl->setOptUserPwd(Application::getInstance()->env->get("TWILIO_SMS_API_KEY") . ":" . Application::getInstance()->env->get("TWILIO_SMS_API_SECRET"))
            ->setUrl("https://api.twilio.com/2010-04-01/Accounts/" . Application::getInstance()->env->get("TWILIO_SMS_ACCOUNT_SID") . "/Messages.json")
            ->setPostData(http_build_query($data))
            ->addHeader("Content-Type: application/x-www-form-urlencoded")
            ->submitAndReturn();

        if($res->status == 201){

            $ref_id = json_decode($res->response)->sid;

            $this->coreModel->flagSMSAsSent($id, get_class($this), $ref_id);

            (new CTypeLog("sms"))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getSystemUserId())
                ->setJustification("SMS Sent ($ref_id)")
                ->setGroupNam("notification_sms")
                ->save();

            return true;
        } else {

            $error = "Failed to send SMS (Id $id)";
            if(isset($res->error)){
                $error .= ": " . $res->error;
            }

            if(isset($res->response)){
                $errData = json_decode($res->response);

                $error .= ": ($errData->code) " . $errData->message;
            }
            
            (new CTypeLog("sms"))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getSystemUserId())
                ->setJustification($error)
                ->setGroupNam("notification_sms")
                ->save();
            Application::getInstance()->pushNotification->add($error,Application::getInstance()->user->getSystemUserId(), null, array('admin'), "sms", $id,"warning",true);
            
            echo $error;
            exit;
            
            return false;
        }
    
            
    }
}