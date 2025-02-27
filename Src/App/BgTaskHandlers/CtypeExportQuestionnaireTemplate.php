<?php

/*
 * Home controller
 */

namespace Ext\BgTaskHandlers;

use App\Core\BgTaskHandlers;
use \App\Helpers\NumberToWords;
use App\Core\Gctypes\Ctype;
class CtypeExportQuestionnaireTemplate extends BgTaskHandlers
{
    private \App\Core\BgTask $task;
    private $spreadsheet;
    private $sheet;
    private $filter_hidden_fields = null;
    private $current_group = ""; 
    private $row = 9;
    private $counter = 1;
    
    public function __construct($task)
    {
        parent::__construct();

        $this->task = $task;

        $fileObj = $this->coreModel->get_document_file_attachments("sys_core_files", "ctype_export_questionnaire_template");
        $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileObj->full_path);            
        $this->sheet = $this->spreadsheet->getActiveSheet();        
    }

    public function run(){
        $_POST = $this->task->getPostData();        
        $ctype_id = null;
        
        if (!empty($_POST['ctype_id'])) {
            $ctype_id = $_POST['ctype_id'];
        }else
            throw new \App\Exceptions\MissingDataFromRequesterException("Content type ID is required , but not provided");        

        if (!empty($_POST['filter_hidden_fields'])) {
            $this->filter_hidden_fields = ($_POST['filter_hidden_fields'] == "true" ? true : false);
        }

        $ctype = $this->coreModel->nodeModel("ctypes")
                                 ->id($ctype_id)
                                 ->loadFirstOrDefault();

        //usort($ctype->fields, fn($a, $b) => strcmp($a->sort, $b->sort));
        
        $this->sheet->setCellValue('C4',$ctype->name);
              
        foreach($ctype->fields as $field){                                                
            if(!$field->is_system_field){                
                
                if(($this->filter_hidden_fields && ($field->is_hidden || $field->is_read_only)) || ($field->field_type_id == "component"))
                    continue; 
                
                $this->getGroupFieldTitle($field);                
                $this->getFieldStyle($field);

                $this->sheet->setCellValue('B'.$this->row,$this->counter);
                $this->sheet->getStyle('B'.$this->row)->applyFromArray($this->getStyle(5));
                $this->sheet->setCellValue('C'.$this->row,$field->title .($field->field_type_id == "note" ? ": ".$field->description : null));
                $this->sheet->setCellValue('D'.$this->row,$field->title_ar .($field->field_type_id == "note" ? ": ".$field->description_ar : null));
                $this->sheet->setCellValue('E'.$this->row,$field->title_ku .($field->field_type_id == "note" ? ": ".$field->description_ku : null));  
                $this->sheet->setCellValue('F'.$this->row,$this->getFieldType($field));
                $this->sheet->setCellValue('G'.$this->row,(($field->is_required || _strlen($field->required_condition) > 0) ? "yes" : "No"));
                
                if($field->field_type_id == "field_collection"){                    
                    $this->getFcFields($field);
                }

                if($field->field_type_id == "relation"){
                    $this->getFieldChoices($field);
                } 
                
                $this->sheet->setCellValue('K'.$this->row, $field->dependencies);                                                               
                $this->counter++;
                $this->row++;                
            }
        }

        $this->spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=".$ctype->name." questionnaire template ".time().".xlsx");
        header('Cache-Control: max-age=0'); flush();
        $filename = UPLOAD_DIR_FULL . "\\bg_tasks\\".$ctype->name." questionnaire template ".time().'.xlsx';
        $writer->save($filename);
        return $filename;
        $filename=basename($filename);
     
    }
    
    private function getGroupFieldTitle($field){
        $group_name = (is_null($field->group_name) ? "General" : $field->group_name);
        if( $this->current_group != $group_name) { 
            $this->current_group = $group_name;    
            $this->sheet->insertNewRowBefore($this->row , 1);     
            $this->sheet->mergeCells("A" . $this->row . ":L" . $this->row);  
            $this->sheet->setCellValue('A'.$this->row, $field->group_name);
            $this->sheet->getStyle('A'.$this->row)->applyFromArray($this->getStyle(1));
            $this->row++;  
        } 
    }

    private function getFcFields($field){        
        $fc = $this->coreModel->nodeModel("ctypes")
                              ->id($field->data_source_id)
                              ->loadFirstOrDefault();
        $fc_start_row = $this->row;
        foreach($fc->fields as $fc_field){
            if(!$fc_field->is_system_field){

                if(($this->filter_hidden_fields && ($fc_field->is_hidden || $fc_field->is_read_only)) || ($fc_field->field_type_id == "component"))
                    continue; 

                $this->counter++;
                $this->row++;
                $this->sheet->insertNewRowBefore($this->row, 1)->getStyle('B' . $this->row . ':L' . $this->row)->applyFromArray($this->getStyle(3));
                $this->sheet->setCellValue('B'.$this->row,$this->counter);
                $this->sheet->getStyle('B'.$this->row)->applyFromArray($this->getStyle(5));
                $this->sheet->setCellValue('C'.$this->row,$fc_field->title .($fc_field->field_type_id == "note" ? ": ".$fc_field->description : null));
                $this->sheet->setCellValue('D'.$this->row,$fc_field->title_ar .($fc_field->field_type_id == "note" ? ": ".$fc_field->description_ar : null));
                $this->sheet->setCellValue('E'.$this->row,$fc_field->title_ku .($fc_field->field_type_id == "note" ? ": ".$fc_field->description_ku : null));
                $this->sheet->setCellValue('F'.$this->row,$this->getFieldType($fc_field));
                $this->sheet->setCellValue('G'.$this->row,(($fc_field->is_required || _strlen($field->required_condition) > 0) ? "yes" : "No"));

                if($fc_field->field_type_id == "relation"){                   
                    $this->getFieldChoices($fc_field);
                }

                $this->sheet->setCellValue('K'.$this->row, $fc_field->dependencies);  
            }
        }
        $this->sheet->mergeCells("A" . $fc_start_row . ":A" . $this->row);  
        $this->sheet->setCellValue('A'.$fc_start_row,$field->title);
        $this->sheet->getStyle('A'.$fc_start_row)->getAlignment()->setTextRotation(90);
        $this->sheet->getStyle('A'.$fc_start_row)->applyFromArray($this->getStyle(4));
    }

    private function getFieldType($field){
        $field_type = "";
        if($field->field_type_id == "boolean"){
            $field_type = "Yes / No";
        }elseif($field->field_type_id == "relation"){
            ($field->is_multi ? $field_type = "Multiple - choice" : $field_type = "Single - choice");
        }else if ($field->field_type_id == "media"){
            ($field->is_multi ? $field_type = "Media - multiple" : $field_type = "Media");
        }else if($field->field_type_id == "field_collection"){
            $field_type = "Repeated group";
        }else{
            $field_type = $field->field_type_id_display;
        }
        return $field_type;
    }

    private function getFieldChoices($field){
        $options = $this->coreModel->nodeModel($field->data_source_id)
                                    ->fields([$field->data_source_display_column,'name_ar','name_ku'])
                                    ->where($field->data_source_fixed_where_condition ? $field->data_source_fixed_where_condition : "1 = 1" )
                                    ->load();

        if(sizeof($options) <= 50){
            $data = "";
            $data_ar = "";
            $data_ku = "";
            foreach($options as $option){
                $data .= $option->{$field->data_source_display_column} . " \n";
                
                if(property_exists($option,"name_ar")){
                    $data_ar .= $option->name_ar . " \n";
                }

                if(property_exists($option,"name_ku")){
                    $data_ku .= $option->name_ku . " \n";
                }
            }
            
            $this->sheet->setCellValue('H'.$this->row, $data);
            $this->sheet->getStyle('H'.$this->row)->getAlignment()->setWrapText(true);

            $this->sheet->setCellValue('I'.$this->row, $data_ar);
            $this->sheet->getStyle('I'.$this->row)->getAlignment()->setWrapText(true);
            
            $this->sheet->setCellValue('J'.$this->row, $data_ku);
            $this->sheet->getStyle('J'.$this->row)->getAlignment()->setWrapText(true);
        }
    }

    private function getStyle($type){
        $style = "";

        if($type == 1){
            //styleArrayTitle
            $style = array(
                'fill' => array(
                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => 'cde2fa')
                ),			
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '000000'),
                    'size'  => 20,
                    'name'  => 'Arial Nova'
                ),
                'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                )
            );            
        }else if($type == 2){      
            //styleArrayfields 
            $style  = array(
                'fill' => array(
                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
                'color' => array('rgb' => 'ffffff')
                ),			
                'font'  => array(
                    'bold'  => false,
                    'color' => array('rgb' => '000000'),
                    'size'  => 14,
                    'name'  => 'Arial Nova'
                ),
                'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                )
            );
        }else if($type == 3){ 
            //styleArrayfcfields
            $style  = array(
                'fill' => array(
                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE,
                'color' => array('rgb' => 'fce4c5')
                ),			
                'font'  => array(
                    'bold'  => false,
                    'color' => array('rgb' => '000000'),
                    'size'  => 14,
                    'name'  => 'Arial Nova'
                ),
                'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                )
            );
        }else if($type == 4){ 
            //styleArrayheaderfcfield
            $style  = array(
                'fill' => array(
                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffce8f')
                ),			
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '000000'),
                    'size'  => 14,
                    'name'  => 'Arial Nova'
                ),
                'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                )
            );
        }else if($type == 5){ 
            //styleArraybold
            $style =  array(            
                'font'  => array(
                    'bold'  => true,                
                ),          
            );
        }

        return $style;
    }

    private function getFieldStyle($field){
        $this->sheet->insertNewRowBefore($this->row , 1)->getStyle('B' . $this->row . ':L' . $this->row)->applyFromArray((($field->field_type_id == "field_collection") ? $this->getStyle(4)  : $this->getStyle(2)) );
        $this->sheet->getStyle('B' . $this->row . ':L' . $this->row)->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('cde2fa'));
        $this->sheet->getStyle('B' . $this->row . ':L' . $this->row)->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('cde2fa'));
        $this->sheet->getStyle('B' . $this->row . ':L' . $this->row)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('cde2fa'));
        $this->sheet->getStyle('B' . $this->row . ':L' . $this->row)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('cde2fa'));                
    }

    public function afterCompletion()
    {
    }
}
