<?php

namespace App\Exceptions;

use App\Core\BaseException;

class AzureADLoginException extends BaseException {

    public function __construct(string $message = null) {
        
        $this->message = empty($message) ? 'Something went wrong while login' : $message;
        $this->code = 500;
        $this->responseCode = 500;
        $this->logError = true;
        $this->suppressError = false;
        $this->log_lvl = 1;
        $this->hideDetail = false;
        $this->isSimpleMode = true;
        
    }
}
