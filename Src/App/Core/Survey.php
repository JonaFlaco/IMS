<?php

/** 
 * This class handles user related methods and helpers such as login, logout, GetUserId
 */

namespace App\Core;

use App\Core\Gctypes\Ctype;
use App\Exceptions\ForbiddenException;
use App\Models\CoreModel;
use App\Models\UserModel;

class Survey {

    private $app;
    private $coreModel;
    private $userModel;

    public function __construct() {
        $this->app = Application::getInstance();
        $this->coreModel = CoreModel::getInstance();
        $this->userModel = UserModel::getInstance();

    }


    public function login($survey_id){

        if(empty($survey_id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Survey ID is missing");
        }

        $lang = $this->app->request->getParam("lang");

        if(Application::getInstance()->request->isPost()) {
            $post = $this->app->request->POST();
            
            $username = _trim($post['username']);
            $password = $post['password'];

            $surveyObj = $this->coreModel->nodeModel("surveys")->id($survey_id)->loadFirst();
            
            $credRecord = $this->app->coreModel->nodeModel("survey_credentials")
                ->where("m.name = :username")
                ->where("m.password = :password")
                ->where("exists(select * from survey_credentials_surveys s where s.value_id = :survey_id)")
                ->where("isnull(m.is_active,0) = 1")
                ->where("(m.expiration_date is null or convert(date,expiration_date,103) >= convert(date,getdate(),103))")
                ->bindValue("username", $username)
                ->bindValue("password", $password)
                ->bindValue("survey_id", $survey_id)
                ->loadFirstOrDefault();


            if($credRecord == null) {
                Application::getInstance()->session->flash("flash_danger","Incorrect username/password");
                
                Application::getInstance()->response->redirect("/surveymanagement/fill/$survey_id?lang=$lang");
                exit;
            }

            $this->app->survey->setCredentialId($credRecord->id);

            $this->app->survey->setUserName($username);

            
            $ctypeObj = (new Ctype)->load($surveyObj->ctype_id);

            $records = $this->coreModel->nodeModel($ctypeObj->id)
                ->where("m.survey_credential_id = :cred_id")
                ->bindValue("cred_id", Application::getInstance()->survey->getCredentialId())
                ->fields(["id"])
                ->loadFirstOrDefault();
            
            if(($surveyObj->allow_multiple_entry || $surveyObj->allow_edit_record) && $records != null) {
                $this->app->response->redirect("/SurveyManagement/list/" . $survey_id . "?lang=$lang");
            } else {
                $this->app->response->redirect("/SurveyManagement/fill/" . $survey_id . "?lang=$lang");
            }
        } else {
            $this->isAuthenticated($survey_id);
        }

    }

    
    public function logout($id){

        $lang = $this->app->request->getParam("lang");

        $this->app->session->remove("loggedin_survey_credential_id");
        $this->app->session->remove("loggedin_survey_username");

        if(isset($id))
            $this->app->response->redirect("/SurveyManagement/fill/" . $id . "?lang=$lang");
        else
            $this->app->response->redirect("/?lang=$lang");
    }


    public function isAuthenticated($survey_id) {

        if(empty($survey_id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Survey ID is missing");
        }
        
        $surveyObj = $this->coreModel->nodeModel("surveys")->id($survey_id)->loadFirst();
        
        if($surveyObj->type_id == "protected") { 
            
                
            if(Application::getInstance()->user->isAuthenticated()) {
                throw new \App\Exceptions\IlegalUserActionException("You should not open this survey while already logged in to the system with your account");
            }
            

            $loggedin_survey_credential_id = Application::getInstance()->survey->getCredentialId();

            
            
            if(Application::getInstance()->survey->isLoggedIn()) {
                $credData = Application::getInstance()->coreModel->nodeModel("survey_credentials")
                    ->id($loggedin_survey_credential_id)
                    ->loadFirstOrDefault();
            } else {
                $credData = [];
            }
            
            if(Application::getInstance()->survey->isLoggedIn() != true || object_exist_in_array_of_objects($credData->surveys, "value", $surveyObj->id, false) == false) {
                
                $title = $surveyObj->name;
                if(!empty($surveyObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()})){
                    $title = $surveyObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()};
                }
                
                $data = [];
                $data['title'] = $title;
                $data['nodeData'] = $surveyObj;
                Application::getInstance()->view->renderView("templates\SurveyLogin", $data);
                exit;
            }
        }

    }

    public function getUserName() {
        return $this->app->session->get("loggedin_survey_username");
    }
    public function setUserName($value) {
        return $this->app->session->set("loggedin_survey_username",$value);
    }

    public function getCredentialId() {
        return $this->app->session->get("loggedin_survey_credential_id");
    }

    public function setCredentialId($value) {
        return $this->app->session->set("loggedin_survey_credential_id", $value);
    }

    
    public function isLoggedIn() {
        return !empty($this->getCredentialId());
    }

    public function isNotLoggedIn() {
        return !$this->isLoggedIn();
    }


}