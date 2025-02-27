<?php

namespace App\Libraries;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;
use App\Helpers\MiscHelper;
use App\Models\CoreModel;

Class DataExportIndividual {

    private static $coreModel;
    private static $imageOffset = 3;
    private static $ctypeObj;

    public static function main(int $recordId = null,array $params = array()){
        
        self::$coreModel = CoreModel::getInstance();

        if(isset($params['ctype_id']))
            $ctypeId = $params['ctype_id'];
        
        self::$ctypeObj = (new Ctype)->load($ctypeId);
        
        if(empty(self::$ctypeObj)) {
            throw new \App\Exceptions\NotFoundException("Content-Type $ctypeId not found");
        }

        $fields = self::$ctypeObj->getFields();
        
        $data_from_db = self::$coreModel->nodeModel(self::$ctypeObj->id)
            ->id($recordId)
            ->deepLoad(true)
            ->loadFirstOrFail();
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(DOC_TEMPLATE_FOLDER . "\\" . toPascalCase(self::$ctypeObj->id) . "Individual.xlsx");
    
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $lastColumn = $sheet->getHighestColumn();
        $lastColumn++;
        $lastRow = $sheet->getHighestRow();

        $last_fc_row_count = 0;
        $last_fc_row_size_max = 0;
        $last_fc_name = "";
        for ($row = 1; $row <= $lastRow; $row++) {

            $fc_row_size_max = 0;
            for ($column = 'A'; $column != $lastColumn; $column++) {
                

                $cell_obj = $sheet->getCell($column.$row);
                $cell = $sheet->getCell($column.$row)->getFormattedValue();
                
                if(_strpos($cell, "\${") !== false){
                
                    
                    if(_strpos($cell, "begin fc") !== false){
                        
                        $last_fc_name = _str_replace("\${begin fc ","",$cell);
                            
                        $last_fc_name = _str_replace("}","",$last_fc_name);
                        
                        $last_fc_row_count = count($data_from_db->{$last_fc_name});
                        
                        if($last_fc_row_count > $fc_row_size_max)
                            $fc_row_size_max = $last_fc_row_count;
                        
                    } else if (_strpos($cell, "end fc") !== false){
                        $last_fc_name = "";
                    } else if ($cell == "\${sum}"){
                        
                        $sheet->setCellValue($column.$row, "=SUM(" . $column . "" . ($row - $last_fc_row_size_max) . ":" . $column . ($row - 1) . ")");

                    } else if ($fc_row_size_max == 0) {
                        
                        $key = _str_replace("\${","",$cell);
                        $key = _str_replace("}","",$key);
                        $ds = "";
                        
                        if(_strpos($key, "->") !== false){
                            $exp = _explode("->", $key);
                            $ds = $exp[0];
                            $key = $exp[1];
                        }

                        foreach($fields as $f){
                            
                            if($key == $f->name || $ds == $f->name){
                                $field = $f;
                                if($f->field_type_id == "media" && $f->is_multi != true){
                                    
                                    if(isset($data_from_db->{$key . "_name"}) && _strlen($data_from_db->{$key . "_name"}) > 0){

                                        $col_width = 0;
                                        $col_height = 0;
                                        $is_merge = false;
                                        foreach ($sheet->getMergeCells() as $cells) {
                                            if ($cell_obj->isInRange($cells)) {
                                                if(!empty($cells)){
                                                    $is_merge = true;
                                                    $from_c = "";
                                                    $to_c = "";
                                                    $from_r = "";
                                                    $to_r = "";

                                                    $ar = _explode(":", $cells);
                                                    
                                                    $from_c = MiscHelper::numToAlphabet(substr($ar[0], 0,1));
                                                    $from_r = substr($ar[0], 1);
                                                    
                                                    $to_c = MiscHelper::numToAlphabet(substr($ar[1], 0,1));
                                                    $to_r = substr($ar[1], 1);
                                                    
                                                    for ($c = $from_c; $c <= $to_c; $c++) {
                                                        $col_width += $sheet->getColumnDimensionByColumn($c)->getWidth();
                                                    }
                                                    for ($r = $from_r; $r <= $to_r; $r++) {
                                                        $col_height += $sheet->getRowDimension($r)->getRowHeight();
                                                    }

                                                }
                                                
                                                
                                                break;
                                            }
                                            
                                        }
                                      
                                    
                                        if($is_merge == false){
                                            $col_width = $sheet->getColumnDimension($column)->getWidth();
                                            $col_height = $sheet->getRowDimension($row)->getRowHeight();
                                        }

                                        $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                        
                                        $objDrawing->setPath(UPLOAD_DIR_FULL . "/" . self::$ctypeObj->id . "/" . $data_from_db->{$key . "_name"});
                                        $objDrawing->setResizeProportional(false); 
                                        
                                        $objDrawing->setWidthAndHeight(($col_width * 7) - (self::$imageOffset * 2),($col_height * 1.34) - (self::$imageOffset * 2));

                                        $objDrawing->setOffsetX(self::$imageOffset); //pixels
                                        $objDrawing->setOffsetY(self::$imageOffset); //pixels

                                        $objDrawing->setCoordinates($column . $row);
                                        $objDrawing->setWorksheet($sheet);


                                        $sheet->setCellValue($column.$row, "");
                                    }
                                     continue;
                                } else if($f->field_type_id == "media" && $f->is_multi == true){

                                    $col_width = 0;
                                    $col_height = 0;
                                    $is_merge = false;
                                    foreach ($sheet->getMergeCells() as $cells) {
                                        if ($cell_obj->isInRange($cells)) {
                                            if(!empty($cells)){
                                                $is_merge = true;
                                                $from_c = "";
                                                $to_c = "";
                                                $from_r = "";
                                                $to_r = "";

                                                $ar = _explode(":", $cells);
                                                
                                                $from_c = MiscHelper::numToAlphabet(substr($ar[0], 0,1));
                                                $from_r = substr($ar[0], 1);
                                                
                                                $to_c = MiscHelper::numToAlphabet(substr($ar[1], 0,1));
                                                $to_r = substr($ar[1], 1);
                                                
                                                for ($c = $from_c; $c <= $to_c; $c++) {
                                                    $col_width += $sheet->getColumnDimensionByColumn($c)->getWidth();
                                                }
                                                for ($r = $from_r; $r <= $to_r; $r++) {
                                                    $col_height += $sheet->getRowDimension($r)->getRowHeight();
                                                }

                                            }
                                            
                                            
                                            break;
                                        }
                                        
                                    }
                                    
                                
                                    if($is_merge == false){
                                        $col_width = $sheet->getColumnDimension($column)->getWidth();
                                        $col_height = $sheet->getRowDimension($row)->getRowHeight();
                                    }

                                    
                                    $count = 0;
                                    $size = sizeof($data_from_db->{$key});
                                    
                                    foreach($data_from_db->{$key} as $itm){
                                        
                                        //echo UPLOAD_DIR_FULL . "\\" . $ctypeObj->name . "\\members.bnf_documents_7f9f21662c69e4fe8a94ebaa4d2c0c14.jpg<br>";
                                        if(isset($itm->name) && _strlen($itm->name) > 0){
                                            $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                            
                                            $objDrawing->setPath(UPLOAD_DIR_FULL . "/" . self::$ctypeObj->id . "/" . $itm->name);
                                            $objDrawing->setResizeProportional(false); 
                                            
                                            
                                            $objDrawing->setWidthAndHeight((($col_width / $size) * 7) - (self::$imageOffset * 2),$col_height * 1.34 - (self::$imageOffset * 2));
                                            $objDrawing->setOffsetX(((($col_width / $size) * 7) * $count) + self::$imageOffset); //pixels
                                            $objDrawing->setOffsetY(self::$imageOffset); //pixels


                                            $objDrawing->setCoordinates($column . $row);
                                            $objDrawing->setWorksheet($sheet);

                                            $count++;
                                        }
                                        $sheet->setCellValue($column.$row, "");
                                    }
                                     
                                     continue;
                                }
                            }
                        }

                        $new_value = self::getValueIndividual($key, $ds, $field->data_source_id, $data_from_db, $fields);
                        
                        $sheet->setCellValue($column.$row, $new_value);
                    }
                        
                }
                

            }
            
            
            $last_fc_row_size_max = $fc_row_size_max;

            if($fc_row_size_max > 0){
                
                $sheet->insertNewRowBefore($row + 2, $fc_row_size_max - 1);

                $pending_fc_name = "";
                $fc_start_column = "";
                for ($column = 'A'; $column != $lastColumn; $column++) {
                

                    $cell = $sheet->getCell($column.$row)->getFormattedValue();
                    if(_strpos($cell, "\${") !== false){
                    
                        if(_strpos($cell, "begin fc") !== false){
                            
                            $last_fc_name = _str_replace("\${begin fc ","",$cell);
                            $last_fc_name = _str_replace("}","",$last_fc_name);
                            
                            
                            $last_fc_row_count = count($data_from_db->{$last_fc_name});
                            $pending_fc_name = $last_fc_name;
                            $fc_start_column = $column;
                            
                            if($last_fc_row_count > $fc_row_size_max)
                                $fc_row_size_max = $last_fc_row_count;
                            

                        } else if (_strpos($cell, "end fc") !== false){

                            //echo "lfc: $last_fc_name <br>";
                            self::generateFCIndividual($sheet, $fc_start_column, $column, $row + 1, $last_fc_row_count, $data_from_db->{$last_fc_name}, (new CtypeField)->loadByCtypeId(self::$ctypeObj->id . "_" . $last_fc_name));
                            
                        } else if ($cell == "\${sum}"){
                            
                            $sheet->setCellValue($column.$row, "=SUM(" . $column . "" . ($row - $fc_row_size_max) . ":" . $column . ($row - 1) . ")");

                        }
                    }
                        
                }
                $sheet->removeRow($row,1);

                $lastRow += $fc_row_size_max - 1;
                $row += $fc_row_size_max - 1;
                
            }
        }
        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        save_file_to_browser("PhpSpreadSheet_Xlsx", self::$ctypeObj->name, $writer);
    }

    private static function generateFCIndividual($sheet, $start_column, $end_column, $first_row, $last_fc_row_count, $data_from_db, $fields){
        
        for ($column = $start_column; $column <= $end_column; $column++) {
            $base_cell_content = $sheet->getCell($column.$first_row)->getFormattedValue();
            for ($row = $first_row; $row <= $first_row + $last_fc_row_count - 1; $row++) {


                if(_strpos($base_cell_content, "\${") !== false){
                    $key = _str_replace("\${","",$base_cell_content);
                    $key = _str_replace("}","",$key);
            
                    foreach($fields as $field){
                            
                        if($key == $field->name){
                            
                            if($field->field_type_id == "media" && $field->is_multi != true){
                                
                                if(isset($data_from_db[$row - $first_row]->{$key . "_name"}) && _strlen($data_from_db[$row - $first_row]->{$key . "_name"}) > 0){
                                    
                                    $col_width = 0;
                                    $col_height = 0;
                                    $is_merge = false;
                                    foreach ($sheet->getMergeCells() as $cells) {
                                        if ($cells->isInRange($cells)) {
                                            if(!empty($cells)){
                                                $is_merge = true;
                                                $from_c = "";
                                                $to_c = "";
                                                $from_r = "";
                                                $to_r = "";

                                                $ar = _explode(":", $cells);
                                                
                                                $from_c = MiscHelper::numToAlphabet(substr($ar[0], 0,1));
                                                $from_r = substr($ar[0], 1);
                                                
                                                $to_c = MiscHelper::numToAlphabet(substr($ar[1], 0,1));
                                                $to_r = substr($ar[1], 1);
                                                
                                                for ($c = $from_c; $c <= $to_c; $c++) {
                                                    $col_width += $sheet->getColumnDimensionByColumn($c)->getWidth();
                                                }
                                                for ($r = $from_r; $r <= $to_r; $r++) {
                                                    $col_height += $sheet->getRowDimension($r)->getRowHeight();
                                                }

                                            }
                                            
                                            
                                            break;
                                        }
                                        
                                    }
                                    
                                
                                    if($is_merge == false){
                                        $col_width = $sheet->getColumnDimension($column)->getWidth();
                                        $col_height = $sheet->getRowDimension($row)->getRowHeight();
                                    }

                                    
                                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                    
                                    $objDrawing->setPath(UPLOAD_DIR_FULL . "/" . self::$ctypeObj->id . "/" . $data_from_db[$row - $first_row]->{$key . "_name"});
                                    $objDrawing->setResizeProportional(false); 
                                    
                                    $objDrawing->setWidthAndHeight(($col_width * 7) - (self::$imageOffset * 2),($col_height * 1.34) - (self::$imageOffset * 2));

                                    $objDrawing->setOffsetX(self::$imageOffset); //pixels
                                    $objDrawing->setOffsetY(self::$imageOffset); //pixels

                                    $objDrawing->setCoordinates($column . $row);
                                    $objDrawing->setWorksheet($sheet);


                                    $sheet->setCellValue($column.$row, "");
                                }
                                 continue;
                            } else if($field->field_type_id == "media" && $field->is_multi == true){

                                $col_width = 0;
                                $col_height = 0;
                                $is_merge = false;
                                foreach ($sheet->getMergeCells() as $cells) {
                                    if ($cells->isInRange($cells)) {
                                        if(!empty($cells)){
                                            $is_merge = true;
                                            $from_c = "";
                                            $to_c = "";
                                            $from_r = "";
                                            $to_r = "";

                                            $ar = _explode(":", $cells);
                                            
                                            $from_c = MiscHelper::numToAlphabet(substr($ar[0], 0,1));
                                            $from_r = substr($ar[0], 1);
                                            
                                            $to_c = MiscHelper::numToAlphabet(substr($ar[1], 0,1));
                                            $to_r = substr($ar[1], 1);
                                            
                                            for ($c = $from_c; $c <= $to_c; $c++) {
                                                $col_width += $sheet->getColumnDimensionByColumn($c)->getWidth();
                                            }
                                            for ($r = $from_r; $r <= $to_r; $r++) {
                                                $col_height += $sheet->getRowDimension($r)->getRowHeight();
                                            }

                                        }
                                        
                                        
                                        break;
                                    }
                                    
                                }
                                
                            
                                if($is_merge == false){
                                    $col_width = $sheet->getColumnDimension($column)->getWidth();
                                    $col_height = $sheet->getRowDimension($row)->getRowHeight();
                                }

                                
                                $count = 0;
                                
                                $size = sizeof($data_from_db[$row - $first_row]->{$key});
                                
                                foreach($data_from_db[$row - $first_row]->{$key} as $itm){
                                    
                                    if(isset($itm->name) && _strlen($itm->name) > 0){
                                        $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                        
                                        $objDrawing->setPath(UPLOAD_DIR_FULL . "/" . self::$ctypeObj->id . "/" . $itm->name);
                                        $objDrawing->setResizeProportional(false); 
                                        
                                        $objDrawing->setWidthAndHeight((($col_width / $size) * 7) - (self::$imageOffset * 2),$col_height * 1.34 - (self::$imageOffset * 2));
                                        $objDrawing->setOffsetX(((($col_width / $size) * 7) * $count) + self::$imageOffset); //pixels
                                        $objDrawing->setOffsetY(self::$imageOffset); //pixels

                                        $objDrawing->setCoordinates($column . $row);
                                        $objDrawing->setWorksheet($sheet);

                                        $count++;
                                    }
                                    $sheet->setCellValue($column.$row, "");
                                }
                                 
                                 continue;
                            }
                        }
                    }



                    if($base_cell_content == "\${#}"){
                        $new_value = ($row - $first_row) + 1;
                    } else {
                        $key = _str_replace("\${","",$base_cell_content);
                        $key = _str_replace("}","",$key);
                        $ds = "";
                        
                        if(_strpos($key, "->") !== false){
                            $exp = _explode("->", $key);
                            $ds = $exp[0];
                            $key = $exp[1];
                        }
                        
                        foreach($fields as $f){
                            if($ds == $f->name){
                                $field = $f;
                            }
                        }
                        
                        if(isset($field)){
                            
                            $new_value = self::getValueIndividual($key,$ds,$field->data_source_id, $data_from_db[$row - $first_row], $fields);
                        } else {
                            $new_value = $key;
                        }

                    }
                    $sheet->setCellValue($column.$row, $new_value);
                }

            }
            
        }

    }

    private static function getValueIndividual($key, $ds,$data_source_id, $data_from_db, $fields){
       
        $key = _str_replace("\${","",$key);
        $key = _str_replace("}","",$key);

        if(isset($ds) && _strlen($ds) > 0 && isset($data_source_id)){ //Empty data_source_id
            $fields = (new CtypeField)->loadByCtypeId($data_source_id);
        }

       foreach($fields as $field){

           if($key == $field->name){
            
               if($field->field_type_id == "relation" && $field->data_source_value_column_is_text != true) {

                    if(isset($ds) && _strlen($ds) > 0){
                        return $data_from_db->{$ds . "_detail"}[0]->{$field->name . "_display"};
                    } else {
                        return $data_from_db->{$field->name . "_display"};
                    }
                } else if($field->field_type_id == "media"){
                
                } else if($field->field_type_id == "boolean") {
                    return $data_from_db->{$field->name} ? "Yes" : "No";
                } else if($field->field_type_id == "date"){
                    if(isset($ds) && _strlen($ds) > 0){
                        
                        return isset($data_from_db->{$ds . "_detail"}[0]->{$field->name}) ? date('d-m-Y',strtotime($data_from_db->{$ds . "_detail"}[0]->{$field->name})) : "";

                    } else {
                        return isset($data_from_db->{$field->name}) ? date('d-m-Y',strtotime($data_from_db->{$field->name})) : "";
                    }
                } else {
                    
                if(isset($ds) && _strlen($ds) > 0){
                    
                    return isset($data_from_db->{$ds . "_detail"}[0]->{$field->name}) ? $data_from_db->{$ds . "_detail"}[0]->{$field->name} : "";

                } else {
                    
                    return isset($data_from_db->{$field->name}) ? $data_from_db->{$field->name} : "";
                }
               }
           }
       }
       return $key;
   }
}