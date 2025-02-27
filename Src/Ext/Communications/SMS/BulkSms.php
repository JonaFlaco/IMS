<?php

namespace Ext\Communications\SMS;

use App\Core\Application;
use App\Core\CurlRequest;
use App\Models\CTypeLog;
use App\Core\Communications\SMS\ISmsHandler;

class BulkSms implements ISmsHandler {

    private $coreModel;
    
    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;
    }

    /*
     * This methods sends the SMS
     */
    public function send($id, $send_to, $body) {
    
        $data = json_encode(
            [
                [
                    'from' => 'IOMIraq',
                    'to' => $send_to,
                    'body' => $body,
                    'longMessageMaxParts' => 10
                ]
            ]
        );

        $res = (new CurlRequest("BulkSMS API"))
            ->setUrl("https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30")
            ->addHeaders([
                'Content-Type:application/json',
                'Authorization:Basic '. Application::getInstance()->env->get('bulksms_auth_token')
            ])
            ->setPostData($data)
            ->submit();

        if($res->status == 201){

            $ref_id = json_decode($res->response['server_response'])[0]->id;

            $this->coreModel->flagSMSAsSent($id, get_class($this), $ref_id);

            (new CTypeLog("sms"))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getSystemUserId())
                ->setJustification("SMS Sent")
                ->setGroupNam("notification_sms")
                ->save();

            return true;
        } else {


            $error = "Failed to send SMS (Id $id)";
            if(isset($res->response)){
                $data = json_decode($res->response);
                
                if(isset($data->title)){
                    $error .= ": " . $data->title;
                }
                
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