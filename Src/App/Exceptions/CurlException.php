<?php

namespace App\Exceptions;

use App\Core\BaseException;

class CurlException extends BaseException {

    public function __construct(string $message = null) {
        
        $this->message = empty($message) ? 'Curl Exception' : $message;
        $this->code = 500;
        $this->responseCode = 500;
        $this->logError = true;
        $this->suppressError = false;
        $this->log_lvl = 1;
        $this->hideDetail = true;
        $this->isSimpleMode = false;
        
    }
}
