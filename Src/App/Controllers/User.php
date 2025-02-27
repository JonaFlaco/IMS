<?php 

/* 
 * This controller will handle all user related requests such as, login, logout, reset, change_pw
 */


namespace App\Controllers;

use \App\Core\Controller;
use \App\Core\Application;

class User extends Controller {

    public function __construct() {
        parent::__construct();
    }
    /**
     * index
     *
     * @return void
     *
     * If user try to access index of user controll, we redirect him to homepage
     */
    public function index(){
        Application::getInstance()->response->redirect('/');
    }




    
    /**
     * login
     *
     * @param  bool $loginUsingAzureAD
     * @return void
     *
     * Login function
     */
    public function login($loginUsingAzureAD = 0){
        Application::getInstance()->user->login($loginUsingAzureAD);
    }


    public function azureCallbackHandler ($id, $params) {
        echo '
        <script> 
            url = window.location.href;
            i = url.indexOf("#");
            if(i > 0) {
                url = url.replace("#","?");
                url = url.replace("azurecallbackhandler","validateloginWithAzureAD");
                window.location.href = url;
            }
        </script>
        ';
    }

    public function validateloginWithAzureAD($id, $params){
        Application::getInstance()->user->validateloginWithAzureAD($params);
    }

    public function loginWithAzureAD($id, $params){
        Application::getInstance()->user->loginWithAzureAD("login");
    }


    /**
     * logout
     *
     * @return void
     *
     * Logout function
     */
    public function logout(){
        Application::getInstance()->user->logout();
    }




        
    /**
     * reset_request
     *
     * @return void
     *
     * This function is request to reset pw
     */
    public function reset_request(){
        Application::getInstance()->user->requestResetPassword();
    }




        
    /**
     * reset
     *
     * @return void
     *
     * This is the reset process function
     */
    public function reset($id, $params){
        Application::getInstance()->user->resetPassword($id, $params);
    }




        
    /**
     * change_pw
     *
     * @return void
     *
     * Change pw function
     */
    public function change_password(){
        Application::getInstance()->user->changePassword();
    }



    
       
    /**
     * setlang
     *
     * @param  int $lang_id
     * @param  array $params
     * @return void
     *
     * This function sets user language
     */
     public function setlang($lang_id, $params){
        
        Application::getInstance()->user->setLang($lang_id);

    }


}
