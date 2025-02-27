<?php

namespace App\Exceptions;

use App\Core\BaseException;

class ForbiddenException extends BaseException {

    public function __construct(string $message = null) {
        
        $this->message = empty($message) ? 'Error 403, access denied' : $message;
        $this->code = 500;
        $this->responseCode = 500;
        $this->logError = false;
        $this->suppressError = false;
        $this->log_lvl = 3;
        $this->hideDetail = false;
        $this->isSimpleMode = true;
        
    }

}