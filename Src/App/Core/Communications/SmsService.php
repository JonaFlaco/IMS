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

class SmsService {

    private $coreModel;

    private ?int $id = null;
    private ?string $to = null;
    private ?string $body = null;

    private ?string $ctype_id = null;
    private ?string $record_id = null;
    private ?string $planned_send_date = null;

    private ?string $user_id = null;

    public function __construct(?string $to = "",?string $body = "") {

        $this->coreModel = Application::getInstance()->coreModel;

        $this->to = $to;
        $this->body = $body;
    }

    
    public function setTo(string $value) {
        $this->to = $value;

        return $this;
    }

    public function setBody(string $value) {
        $this->body = $value;

        return $this;
    }

    public function setCtypeId(string $value) {
        $this->ctype_id = $value;

        return $this;
    }

    public function setRecordId(string $value) {
        $this->record_id = $value;

        return $this;
    }

    public function setPlannedSendDate(string $value) {
        $this->planned_send_date = $value;

        return $this;
    }

    public function setUserId(string $value) {
        $this->user_id = $value;

        return $this;
    }
    
    public function schedule(){

        $data = new \stdClass();
        $data->sett_ctype_id = "sms";
        $data->send_to = $this->to;
        $data->body = $this->body;
        $data->ctype_id = $this->ctype_id;
        $data->record_id = $this->record_id;
        $data->planned_send_date = $this->planned_send_date;

        $this->id = $this->coreModel->node_save($data, array("user_id" => $this->user_id, "dont_clean_input" => true));

    }

    public function sendNow() {

        $this->schedule();
           
        Application::getInstance()->sms->send($this->id, $this->to, $this->body);
    }


}