<?php

/*
 * This class is the main class in the system.
 * It is the loader of the app and handles all the necessory classes and helpers
 */

namespace App\Core\SystemUpdate;

use App\Core\Application;
use App\Core\Gctypes\CtypeField;
use Exception;

class DataExport {

    private $coreModel;
    private $ctypeObj;
    private $ctypeId;
    private $exportRecords;

    private $result = [];
    private $ATTACHMENT_DIR_NAME = 'attachments';

    public function __construct($ctypeId, $exportRecords = false) {

        if(empty($ctypeId)) {
            throw new Exception("Content-Type name is missing");
        }

        $this->ctypeId = $ctypeId;
        $this->exportRecords = $exportRecords;

        $this->coreModel = Application::getInstance()->coreModel;

        if(!file_exists(SYSTEM_UPDATE_OUTPUT_DIR)) {
            mkdir(SYSTEM_UPDATE_OUTPUT_DIR, 0777, true);
        }

    }

    
    public function main() {

        if($this->exportRecords) {
           return $this->exportRecords();
        }

        //ctypes
        if(!file_exists(SYSTEM_UPDATE_OUTPUT_DIR . DS . "ctypes")) {
            mkdir(SYSTEM_UPDATE_OUTPUT_DIR . DS . "ctypes", 0777, true);
        }

        $data = $this->coreModel->nodeModel("ctypes")
            ->id($this->ctypeId)
            ->where("isnull(m.is_field_collection,0) = 0")
            ->load();

        //Loop throw the ctypes one by one
        foreach($data as $itm){
            $this->exportCtype($itm);
        }
    
        return $this->result;
    }

    private function CtypeExistInOutput($name) {
        
        return file_exists(SYSTEM_UPDATE_OUTPUT_DIR . DS . "ctypes" . DS . $name . ".zip");
    }

