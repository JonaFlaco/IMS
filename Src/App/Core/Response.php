<?php

/**
 * This request contains all informations and helper classess to response to the request
 */


namespace App\Core;

class Response {

    public static int $CODE_NOT_FOUND = 404;
    public static int $CODE_INTERNAL_ERROR = 500;
    public static int $CODE_SUCCESS = 200;
    public static int $CODE_SUCCESS_WITH_CHANGE = 201;
    public static int $CODE_ACCESS_DENIED = 403;

    public static string $FORMAT_RICH = 'rich';
    public static string $FORMAT_SIMPLE = 'simple';
    public static string $FORMAT_JSON = 'json';

    public static int $MAX_EXECUTION_TIME_DEFAULT = 600;
    public static int $MAX_EXECUTION_TIME_LONG = 3600;
    public static int $MAX_EXECUTION_TIME_TOO_LONG = 86400;


    private ?string $responseFormat;

    public function __construct() {
        $this->setResponseFormat(Application::getInstance()->request->getParam("response_format"));
    }

    //Set status code (404, 403)
    public function statusCode(int $code){
        http_response_code($code);
    }

    //Redirect to a link
    public function redirect($url){   
        echo '<script> location.href=`' . $url . '`; </script>';
        //header("Location: $url");
        exit;
    }

    //Change max execution timeout
    public function setMaxExecutionTime($min = 0){
        
        if(intval($min) == 0){
            $min = self::$MAX_EXECUTION_TIME_DEFAULT;
        }

        ini_set('max_execution_time', $min);
        
    }

    //return success response
    public function returnSuccess(string $message = null) {
        
        if(empty($message)){
            $message = "Task completed successfuly";
        }
    
        $result = (object)[
            "status" => "success",
            "message" => $message
        ];

        return_json($result);        
    }

    public function returnFailed(string $message = null) {
        
        http_response_code(500);

        if(empty($message)){
            $message = "Something went wrong";
        }
    
        $result = (object)[
            "status" => "failed",
            "message" => $message
        ];

        return_json($result);        
    }

    public function returnWarning(string $message = null) {
        
        if(empty($message)){
            $message = "Something went wrong";
        }
    
        $result = (object)[
            "status" => "warning",
            "message" => $message
        ];

        return_json($result);        
    }


    public function setResponseContentTypeAsJson(){
        header('Content-type: application/json; charset=UTF-8');
    }

    //redirect the user to login page
    public function redirectToLogin(){
        
        $url = \App\Core\Application::getInstance()->request->getUrl();
        $params = \App\Core\Application::getInstance()->request->getParams();

        $dest = "";
        if(!empty($url)){

            foreach($url as $itm){
                if(!empty($dest)){
                    $dest .= "/";
                }
                $dest .= $itm ;
            }
        }

        $dest .= "&params=" . json_encode($params);

        \App\Core\Application::getInstance()->response->redirect("/user/login" . (!empty($dest) ? "?destination=/" . $dest : ""));
        exit;
    }


    //return 'Needs login' response
    public function returnNeedsLogin(string $message = null){

        if(empty($message)){
            $message = "You need to login first";
        }


        if(Application::getInstance()->response->getResponseFormat() == Response::$FORMAT_JSON){
            $result = (object)[
                "status" => "faield",
                "type" => "needs_login",
                "message" => $message
            ];

            return_json($result);
        
        } else {
            Application::getInstance()->session->flash("flash_warning", $message);
            $this->redirectToLogin();
        }
        $this->statusCode(403);
        
        exit;
    }

    
    //Set response format
    public function setResponseFormat($format = "rich"){
        $this->responseFormat = $format;
    }

    //Get response format
    public function getResponseFormat() : string {
        return $this->responseFormat ?? "rich";
    }

    //Check is response format is empty
    public function responseFormatIsEmpty() : string {
        return $this->responseFormat == null;
    }

}
