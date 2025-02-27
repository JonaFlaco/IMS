<?php

/**
 * This class contains all information and helpers classes about the request
 */

namespace App\Core;

class Request {

    private array $url = [];
    private array $params = [];

    public function __construct() {
    
        $this->url = $this->loadUrl();
        $this->params = $this->loadParams();
        
    }

    //Get if the request is POST, or GET
    public function getMethod(){
        return _strtolower($_SERVER['REQUEST_METHOD']);
    }

    //Get URL array and change they key to lower case
    public function getUrlAsLowerCase() : array {
        $url = $this->getUrl();

        for($i = 0; $i < sizeof($url); $i++){
            $url[$i] = _strtolower($url[$i]);
        }

        if(empty($url)) {
            $url[0] = "home";
        }
        return $url;
    }
    
    public function getRequestUrl() {
        return $_SERVER['REQUEST_URI'];
    }
    //Get URL array
    public function loadUrl() : array {
        
        if(!empty($_GET['url'])){

            $url = _rtrim($_GET['url'],'/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = _explode('/',$url);
    
            return $url;
        }
        
        return array();

    }

    public function getUrl() : array
    {
        return $this->url;
    }


    //Get URL as string
    public function getUrlStr() : ?string {
        
        if(!empty($_GET['url'])){
            return $_GET['url'];
        }

        return null;;

    }


    //Get parameters
    private function loadParams(){
        
        $data = [];

        foreach($this->GET() as $key => $value){
            $data[$key] = $value;
        }

        return $data;
        
    }

    //Get specific parameter
    public function getParam(string $key) : ?string {

        if(isset($this->params[$key])){
            return $this->params[$key];
        }
        
        return null;
        
    }

    public function getParams() : array
    {
        return $this->params;
    }

    //Check is the request coming from localhost or https? or it is not secure (http)
    function isSecure(){
        
        if($_SERVER['SERVER_PORT'] != 443 || $this->getClientIPAddress() == "::1" || $this->getClientIPAddress() != "127.0.0.1")
            return true;

        if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
            return true;


        return false;
    }


    //Check is the request is GET
    public function isGet(){
        return $this->getMethod() === 'get';
    }

    //Check is the request is POST
    public function isPost(){
        return $this->getMethod() === 'post';
    }

    //Check is the request is DELETE
    public function isDelete(){
        return $this->getMethod() === 'delete';
    }

    //Check is the request is PUT
    public function isPut(){
        return $this->getMethod() === 'put';
    }


    //This method returns CSRF-Token for the request
    public function getCsrfToken() {
        
        foreach(getallheaders() as $name => $value){
            if(_strtolower($name) == "csrf-token"){
                return $value;
            }
        }

        return null;
    }
    
    //Returns GET data
    public function GET() : array {

        $data = [];

        foreach($_GET as $key => $value) {
            
            $key = _strtolower($key);

            //ignore url since we retrive it with get url function
            if($key == "url")
                continue;
            
            $data[$key] = filter_input(INPUT_GET, $key, FILTER_DEFAULT);

            //$data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // $data[$key] = _str_replace('&#34;', '"', $data[$key]);
            // $data[$key] = _str_replace('&#39;', "'", $data[$key]);
            // $data[$key] = _str_replace('&#38;', '&', $data[$key]);
        }
        
        return $data;

    }


    //Returns POST data
    public function POST() : array {

        $data = [];
    
        foreach($_POST as $key => $value) {

            $key = _strtolower($key);

            //$data[$key] = filter_input(INPUT_POST, $key, FILTER_DEFAULT);
            $data[$key] = $value;

            //$data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            
            // $data[$key] = _str_replace('&#34;', '"', $data[$key]);
            // $data[$key] = _str_replace('&#39;', "'", $data[$key]);
            // $data[$key] = _str_replace('&#38;', '&', $data[$key]);
        }
        
        return $data;

    }

    //If the request is get returns GET data, otherwise returns POST data
    public function getBody() : array {
        
        if($this->isGet()) {
            return $this->GET();
        } else {
            return $this->POST();
        }
        
    }


    //Check is the request coming from localhost
    public function isLocal(){
        return $this->getClientIPAddress() == "::1" || $this->getClientIPAddress() == "127.0.0.1";
    }

    public function isCli() {
        return php_sapi_name() == "cli";
    }

    public function getClientIPAddress() {
        // Get real visitor IP behind CloudFlare network
         if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
             $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
             $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
         }
         $client  = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : null;
         $forward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;
         $remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
 
         if(filter_var($client, FILTER_VALIDATE_IP))
         {
             $ip = $client;
         }
         elseif(filter_var($forward, FILTER_VALIDATE_IP))
         {
             $ip = $forward;
         }
         else
         {
             $ip = $remote;
         }
 
         return $ip;
     }

}