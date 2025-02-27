<?php

namespace App\Core\Communications\SMS;

interface ISmsHandler {

    public function send($id, $send_to, $body);

}
