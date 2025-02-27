
<?php

use App\Core\Application;
use App\Exceptions\NotFoundException;

$data = (object)$data; 
$surveyObj = $data->nodeData;
$ctypeObj = $data->ctypeObj;
$record_id = ($surveyObj->type_id == "protected" && isset($data->{"record_id"}) ? $data->record_id : null);

//get start date and end date for the survey
$start_date = new \DateTime($surveyObj->start_date == null ? Date('Y-m-d') : date('Y-m-d',strtotime($surveyObj->start_date)));
$end_date = new \DateTime($surveyObj->end_date == null ? Date('Y-m-d') : date('Y-m-d',strtotime($surveyObj->end_date)));

//if the survey is published
if($surveyObj->status_id != 82){ // 82 => punlished

    if($surveyObj->status_id == 90) {
        $message = "This survey is closed";
    }else{
        $message = "This survey is not published";
    }

    if(Application::getInstance()->user->isAdmin()){
        Application::getInstance()->session->flash('flash_warning',$message);
    } else {
        echo $message;
        exit;
    }

//If the survey is finished
} else if ($start_date > new \DateTime(Date('Y-m-d'))){
    $message = "This survey is not started yet";
    if(Application::getInstance()->user->isAdmin()){
        Application::getInstance()->session->flash('flash_warning', $message);
    } else {
        echo $message;
        exit;
    }

//if the survey is expired
} else if($end_date < new \DateTime(Date('Y-m-d'))){
    $message = "This survey is expired!";
    if(Application::getInstance()->user->isAdmin()){
        Application::getInstance()->session->flash('flash_warning', $message);
    } else {
        echo $message;
        exit;
    }
}


$coreModel = App\Core\Application::getInstance()->coreModel;



if(isset($record_id)) {
    $recordObj = $coreModel->nodeModel($ctypeObj->id)->id($record_id)->fields(["survey_credential_id"])->loadFirstOrDefault();

    if($recordObj == null || $recordObj->survey_credential_id != Application::getInstance()->survey->getCredentialId()) {
        throw new \App\Exceptions\ForbiddenException("Unable to access this object");
    }
}


//check multiple_entry
if (!isset($record_id) && !$surveyObj->allow_multiple_entry && $data->has_previous_records) {
    Application::getInstance()->session->flash("flash_danger","You are allowed to have only one submission");
    Application::getInstance()->response->redirect("/SurveyManagement/list/" . $surveyObj->id);
}

if (isset($record_id) && !$surveyObj->allow_edit_record) {
    Application::getInstance()->session->flash("flash_danger","You are allowed to edit submission");
    Application::getInstance()->response->redirect("/SurveyManagement/list/" . $surveyObj->id);
}

$data = (new \App\Core\Gctypes\AddEditGen($ctypeObj, $record_id, $surveyObj))->generate();

$found = false;
$file =  APP_ROOT_DIR . "\\Views\\Surveys\\" . toPascalCase($surveyObj->id) . ".php";

if(!is_file($file)){
    
    $file =  EXT_ROOT_DIR . "\\Views\\Surveys\\" . toPascalCase($surveyObj->id) . ".php"; 
    
    if(is_file($file)){
        $found = true;
    }
} else {
    $found = true;
}

$title = $surveyObj->name;
if(!empty($surveyObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()})){
    $title = $surveyObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()};
}

$data = [
    'title' => "$title",
    'coreModel' => $coreModel,
    'script' => $data['script'],
    'surveyObj' => $surveyObj
];

if($found){
    Application::getInstance()->view->renderView("surveys/" . toPascalCase($surveyObj->id),$data);
} else {
    Application::getInstance()->view->renderView("surveys/default",$data);
}