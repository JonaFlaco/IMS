<?php 

/*
 * This controller use to create actions
 * can be access as /actions/${FUN_NAME}
 */


namespace App\Controllers;

use App\Core\Controller;
use App\Core\Application;

class Actions extends Controller {

    public function __construct(){

        parent::__construct();
        
        //Check if user is logged in or on local
        $this->app->user->checkAuthentication(true);

    }
    
    /**
     * sendEmail
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function will trigger send email
     */
    public function sendEmail($id, $params){

        //If current user is not admin and not local then show error 403
        if($this->app->user->isAdmin() || $this->app->request->isLocal() || $this->app->request->isCli()){
            Application::getInstance()->email->Send($id);
        } else {
            throw new \App\Exceptions\ForbiddenException();
        }

        
    }




        
    /**
     * sendSMS
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function trigger send SMS
     */
    public function sendSMS($id, $params){

        //If current user is not admin and not local then show error 403
        if($this->app->user->isAdmin() || $this->app->request->isLocal() || $this->app->request->isCli()){
            Application::getInstance()->sms->send($id);
        } else {
            throw new \App\Exceptions\ForbiddenException();
        }

        
    }
}