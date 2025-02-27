<?php

namespace Ext\Communications\SMS;

use App\Core\Application;
use App\Core\CurlRequest;
use App\Models\CTypeLog;
use App\Core\Communications\SMS\ISmsHandler;

class Infobip implements ISmsHandler {

    private $coreModel;
    
    public function __construct() {
        $this->coreModel = Application::getInstance()->coreModel;
    }


    public function send($id, $send_to, $body) {

        $data = json_encode([
            'messages' => [
                [
                    'destinations' => [
                        [
                            'to' => $send_to
                        ]
                    ],
                    'from' => 'IOM-IRAQ',
                    'text' => $body
                ]
            ]
        ]);

        $res = (new CurlRequest("Infobip API"))
            ->setUrl("https://5y5mnx.api.infobip.com/sms/1/text/advanced")
            ->addHeaders([
                'Authorization: App ' . Application::getInstance()->env->get("INFOBIP_SMS_API_KEY"),
                'Content-Type: application/json',
                'Accept: application/json'
            ])
            ->setPostData($data)
            ->submit();

        $data = json_decode($res->response, true);

        // Check if the response is success and the message is sent. 
        // 1 stands goup id means the message is sent to the next service provider.
        if($res->status == 200 && $data['messages'][0]['status']['groupId'] == 1){

            // Access the "messageId" property
            $ref_id = $data['messages'][0]['messageId'];

            $this->coreModel->flagSMSAsSent($id, get_class($this), $ref_id);

            (new CTypeLog("sms"))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getSystemUserId())
                ->setJustification("SMS Sent")
                ->setGroupNam("notification_sms")
                ->save();

            return true;

        } else {

            $error = "Failed to send SMS (Id $id). <br>";

            if(isset($res->response)){
                $data = json_decode($res->response, true);

                $error_name = $data['messages'][0]['status']['name'];
                $error_description = $data['messages'][0]['status']['description'];
                
                if(isset($error_name) && isset($error_description)){
                    $error .= " Error Name: " . $error_name . "<br>" . 'Error Description: ' . $error_description;
                }
            }
            
            (new CTypeLog("sms"))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getSystemUserId())
                ->setJustification($error)
                ->setGroupNam("notification_sms")
                ->save();
                
            echo $error;
            exit;
            
            return false;
            
        }
    }
}