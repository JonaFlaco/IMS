<?php

namespace App\Core\Communications\Email;

interface IEmailHandler {

    public function send($id, $to, $subject, $body, $cc, $bcc, $attachments, $template_id, $params);

}
