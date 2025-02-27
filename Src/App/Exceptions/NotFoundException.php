<?php

namespace App\Exceptions;

use App\Core\BaseException;

class NotFoundException extends BaseException {

    public function __construct(string $message = null) {
        
        $this->message = empty($message) ? 'Error 404, route not found' : $message;
        $this->code = 404;
        $this->responseCode = 404;
        $this->logError = false;
        $this->suppressError = false;
        $this->log_lvl = 3;
        $this->hideDetail = false;
        $this->isSimpleMode = true;
        
    }
}