    private function exportCtype($itm) {
        
        // if($this->CtypeExistInOutput($itm->name)) {
        //     return;
        // }

        $this->result[] = $itm->name;
        
        $this->ctypeObj = $itm;

        //Create a new Zip class
        $zip = new \ZipArchive();

        //Zip file path
        $filename = SYSTEM_UPDATE_OUTPUT_DIR . DS . "ctypes" . DS . $itm->name . ".zip";

        //Create the file
        if ($zip->open($filename, \ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$filename>\n");
        }

        
        $metaData = new \stdClass();
        $metaData->id = $itm->id;
        $metaData->name = $itm->name;
        $metaData->title = $itm->title;
        $metaData->is_field_collection = $itm->is_field_collection;
        $metaData->description = $itm->description;
        $metaData->module = $itm->module_id_display;
        $metaData->last_update_date = $itm->last_update_date;
        $metaData->view_id = $itm->view_id;
        $metaData->created_user_name = $itm->created_user_id_display;
        $metaData->last_updated_user_name = $itm->updated_user_id_display;
        $metaData->data = [];

        //Put main ctype data into the zip file
        $zip->addFromString($itm->name . ".json", json_encode($itm));

        $metaDataMainItem = new \stdClass();
        $metaDataMainItem->name = $itm->name;
        $metaDataMainItem->type = "main";
        $metaDataMainItem->data = [];

        
        //get the ctype's fields
        $fields = (new CtypeField)->loadByCtypeId($itm->id);
        //Loop throw the fields
        foreach($fields as $field) {

            //If the field is Field-Collection
            if($field->field_type_id == "field_collection") {

                $fcObj = $this->coreModel->nodeModel("ctypes")
                    ->id($field->data_source_id)
                    ->loadFirstOrFail();

                $sub = new \stdClass();
                $sub->name = $field->data_source_table_name;
                $sub->id = $field->data_source_id;
                $sub->is_field_collection = $fcObj->is_field_collection;
                $sub->title = $fcObj->title;
                $sub->created_user_name = $fcObj->created_user_id_display;
                $sub->last_updated_user_name = $fcObj->updated_user_id_display;

                $metaDataMainItem->data[] = $sub;


                $metaDataFcItem = new \stdClass();
                $metaDataFcItem->name = $field->data_source_table_name;
                $metaDataFcItem->id = $field->data_source_id;
                $metaDataFcItem->type = "fc";
                $metaDataFcItem->data = [];

                //get the ctype's fields
                $fcFields = $field->getFields();
                //Loop throw the fields
                foreach($fcFields as $fcField) {
                    
                    if ($fcField->field_type_id == "relation" && object_exist_in_array_of_objects($metaDataFcItem->data,"name", $fcField->data_source_table_name) == false && !empty($fcField->data_source_table_name)) {
                        
                        $cbxObj = $this->coreModel->nodeModel("ctypes")
                            ->id($fcField->data_source_id)
                            ->loadFirstOrFail();

                        $sub = new \stdClass();
                        $sub->id = $fcField->data_source_id;
                        $sub->name = $fcField->data_source_table_name;
                        $sub->is_field_collection = $cbxObj->is_field_collection;
                        $sub->title = $cbxObj->title;
                        $sub->created_user_name = $cbxObj->created_user_id_display;
                        $sub->last_updated_user_name = $cbxObj->updated_user_id_display;
                        
                        $metaDataFcItem->data[] = $sub;

                    }
                }

                $metaData->data[] = $metaDataFcItem;

            } else if ($field->field_type_id == "relation" && object_exist_in_array_of_objects($metaDataMainItem->data,"name", $field->data_source_table_name) == false && !empty($field->data_source_table_name)) {
                
                $cbxObj = $this->coreModel->nodeModel("ctypes")
                    ->id($field->data_source_id)
                    ->loadFirstOrFail();
             
                $sub = new \stdClass();
                $sub->name = $field->data_source_table_name;
                $sub->id = $field->data_source_id;
                $sub->is_field_collection = $cbxObj->is_field_collection;
                $sub->title = $cbxObj->title;
                $sub->created_user_name = $cbxObj->created_user_id_display;
                $sub->last_updated_user_name = $cbxObj->updated_user_id_display;

                $metaDataMainItem->data[] = $sub;
            }

            //TODO: Put event files and SP here

            // //If the ctype a view 
            // if(!empty($itm->view_id)) {
            //     $viewData = $this->coreModel->nodeModel("views")
            //        ->id($itm->view_id)
            //        ->loadFirstOrFail();

            //     $zip->addFromString($viewData->name . ".json", json_encode($viewData));
            // }
        }

        $metaData->data[] = $metaDataMainItem;

        $zip->addFromString("metadata.json", json_encode($metaData));

        $exportedArray = [];

        foreach($metaData->data as $data) {

            foreach($data->data as $sub){
                if(!in_array($data->name, $exportedArray)) {

                    $object = $this->coreModel->nodeModel("ctypes")
                        ->id($sub->name)
                        ->loadFirstOrFail();

                    $zip->addFromString($object->name . ".json", json_encode($object));

                    $exportedArray[] = $sub->name;
                }

            }
        }

        if(isset($itm->view_id)) {
            $viewObj = $this->coreModel->nodeModel("views")
                ->id($itm->view_id)
                ->loadFirstOrFail();

            $zip->addFromString("view.json", json_encode($viewObj));
        }
        
        $zip->close();    
      

        


        foreach($metaData->data as $item) {
            foreach($item->data as $sub) {
                
                if(!$this->CtypeExistInOutput($sub->name)) {
                    
                    $x = $this->coreModel->nodeModel("ctypes")
                        ->id($sub->id)
                        ->loadFirstOrFail();

                    $this->exportCtype($x);
                }
                
            }
        }
        

    }

    private function exportRecords() {
        
        //Export records
        if($this->exportRecords) {

            if($this->ctypeId == "ctypes") {
                return 0;
            }

            if(!file_exists(SYSTEM_UPDATE_OUTPUT_DIR . DS . $this->ctypeId)) {
                mkdir(SYSTEM_UPDATE_OUTPUT_DIR . DS . $this->ctypeId, 0777, true);
            }

            $data = $this->coreModel->nodeModel($this->ctypeId)
                ->load();

            foreach($data as $itm){
            
                //Create a new Zip class
                $zipRecord = new \ZipArchive();

                //Zip file path
                $filename = SYSTEM_UPDATE_OUTPUT_DIR . DS . $this->ctypeId . DS . $itm->id . ".zip";

                //Create the file
                if ($zipRecord->open($filename, \ZipArchive::CREATE)!==TRUE) {
                    exit("cannot open <$filename>\n");
                }

                $zipRecord->addFromString("metadata.json", json_encode($itm));
                
                $this->exportAttachments($zipRecord, $itm);

                $zipRecord->close(); 
            }

            return sizeof($data);
        }
    }

    private function exportAttachments($zipRecord, $itm) {

        $fields = (new CtypeField)->loadByCtypeId($this->ctypeId);
        
        $dirCreated = false;

        foreach($fields as $field) {
            if($field->field_type_id == "media") {

                if($field->is_multi != true) {

                    $source = UPLOAD_DIR_FULL . DS . $this->ctypeObj->name . DS . $itm->{$field->name . "_name"};
                    
                    if(!$dirCreated) {
                        $zipRecord->addEmptyDir($this->ATTACHMENT_DIR_NAME);
                        $dirCreated = true;
                    }

                    $zipRecord->addFile($source, sprintf('%s/%s', $this->ATTACHMENT_DIR_NAME, $itm->{$field->name . "_name"}));

                    
                } else {

                    foreach($itm->{$field->name} as $file) {

                        $source = UPLOAD_DIR_FULL . DS . $this->ctypeObj->name . DS . $file->name;
                        
                        if(!$dirCreated) {
                            $zipRecord->addEmptyDir($this->ATTACHMENT_DIR_NAME);
                            $dirCreated = true;
                        }

                        $zipRecord->addFile($source, sprintf('%s/%s', $this->ATTACHMENT_DIR_NAME, $file->name));
                    }

                }


            }
        }




    }


    public function removeCtype(){
        
        if(file_exists(SYSTEM_UPDATE_OUTPUT_DIR . DS . "ctypes" . DS . $this->ctypeId . ".zip")){
            unlink(SYSTEM_UPDATE_OUTPUT_DIR . DS . "ctypes" . DS . $this->ctypeId . ".zip");
        }

        $this->removeExportedData();
    }


    public function removeExportedData(){
        
        if($this->ctypeId == "ctypes") {
            return;
        }
        
        rrmdir(SYSTEM_UPDATE_OUTPUT_DIR . DS . $this->ctypeId);
            
    }

}