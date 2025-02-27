<?php

/** 
 * This class handles user related methods and helpers such as login, logout, GetUserId
 */

namespace App\Core;

use App\Core\Communications\EmailService;
use App\Exceptions\ForbiddenException;
use App\Models\CoreModel;
use App\Models\UserModel;

class User
{

    private $app;
    private $coreModel;
    private $userModel;

    private $guestUserId = null;
    private $systemUserId = null;

    public $ignoreLang = false;

    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->coreModel = CoreModel::getInstance();
        $this->userModel = UserModel::getInstance();

    }


    public function loadSysUsers() {
        
        $this->guestUserId = $this->coreModel->nodeModel("users")
            ->where("m.name = 'guest'")
            ->fields(["id"])
            ->useCache("users.user_id.guest", 86400)
            ->loadFirst()
            ->id;
        
        $this->systemUserId = $this->coreModel->nodeModel("users")
        ->fields(["id"])
        ->where("m.name = 'ims'")
        ->useCache("users.user_id.ims", 86400)
        ->loadFirst()
        ->id;
    }

    //Check if current user is authenticated
    public function checkAuthentication(bool $ignoreLoginOnLocal = false, string $message = null, string $format = null)
    {

        if ($this->app->request->getParam("ignore_login_on_local") != null) {
            $ignoreLoginOnLocal = $this->app->request->getParam("ignore_login_on_local") ?? false;
        }


        if ($this->app->globalVar->get('RESPONSE_TYPE') !== null) {
            $format = $this->app->globalVar->get('RESPONSE_TYPE');
        }

        if (isset($_GET['response_format'])) {
            $format = $_GET['response_format'];
        }

        if (empty($message)) {
            $message = "You need to login first";
        }

        if ($this->isAuthenticated($ignoreLoginOnLocal)) {
            //Authorized

        } else {

            if (_strtolower($format) == "json") {

                $result = (object)[
                    "status" => "faield",
                    "message" => $message
                ];

                return_json($result);
            } else if (_strtolower($format) == "simple") {
                echo $message;
            } else {
                $this->app->response->redirectToLogin();
            }

            exit;
        }
    }


    public function getGuestUserId()
    {
        return $this->guestUserId;
    }

    public function getSystemUserId()
    {
        return $this->systemUserId;
    }


    public function checkLanguage()
    {
        
        $url = $this->app->request->getUrlAsLowerCase();

        if(sizeof($url) >= 3 && ((strtolower($url[0]) == "surveys" && $url[1] == "show") || (strtolower($url[0]) == "surveymanagement"))) {

            $survey_name = $url[2];
            
            $surveyObj = $this->coreModel->nodeModel("Surveys")->id($survey_name)->loadFirstOrFail();
            $lang = !empty($surveyObj->languages) ? $surveyObj->languages[0]->value : null;
            

            if(!empty($this->app->request->getParam("lang")))
            foreach ($surveyObj->languages as $langItem) {

                if( _strtolower($this->app->request->getParam("lang") == $langItem->value))
                    $lang = _strtolower($this->app->request->getParam("lang"));

            }

        } else if ($this->isAuthenticated()) {

            if(!empty($this->app->request->getParam("lang")))
                $lang = _strtolower($this->app->request->getParam("lang"));

        }
        
        if (!empty($lang)) {
            
            $langObj = $this->coreModel->nodeModel("languages")
                ->id($lang)
                ->loadFirstOrDefault();
            
            if (isset($langObj)) {
                $this->ignoreLang = true;
                $this->app->session->set('user_lang_id', $langObj->id);
                $this->app->session->set('user_lang_direction', $langObj->direction);
                $this->app->session->set('user_lang_name', $langObj->name);
            }
            
        } else {

            $this->app->session->set('user_lang_id', "en");
            $this->app->session->set('user_lang_direction', "ltr");
            $this->app->session->set('user_lang_name', "English");
        }

    }




    //Login using basic auth (for API)
    function LoginUsingBasicAuth(): ?object
    {

        $user_model = new \App\Models\UserModel();

        if (!isset($_SERVER['PHP_AUTH_USER']) || _strlen($_SERVER['PHP_AUTH_USER']) == 0 || !isset($_SERVER['PHP_AUTH_PW']) || _strlen($_SERVER['PHP_AUTH_PW']) == 0) {
            throw new ForbiddenException("Access denied");
        }

        $obj = $user_model->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], true);

        if ($obj == null) {
            throw new ForbiddenException("Access denied", "json");
        } else {

            $this->app->user->setSession($obj);

            return $obj;
        }

        return null;
    }


    //Check if current user is logged in (Guest)
    public function isAuthenticated(bool $ignoreLoginOnLocal = false, string $message = null, string $format = null): bool
    {

        if ($this->app->user->getId() != null || ($ignoreLoginOnLocal == true && $this->app->request->isLocal() == true) || $this->app->request->isCli()) {
            return true;
        } else {
            return false;
        }
    }


    //Check if current user id guest (Not logged in)
    public function isGuest()
    {
        return $this->app->user->getId() === null;
    }


    //Return current user's governorates
    public function getUserGovernorates($ctypeId = null, string $action = null, int $userId = null): array
    {

        $result = $this->coreModel->getUserGovernorates($ctypeId, $action, $userId);

        return _explode(",", $result);
    }

    //Return current user's units
    public function getUserUnits($ctypeId = null, string $action = null, int $userId = null): array
    {

        $result = $this->coreModel->getUserUnits($ctypeId, $action, $userId);;

        return _explode(",", $result);
    }


    // //Returns current user's programmes
    // public function getUserProgrammes($ctypeId = null, string $action = null, int $userId = null) : array {

    //     $result = $this->coreModel->getUserProgrammes($ctypeId, $action, $userId);;

    //     return _explode(",", $result);

    // }


    //Returns current user's form types
    public function getUserFormTypes(string $ctypeId = null, string $action = null, int $userId = null): array
    {

        $result = $this->coreModel->getUserFormTypes($ctypeId, $action, $userId);;

        return _explode(",", $result);
    }

    public function checkCtypeExtraPermission($ctypeObj, $recordData, $action = null)
    {
        if(property_exists($recordData,"is_system_object") && $recordData->is_system_object && !Application::getInstance()->user->isSuperAdmin()) {
            throw new ForbiddenException("You don't have permission to work on system objects");
        }

        //If the user is not admin
        if (Application::getInstance()->user->isAdmin() != true) {

            //Check if the ctype has permission by governorate, if so does the user has access to the governorate of the record or not
            if (!empty($ctypeObj->governorate_field_name)) {
                if (!in_array($recordData->{$ctypeObj->governorate_field_name}, Application::getInstance()->user->getUserGovernorates($ctypeObj->id, $action))) {
                    throw new ForbiddenException("You don't have permission to this governorate");
                }
            }

            //Check if the ctype has permission by unit, if so does the user has access to the unit of the record or not
            if (!empty($ctypeObj->unit_field_name)) {
                if (!in_array($recordData->{$ctypeObj->unit_field_name}, Application::getInstance()->user->getUserUnits($ctypeObj->id, $action))) {
                    throw new ForbiddenException("You don't have permission to this Unit");
                }
            }

            //Check if the ctype has permission by form type, if so does the user has access to the form type of the record or not
            if (!empty($ctypeObj->form_type_field_name)) {
                if (!in_array($recordData->{$ctypeObj->form_type_field_name}, Application::getInstance()->user->getUserFormTypes($ctypeObj->id, $action))) {
                    throw new ForbiddenException("You don't have permission to this form type");
                }
            }


        }
    }

    public function requestAccountCreationHandler($params)
    {
        // $json = '{"@odata.context":"https://graph.microsoft.com/v1.0/$metadata#users/$entity","businessPhones":["+9647507987955"],"displayName":"AZEEZ Blnd","givenName":"Blnd","jobTitle":"Senior Data Developer","mail":"BAZEEZ@iom.int","mobilePhone":null,"officeLocation":"Erbil Office","preferredLanguage":null,"surname":"AZEEZ","userPrincipalName":"bazeez@iom.int","id":"286f0ef4-3d45-4e9e-8015-b5685e635055"}';
        // $data = json_decode($json);

        if (!isset($params["access_token"])) {
            throw new \App\Exceptions\AzureADLoginException("Access token is empty");
        }

        $res = (new CurlRequest("Microsoft API (AD Login)"))
            ->setUrl("https://graph.microsoft.com/v1.0/me/")
            ->addHeaders([
                'Authorization: Bearer ' . $params["access_token"],
                'Content-type: application/json',
            ])
            ->submit();

        $l = _explode("\n", $res->response);
        $data = json_decode($l[sizeof($l) - 1]);
        
        if(empty($data->mail) || empty($data->displayName))
        {
            Application::getInstance()->session->flash("flash_warning", "Unable to process your request, due to missing information");
            Application::getInstance()->response->redirect("/");
        }

        $acc = CoreModel::getInstance()->nodeModel("users")
            ->fields(["id", "email"])
            ->where("m.email = :email")
            ->bindValue("email", $data->mail)
            ->loadFirstOrDefault();

        // if($acc != null)
        // {
        //     Application::getInstance()->session->flash("flash_warning", "You already have account, please login. If you forgot your password you can reset it or login using the other methods");
        //     Application::getInstance()->response->redirect("/");
        // }

        $data = [
            "title" => "Request Account Creation",
            "error" => null,
            "info" => (object)[
                "full_name" => $data->displayName,
                "email" => _strtolower($data->mail),
                "position" => $data->jobTitle,
                "phone" => implode(",", $data->businessPhones),
                "note" => null,
                "mission" => null,
                "office" => $data->officeLocation,
                "unit_id" => '',
                "odk_account_required" => 1,
                "governorates" => [],
                "type_of_access" => null
            ]
        ];
        
        $this->app->view->renderView("users/RequestAccountCreation", $data);

    }

    public function loginWithAzureAD()
    {

        $clientId = Application::getInstance()->env->get("AZURE_APP_CLIENT_ID");
        $tennantId = Application::getInstance()->env->get("AZURE_APP_TENNANT_ID");
        $redirectUrl = Application::getInstance()->env->get("AZURE_APP_REDIRECT_URL");
        $login_url = "https://login.microsoftonline.com/" . $tennantId . "/oauth2/v2.0/authorize";

        // session_abort();
        // session_start();

        // $_SESSION['state'] = session_id();

        $this->app->session->set('state', session_id());

        session_commit();

        $params = array(
            'client_id' => $clientId,
            'redirect_uri' => $redirectUrl,
            'response_type' => 'token',
            'scope' => 'https://graph.microsoft.com/User.Read',
            'state' => $_SESSION['state']
        );
        header('Location: ' . $login_url . '?' . http_build_query($params));
    }

    public function validateloginWithAzureAD($params = [])
    {

        if (!isset($params["access_token"])) {
            throw new \App\Exceptions\AzureADLoginException("Access token is empty");
        }

        $res = (new CurlRequest("Microsoft API (AD Login)"))
            ->setUrl("https://graph.microsoft.com/v1.0/me/")
            ->addHeaders([
                'Authorization: Bearer ' . $params["access_token"],
                'Content-type: application/json',
            ])
            ->submit();

        $l = _explode("\n", $res->response);
        $dataFromAD = json_decode($l[sizeof($l) - 1]);
        
        $username = _explode("@", $dataFromAD->mail)[0];


        $error = $this->checkUserForLogin($username, true);
        if (!empty($error)) {
            throw new \App\Exceptions\ForbiddenException($error);
        }

        $logged_in_user = $this->userModel->login($username, null, false);

        if (empty($logged_in_user)) {
            throw new \App\Exceptions\ForbiddenException("$username You don't have account, please contact system administrator to create one for you");
        }

        if ($this->app->settings->get('MAINTENANCE_MODE_IS_ACTIVE') == 1) {

            $is_admin = false;
            foreach ($logged_in_user->roles as $role) {

                if ($role->is_admin == true || $role->is_super_admin == true)
                    $is_admin = true;
            }

            if ($is_admin != true) {
				$data = [];
                $data['error'] = $this->app->settings->get("maintenance_mode_message");

                $this->app->view->renderView('users/Login', $data);
                exit;
            }
        }


        // Create Session
        $this->logUserLogin(true, $logged_in_user->id, $username, 1);
        
        $this->setSession($logged_in_user, false, true, null, null);
    }


    private function checkUserForLogin($username, $loginUsingAzureAD)
    {
        // Check for user/email
        //Get user obj based on email
        $res = $this->coreModel->nodeModel("users")
            ->fields(["id", "full_name", "email", "is_active"])
            ->where("m.name = :name")
            ->bindValue(":name", $username)
            ->limit(1)
            ->loadFirstOrDefault();

        if ($res == null) {
            
            // User not found
            $this->logUserLogin(false, null, $username, $loginUsingAzureAD);
            
            if($loginUsingAzureAD) {
                return "Error! You don't have account, please contact system administrator to create one for you";
            }

            return 'Incorrect username/password';
        } else {

            // User found
            if ($res->is_active != true) {
                $this->logUserLogin(false, null, $username, $loginUsingAzureAD);
                return 'Your account is disabled';
            }
        }

        return null;
    }

    //Login method
    public function login($loginUsingAzureAD)
    {

        //If the user is already logged in the redirect to homepage
        if ($this->isAuthenticated()) {

            $this->app->response->redirect('/');
        }

        // If request type is POST
        if ($this->app->request->isPost()) {

            // Process form
            // Sanitize POST data
            $postData = $this->app->request->getBody();

            // Init data
            $data = [
                'title' => 'Login',
                'username' => _trim($postData['username']),
                'password' => $postData['password'],
                'error' => '',
                'destination' => _trim(isset($postData['destination']) ? $postData['destination'] : null),
                'params' => _trim(isset($postData['params']) ? $postData['params'] : null),
                'loginUsingAzureAD' => $loginUsingAzureAD
            ];

            $username = $data['username'];

            //remove default email domain in username if exist
            if ($this->app->env->get('default_email_domain_name') != null) {
                $username = _str_replace("@" . $this->app->env->get('default_email_domain_name'), "", _strtolower($data['username']));
            }


            // Validate Email
            if (empty($data['username'])) {
                $data['error'] = 'Pleae enter username';
            }

            // Validate pw
            if (empty($data['password'])) {
                $data['error'] = 'Please enter password';
            }


            $data['error'] = $this->checkUserForLogin($username, $loginUsingAzureAD);



            // Make sure errors are empty
            if (empty($data['error'])) {
                // Validated
                // Check and set logged in user

                //try to login the user
                $logged_in_user = $this->userModel->login($username, $data['password'], !$loginUsingAzureAD);

                $access_valid = false;

                //check if login is success
                if ($loginUsingAzureAD && $logged_in_user && $this->loginUsingLdap(1, array("username" => $username, "password" => base64_encode($data['password']))) == "1") {
                    $access_valid = true;
                }

                if ($loginUsingAzureAD != true && $logged_in_user) {
                    $access_valid = true;
                }

                if ($access_valid && $this->app->settings->get('MAINTENANCE_MODE_IS_ACTIVE') == 1) {

                    $is_admin = false;
                    foreach ($logged_in_user->roles as $role) {

                        if ($role->is_admin == true || $role->is_super_admin == true)
                            $is_admin = true;
                    }

                    if ($is_admin != true) {
                        $data['error'] = $this->app->settings->get("maintenance_mode_message");

                        $this->app->view->renderView('users/Login', $data);
                        exit;
                    }
                }

                //check if the user logged in
                if ($access_valid == true) {
                    
                    $this->setSession($logged_in_user, false, true, (isset($postData['destination']) ? $postData['destination'] : ""), (isset($postData['params']) ? $postData['params'] : ""));


                    //If the user is not logged in
                } else {

                    $this->logUserLogin(false, isset($logged_in_user) && isset($logged_in_user->id) ? $logged_in_user->id : null, $data['username'], $loginUsingAzureAD);
                    $data['error'] = 'Incorrect username/password';

                    $this->app->view->renderView('users/Login', $data);
                }

                //If there is error then render login page and and show errors
            } else {

                // Load view with errors
                $this->app->view->renderView('users/Login', $data);
            }


            //If request type is not POST
        } else {
            // Init data
            $error = isset($_GET['needs_login']) && $_GET['needs_login'] == 1 ? 'You have been logged out, please login again' : '';

            $data = [
                'title' => 'Login',
                'error' => $error,
                'username' => null,
                'password' => null,
                'error' => null,
                'destination' => (isset($_GET['destination']) ?  $_GET['destination'] : null),
                'params' => (isset($_GET['params']) ?  $_GET['params'] : null),
            ];

            // Load view
            $this->app->view->renderView('users/Login', $data);
        }
    }



    /**
     * logUserLogin
     *
     * @param  bool $logged_in
     * @param  int $user_id
     * @param  string $user_name
     * @param  bool $loginUsingAzureAD
     * @param  string $api_key
     * @return void
     * 
     * Log user login
     */
    public function logUserLogin($logged_in, $user_id = null, $user_name = null, $loginUsingAzureAD = false)
    {

        $node = new Node("users_login_logs");
        $node->logged_in = $logged_in;
        $node->tried_user_name = $user_name;
        $node->user_id = $user_id;
        $node->browser = get_browser_name();
        $node->ip_address = Application::getInstance()->request->getClientIPAddress();
        $node->login_method = ($loginUsingAzureAD ? "azure_ad" : "local");

        $node->save(array("user_id" => $user_id));
    }


    //Login using LDAP
    private function loginUsingLdap($return = 0, $params = [])
    {

        //Ignore error if happen
        set_error_handler(function () { /* ignore errors */
        });

        $username = isset($params['username']) ? $params['username'] : null;
        $password = isset($params['password']) ? $params['password'] : null;

        $email = $username;
        if (_strpos($email, "@") == false) {
            $email = $email . '@' . $this->app->env->get("LDAP_DOMAIN");
        }

        //connect to ldap
        $ldapconn = ldap_connect($this->app->env->get("LDAP_HOST"), $this->app->env->get("LDAP_PORT"))
            or die("Could not connect to {" . $this->app->env->get("LDAP_HOST") . "}");

        //If connection success
        if ($ldapconn) {

            //try to check for user and pw if valid
            if (!$bind = ldap_bind($ldapconn, $email, base64_decode($password))) {

                //If we need to return value then return otherwise echo the result
                if ($return == 1)
                    return "0";
                else
                    echo "0";
            } else {

                //If we need to return value then return otherwise echo the result
                if ($return == 1)
                    return "1";
                else
                    echo "1";
            }
        }

        ldap_close($ldapconn);

        //retore error handling to normal
        restore_error_handler();
    }


    //Check current user's permission to a ctype
    public function getCtypePermission($ctypeId): object
    {

        if (Application::getInstance()->request->isCli() || $this->app->user->isAdmin()) {

            $value = array(
                "allow_read" => true,
                "allow_edit_only_your_own_records" => false,
                "allow_read_only_your_own_records" => false,
                "allow_delete_only_your_own_records" => false,
                "allow_export_only_your_own_records" => false,
                "allow_edit" => true,
                "allow_delete" => true,
                "allow_add" => true,
                "allow_generic_import_add" => true,
                "allow_generic_import_edit" => true,
                "allow_generic_export" => true,
                "allow_verify" => true,
                "allow_unverify" => true,
                "allow_view_log" => true
            );
        } else {

            $value = $this->app->coreModel->getCtypePermission($ctypeId);

            if (!isset($value)) {
                $value = array(
                    "allow_read" => false,
                    "allow_edit_only_your_own_records" => false,
                    "allow_read_only_your_own_records" => false,
                    "allow_delete_only_your_own_records" => false,
                    "allow_export_only_your_own_records" => false,
                    "allow_edit" => false,
                    "allow_delete" => false,
                    "allow_add" => false,
                    "allow_generic_import_add" => false,
                    "allow_generic_import_edit" => false,
                    "allow_generic_export" => false,
                    "allow_verify" => false,
                    "allow_unverify" => false,
                    "allow_view_log" => false
                );
            }
        }

        return (object)$value;
    }


    //Log out method
    public function logout(bool $startSession = true)
    {

        //If we need to start session first
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        //clear current session
        session_unset();
        session_destroy();


        $getData = $this->app->request->getBody();

        $p = "";
        if (isset($getData['needs_login'])) {
            $p .= "?needs_login=1";
        }

        if (isset($getData['destination'])) {
            if (!empty($p))
                $p .= "&";
            else
                $p .= "?";

            $p .= "destination=" . $getData['destination'];
        }

        //redirect to destination or homepage
        $this->app->response->redirect("/user/login$p");
    }


    //Request Reset pw
    public function requestResetPassword()
    {

        //If the user is logged in already then redirect to homepage
        if ($this->app->user->isAuthenticated()) {
            $this->app->response->redirect('/');
        }

        //If request type is POST
        if ($this->app->request->isPost()) {

            $postData = $this->app->request->getBody();

            $data = [
                'title' => "Reset Password",
                'email' => $postData['email'],
            ];

            //Get user obj based on email
            $user = $this->coreModel->nodeModel("users")
                ->fields(["id", "full_name", "email"])
                ->where("m.email = :email")
                ->bindValue(":email", $data['email'])
                ->limit(1)
                ->loadFirstOrDefault();



            //If result is not valid
            if ($user == null) {

                //render reset_request view
                $data = [
                    'title' => "Reset Password",
                    'email' => $postData['email'],
                    'error' => "Email not found"
                ];

                $this->app->view->renderView('users/ResetPasswordRequest', $data);
            } else {
                //If result is valid

                //generate a random string for ukey
                $ukey = \App\Helpers\MiscHelper::randomString(25);

                //insert pw reset request to db
                $this->addPasswordRequest($user->id, $ukey);

                $this->sendResetPasswordEmail($user, $ukey);

                //render check_mail view
                $this->app->view->renderView('users/ResetPasswordCheckMail', $data);
            }

            //If request type is not POST
        } else {

            $data = array(
                'title' => 'Reset Password'
            );
            $this->app->view->renderView('users/ResetPasswordRequest', $data);
        }
    }

    private function sendResetPasswordEmail($user, $ukey)
    {

        $body = file_get_contents(APP_EMAIL_TEMPLATE_FOLDER . '\\ResetPasswordEmail.html', true);

        $body = _str_replace("{{name}}", $user->full_name, $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{link}}", $this->app->settings->get('APP_URL') . "/user/reset?ukey=$ukey", $body);

        $attachments = LOGO_FULL_PATH;

        //Send email to the user's email
        (new EmailService($user->email, "Reset Password", $body))
            ->setUserId($this->getSystemUserId())
            ->setCtypeId("users")
            ->setRecordId($user->id)
            ->setAttachments($attachments)
            ->sendNow();
    }


    /**
     * addPwRequest
     *
     * @param  int $user_id
     * @param  string $ukey
     * @return void
     *
     * Add pw request record
     */
    private function addPasswordRequest($user_id, $ukey)
    {

        $node = new Node("password_reset_requests");
        $node->ukey = $ukey;
        $node->user_id = $user_id;
        $node->is_used = false;
        $node->save();
    }


    //Reset pw (Action)
    public function resetPassword($id, $params = [])
    {

        //check if user is logged in then redirect to homepage
        if ($this->app->user->isAuthenticated()) {
            $this->app->response->redirect('/');
        }

        //If request type is POST
        if ($this->app->request->isPost()) {

            $postData = $this->app->request->getBody();
            //create a data array
            $data = [
                'title' => 'Reset Password',
                'password' => isset($postData['password']) ? $postData['password'] : null,
                'repassword' => isset($postData['repassword']) ? $postData['repassword'] : null,
                'update_odk_password' => isset($postData['update_odk_password']) ? $postData['update_odk_password'] : null,
                'ukey' => $postData['ukey'],
            ];

            //get reset pw session based on ukey
            $user = $this->userModel->getResetPasswordSession($data['ukey']);

            //if the result is valid
            if (isset($user) && isset($user->id)) {
                $user_id = $user->id;

                //If pw had more than 7 chars
                if (isset($data['password']) && _strlen($data['password']) >= 8) {

                    //if pw and repw are equal
                    if ($data['password'] == $data['repassword']) {

                        //hash the pw
                        $password = password_hash($data['password'], PASSWORD_DEFAULT);

                        //update the user's pw
                        $this->userModel->updatePassword($user_id, $password, $data['ukey']);

                        if (isset($postData["update_odk_password"]) && $postData["update_odk_password"]) {
                            $this->updateOdkAccountsPassword($user->name, $postData['repassword']);
                        }

                        //redirect to login page
                        $this->app->response->redirect('/user/login');
                    } else {
                        $data['error'] = 'Passwords do not match';
                    }
                } else {
                    $data['error'] = 'Password must be 8 chars or more';
                }

                //If the result is not valid
            } else {
                throw new \App\Exceptions\CriticalException("Session expired");
            }

            //render reset interface
            $this->app->view->renderView('users/ResetPassword', $data);


            //If request type is not POST
        } else {

            //If ukey is not set then show error
            if (!isset($params['ukey'])) {
                throw new \App\Exceptions\InvalidSessionException("Invalid session");
            }


            //get reset session based on ukey
            $user = $this->userModel->getResetPasswordSession($params['ukey']);

            //if result is valid
            if (isset($user) && isset($user->id)) {

                // render reset view
                $data = [
                    'title' => 'Reset Password',
                    'ukey' => (isset($params['ukey']) ? $params['ukey'] : ''),
                    'user_full_name' => $user->full_name,
                ];

                $this->app->view->renderView('users/ResetPassword', $data);

                //If result is not valid, show error
            } else {
                throw new \App\Exceptions\InvalidSessionException("Invalid session");
            }
        }
    }


    //Change pw
    public function changePassword()
    {

        $params = $this->app->request->getParams();
        //If the user is not logged in then redirect to login page
        if ($this->app->user->isGuest()) {
            $this->app->response->redirect('/user/login');
        }

        //If the request type is POST
        if ($this->app->request->isPost()) {

            //Make sure response type is json so the front end can handle it
            $this->app->response->setResponseFormat(Response::$FORMAT_JSON);

            // get csrf token
            $csrf_token = null;
            foreach (getallheaders() as $name => $value) {
                if (_strtolower($name) == "csrf-token") {
                    $csrf_token = $value;
                }
            }

            //check if the csrf token is valid
            $this->app->csrfProtection->check($csrf_token);

            $postData = $this->app->request->getBody();

            //get user_id and ask_for_current_pw
            $user_id = null;
            $ask_for_current_password = true;
            if (isset($postData['user_id']) && $postData['user_id'] != "") {
                $user_id = $postData['user_id'];
                $ask_for_current_password = false;
            } else {
                $user_id = $this->app->session->get('user_id');
            }


            $user = $this->coreModel->nodeModel("users")->id($user_id)->loadFirstOrFail();

            //check if current pw is required and if it is correct
            if ($ask_for_current_password == true && (!isset($postData['current_password']) || $postData['current_password'] == "")) {
                throw new \App\Exceptions\PasswordOperationException("Current password is empty");
            }

            //check if new pw is empty
            if (!isset($postData['new_password']) || $postData['new_password'] == "") {
                throw new \App\Exceptions\PasswordOperationException("New password is empty");
            }

            //Check if new pw 2 is empty
            if (!isset($postData['new_password2']) || $postData['new_password2'] == "") {
                throw new \App\Exceptions\PasswordOperationException("Re-type New password is empty");
            }

            //check if the user try to set the same current pw as new pw
            if ($postData['new_password'] == $postData['current_password']) {
                throw new \App\Exceptions\PasswordOperationException("You can not use current password as new password");
            }


            if ($ask_for_current_password) {
                //check if current pasword is correct

                if (!password_verify($postData['current_password'] ?? "", $user->password ?? "")) {
                    throw new \App\Exceptions\PasswordOperationException("Current password is incorrect");
                }
            }


            //check for new pw if it is strong enough
            $uppercase = preg_match('@[A-Z]@', $postData['new_password']);
            $lowercase = preg_match('@[a-z]@', $postData['new_password']);
            $number    = preg_match('@[0-9]@', $postData['new_password']);
            $specialChars = preg_match('@[^\w]@', $postData['new_password']);

            $error = "";
            if (!isset($postData['new_password']) || _strlen($postData['new_password']) < 8) {
                $error .= '<i class="mdi mdi-close-circle me-1"></i> New password is too short<br>';
            }
            if ($uppercase != true) {
                $error .= '<i class="mdi mdi-close-circle me-1"></i> New password must include at least one uppercase charecter.<br>';
            }
            if ($lowercase != true) {
                $error .= '<i class="mdi mdi-close-circle me-1"></i> New password must include at least one lowercase charecter.<br>';
            }
            if ($number != true) {
                $error .= '<i class="mdi mdi-close-circle me-1"></i> New password must include at least one number.<br>';
            }
            if ($specialChars != true) {
                $error .= '<i class="mdi mdi-close-circle me-1"></i> New password must include at least one special charecter.<br>';
            }

            if (!empty($error)) {
                throw new \App\Exceptions\PasswordOperationException($error);
            }

            //check if the new pw length is more than or equal to 8
            if (isset($postData['new_password']) && _strlen($postData['new_password']) >= 8) {

                //check if new pw and new pw 2 is equal
                if ($postData['new_password'] == $postData['new_password2']) {

                    //has the new pw
                    $password = password_hash($postData['new_password'], PASSWORD_DEFAULT);

                    //Update the user's pw
                    $this->userModel->updatePassword($user_id, $password, null);

                    if (isset($postData["update_odk_password"]) && $postData["update_odk_password"]) {
                        $this->updateOdkAccountsPassword($user->name, $postData['new_password']);
                    }

                    $this->app->response->returnSuccess("Password changed successfuly");
                } else {
                    throw new \App\Exceptions\PasswordOperationException("Passwords do not match");
                }
            } else {
                throw new \App\Exceptions\PasswordOperationException("Passwords must be 8 or more charecters");
            }


            //If request type is not POST
        } else {

            $user_obj = null;
            //check if you current user is admin and wants to reset another user's pw
            if ($this->app->user->isAdmin() && isset($params['user_id']) && _strlen($params['user_id']) > 0) {

                //get user info
                $user_obj = $this->userModel->nodeModel("users")
                    ->id($params['user_id'])
                    ->loadFirst("User not found");

                //If it is admin then show error as you should not be able to reset admin pw
                if ($user_obj->id != Application::getInstance()->user->getId() && $this->userModel->checkIfUserIsAdmin($user_obj->id)) {
                    throw new ForbiddenException("Access denied, You can not reset admins password");
                }
            }

            $data = array(
                'title' => 'Change Password',
                'user_obj' => $user_obj
            );
            //render change pw view
            $this->app->view->renderView('users/ChangePassword', $data);
        }
    }

    private function updateOdkAccountsPassword($username, $password)
    {

        $odkDbs = $this->coreModel->getAllOdkDatabases();
        foreach ($odkDbs as $odk) {
            $odkModel = new \App\Models\OdkModel($odk->id);

            $user = $odkModel->getUser($username);

            if (!empty($user)) {
                $odkModel->changeUserPassword($username, $password);
            }
        }
    }

    //Set current user's language
    public function setLang($lang_id)
    {


        if ($this->app->user->isGuest()) {
            $this->app->response->redirect('/');
        }

        if ($lang_id == array()) {
            $id = null;
        }

        $this->userModel->setLang($lang_id);

        $this->app->response->redirect('/');
    }


    public function getId()
    {
        return $this->app->session->get("user_id");
    }

    public function currentUserIdOrOicEqualTo($id)
    {

        return in_array($id, _explode(",", $this->app->session->get("user_oic_of")));
    }

    public function getFullName()
    {
        if ($this->isAuthenticated())
            return $this->app->session->get("user_full_name");
        else
            return null;
    }


    public function getEmail()
    {
        if ($this->isAuthenticated())
            return $this->app->session->get("user_email");
        else
            return null;
    }


    public function getName()
    {
        if ($this->isAuthenticated())
            return $this->app->session->get("user_name");
        else
            return null;
    }

    public function getRoles()
    {
        if ($this->isAuthenticated())
            return $this->app->session->get("user_roles");
        else
            return null;
    }

    public function getProfilePicture()
    {
        if ($this->isAuthenticated())
            return $this->app->session->get("user_profile_picture");
        else
            return null;
    }


    public function getLangId($returnEnAsDefault = false)
    {

        if ($returnEnAsDefault) {
            return $this->app->session->get("user_lang_id") ?? "en";
        }

        if ($this->app->session->get("user_lang_id") == "en")
            return null;
        else
            return $this->app->session->get("user_lang_id");
    }

    public function getLangName()
    {
        return $this->app->session->get("user_lang_name") ?? "English";
    }

    public function getLangDirection()
    {
        if ($this->app->session->get("user_lang_direction") == "rtl")
            return "rtl";
        else
            return "ltr";
    }

    function isAdmin()
    {
        return $this->isAuthenticated() && $this->app->session->get('user_is_admin') === true;
    }

    function isNotAdmin()
    {
        return !$this->isAdmin();
    }

    function isSuperAdmin()
    {
        return $this->isAuthenticated() && $this->app->session->get('user_is_super_admin') === true;
    }

    public function getSecretKey()
    {

        return $this->app->session->get('user_secret_key');
    }

    public function setSecretKey($value)
    {

        return $this->app->session->set('user_secret_key', $value);
    }


    //After successful login, this class be called to set session for the user
    public function setSession($user, $is_using_api_key = false, $redirect = false, $destination = null, $params = null)
    {

        // if(session_status() !== PHP_SESSION_ACTIVE)
        // { 
        //     // Finally, destroy the session.
        //     session_start(); 
        //     session_destroy();
        //     unset($_SESSION);
        //     session_start(); 
        //     session_regenerate_id(true);
        // }

        //If session timeout then logout else update timestamp
        // if (isset($_SESSION['timestamp']) && time() - $_SESSION['timestamp'] > 86400) { //subtract new timestamp from the old one
        //     $this->app->user->logout(false);
        // } else {
        //     $_SESSION['timestamp'] = time(); //set new timestamp
        // }

        if ($this->app->session->exist('timestamp') && time() - $this->app->session->get('timestamp') > 86400) {
            $this->app->user->logout(false);
        }else{
            $this->app->session->set('timestamp', time());
        }



        //get roles, is_admin, is_super_admin for current user
        $roles = "";
        $is_admin = false;
        $is_super_admin = false;
        foreach ($user->roles as $role) {
            if(!empty($roles))
                $roles .= ",";
            $roles .= "$role->role_id";

            if ($role->is_admin == true)
                $is_admin = true;

            if ($role->is_super_admin == true)
                $is_super_admin = true;
        }


        //update session

        $this->app->session->set('user_id', $user->id);
        $this->app->session->set('user_email', $user->email);

        $this->app->session->set('user_oic_of', $user->user_oic_of);
        $this->app->session->set('is_current_user_logged_in_with_api_key', $is_using_api_key);

        $this->app->session->set('user_secret_key', $user->secret_key);

        if(!$this->ignoreLang || empty($this->app->session->get('user_lang_id')))
            $this->app->session->set('user_lang_id', $user->lang_id);

        if(!$this->ignoreLang || empty($this->app->session->get('user_lang_direction')))
            $this->app->session->set('user_lang_direction', $user->lang_direction);

        if(!$this->ignoreLang || empty($this->app->session->get('user_lang_name')))
            $this->app->session->set('user_lang_name', $user->lang_name);


        $this->app->session->set('user_roles', $roles);

        $this->app->session->set('user_is_admin', $is_admin);
        $this->app->session->set('user_is_super_admin', $is_super_admin);

        $this->app->session->set('user_name', $user->name);
        $this->app->session->set('user_full_name', $user->full_name);

        //set profle picture
        if (isset($user->profile_picture_name)) {
            $this->app->session->set('user_profile_picture',  "/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=" . $user->profile_picture_name);
            $this->app->session->set('user_profile_picture_name',  $user->profile_picture_name);
        } else {

            if ($user->gender_id == 2) {
                $this->app->session->set('user_profile_picture', DEFAULT_PROFILE_PICTURE_FEMALE_FULL);
                $this->app->session->set('user_profile_picture_name',  DEFAULT_PROFILE_PICTURE_FEMALE);
            } else {
                $this->app->session->set('user_profile_picture', DEFAULT_PROFILE_PICTURE_MALE_FULL);
                $this->app->session->set('user_profile_picture_name',  DEFAULT_PROFILE_PICTURE_MALE);
            }
        }

        session_write_close();

        if ($redirect) {
            if (!empty($destination)) {

                $params = json_decode($params);

                if (!empty($params) && is_array($params)) {

                    $i = 0;
                    foreach ($params as $key => $value) {
                        $destination .= ($i++ == 0 ? "?" : "&");

                        $destination .= "$key=$value";
                    }
                }

                $this->app->response->redirect($destination);
            } else {
                $this->app->response->redirect('/');
            }
        }
    }


    public function switchToUser($username)
    {
        $user = $this->userModel->getUserForLogin($username);
        $this->setSession($user);
    }
}
