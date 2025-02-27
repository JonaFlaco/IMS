<?php

/*
 * Home controller
 */

namespace Ext\BgTaskHandlers;

use App\Core\BgTaskHandlers;
use \App\Helpers\NumberToWords;
use App\Core\Gctypes\Ctype;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class CtypeExportOdkTemplate extends BgTaskHandlers
{
    private \App\Core\BgTask $task;
    
    private $spreadsheet;
    private $sheet;

    private $current_group = ""; 
    private $survey_sheet_row = 6;
    private $choices_sheet_row = 2;
    private $settings_sheet_row = 2;
    private $group_counter = 1;
    private $question_counter = 1;


    public function __construct($task)
    {
        parent::__construct();

        $this->task = $task;

        $fileObj = $this->coreModel->get_document_file_attachments("sys_core_files", "ctype_export_odk_template");
        $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileObj->full_path);            
        $this->sheet = $this->spreadsheet->getActiveSheet();        
    }

    public function run(){
        $_POST = $this->task->getPostData();        
        
        if(empty($_POST['ctype_id'])){
            throw new \App\Exceptions\MissingDataFromRequesterException("Content type ID is required , but not provided");
        }
                    

        $ctype = $this->coreModel->nodeModel("ctypes")
                                 ->id($_POST['ctype_id'])
                                 ->loadFirstOrDefault();

        // set settigns in the odk third sheet                        
        $this->setSettings($ctype);

        foreach($ctype->fields as $field){                                                  
            
            if(($field->is_system_field) || ($field->field_type_id == "component") || $field->is_hidden || $field->is_read_only)
                continue;

            if($field->field_type_id == "relation"){
                $ctype_check = $this->coreModel->nodeModel("ctypes")
                                            ->where("m.id = :id")
                                            ->bindValue("id", $field->data_source_id)
                                            ->loadFirstOrDefault();
                
                // exclude preload lists. Both the question and its option should be added manually to the ODK file.
                if($ctype_check->category_id != "lookup_table"){
                    continue;
                }
            }
            
            $this->setGroupFieldTitle($field);                

            $this->sheet->setCellValue('A'.$this->survey_sheet_row,$this->getFieldType($field));

            $this->sheet->setCellValue('B'.$this->survey_sheet_row, $field->name);
            $this->sheet->setCellValue('C'.$this->survey_sheet_row, $field->name);
            $this->sheet->setCellValue('E'.$this->survey_sheet_row, $field->title);
            $this->sheet->setCellValue('G'.$this->survey_sheet_row, (($field->is_required || _strlen($field->required_condition) > 0) ? "Yes" : "No"));
            $this->sheet->setCellValue('I'.$this->survey_sheet_row, $field->title_ku);
            $this->sheet->setCellValue('J'.$this->survey_sheet_row, $field->title_ar);

            if($field->field_type_id == "field_collection"){                    
                $this->getFcFields($field);
            }

            if($field->field_type_id == "relation"){
                $this->getFieldChoices($field);
            }
                                                                            
            $this->question_counter++;
            $this->survey_sheet_row++;                
        }

        // add boolean options in the end
        $this->setBooleanFieldChoices();

        // insert the last 'end group'
        $this->sheet->insertNewRowBefore($this->survey_sheet_row , 1);   
        $this->sheet->setCellValue('A'.$this->survey_sheet_row, "end group");
        $this->sheet->getStyle($this->survey_sheet_row)->applyFromArray($this->getStyle(1));
        $this->survey_sheet_row++;



        $this->spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=".$ctype->name." questionnaire template ".time().".xlsx");
        header('Cache-Control: max-age=0'); flush();
        $filename = UPLOAD_DIR_FULL . "\\bg_tasks\\".$ctype->name."_ODK_Template".time().'.xlsx';
        $writer->save($filename);
        return $filename;
        $filename=basename($filename);
    }

    private function getFieldType($field){

        $field_type = "";

        if ($field->field_type_id == "relation") {

            ($field->is_multi ? $field_type = "select_multiple " . 'Q_0' . $this->question_counter : $field_type = "select_one " . 'Q_0' . $this->question_counter);
        } else if ($field->field_type_id == "field_collection") {

            $field_type = "Repeated group";
        } else if ($field->field_type_id == "media") {

            $field_type = "image";
        } else if ($field->field_type_id == "boolean") {

            $field_type = "select_one " . 'Q_00';
        } else {

            $field_type = $field->field_type_id;
        }

        return $field_type;
    }
    
    private function setGroupFieldTitle($field){
        $group_name = (is_null($field->group_name) ? "General" : $field->group_name);
        if($this->current_group != $group_name) { 
            $this->current_group = $group_name;

            // prevent to inset 'end group' at the first group
            if($this->survey_sheet_row > 6){
                $this->sheet->insertNewRowBefore($this->survey_sheet_row , 1);   
                $this->sheet->setCellValue('A'.$this->survey_sheet_row, "end group");
                $this->sheet->getStyle($this->survey_sheet_row )->applyFromArray($this->getStyle(1));
                $this->survey_sheet_row++;
            }
            
            $this->sheet->insertNewRowBefore($this->survey_sheet_row , 1);   
            $this->sheet->setCellValue('A'.$this->survey_sheet_row, "begin group");
            $this->sheet->setCellValue('B'.$this->survey_sheet_row, 'G_' . $this->group_counter);
            $this->sheet->setCellValue('C'.$this->survey_sheet_row, $field->group_name);
            $this->sheet->setCellValue('E'.$this->survey_sheet_row, $field->group_name);
            $this->sheet->getStyle($this->survey_sheet_row)->applyFromArray($this->getStyle(1));
            $this->survey_sheet_row++;
            $this->group_counter++;   
        } 
    }

    private function getFieldChoices($field){

        $excluded_options = array('governorates', 'districts', 'sub_districts', 'communities');
        if(in_array($field->data_source_id, $excluded_options)){
            return;
        }

        // change sheet to choices (sheet 2)
        $this->sheet = $this->spreadsheet->getSheet(1);

        $options = $this->coreModel->nodeModel($field->data_source_id)
                                    ->fields([$field->data_source_display_column, 'name_ar', 'name_ku'])
                                    ->where($field->data_source_fixed_where_condition ? $field->data_source_fixed_where_condition : "1 = 1" )
                                    ->load();
    
        foreach($options as $option){

            $option_label = 'Q_0' . $this->question_counter;
            $this->sheet->setCellValue('A'.$this->choices_sheet_row, $option_label);


            if(property_exists($option, "id")){
                $data_id = $option->id;
                $this->sheet->setCellValue('B'.$this->choices_sheet_row, $data_id);
            }

            if(property_exists($option, "name")){
                $data_eng = $option->name;
                $this->sheet->setCellValue('C'.$this->choices_sheet_row, $data_eng);
            }
            
            if(property_exists($option, "name_ar")){
                $data_ar = $option->name_ar;
                $this->sheet->setCellValue('D'.$this->choices_sheet_row, $data_ar);
            }

            if(property_exists($option,"name_ku")){
                $data_ku = $option->name_ku;
                $this->sheet->setCellValue('E'.$this->choices_sheet_row, $data_ku);
            }

            $this->choices_sheet_row++;
        }
            
        $this->sheet = $this->spreadsheet->getSheet(0);
    }


    private function setBooleanFieldChoices(){
        // change sheet to choices (sheet 2)
        $this->sheet = $this->spreadsheet->getSheet(1);

        $option_label = 'Q_00';
        $this->sheet->setCellValue('A'.$this->choices_sheet_row, $option_label); //label
        $this->sheet->setCellValue('B'.$this->choices_sheet_row, 1); // id
        $this->sheet->setCellValue('C'.$this->choices_sheet_row, "Yes"); // english label
        $this->sheet->setCellValue('D'.$this->choices_sheet_row, "نعم"); // arabic label
        $this->sheet->setCellValue('E'.$this->choices_sheet_row, "به لي"); // kurdish label

        $this->choices_sheet_row++;


        $option_label = 'Q_00';
        $this->sheet->setCellValue('A'.$this->choices_sheet_row, $option_label); //label
        $this->sheet->setCellValue('B'.$this->choices_sheet_row, 0); // id
        $this->sheet->setCellValue('C'.$this->choices_sheet_row, "No"); // english label
        $this->sheet->setCellValue('D'.$this->choices_sheet_row, "لا"); // arabic label
        $this->sheet->setCellValue('E'.$this->choices_sheet_row, "نه خير"); // kurdish label

        $this->choices_sheet_row++;

        $this->sheet = $this->spreadsheet->getSheet(0);
    }

    private function setSettings($ctype){
        // change sheet to settings (sheet 3)
        $this->sheet = $this->spreadsheet->getSheet(2);

        $this->sheet->setCellValue('A'.$this->settings_sheet_row, $ctype->name);
        $this->sheet->setCellValue('B'.$this->settings_sheet_row, $ctype->id);
        $this->sheet->setCellValue('C'.$this->settings_sheet_row, "English");
        $this->sheet->setCellValue('D'.$this->settings_sheet_row, 1);
        $this->sheet->setCellValue('E'.$this->settings_sheet_row, $ctype->id);

        $this->settings_sheet_row++;

        $this->sheet = $this->spreadsheet->getSheet(0);
    }


    private function getFcFields($field){        
        $field_collection = $this->coreModel->nodeModel("ctypes")
                              ->id($field->data_source_id)
                              ->loadFirstOrDefault();

        // add header for repeat group  
        $this->sheet->setCellValue('A'.$this->survey_sheet_row, "begin repeat");
        $this->sheet->setCellValue('B'.$this->survey_sheet_row, $field->name);
        $this->sheet->setCellValue('C'.$this->survey_sheet_row, $field->name);
        $this->sheet->setCellValue('E'.$this->survey_sheet_row, $field->title);
        $this->sheet->setCellValue('G'.$this->survey_sheet_row, (($field->is_required || _strlen($field->required_condition) > 0) ? "Yes" : "No"));
        $this->sheet->setCellValue('I'.$this->survey_sheet_row, $field->title_ku);
        $this->sheet->setCellValue('J'.$this->survey_sheet_row, $field->title_ar);
        $this->sheet->getStyle($this->survey_sheet_row)->applyFromArray($this->getStyle(2));
        $this->survey_sheet_row++;


        foreach($field_collection->fields as $fc_field){
            if(!$fc_field->is_system_field){
 
                if(($fc_field->field_type_id == "component") || $fc_field->is_hidden || $fc_field->is_read_only)
                    continue;

                $this->sheet->setCellValue('A'.$this->survey_sheet_row,$this->getFieldType($fc_field));

                $this->sheet->setCellValue('B'.$this->survey_sheet_row, $fc_field->name);
                $this->sheet->setCellValue('C'.$this->survey_sheet_row, $fc_field->name);
                $this->sheet->setCellValue('E'.$this->survey_sheet_row, $fc_field->title);
                $this->sheet->setCellValue('G'.$this->survey_sheet_row, (($fc_field->is_required || _strlen($fc_field->required_condition) > 0) ? "Yes" : "No"));
                $this->sheet->setCellValue('I'.$this->survey_sheet_row, $fc_field->title_ku);
                $this->sheet->setCellValue('J'.$this->survey_sheet_row, $fc_field->title_ar);


                if($fc_field->field_type_id == "relation"){                   
                    $this->getFieldChoices($fc_field);
                }
                                                                                 
                $this->question_counter++;
                $this->survey_sheet_row++;   

            }
        }

        // add header for end repeat group
        $this->sheet->setCellValue('A'.$this->survey_sheet_row, "end repeat");
        $this->sheet->getStyle($this->survey_sheet_row)->applyFromArray($this->getStyle(2));
        // $this->survey_sheet_row++; 
    }


    private function getStyle($type){
        $style = "";

        if($type == 1){
            //styleArrayTitle
            $style = array(
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'B8CCE4'],
                ]
            );
            
        }else if($type == 2){      
            //styleArrayfields 
            $style = array(
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '000000'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D8E4BC'],
                ]
            );

        }else if($type == 3){ 
            //styleArraybold
            $style =  array(            
                'font'  => array(
                    'bold'  => true,                
                ),          
            );
        }

        return $style;
    }

    public function afterCompletion()
    {
    }
}
