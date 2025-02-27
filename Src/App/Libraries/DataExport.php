<?php

/**
 * This class will recieve a request to export data of a Content-Type entirely or some specific records
 */

namespace App\Libraries;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Exceptions\ForbiddenException;

Class DataExport  {

    private static $coreModel;
    private static $ctypeObj;
    private static $is_bg_task;
    private static $whiteSystemListFieldNames = ["id", "created_user_id", "created_date", "updated_user_id", "last_updated_date", "status_id"];
    /**
    * main
    *
    * @param  int $primary_excel_index
    * @param  array $params
    * @return void
    *
    * This is the public function, which received the request to export the data.
    */
    public static function main($ctype_id = null, array $params = array()){

        self::$coreModel = Application::getInstance()->coreModel;
        self::$is_bg_task = isset($params["is_bg_task"]) && $params["is_bg_task"];

        //Check if the use have permission to export the requested Content-Type
        $permissions = Application::getInstance()->user->getCtypePermission($ctype_id);
        if($permissions->allow_generic_export != true){
            throw new ForbiddenException("You don't have permission to export");
        }

        //Get records to export, if provided
        $record_id = null;
        if(isset($params['record_id'])){
            $record_id = intval($params['record_id']);
        }
        
        //Create a new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet_main = $spreadsheet->getSheet(0);

        //Get Content-Type object
        self::$ctypeObj = (new Ctype)->load($ctype_id);
        //Get fields
        $fields = self::$ctypeObj->getFields();
        
        

        // get all filters from $_POST and try to produce a where clause
        $where = " 1 = 1 ";
        
        //Loop throw items inside $_POST
        foreach($_POST as $key => $value){

            //If value has data
            if(!empty($value) > 0){
                
                if($key == "selected_ids"){

                    //Set limit to download records less than 200,000 if you pass record ids, since the query will be too big and server will not be able to respond
                    if (_strlen($value) > 200000){
                        throw new \App\Exceptions\IlegalUserActionException("Too many records to download, please use filter to narrow down the result result");
                    }

                    $value = preg_replace("/[^0-9a-zA-Z,_]/", "", $value);

                    $valueResult = "";
                    foreach(_explode(",", $value) as $item) {
                        if(_strlen($valueResult) > 0)
                            $valueResult .= ",";
                        $valueResult .= "'" . $item . "'";
                    }
                    
                    $where = " m.id in (" . $valueResult . ") ";
                    
                    break;
                }

                //If the condition is based on the main Content-Type that we export, then process
                //Since the condition might come from another Content-Type which have relation with it. that's why we filter by the condition for the main Content-Type only
                if(substr($key,0, _strlen(self::$ctypeObj->id)) == self::$ctypeObj->id){
                    
                    //get pure field name, whichout Content-Type prefix
                    $field_name = substr($key,_strlen(self::$ctypeObj->id) + 1);
                    $value = _str_replace("'","''",$value);
                    
                    //If the value contains ; then count it as multi value condition
                    if(_strpos($value,";") !== false){
                        $where .= " AND (";
                        $i = 0;
                        foreach(_explode(";",$value) as $sub_value){
                            if($i++ > 0){
                                $where .= " OR ";
                            }
                            $sub_value = _str_replace("\n","",$sub_value);
                            $sub_value = _trim($sub_value);
                            $where .=  "trim(m.$field_name) = N'$sub_value'";
                        }
                        $where .= ") ";
                    
                    } else {
                        $where .= " AND m.$field_name like '%$value%' ";
                    }
                }
            }
        }
        
        // Now that we prepared where clause (if user provided) then we load data from database
        $data = self::$coreModel->nodeModel(self::$ctypeObj->id)
            ->id($record_id)
            ->where($where)
            ->loadFc(false)
            ->load();
        
        //create where clause for Field-Collection
        $fc_where = "";
        foreach($data as $item){
            if(_strlen($fc_where) > 0)
                $fc_where .= ",";
            $fc_where .= "'" . $item->id . "'";
        }

        if(_strlen($fc_where) > 0){
            $fc_where = " m.parent_id in ($fc_where) ";
        }
        
        //Set sheet name to Content-Type name
        if(_strlen(self::$ctypeObj->id) <= 31){
            $sheet_main->setTitle(self::$ctypeObj->id);
        } else {
            $sheet_main->setTitle(substr(self::$ctypeObj->id, 0, 28). "...");
        }

        $sheet_main->getRowDimension(1)->setVisible(false);

        //Export data to the sheet
        self::exportSheet(self::$ctypeObj, $fields, $sheet_main, $data);

        $sheetId = 1;
        //Loop throw Field-Collections
        foreach($fields as $field){
            if($field->field_type_id == "field_collection"){

                //create sheet for each Field-Collection
                $sheet_fc = $spreadsheet->createSheet($sheetId++);

                //Set sheet name
                if(_strlen($field->name) <= 31){
                    $sheet_fc->setTitle($field->name);
                } else {
                    $sheet_fc->setTitle(substr($field->name, 0, 28) . "...");
                }
                
                $sheet_fc->getRowDimension(1)->setVisible(false);
                

                //Load Field-Collection data
                $fc_fields = $field->getFields();
                $fcCtypeObj = (new Ctype)->load($field->data_source_id);
                
                $fc_data = self::$coreModel->nodeModel($fcCtypeObj->id)
                    ->id($record_id)
                    ->where($fc_where)
                    ->load();
    
                //Export data to the sheet
                self::exportSheet($fcCtypeObj, $fc_fields, $sheet_fc, $fc_data);

            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        //If response type is json, then save the file to temp folder and return back file name
        if(self::$is_bg_task){
            $new_file_name = self::$ctypeObj->name . " " . time() . '.xlsx';

            //Check if temp folder otherwise create it
            if(!file_exists(UPLOAD_DIR_FULL . DS . "bg_tasks")){
                mkdir(UPLOAD_DIR_FULL . DS . "bg_tasks", 0777, true);
            }
            
            //Create full path of the file
            $fileName = UPLOAD_DIR_FULL . DS . 'bg_tasks' . DS . $new_file_name;
            
            //Save it
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $writer->save($fileName);

            return $fileName;

        } else if(Application::getInstance()->response->getResponseFormat() == "json"){
            $new_file_name = self::$ctypeObj->name . " " . time() . '.xlsx';

            //Check if temp folder otherwise create it
            if(!file_exists(TEMP_DIR)){
                mkdir(TEMP_DIR, 0777, true);
            }
            
            //Create full path of the file
            $fileName = TEMP_DIR . DS . $new_file_name;
            
            //Save it
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $writer->save($fileName);

            $result = (object)[
                "status" => "success",
                "fileName" => $new_file_name
            ];
    
            return_json($result);

        //If response type is not json then download the file to user
        } else {
            
            save_file_to_browser("PhpSpreadSheet_Xlsx", self::$ctypeObj->name, $writer);
        }

            
    }

        
    /**
     * exportSheet
     *
     * @param  object $ctypeObj
     * @param  object $fields
     * @param  object $sheet
     * @param  array $data
     * @return void
     *
     * This function will receive an excel sheet with data to export the data into the excel sheet
     */
    private static function exportSheet($obj, $fields, $sheet, $data){
            
        //We use the first row as Title of the filed and second row as machine name of the field

        //Set column titles
        $sheet->setCellValueByColumnAndRow(1, 1, "excel_index");
        $sheet->setCellValueByColumnAndRow(1, 2, "Excel Index");
        
        $sheet = setHeaderRowFormat($sheet, 1,1);
        $sheet = setHeaderRowFormat($sheet, 1,2);

        if($obj->is_field_collection != true) {
            $sheet->setCellValueByColumnAndRow(2, 1, "sett_update_justification");
            $sheet->setCellValueByColumnAndRow(2, 2, "Justification for Update");
        
            $sheet = setHeaderRowFormat($sheet, 2,1);
            $sheet = setHeaderRowFormat($sheet, 2,2);
        }

        //Set first 2 rows format
            
        $allowed_field_types = Application::getInstance()->globalVar->get('ALLOWED_FIELD_TYPES_TO_EXPORT');
        
        $columnIndex = $obj->is_field_collection ? 2 : 3;

        foreach($fields as $field){
    
            //If not valid field for export, then go to new field
            if(!in_array($field->field_type_id, $allowed_field_types) || ($field->is_system_field == true && !in_array($field->name, self::$whiteSystemListFieldNames)))
                continue;

            //Set first 2 rows format
            $sheet = setHeaderRowFormat($sheet, $columnIndex,1);
            $sheet = setHeaderRowFormat($sheet, $columnIndex,2);
            
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $field->name);
            $sheet->setCellValueByColumnAndRow($columnIndex++, 2, $field->title);
            

        }

        \App\Helpers\PhpOfficeHelper::phpSpreadSheetAutosizeAllColumns($sheet);

        \App\Helpers\PhpOfficeHelper::phpSpreadSheetCellColor($sheet,'A1:' . \App\Helpers\MiscHelper::numToAlphabet($columnIndex - 1) . '2', 'F28A8C');


        //If data is empty, then return the empty sheet
        if($data == array())
            return;

        //Starting form row 3 we start appending data from database
        $rowIndex = 3;
        //Loop throw records one by one
        foreach($data as $item){

            $sheet->setCellValueByColumnAndRow(1, $rowIndex, ($obj->is_field_collection != true ? $item->id : $item->parent_id));
            
            $columnIndex = $obj->is_field_collection ? 2 : 3;

            //Loop throw fields one by one
            foreach($fields as $field){
        
                //If not valid field for export, then go to new field
                if(!in_array($field->field_type_id, $allowed_field_types) || ($field->is_system_field == true && !in_array($field->name, self::$whiteSystemListFieldNames)))
                    continue;

                $value = null;

                //1. Text
                //6. Number
                //7. Decimal
                if($field->field_type_id == "text" || $field->field_type_id == "number" || $field->field_type_id == "decimal" || ($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text == true)){
                    
                    $value = $item->{$field->name};

                //5. Attachment
                } else if($field->field_type_id == "media"){

                    //5. Attachment Multi
                    if($field->is_multi == true) {

                        $value = "";
                        foreach($item->{$field->name} as $img){
                            if(_strlen($value) > 0){
                                $value .= "\n";
                            }
                            $value .=  "[UPLOAD_DIR]\\" . self::$ctypeObj->id . "\\" . $img->name;
                        }

                    //5. Attachment Single
                    } else {
                        if(_strlen($item->{$field->name . "_name"}) == 0){
                            $value = "";
                        } else {
                            $value = "[UPLOAD_DIR]\\" . self::$ctypeObj->id . "\\" . $item->{$field->name . "_name"};
                        }
                    }

                //2. Combobox Multi
                } else if($field->field_type_id == "relation" && $field->is_multi == true){

                    $value = $item->{$field->name . "_display"};

                //2. Combobox Single, value not text
                } else if ($field->field_type_id == "relation" && $field->is_multi != true && $field->data_source_value_column_is_text != true){
                    $value = $item->{$field->name . "_display"};
                
                //4. Date
                } else if ($field->field_type_id == "date"){
                    
                    $value = $item->{$field->name};
                    if(isset($value)){
                        $value = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(date_format(date_create($value),"d/m/Y H:i:s"));
                        $sheet->getStyleByColumnAndRow($columnIndex, $rowIndex)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
                    }
                
                //9. Boolean
                } else if($field->field_type_id == "boolean") {
                    $value = "No";
                    if($item->{$field->name} == true){
                        $value = "Yes";
                    }
                } else {
                    continue;
                }
                
                
                //Write data to excel sheet
                if($field->field_type_id == "text"){
                    $sheet->getCellByColumnAndRow($columnIndex++, $rowIndex)->setValueExplicit($value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValueByColumnAndRow($columnIndex++, $rowIndex, $value);
                }

            }

            $rowIndex++;
            
        }

        
    }
    

}
