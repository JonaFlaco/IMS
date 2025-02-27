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

class EmailService {

    private $coreModel;
    private IEmailHandler $emailHandler;

    private ?int $id = null;
    private ?string $to = null;
    private ?string $cc = null;
    private ?string $bcc = null;
    private ?string $subject = null;
    private ?string $body = null;

    private ?string $ctype_id = null;
    private ?string $record_id = null;
    private ?string $planned_send_date = null;
    private ?string $attachments = null;

    private ?string $template_id = null;
    private $params = null;

    private ?string $user_id = null;

    public function __construct(?string $to = "",?string $subject = "",?string $body = "") {

        $this->coreModel = Application::getInstance()->coreModel;

        $emailSenderClassName = '\Ext\Communications\Email\\' . Application::getInstance()->settings->get("mail_provider_class", "SendEmailWithPhpMailer");
        $this->emailHandler = new $emailSenderClassName;

        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
    }

    
    public function setTo(string $value) {
        $this->to = $value;

        return $this;
    }

    public function setSubject(string $value) {
        $this->subject = $value;

        return $this;
    }

    public function setBody(string $value) {
        $this->body = $value;

        return $this;
    }

    public function setCc(?string $value) {
        $this->cc = $value;

        return $this;
    }

    public function setBcc(?string $value) {
        $this->bcc = $value;

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

    public function setAttachments(string $value) {
        $this->attachments = $value;

        return $this;
    }

    public function setUserId(string $value) {
        $this->user_id = $value;

        return $this;
    }

    public function setTemplateId(string $value) {
        $this->template_id = $value;

        return $this;
    }

    public function setParam(string $key, string $value) {
        if($this->params == null)
            $this->params = new \stdClass();
        $this->params->{$key} = $value;

        return $this;
    }
    
    public function schedule(){

        $data = new \stdClass();
        $data->sett_ctype_id = "emails";
        $data->email_to = $this->to;
        $data->subject = $this->subject;
        $data->body = $this->body;
        $data->email_cc = $this->cc;
        $data->email_bcc = $this->bcc;
        $data->ctype_id = $this->ctype_id;
        $data->record_id = $this->record_id;
        $data->planned_send_date = $this->planned_send_date;
        $data->attachments = $this->attachments;
        $data->template_id = $this->template_id;
        $data->params = empty($this->params) ? null : json_encode($this->params);

        $this->id = $this->coreModel->node_save($data, array("user_id" => $this->user_id, "dont_clean_input" => true));


        return $this;
    }

    public function sendNow() {

        $this->schedule();
        
        (new Email())->Send($this->id);

        return $this;
    }


    public function getId() {
        return $this->id;
    }


}