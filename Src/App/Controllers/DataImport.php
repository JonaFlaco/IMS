<?php 

/*
 * This controller handles data import requests
 */

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Gctypes\Ctype;
use App\Core\Response;
use App\Exceptions\ForbiddenException;

class Dataimport extends Controller {

    private $excel_data_start_row_no = 3;
    public function __construct(){
        parent::__construct();
        //Set execution timeout to long time so it will not timeout if the excel file is big
        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_LONG);
        
        //Check if use is logged in or not
        $this->app->user->checkAuthentication();

    }
    

    /**
    * index
    *
    * @return void
    *
    * This interface is used to import excel file with ablility of selecting individual rows to import and easier error handling.
    * However the speed will be slower than the regular import
    */
    function index(){

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            $old_file_name = $_FILES['file']['name'];
            
            $file_name = sprintf("%s %s %s %s.xlsx", 
                date("yy.m.d"), 
                Application::getInstance()->user->getFullName(), 
                $old_file_name,
                time()
            );

            //Check if temp folder exist, if not create it
            if(!file_exists(TEMP_DIR)){
                mkdir(TEMP_DIR, 0777, true);
            }
            
            //now double check if temp folder exist, maybe the web app user does not have permission to create folder
            if(!file_exists(TEMP_DIR)){
                throw new \App\Exceptions\UnableToCreateFileException("Unable to create temp folder inside Uploaded files dir");
            }

            //create full path for destination file
            $dest_path = TEMP_DIR . DS . $file_name;

            $input_file_name = $_FILES['file']['tmp_name'];
            if(substr(_strtolower($input_file_name), 0, _strlen(PHP_UPLOAD_TMP_FOLDER)) !== _strtolower(PHP_UPLOAD_TMP_FOLDER)){
                throw new \App\Exceptions\FileOperationFailedException("Invalid file path");
            }

            //move the file to destination folder
            if(!move_uploaded_file($input_file_name , $dest_path)){
                throw new \App\Exceptions\FileOperationFailedException("unable to copy file to temp folder");
            }


            $input_file_name = $dest_path;

            //open the excel file after moving it to destination folder
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($input_file_name);
            
            //Check if the excel file has customized starting row no
            $excel_settings_sheet = $spreadsheet->getSheetByName('excel_settings'); 
            if(isset($excel_settings_sheet)){
                $start_at_row = _trim($excel_settings_sheet->getCellByColumnAndRow(1,1)->getValue());
                
                if(!empty($start_at_row)){
                    $this->excel_data_start_row_no = $start_at_row;
                }
            }

            $sheet1 = $spreadsheet->getSheet(0);
            $ctypeRef = $sheet1->getTitle();
            //Check if sheet name matches any Content-Type from system
            if(!isset($ctypeRef) || _strlen($ctypeRef) == 0){
                throw new \App\Exceptions\NotFoundException("Ctype Not Found");
            }

            if(!isset($ctypeRef) || _strlen($ctypeRef) == 0){
                throw new \App\Exceptions\NotFoundException("Content-Type not found");
            }

            //Get Content-Type object
            $ctype_obj = (new Ctype)->load($ctypeRef);    
            
            if($ctype_obj == null){
                throw new \App\Exceptions\NotFoundException("$ctype_obj->name not found");
            }

            //If Content-Type category is Field-Collection, show error
            if($ctype_obj->is_field_collection){
                throw new \App\Exceptions\IlegalUserActionException("You can not insert/update data directly from Field-Collection");
            }



            $field_headings = array_map('_trim',array_map('_strtolower',$sheet1->rangeToArray('A1:' . $sheet1->getHighestColumn() . 1,NULL,TRUE,FALSE)[0]));
            $excel_index_array = array();

            //Read the excel sheet row by row
            for ($row = $this->excel_data_start_row_no; $row <= $sheet1->getHighestRow(); $row++) {
                
                //read the excel index
                $excel_index = _trim($sheet1->getCellByColumnAndRow(\App\Helpers\PhpOfficeHelper::phpSpreadSheetGetCellIndexByName($field_headings, "excel_index"),$row)->getValue());
                
                //do some validaton
                if(empty($excel_index)){
                    continue;
                }

                //Check if the excel_index is duplicate
                if(in_array($excel_index, $excel_index_array)){
                    throw new \App\Exceptions\GImportException("Row $row: Excel index ($excel_index) is duplicate");
                }

                //Add it to the array
                $excel_index_array[] = $excel_index;
                
            }

            //If size of the array is zero means the excel is empty
            if(sizeof($excel_index_array) == 0){
                Application::getInstance()->session->flash('flash_warning', 'Nothing found to import');
                
                $data = [
                    'title' => "Generic Import"
                ];

                $this->renderView('admin/GenericImport/importSelectionInterface', $data);
                exit;
            }



            //load import_selection view and pass necessory data
            $data = [
                'title' => "Generic Import",
                'ctype_obj' => $ctype_obj,
                'file_name' => $file_name,
                //getItems will create a list of excel_index and pass it to the view
                'items' => $excel_index_array
            ];
            
            $this->renderView('admin/GenericImport/GenericImportSelectionInterface',$data);
        
        } else {

            //If it is not post request, show the view so user can select an excel file and POST it to import

            $data = [
                'title' => "Generic Import"
            ];

            $this->renderView('admin/GenericImport/GenericImportSelection', $data);
        }


    }
    


    /**
    * advanced
    *
    * @param  int $primary_excel_index
    * @param  array $params
    * @return void
    *
    * This function is used to import data from excel, but it is not allow the user to select rows to import, it will import all at once, also not easy for error handling for end user. 
    * The user can use main import function for better user experience
    */
    function advanced($primary_excel_index, $params){

        if($primary_excel_index == array()){
            $primary_excel_index = null;
        }

        if(_strlen($primary_excel_index) == 0 && $this->app->user->isAdmin() != true) {
            throw new ForbiddenException();
        }
        
        \App\Libraries\Dataimport::main($primary_excel_index, $params);
    }

    
    

    
    /**
    * import_gcron
    *
    * @return void
    *
    * This function is responsibile for importing gcron from an excel template
    */
    public function importGcron(){
        
        \App\Libraries\DataImportCron::main();

    }


}