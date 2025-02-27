<?php

/**
 * This class is responsible to export gview to excel or csv
 *
 *
 * Note 1: We create two rows for header:
 *  - First Row: Main fields
 *  - Second Row: Field-Collection fields
 *
 * But if we export to CSV then we export only one row for header
 */


namespace App\Core\Gviews;

use App\Models\CoreModel;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;
use App\Helpers\MiscHelper;

Class Export {

    private $id;
    private $params;

    private $coreModel;
    private $viewData;
    private $ctypeObj;
    private $useDefaultFiltersOnly = false;
    private $returnTheFile = false;

    private $is_bg_task = false;

    public function __construct($id, $params = []) {
        $this->id = $id;
        $this->params = $params;

        $this->is_bg_task = isset($params["is_bg_task"]) && $params["is_bg_task"];
        $this->returnTheFile = isset($params["returnTheFile"]) && $params["returnTheFile"];
      
        $this->coreModel = CoreModel::getInstance();

        if(isset($params['use_default_filters_only']) && $params['use_default_filters_only'] == 1)
            $this->useDefaultFiltersOnly = true;

        //Load the view object based on the id provided
        $this->viewData = $this->coreModel->nodeModel("views")
            ->id($id)
            ->loadFirstOrFail();

        //Load ctype object based on the view object
        $this->ctypeObj = (new Ctype)->load($this->viewData->ctype_id);
        
    }

    /**
    * main
    *
    * @param  int $id
    * @param  array $params
    * @return void
    *
    * This is the main function which receives the request and start exporting
    */
    public function main(){

        //Call generateSpreadsheetWithHeaderRows to create a new spreadsheet with header rows
        $spreadsheet = $this->generateSpreadsheetWithHeaderRows();
        
        //Get first sheet
        $sheet1 = $spreadsheet->getSheet(0);

        $postData = Application::getInstance()->request->POST();

        //Get where clause from GenerateFilterCriteria
        $filter_query = (new \App\Core\Gviews\GenerateFilterCriteria($postData, null, $this->viewData,$this->useDefaultFiltersOnly))->main();

        //Load data from database based on the where condition
        $data = \App\Models\Sub\LoadViewData::main($this->viewData, array("where" => $filter_query, "returnAll" => true, "fc_value_seperator" => "'" . GVIEW_VALUE_DELIMITER . "'", "postData" => $postData));

        //Set starting row, if export to csv then start from 2 otherwise start from 3
        $row_index = ($this->viewData->export_type_id != EXPORT_CSV_ID ? 2 : 2);

        $column_index = 1; //Column starting index
        $row_number = 1; //row number, which increment for each row
        
        $last_field_collection_max_size = 0; // We use this to know what was Field-Collection size was so we can use it for merging cells and finding next row index

        //Loop throw the records one by one
        foreach($data['records'] as $item){
        
            $current_field_collection_max_size = 0; // We use this to know what was Field-Collection size was so we can use it for merging cells and finding next row index    
            
            $main_fields_to_merge = array();

            //Set row number
            $sheet1->setCellValueByColumnAndRow($column_index++, $row_index, $row_number++);
            
            //Loop throw fields one by one
            foreach($this->viewData->fields as $vfield){
                
                $field = null; // Create an empty field

                //Check if the vfield has ctype specific for it or not
                if(isset($vfield->ctype_id)){

                    //Check if field we have field name or not
                    if(!empty($vfield->field_name)){
                        $field = (new CtypeField)->loadByCtypeIdAndFieldNameOrDefault($vfield->ctype_id, $vfield->field_name);
                    }
                    
                    //check and ignore some field types
                    if(isset($field) && ($field->field_type_id == "field_collection" || $field->field_type_id == "button" || (\App\Core\Application::getInstance()->user->isAdmin() != true && $field->is_hidden_updated_read == true))){
                        continue;
                    }

                    $varCtype = $this->ctypeObj;

                    //check if the vfield has it own ctype then load it
                    if(!empty($vfield->ctype_id) > 0){
                        $varCtype = (new Ctype)->load($vfield->ctype_id);
                    }

                    //If $field is not found
                    if(!isset($field)){

                        $valueReady = $item->{$varCtype->id . "_" . get_machine_name($vfield->custom_title)};
                        if(isset($valueReady) && $valueReady[0] == "=") {
                            $sheet1->getCellByColumnAndRow($column_index++, $row_index)->setValueExplicit($valueReady, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);                            
                        } else {

                            $sheet1->setCellValueByColumnAndRow($column_index++, $row_index, $valueReady);
                        }

                    //If the vfield is from Field-Collection 
                    } else if($this->viewData->export_type_id != EXPORT_CSV_ID && $this->ctypeObj->is_field_collection != true && $varCtype->is_field_collection == true){

                        //Load Field-Collection size
                        if($item->{$varCtype->id . "_count"} > $current_field_collection_max_size){
                            $current_field_collection_max_size = $item->{$varCtype->id . "_count"};
                        }

                        //get Field-Collection items which was separated by CRONS_VALUE_DELIMITER
                        $items = _explode(GVIEW_VALUE_DELIMITER,$item->{$varCtype->id . "_" . $field->name});

                        $row_index_before = $row_index;
                        //Loop throw the values after explode and put each of the in their own cell
                        $fc_data ="";
                        foreach($items as $value){

                            //9. Boolean
                            if($field->field_type_id == "boolean"){

                                //$sheet1->setCellValueByColumnAndRow($column_index, $row_index, ($value == 1 ? "Yes" : "No"));
                                $fc_data .= ($value == 1 ? "Yes" : "No").' '.PHP_EOL;
                                
                            }else {
                                $fc_data .= $value.' '.PHP_EOL;
                            } 
                            
                            
                            // else if ($field->field_type_id == "decimal") {
                            //     $fc_data .= $value.' '.PHP_EOL;
                            // }else {    
                            //     //Set value for each cell
                            //     //$sheet1->setCellValueByColumnAndRow($column_index, $row_index, $value);
                                
                            //     if (($pos = _strpos($value, ".")) !== FALSE) { 
                            //         $fc_data .= strtok($value, '.').' '.PHP_EOL; 
                            //     }else {
                            //         $fc_data .= $value.' '.PHP_EOL;
                            //     }
                            // }

                            //Next row
                            //$row_index++;

                        }
                        $sheet1->setCellValueByColumnAndRow($column_index, $row_index, $fc_data);
                        $row_index = $row_index_before;

                        //Next column
                        $column_index++;

                    //If the vfield is not from Field-Collection
                    } else {

                        /*
                        //If we are at column index 2 and last Field-Collection had data then go to row number (current + size of last Field-Collection)
                        if($column_index == 2 && $last_field_collection_max_size > 0){
                            
                            //$row_index += ($last_field_collection_max_size - 1);

                            if($this->viewData->export_type_id != EXPORT_CSV_ID){
                                //row number
                               // $main_fields_to_merge[] = array("column_index" => 1, "row_index" => $row_index);
                            }
                        }

                        if($this->viewData->export_type_id != EXPORT_CSV_ID){
                            $main_fields_to_merge[] = array("column_index" => $column_index, "row_index" => $row_index);
                        }
                        */
                        //9. Boolean
                        if($field->field_type_id == "boolean"){

                            $sheet1->setCellValueByColumnAndRow($column_index++, $row_index, ($item->{$varCtype->id . "_" . $field->name} == 1 ? "Yes" : "No"));

                        //5. Attachment
                        } else if ($field->field_type_id == "media"){

                            $sheet1->getColumnDimensionByColumn($column_index)->setAutoSize(false);

                            $sheet1->getRowDimension($row_index)->setRowHeight(20);
                            $sheet1->getColumnDimensionByColumn($column_index)->setWidth(10);

                            $field_full_name = "{$varCtype->id}_{$field->name}_name";

                            //Check if we have data or not
                            if(isset( $item->{$field_full_name}) && _strlen($item->{$field_full_name}) > 0){

                                //Get column width
                                $col_width = $sheet1->getColumnDimensionByColumn($column_index)->getWidth();
                                
                                //Get column height
                                $col_height = $sheet1->getRowDimension($row_index)->getRowHeight();

                                
                                $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                            
                                //Set file path
                                $objDrawing->setPath(UPLOAD_DIR_FULL . DS . $varCtype->id . DS . $item->{$field_full_name});
                                $objDrawing->setResizeProportional(false); 

                                //Set image size
                                $objDrawing->setWidthAndHeight($col_width * 7, $col_height * 1.34);

                                //Set coordinates
                                $objDrawing->setCoordinates(MiscHelper::numToAlphabet($column_index) . $row_index);

                                //Put the image on the sheet
                                $objDrawing->setWorksheet($sheet1);
                            }

                            //Next column
                            $column_index++;
                        } else {

                            if($field->field_type_id == "text") {
                                $sheet1->getCellByColumnAndRow($column_index++, $row_index)->setValueExplicit($item->{$varCtype->id . "_" . $field->name}, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                            } else {
                                $sheet1->setCellValueByColumnAndRow($column_index++, $row_index, $item->{$varCtype->id . "_" . $field->name});
                            }

                        }

                    }

                
                //If the vfield does not have specific ctype_id
                } else {

                    $sheet1->setCellValueByColumnAndRow($column_index++, $row_index, $item->{$vfield->custom_title});

                }
            }


            //Here we exported the data successfuly, but we need to check if we had Field-Collection so we need to merge cells for the main record
            /*
            if($current_field_collection_max_size > 1 && sizeof($main_fields_to_merge) > 0){

                //$sheet1->mergeCells("A" .  $row_index  . ":A" . ($row_index +  $current_field_collection_max_size - 1));

                foreach($main_fields_to_merge as $obj){

                    $col = $obj['column_index'];
                    $row = $obj['row_index'];
                    //merge
                    $columnLetter = MiscHelper::numToAlphabet($col - 1];
                                
                   // $sheet1->mergeCells($columnLetter .  $row  . ":" . $columnLetter . ($row +  $current_field_collection_max_size - 1));

                }

            }
        */
            $last_field_collection_max_size = $current_field_collection_max_size;
            $row_index++; //Go to next row
            $column_index = 1; //First column
        }

        //Create file name
        $export_file_name = (!empty($this->viewData->export_file_name) ? $this->viewData->export_file_name : $this->viewData->name);

        if($this->is_bg_task) {

            $isCsv = ($this->viewData->export_type_id == EXPORT_CSV_ID);
            
            $export_file_name .= time() . "." . ($isCsv ? 'csv' : 'xlsx');

            //Check if temp folder otherwise create it
            if(!file_exists(UPLOAD_DIR_FULL . DS . "bg_tasks")){
                mkdir(UPLOAD_DIR_FULL . DS . "bg_tasks", 0777, true);
            }
            
            //Create full path of the file
            $fileName = UPLOAD_DIR_FULL . DS . 'bg_tasks' . DS . $export_file_name;
            
            //Save it
            if($isCsv)
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            else 
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            $writer->save($fileName);

            return $fileName;
        } else if ($this->returnTheFile) {
            return $spreadsheet;
        } else {
            //If it is CSV
            if($this->viewData->export_type_id == EXPORT_CSV_ID){

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
                save_file_to_browser("PhpSpreadSheet_CSV", $export_file_name, $writer);

            //Else export as excel
            } else {

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                save_file_to_browser("PhpSpreadSheet_Xlsx", $export_file_name, $writer);
                
            }
        }
        
    }

    
    /**
     * generateSpreadsheetWithHeaderRows
     *
     * @return object
     *
     * This function will generate a spreadhsheet and puts header rows as well and returns it
     */
    private function generateSpreadsheetWithHeaderRows() : object {

        //Create a new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                
        //Get first sheet
        $sheet1 = $spreadsheet->getSheet(0);

        //Set title of first sheet
        $sheet1->setTitle('Data');


        //Set column index to row_number column
        $column_index = 1;

        if($this->viewData->export_type_id != EXPORT_CSV_ID){
            $sheet1 = setHeaderRowFormat($sheet1, $column_index,1);
            //$sheet1 = setHeaderRowFormat($sheet1, $column_index,2);
        
            //$sheet1->mergeCells("A1:A2");
        }
        
        //Put row number header
        $sheet1->setCellValueByColumnAndRow($column_index, 1, "#");
        
        //then start from column index 2 to put fields header
        $column_index = 2;
        
        $current_fc_id = null;
        $current_fc_id_field_count = 0;

        $merge_array = array();
        //Loop throw the fields one by one and create header for then inside the sheet
        foreach($this->viewData->fields as $vfield){
    
            $field = null; // Create an empty field

            //Check if field we have field name or not
            if(!empty($vfield->field_name)){
                $field = (new CtypeField)->loadByCtypeIdAndFieldNameOrDefault($vfield->ctype_id, $vfield->field_name);
            }
            
            if(isset($field) && (
                $field->field_type_id == "field_collection" || $field->field_type_id == "button" || (\App\Core\Application::getInstance()->user->isAdmin() != true && $field->is_hidden_updated_read == true)
                ))
                continue;

            $varCtype = $this->ctypeObj;

            //If the field have it is own Content-Type get it
            if(!empty($vfield->ctype_id) > 0){
                $varCtype = (new Ctype)->load($vfield->ctype_id);
            }

            //If export type is not CSV then export two row as header, one for main fields and second for Field-Collection fields
            if($this->viewData->export_type_id != EXPORT_CSV_ID){
                
                //In the loop if current_fc id not equal to current field Content-Type id
                if($current_fc_id != $varCtype->id){

                    //If fc counter is more than 0 then merge header columns
                    if($current_fc_id_field_count > 0){

                        //put it into array so later we do all merge at once
                        $merge_array[] = array("col1" => $column_index - $current_fc_id_field_count - 1, "col2" => $column_index - 2);
                        
                    }

                    $current_fc_id = $varCtype->id;
                    $current_fc_id_field_count = 0;
                
                }

                
                $sheet1 = setHeaderRowFormat($sheet1, $column_index,1);
                //$sheet1 = setHeaderRowFormat($sheet1, $column_index,2);

            }

            

            //If field Content-Type is Field-Collection
            if($this->viewData->export_type_id != EXPORT_CSV_ID && $varCtype->is_field_collection){
                $fc_tilte ="";
                if(isset($varCtype->parent_ctype_id)){
                    $xfields = $varCtype->getParentCtypeFields();
                    foreach($xfields as $xf){
                        if($xf->field_type_id == "field_collection" && $xf->data_source_id == $varCtype->id){
                            //$sheet1->setCellValueByColumnAndRow($column_index, 1, $xf->title); 
                            $fc_tilte = $xf->title;
                            break;
                        }
                    }
                   
                } else {
                
                        $fc_tilte = $field->ctype_name ;
                     //$sheet1->setCellValueByColumnAndRow($column_index, 1,$field->title); 
                }

                if(isset($vfield->custom_title) && _strlen($vfield->custom_title) > 0){
                    $sheet1->setCellValueByColumnAndRow($column_index, 1,  $fc_tilte.PHP_EOL. $vfield->custom_title);
                } else 
                    $sheet1->setCellValueByColumnAndRow($column_index, 1, $fc_tilte.PHP_EOL. $field->title);

                $current_fc_id_field_count++;
                    
            } else {

                //$columnLetter = MiscHelper::numToAlphabet($column_index - 1);
                //$sheet1->mergeCells($columnLetter . "1:" . $columnLetter . "2");
                
                if(isset($vfield->custom_title) && _strlen($vfield->custom_title) > 0){
                    $sheet1->setCellValueByColumnAndRow($column_index, 1, $vfield->custom_title);
                } else 
                   $sheet1->setCellValueByColumnAndRow($column_index, 1, $field->title);
                
                    
            }

            $column_index++;
        }
/*
        // continue column merge if there is pending
        if($current_fc_id_field_count > 0){
           // $merge_array[] = array("col1" => $column_index - $current_fc_id_field_count - 1, "col2" => $column_index - 2);
        }

        //Loop throw the merge array and do the merge
        foreach($merge_array as $obj){
            
            $col1 = $obj['col1'];
            $col2 = $obj['col2'];

            $columnLetter1 = MiscHelper::numToAlphabet($col1);
            $columnLetter2 = MiscHelper::numToAlphabet($col2);
            
           // $sheet1->mergeCells($columnLetter1 . "1:" . $columnLetter2 . "1");

        }
*/
        //Set column size to auto
        foreach(range('A',MiscHelper::numToAlphabet($column_index - 1)) as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        //Return the spreadsheet
        return $spreadsheet;
    }

}