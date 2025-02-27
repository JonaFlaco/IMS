<?php

/**
 * This class create/removes/checks csrf tokens
 */

namespace App\Core;

use \App\Core\Application;
use App\Exceptions\CsrfException;
use App\Helpers\MiscHelper;

class CsrfProtection {

    private function getFilePath() {
        return CSRF_TOKENS_FOLDER_PATH . DS . session_id() . ".json";
    }

    //This method creates csrf token
    public function create($location = null){
            
        $token = sprintf("%s_%s", 
            time(), 
            MiscHelper::randomString(10)
        );

        if(Application::getInstance()->settings->get('security_create_csrf_per_request') != true){
            return $token;
        }
        
        if(file_exists($this->getFilePath())){
            $obj = json_decode(file_get_contents($this->getFilePath()),true);
        } else {
            $obj = array();
        }
            
        $data = array();

        $data['form'] = $location;
        $data['token'] = $token;
        $data['created_date'] = time();

        $obj[] = $data;

        $json = json_encode($obj);
        
        if(!file_exists(CSRF_TOKENS_FOLDER_PATH)){
            mkdir(CSRF_TOKENS_FOLDER_PATH, 0777, true);
        }

        file_put_contents($this->getFilePath(),$json);

        return $token;
    }


    //This method removes csrf token
    public function remove(){

        if(Application::getInstance()->settings->get('security_create_csrf_per_request') != true){
            return;
        }

        $token = Application::getInstance()->request->getCsrfToken();

        if(file_exists($this->getFilePath())){
            $obj = json_decode(file_get_contents($this->getFilePath()),true);
        } else {
            $obj = array();
        }
        
        foreach($obj as $index => $item){
            
            if($item['token'] == $token){
                unset($obj[$index]);
            }
        }
        
        file_put_contents($this->getFilePath(), json_encode($obj));
    }


    //This method checks csrf token if valid or not
    public function check(){
        
        if(Application::getInstance()->settings->get('security_create_csrf_per_request') != true){
            return true;
        }

        $token = Application::getInstance()->request->getCsrfToken();
        
        if(file_exists($this->getFilePath())){
            $obj = json_decode(file_get_contents($this->getFilePath()),true);
        } else {
            $obj = array();
        }
        
        foreach($obj as $index => $item){
            
            if($item['token'] == $token){
                return true;
            }
        }
        
        //Is invalid
        throw (new CsrfException(CSRF_TOKEN_INVALID_ERROR_MESSAGE))->addExtraDetail("Token: " . $token);
    }


}