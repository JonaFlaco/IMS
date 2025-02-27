<?php

namespace App\Triggers\Base;

use App\Core\Application;
use App\Core\BaseTrigger;
use App\Exceptions\IlegalUserActionException;

class BeforeSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($data, $is_update = false)
    {

        $survey_id = Application::getInstance()->request->getParam("survey_id");
        $survey_credential_id = Application::getInstance()->survey->getCredentialId();

        $ctypeId = $data->tables[0]->id;

        if (!$is_update && $survey_id != null && $survey_credential_id != null) {

            $surveyObj = $this->coreModel->nodeModel("surveys")->id($survey_id)->loadFirst();
            if ($surveyObj->type_id == "protected" && $surveyObj->ctype_id == $ctypeId) {

                $records = $this->coreModel->nodeModel($ctypeId)
                    ->fields(["id"])
                    ->where("m.survey_credential_id = :cred_id")
                    ->bindValue("cred_id", Application::getInstance()->survey->getCredentialId())
                    ->load();

                if (!$surveyObj->allow_multiple_entry && sizeof($records) > 0) {
                    throw new IlegalUserActionException("This survey allow only one submission");
                }
            }

            $data->tables[0]->data->survey_credential_id = $survey_credential_id;
        }

        if (!$is_update && empty($data->tables[0]->data->ip_address)) {
            $data->tables[0]->data->ip_address = Application::getInstance()->request->getClientIPAddress();
        }

        if(in_array($ctypeId, [
            "ctypes", 
            "field_types", 
            "ctypes_categories", 
            "status_workflow_templates",
            "file_extension_types",
            "views",
            "crons",
            "crons_log_types",
            "roles",
            "menu",
            "settings",
            "text_align_types",
            "widgets",
            "dashboards",
            "documents",
            "surveys",
            "pages",
            "languages",
            "modules",
            "filter_operators",
            "notification_types",
            "db_connection_strings",
            "documentations",
            "field_type_appearances",
            "crons_types",
            "crons_jobs",
            ]))
        {
            $data->tables[0]->data->id = get_machine_name($data->tables[0]->data->id);
        }

    }
}
