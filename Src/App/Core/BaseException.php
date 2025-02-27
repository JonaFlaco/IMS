<?php

namespace App\Core;

class BaseException extends \Exception {

    protected $message = '';
    protected $code = 500;
    protected $logError = true;
    protected $suppressError = false;
    protected $log_lvl = 0;
    protected $extraDetail = null;
    protected $hideDetail = true;
    protected $isSimpleMode = false;
    protected $responseCode = 500;

    public function __construct(string $message = null) {
        
        if(!empty($message))
            $this->message = $message;
    }
    
    public function addExtraDetail($value) {
        
        $this->extraDetail = $value;

        return $this;
    }

    public function getExtraDetail() {
        return $this->extraDetail;
    }

    public function getLogError() {
        return $this->logError;
    }

    public function getSuppressError() {
        return $this->suppressError;
    }

    public function getLogLevel() {
        return $this->log_lvl;
    }

    public function getHideDetail() {
        return $this->hideDetail;
    }

    public function getIsSimpleMode() {
        return $this->isSimpleMode;
    }

    public function getResponseCode() {
        return $this->responseCode;
    }

}
