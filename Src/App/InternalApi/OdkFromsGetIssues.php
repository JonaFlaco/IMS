<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\NodeModel;
use Exception;
use Throwable;

class OdkFromsGetIssues extends BaseInternalApi {

    private $cronObj;
    private $ctypeObj;
    private $maxTableNameLenght = 64;

    public function __construct(){

        parent::__construct();

        if($this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }

        
    }

    public function index($id, $params = []){
        
        $this->cronObj = (new NodeModel("crons"))->id($id)->loadFirstOrFail();
        
        $this->ctypeObj = (new NodeModel("ctypes"))->id($this->cronObj->ctype_id)->loadFirstOrFail();

        $resultList = [];

        if(strlen($this->cronObj->id) > $this->maxTableNameLenght)
            $resultList[] = (object)[
                "detail" => sprintf("Form name too long (%s)", $this->cronObj->id),
                "type" => "table_name_length"
            ];

        foreach($this->cronObj->fields as $cronField) {
           $resultList = $this->process($resultList, $cronField, $this->ctypeObj->fields);
        }

        foreach($this->cronObj->field_collections as $cronField) {

            if($cronField->data_type == "image" && $cronField->tag == "image_repeat")
                continue;
            
            
            if(strlen($this->cronObj->id . "_" . $cronField->repeat_name) > $this->maxTableNameLenght)
                $resultList[] = (object)[
                    "detail" => sprintf("Form name too long (%s)", $this->cronObj->id . "_" . $cronField->repeat_name),
                    "type" => "table_name_length"
                ];
                
            try {
                $fieldCollectionObj = (new NodeModel("ctypes"))->id($this->ctypeObj->id . "_" . $cronField->gc_fc_name)->loadFirstOrFail();
            } catch (NotFoundException $exc){
                $resultList[] = (object)[
                    "detail" => sprintf("Name (%s)", $this->cronObj->id . "_" . $cronField->gc_fc_name),
                    "type" => "Field collection not found"
                ];
            } catch (Exception $exc){
                error_handler(null, $exc->getMessage(), $exc->getFile(), $exc->getLine(), $exc->getTrace(), get_class($exc));
            } catch (Throwable $exc) {
                error_handler(null, $exc->getMessage(), $exc->getFile(), $exc->getLine(), $exc->getTrace(), get_class($exc));
            }
            
            $resultList = $this->process($resultList, $cronField, $fieldCollectionObj->fields);
         }

        $result = (object)[
            "status" => "success",
            "result" => $resultList,
        ];

        return_json($result);

    }


    private function process($resultList, $cronField, $ctypeFields) {
        $cronField->found = false;
        $cronField->field_type_id = null;
        $cronField->data_type_correct = false;

        if(
            (
                $cronField->data_type == "select_multiple" ||
                (in_array($cronField->data_type, ["image", "file"]) && $cronField->tag = "image_repeat")
            ) &&
                strlen($this->cronObj->id . "_" .  $cronField->odk_name) > $this->maxTableNameLenght
            ) {
                $resultList[] = (object)[
                    "detail" => sprintf("Table name too long (%s)", $this->cronObj->id . "_" . $cronField->odk_name),
                    "type" => "table_name_length"
                ];
            }

        foreach($ctypeFields as $ctypeField) {

            if(
                $ctypeField->name == $cronField->gc_name || 
                ($cronField->data_type == "geopoint" && (
                    $cronField->gc_name . "_lat" == $ctypeField->name || 
                    $cronField->gc_name . "_lng" == $ctypeField->name ))
                ) {
                
                $cronField->data_type_correct = $this->isDataTypeEqual($cronField, $ctypeField);
                $cronField->field_type_id = $ctypeField->field_type_id;
                $cronField->found = true;
            }

        }

        if($cronField->found != true) {
            $resultList[] = (object)[
                "detail" => sprintf("Field not found on: ODK %s, IMS %s", $cronField->odk_name, $cronField->gc_name),
                "type" => "not_found"
            ];
        } else if($cronField->data_type_correct != true) {
            $resultList[] = (object)[
                "detail" => sprintf("Field with mismatched data type found: ODK %s %s, IMS: %s %s", $cronField->odk_name, $cronField->data_type, $cronField->gc_name, $cronField->field_type_id),
                "type" => "mismatch_field_type"
            ];
        }

        return $resultList;
    }

    private function isDataTypeEqual($cronField, $ctypeField) {

        if($cronField->data_type == "text" && $ctypeField->field_type_id == "text")
            return true;
        if($cronField->data_type == "integer" && $ctypeField->field_type_id == "number")
            return true;
        if($cronField->data_type == "geopoint" && $ctypeField->field_type_id == "decimal")
            return true;
        if($cronField->data_type == "decimal" && $ctypeField->field_type_id == "decimal")
            return true;
        if($cronField->data_type == "decimal" && $ctypeField->field_type_id == "decimal")
            return true;
        if($cronField->data_type == "start" && $ctypeField->field_type_id == "date")
            return true;
        if($cronField->data_type == "end" && $ctypeField->field_type_id == "date")
            return true;
        else if($cronField->data_type == "date" && $ctypeField->field_type_id == "date")
            return true;
        else if($cronField->data_type == "select_one" && $ctypeField->field_type_id == "relation" && $ctypeField->is_multi != true)
            return true;
        else if($cronField->data_type == "select_one" && $ctypeField->field_type_id == "boolean")
            return true;
        else if($cronField->data_type == "select_multiple" && $ctypeField->field_type_id == "relation" && $ctypeField->is_multi == true)
            return true;
        else if($cronField->data_type == "image" && $ctypeField->field_type_id == "media" && $cronField->tag == "image_repeat" && $ctypeField->is_multi == true && $ctypeField->is_multi)
            return true;
        else if($cronField->data_type == "image" && $ctypeField->field_type_id == "media" && $cronField->tag == "image_repeat" && $ctypeField->is_multi != true && $ctypeField->is_multi != true)
            return true;
        else if($cronField->data_type == "file" && $ctypeField->field_type_id == "media" && $cronField->tag == "image_repeat" && $ctypeField->is_multi == true && $ctypeField->is_multi)
            return true;
        else if($cronField->data_type == "file" && $ctypeField->field_type_id == "media" && $cronField->tag == "image_repeat" && $ctypeField->is_multi != true && $ctypeField->is_multi != true)
            return true;

        return false;
    }
}
