<?php

namespace App\Core;

class CurlRequest {

    private $curl;
    private string $url;
    private array $headers = [];
    private string $name;

    public function __construct($name) {
        
        $this->name = $name;

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_HEADER, false);

    }

    public function setUrl(string $value) {
        $this->url = $value;

        return $this;
    }

    public function addHeader(string $value) {
        $this->headers[] = $value;

        return $this;
    }

    public function addHeaders(array $value) {
        $this->headers = array_merge($this->headers, $value);

        return $this;
    }

    public function setOptReturnTransfer(bool $value) {
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $value);

        return $this;
    }

    public function setOptUserAgent(string $value) {
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $value);

        return $this;
    }
    
    public function setOptFollowLocation(bool $value) {
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $value);

        return $this;
    }

    public function setOptUserPwd(string $value) {
        curl_setopt($this->curl, CURLOPT_USERPWD, $value);

        return $this;
    }

    public function setOptSslVerifyPeer(bool $value) {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $value);

        return $this;
    }

    public function setPostData($value) {
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $value);
        curl_setopt($this->curl, CURLOPT_POST, true);

        return $this;
    }


    private function execute() {
        if(!isset($this->url)) {
            throw new \App\Exceptions\CriticalException("Url is missing");
        }

        curl_setopt($this->curl, CURLOPT_URL, $this->url);

        if(!empty($this->headers)) {
            
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        }

        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        
        $response = curl_exec($this->curl);

        $info = (object)curl_getinfo($this->curl);
        
        $error = curl_error($this->curl);

        curl_close($this->curl); 

        return (object)[
            "status" => $info->http_code,
            "info" => $info,
            "response" => $response,
            "error" => $error
        ];
    }

    public function submitAndReturn() {

        $res = $this->execute();
            
        return $res;

        
    }

    public function submit() {

        $res = $this->execute();

        if($res->status == "200" || $res->status == "201") {
            
            return $res;

        } else {
            $message = sprintf("Error in %s", $this->name);
            throw (new \App\Exceptions\CurlException($message))->addExtraDetail($res->response . " " . $res->error);

        }
        
    }

}