<?php

namespace App\Exceptions;

use App\Core\BaseException;

class SendEmailFailureException extends BaseException {
    
    public function __construct($message = null,$code = null,$file = null,$line = null, $trace = null, $extraDetail = null) {
        
        if(!empty($message))
            $this->message = $message;
        
        if(!empty($code))
            $this->code = $code;
            
        if(!empty($file))
            $this->file = $file;

        if(!empty($line))
            $this->line = $line;

        if(!empty($trace))
            $this->trace = $trace;

        if(!empty($extraDetail))
            $this->extraDetail = $extraDetail;
        
        $this->logError = true;
        $this->suppressError = true;
        $this->log_lvl = 1;
        $this->hideDetail = false;
        $this->isSimpleMode = true;
        $this->responseCode = 500;
    }
    
}