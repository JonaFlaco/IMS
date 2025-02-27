<?php 

/*
 * This controller use to create actions
 * can be access as /actions/${FUN_NAME}
 */


namespace App\Controllers;

use App\Core\Controller;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Exceptions\ForbiddenException;

class SurveyManagement extends Controller {

    public function __construct(){

        parent::__construct();

    }
    

    public function fill($survey_id, $params) {

        if(empty($survey_id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Survey ID is missing");
        }

        $this->app->survey->isAuthenticated($survey_id);

        $record_id = (isset($params['record_id']) ? $params['record_id'] : null);

        $surveyObj = $this->coreModel->nodeModel("surveys")->id($survey_id)->loadFirst();
        $ctypeObj = (new Ctype)->load($surveyObj->ctype_id);
        
        $records = [];
        if($surveyObj->type_id == "protected") { 
            $records = $this->coreModel->nodeModel($ctypeObj->id)
                ->fields(["id"])
                ->where("m.survey_credential_id = :cred_id")
                ->bindValue("cred_id", Application::getInstance()->survey->getCredentialId())
                ->load();
        }
        

        $data = [
            "nodeData" => $surveyObj,
            "ctypeObj" => $ctypeObj,
            "record_id" => $record_id,
            "has_previous_records" => sizeof($records) > 0
        ];

        $this->renderView("CustomTpls/surveys", $data);
    }


    public function login($survey_id, $params) {

        if(empty($survey_id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Survey ID is missing");
        }


        $this->app->survey->login($survey_id);
    }

    
    public function register($id, $params) {
        
    }

    public function logout($survey_id, $params) {
        
        if(empty($survey_id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Survey ID is missing");
        }


        $this->app->survey->logout($survey_id);
    }


    public function list($survey_id, $params) {

        if(empty($survey_id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Survey ID is missing");
        }


        $this->app->survey->isAuthenticated($survey_id);

        $surveyObj = $this->coreModel->nodeModel("surveys")->id($survey_id)->loadFirst();
        $ctypeObj = (new Ctype)->load($surveyObj->ctype_id);
        
        if($surveyObj->type_id != "protected") { 
            $this->fill($survey_id, $params);
            exit;
        }

        $records = $this->coreModel->nodeModel($ctypeObj->id)
            ->where("m.survey_credential_id = :cred_id")
            ->bindValue("cred_id", Application::getInstance()->session->get("loggedin_survey_credential_id"))
            ->load();
        
                    
        $title = $surveyObj->name;
        if(!empty($surveyObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()})){
            $title = $surveyObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()};
        }

        $data = [
            "surveyObj" => $surveyObj,
            "ctypeObj" => $ctypeObj,
            "records" => $records,
            'title' => $title
        ];

        $this->renderView("templates/SurveyRecordsList", $data);
    }

    

    public function ServeyRecordsList($survey_id) {

        if(empty($survey_id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Survey ID is missing");
        }


        $surveyObj = $this->coreModel->nodeModel("surveys")->id($survey_id)->loadFirst();
        $ctypeObj = (new Ctype)->load($surveyObj->ctype_id);
        
        $records = $this->coreModel->nodeModel($ctypeObj->id)
            ->where("m.survey_credential_id = :cred_id")
            ->bindValue("cred_id", Application::getInstance()->session->get("loggedin_survey_credential_id"))
            ->load();
        
            
        $title = $surveyObj->name;
        if(!empty($surveyObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()})){
            $title = $surveyObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()};
        }

        $data = [
            "surveyObj" => $surveyObj,
            "ctypeObj" => $ctypeObj,
            "records" => $records,
            'title' => $title
        ];

        $this->renderView("templates/SurveyRecordsList", $data);
    }

    
}