<?php

namespace App\Exceptions;

use App\Core\BaseException;

class AuthenticationRequiredException extends BaseException {

    public function __construct(string $message = null) {
        
        $this->message = empty($message) ? 'Error 403, authentication required' : $message;
        $this->code = 403;
        $this->responseCode = 403;
        $this->logError = false;
        $this->suppressError = false;
        $this->log_lvl = 3;
        $this->hideDetail = false;
        $this->isSimpleMode = true;
        
    }

}