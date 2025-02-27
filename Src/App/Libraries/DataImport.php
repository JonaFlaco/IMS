<?php

/**
 * This class will receive an excel file to import
 * The excel file have standard structure
 */

namespace App\Libraries;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Response;

use App\Exceptions\ForbiddenException;
use App\Exceptions\InvalidDateFormat;
use \App\Models\CoreModel;
use \App\Libraries\MySqlDatabase;

Class Dataimport {


    private static $excel_data_start_row_no = 3;
    private static $coreModel;
    private static $ctype_obj;
    private static $spreadsheet;
    private static $imported_records = 0;
    private static $excel_index_array;
    private static $cached_data_source;
    private static $ctype_permissions;
    private static $ctype_fields;

    /**
    * main
    *
    * @param  int $primary_excel_index
    * @param  array $params
    * @return void
    *
    * This is the public function, which receives an excel file or a row index to import.
    */
    public static function main($primary_excel_index = null,array $params = array()){

        self::$coreModel = CoreModel::getInstance();
        
        $file_name = null;
        if(isset($params['file_name'])){
            $file_name = $params['file_name'];
        }
        
        //If the request is POST which means import entire excel file, or if the request is sending file name and excel_index to import
        if($_SERVER['REQUEST_METHOD'] == 'POST' || (!empty($file_name) && !empty($primary_excel_index))){

            if(!empty($file_name)){
                $input_file_name = TEMP_DIR . DS . $file_name;
                Application::getInstance()->response->setResponseFormat(Response::$FORMAT_JSON);
            } else {
                $input_file_name = $_FILES['file']['tmp_name'];

                if(substr(_strtolower($input_file_name), 0, _strlen(PHP_UPLOAD_TMP_FOLDER)) !== _strtolower(PHP_UPLOAD_TMP_FOLDER)){
                    throw new \App\Exceptions\FileOperationFailedException("Invalid file path");
                }
            }

            //Load the spreadsheet
            self::$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($input_file_name);

            $excel_settings_sheet = self::$spreadsheet->getSheetByName('excel_settings'); 
            if(isset($excel_settings_sheet)){

                //Set starting row if defined in excel_settings sheet
                $start_at_row = $excel_settings_sheet->getCellByColumnAndRow(1,1)->getValue();

                if(!empty($start_at_row)){
                    self::$excel_data_start_row_no = $start_at_row;
                }
            }

            $sheet_main = self::$spreadsheet->getSheet(0);
            $CtypeRef = _strtolower($sheet_main->getTitle());
            
            if(!isset($CtypeRef) || _strlen($CtypeRef) == 0){
                throw new \App\Exceptions\NotFoundException("Content-Type not found");
            }

            self::$ctype_obj = (new Ctype)->load($CtypeRef);
            
            $ctypeId = self::$ctype_obj->id;

            if(self::$ctype_obj == null){
                throw new \App\Exceptions\NotFoundException("'$ctypeId' not found in Content-Types");
            }
            
            if(self::$ctype_obj->is_field_collection){
                throw new \App\Exceptions\IlegalUserActionException("You can not insert/update data directly from Field-Collection");
            }

            //Get permission for this Content-Type
            self::$ctype_permissions = Application::getInstance()->user->getCtypePermission(self::$ctype_obj->id);

            self::$ctype_fields = self::$ctype_obj->getFields();
            

            self::$cached_data_source = array();
            self::$excel_index_array = array();

            //Get column header for main sheet
            $field_headings = array_map('_trim',array_map('_strtolower',$sheet_main->rangeToArray('A1:' . $sheet_main->getHighestColumn() . 1,NULL,TRUE,FALSE)[0]));
            
            //Read the excel rows one by one
            for ($row = self::$excel_data_start_row_no; $row <= $sheet_main->getHighestRow(); $row++) {
            
                
                $excel_index = null;
                if(in_array("excel_index", $field_headings)){
                    $excel_index = _trim($sheet_main->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, "excel_index"),$row)->getValue());
                } else {
                    throw new \App\Exceptions\NotFoundException("Sheet: " . self::$ctype_obj->id . ", excel_index column not found");
                }

                        
                //If the user is sending request to import one specific row then import only that row
                if(!isset($excel_index) || _strlen($excel_index) == 0 || (!empty($primary_excel_index) && $primary_excel_index != $excel_index))
                    continue;

                $id = self::import($sheet_main, $field_headings, $row, $excel_index);

                //If specific excel index was provided from the user then exit the import here, otherwise continue imoprting other records
                if(!empty($primary_excel_index) && intval(self::$imported_records) > 0){
                    
                    $result = (object)[
                        "status" => "success",
                        "record_id" => $id
                    ];
            
                    return_json($result);
                }

            }

            if(self::$imported_records > 0){
                //If record counter is bigger than 0 then means at leas one record is imported
                Application::getInstance()->session->flash("flash_success", self::$imported_records . " record(s) imported successfuly");
            } else {

                //If record counter is zero means no record is imported, show a warning to the user
                Application::getInstance()->session->flash("flash_warning", "The file is empty");
            }

            //All done, go back to Import data interface
            Application::getInstance()->view->renderView("admin/GenericImport/GenericImportOneBatch", array("title" => "Generic Import"));

        } else {
            
            //If it is not POST request, then show the import interface
            Application::getInstance()->view->renderView("admin/GenericImport/GenericImportOneBatch", array("title" => "Generic Import"));
        }

    }


    
    /**
     * import
     *
     * @param  object $sheet_main
     * @param  array $field_headings
     * @param  int $row
     * @param  int $excel_index
     * @return void
     *
     * This function will receive records one by one and import it
     */
    private static function import($sheet_main, $field_headings, $row, $excel_index){
        $data = new \stdClass();
        //Set Content-Type name for the data object
        $data->sett_ctype_id = self::$ctype_obj->id;
        
        
        
        

        //this column will specify to import the record on behalf of which user.
        //If it is empty it will import on behalf the user who importing data
        $excel_user_id = null;
        if(in_array("excel_user_email", $field_headings)){
            $excel_user_email = _trim($sheet_main->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, "excel_user_email"),$row)->getValue());

            if(\App\Core\Application::getInstance()->user->isAdmin() == true && !empty($excel_user_email)){
                $excel_user_id = self::$coreModel->getUserIdByEmail($excel_user_email);

                if(empty($excel_user_id)){
                    throw new \App\Exceptions\NotFoundException("Sheet: " . self::$ctype_obj->id . ", Row $row: Excel index ($excel_index) '$excel_user_email' not found in users");
                }
            }
        }

        
        //Some validation for excel_index
        if(empty($excel_index)){
            throw new \App\Exceptions\GImportException("Sheet: " . self::$ctype_obj->id . ", Row $row: Excel index is empty");
        }

        if(in_array($excel_index, self::$excel_index_array)){
            throw new \App\Exceptions\GImportException("Sheet: " . self::$ctype_obj->id . ", Row $row: Excel index ($excel_index) is duplicate");
        }

        //Add the excel_index into imported array in order to avoid duplicates
        self::$excel_index_array[] = $excel_index;
        
        //Import data other than Field-Collection
        $data = self::generateData($data,$row, self::$ctype_fields, $sheet_main, $field_headings, null);

        //Loop throw Field-Collections
        foreach(self::$ctype_fields as $field){
            if($field->field_type_id == "field_collection"){
                //Import Field-Collection data
                $data = self::generateFCData($data, $field, $excel_index);
            }
        }

        
        $sett_update_justification = null;
        if(in_array("sett_update_justification", $field_headings)){
            $sett_update_justification = _trim($sheet_main->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, "sett_update_justification"),$row)->getValue());
        }

        
        //Check if it is update mode or add new
        $isUpdate = false;
        if(isset($data->id) && intval($data->id) > 0)  {
            $isUpdate = true;

            if(!empty($sett_update_justification) != true){
                $sett_update_justification = "Record updated from excel file";
            }
        }

        
        if(in_array("sett_is_new_record", $field_headings)){
            
            $sett_is_new_record = true;
            $sett_is_new_record = _trim($sheet_main->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, "sett_is_new_record"),$row)->getValue()) == true ? true : false;
            $data->sett_is_update = !$sett_is_new_record;

        }

        
        
        //If update mode then check if the user have permission to edit
        if($isUpdate && self::$ctype_permissions->allow_generic_import_edit != true){
            throw new ForbiddenException("You don't have permission to update '" . self::$ctype_obj->id . "'");
        }

        //If add new mode then check if the user have permission to add
        if(!$isUpdate && self::$ctype_permissions->allow_generic_import_add != true){
            throw new ForbiddenException("You don't have permission to add '" . self::$ctype_obj->id . "'");
        }
        
        //Set the record counter +1
        self::$imported_records++;

        //Send the data to node_save() for saving
        $id = self::$coreModel->node_save($data, array("user_id" => $excel_user_id, "justification" => $sett_update_justification));

        if(_strlen($id) == 0){
            throw new \App\Exceptions\GImportException("Something went wrong while saving row_index $excel_index");
        }

        return $id;
    }
    


    /**
     * generateData
     *
     * @param  object $data
     * @param  int $row
     * @param  array $fields
     * @param  object $sheet
     * @param  array $field_headings
     * @param  string $fc_name
     * @return object
     *
     * This function will set data for main Content-Type or Field-Collection
     */
    private static function generateData($data,$row, $fields, $sheet, $field_headings, $fc_name = null) : object {
        
        $generic_preload_list_obj = new \App\InternalApi\GenericPreloadList(self::$coreModel);

        $is_update = false;
        if(in_array("id", $field_headings) && intval(_trim($sheet->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, "id"),$row)->getValue())) > 0){
            $is_update = true;
        }
        
        //Loop throw the fields one by one
        foreach($fields as $field){

            if($field->name == "status_id" ||($field->is_system_field && $field->name == "parent_id"))
                continue;

            if($field->is_read_only || ($is_update && $field->is_read_only_updated_edit) || (!$is_update && $field->is_read_only_updated_add)) {
                continue;
            }

            $field->name = _trim(_strtolower($field->name));

            //get column header
            $header = _trim(_strtolower($sheet->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, $field->name),1)->getValue()));

            //get cell value
            $value = _trim($sheet->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, $field->name),$row)->getValue());
            
            //If field name is not equals to column header name ignore and go to the next field 
            if($field->name != $header)
                continue;

            //If the value in excel file is empty
            if((!isset($value) || _strlen($value) == 0)){
                
                //But it is required, show error
                if($field->is_required == true){
                    if(isset($fc_name)){
                        throw new \App\Exceptions\GImportException("Sheet: " . $fc_name . ", Column: $field->name, Row #: $row, Value is empty");
                    } else {
                        throw new \App\Exceptions\GImportException("Sheet: " . self::$ctype_obj->id . ", Column: $field->name, Row #: $row, Value is empty");
                    }
                } else {
                    //If not required then ignore this field
                    //Dont Ignore the field as you may wish you update to empty value
                    //continue;
                    $value = null;
                }

            }

            //1. Text
            //2. Combobox value text
            //6. Number
            //7. Decimal
            if ($field->field_type_id == "text" || ($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text == true) || $field->field_type_id == "number" || $field->field_type_id == "decimal"){

                $data->{$field->name} = $value;
                
            // 2 Combobox Single, value not text
            } else if ($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text != true){

                //First check if data source for this field exist in cache then use it, otherwise load it from $generic_preload_list_obj
                if(!isset(self::$cached_data_source[$field->data_source_table_name]) || $field->data_source_table_name == "ctypes"){

                    $p = ["field_id" => $field->id,"return_object" => 1];
                    $result = $generic_preload_list_obj->index(null,$p);
                    
                    self::$cached_data_source[$field->data_source_table_name] = $result;
                }
                
                $found_value = null;
                foreach(self::$cached_data_source[$field->data_source_table_name] as $itm){
                    
                    if(_trim(_strtolower($itm->name)) == _trim(_strtolower($value))){
                        $found_value = $itm->id;
                    }
                }

                if(!isset($found_value) && _strlen($value) > 0){

                    if(isset($fc_name)){
                        throw new \App\Exceptions\NotFoundException("Sheet: " . $fc_name . ", Column: $field->name, Row #: $row, Value: '$found_value' not found");
                    } else {
                        throw new \App\Exceptions\NotFoundException("Sheet: " . self::$ctype_obj->id . ", Column: $field->name, Row #: $row, Value: '$found_value' not found");
                    }
                }

                $data->{$field->name} = $found_value;

            //2. Combobox Multi Text
            } else if ($field->field_type_id == "relation" && $field->is_multi == true && $field->data_source_value_column_is_text == true) {

                $data->{$field->name} = array();

                foreach(_explode("\n", $value) as $sub_value){
                    $data->{$field->name}[] = $sub_value;
                }

            //2 Combobox Multi
            } else if($field->field_type_id == "relation" && $field->is_multi == true){


                if(!isset(self::$cached_data_source[$field->data_source_table_name]) || $field->data_source_table_name == "ctypes"){
                    $p = ["field_id" => $field->id,"return_object" => 1,];
                    $result = $generic_preload_list_obj->index(null,$p);
                    
                    self::$cached_data_source[$field->data_source_table_name] = $result;
                }

                $data->{$field->name} = array();

                //Loop throw the values one by one
                foreach(_explode("\n", $value) as $sub_value){

                    $found_value = null;
                    foreach(self::$cached_data_source[$field->data_source_table_name] as $itm){
                        if(_trim(_strtolower($itm->name)) == _trim(_strtolower($sub_value))){
                            $found_value = $itm->id;
                        }
                    }

                    if(!isset($found_value) && _strlen($value) > 0){

                        if(isset($fc_name)){
                            throw new \App\Exceptions\NotFoundException("Sheet: " . $fc_name . ", Column: $field->name, Row #: $row, Value: '$sub_value' not found");
                        } else {
                            throw new \App\Exceptions\NotFoundException("Sheet: " . self::$ctype_obj->id . ", Column: $field->name, Row #: $row, Value: '$sub_value' not found");
                        }
                        
                    }

                    $data->{$field->name}[] = $found_value;
                }
                 

            //4 Date
            } else if ($field->field_type_id == "date"){

                //Get the cell
                $cell = $sheet->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, $field->name),$row);

                //If the cell format is datetime
                if(\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                    try {
                        $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(_trim($sheet->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, $field->name),$row)->getValue()))->format('d/m/Y H:i:s');
                    } catch (\Exception $exc) {
                        throw new InvalidDateFormat();
                    }
                }
                
                $data->{$field->name} = $value;

            //5 Image Single
            } else if($field->field_type_id == "media" && $field->is_multi != true){
            
                $file_name = $value;
                $file_name = _str_replace("[UPLOAD_DIR]",UPLOAD_DIR_FULL,$file_name);

                $same_image = $is_update && startsWith($file_name, UPLOAD_DIR_FULL . "\\" . self::$ctype_obj->id);
                
                //Copy the file upload dir
                $file_obj = \App\Helpers\UploadHelper::uploadFile($file_name, self::$ctype_obj->id, basename($file_name), $same_image);

                if(isset($file_obj)){
                    $data->{$field->name . "_name"} = $file_obj->name;
                    $data->{$field->name . "_original_name"} = $file_obj->original_name;
                    $data->{$field->name . "_type"} = $file_obj->type;
                    $data->{$field->name . "_size"} = $file_obj->size;
                    $data->{$field->name . "_extension"} = $file_obj->extension;
                }
                
            //5 Attachment Multi
            } else if($field->field_type_id == "media" && $field->is_multi == true){
                    
                $data->{$field->name} = array();

                //Loop throw files one by one
                foreach(_explode("\n", $value) as $value){
					$file_name = $value;
                    $file_name = _str_replace("[UPLOAD_DIR]",UPLOAD_DIR_FULL,$file_name);                                                    
                    $file_name = _trim(_str_replace("_x000D_","",$file_name));

                    $same_image = $is_update && startsWith($file_name, UPLOAD_DIR_FULL . "\\" . self::$ctype_obj->id);
                
                    //Copy the file upload dir
                    $file_obj = \App\Helpers\UploadHelper::uploadFile($file_name, self::$ctype_obj->id, basename($file_name), $same_image);

                    if(isset($file_obj)){

                        $obj = new \stdClass();
                        $obj->name = $file_obj->name;
                        $obj->original_name = $file_obj->original_name;
                        $obj->type = $file_obj->type;
                        $obj->size = $file_obj->size;
                        $obj->extension = $file_obj->extension;

                        $data->{$field->name}[] = $obj;
                    }
                }
            
            

            //9. Boolean
            } else if($field->field_type_id == "boolean"){
                
                $value = _strtolower($value);

                //Check if value is true
                if($value == "1" || $value == "yes" || $value == "true"){
                    $value = 1;
                
                //If value is false
                } else if(empty($value) || $value == "0" || $value == "no" || $value == "false"){
                    $value = 0;

                //If value is invalid
                } else {
  
                    if(isset($fc_name)){
                        throw new \App\Exceptions\GImportException("Sheet: " . $fc_name . ", Column: $field->name, Row #: $row, Value: '$value' is not a valid bool");
                    } else {
                        throw new \App\Exceptions\GImportException("Sheet: " . self::$ctype_obj->id . ", Column: $field->name, Row #: $row, Value: '$value' is not a valid bool");
                    }
                }

                $data->{$field->name} = $value;

            }
            
        }

        //return back the data

        return $data;
        
    }

    
        
    /**
     * generateFCData
     *
     * @param  object $data
     * @param  object $field
     * @param  int $excel_index
     * @return void
     *
     * This function will loop throw Field-Collections and read its data and set it to the data object
     */
    private static function generateFCData($data, $field, $excel_index){

        //Find the sheet for this FC
        $sheet_fc = self::$spreadsheet->getSheetByName($field->name);
        
        if(!isset($sheet_fc)){
            $sheet_fc = self::$spreadsheet->getSheetByName($field->data_source_id);
        }

        if(!isset($sheet_fc)){
            foreach(self::$spreadsheet->getSheetNames() as $sheet_name){
                if(_trim(_strtolower($sheet_name)) == _trim(_strtolower($field->name) || _trim(_strtolower($sheet_name)) == $field->data_source_id)){
                    $sheet_fc = self::$spreadsheet->getSheetByName($sheet_name);
                }
            }
        }
            
        

        //If sheet found then
        if(isset($sheet_fc)){
            
            //Load FC fields
            $fields_fc = $field->getFields();

            //Load FC column headers
            $field_headings_fc = array_map('_trim',array_map('_strtolower',$sheet_fc->rangeToArray('A1:' . $sheet_fc->getHighestColumn() . 1,NULL,TRUE,FALSE)[0]));

            //Create new array for the FC
            $data->{$field->name} = array();

            //Loop throw the fc sheet records one by one
            for ($row_fc = self::$excel_data_start_row_no; $row_fc <= $sheet_fc->getHighestRow(); $row_fc++) {
            
                $excel_index_fc = _trim($sheet_fc->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings_fc, "excel_index"),$row_fc)->getValue());

                if($excel_index_fc != $excel_index || !isset($excel_index_fc) || _strlen($excel_index_fc) == 0)
                    continue;

                //Create data object for each record
                $fc_obj = new \stdClass();
                
                //Fill the data object with data from the sheet
                $fc_obj = self::generateData($fc_obj, $row_fc, $fields_fc, $sheet_fc, $field_headings_fc, $field->name);

                //Assign the data object to the fc array
                $data->{$field->name}[] = $fc_obj;

            }
                
        }

        //Return back the data
        return $data;

    }
            
}
