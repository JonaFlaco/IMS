<?php

namespace App\Helpers;

class PhpOfficeHelper {
    
    public static function phpSpreadSheetCellColor($sheet, $cells,$color){
        $sheet->getStyle($cells)->getFill()->applyFromArray(array(
            'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => $color
            )
        ));
    }

    public static function phpSpreadSheetGetCellIndexByName($headings, $name){
        
        foreach($headings as $key => $h){
            
            if(_trim(_strtolower($h) == _strtolower($name)))
                return intval($key) + 1;
        }
        return null;
    }

    public static function phpSpreadSheetAutosizeAllColumns(&$sheet){
        
        foreach($sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . 1,NULL,TRUE,FALSE)[0] as $key => $value){
            $sheet->getColumnDimension(\App\Helpers\MiscHelper::numToAlphabet($key))->setAutoSize(true);
        }
        $sheet->calculateColumnWidths();

        // foreach(range('A',\MiscHelper::numToAlphabet($noOfColumns - 2)) as $columnID) {
        //     $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
        //         ->setAutoSize(true);
        // }

    }

    public static function phpSpreadSheetSetAsProtected($spreadsheet){

        $spreadsheet->getSecurity()->setLockWindows(true);
        $spreadsheet->getSecurity()->setLockStructure(true);
        $spreadsheet->getSecurity()->setWorkbookPassword('misEXCELpwd');

        $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);
        $spreadsheet->getActiveSheet()->getProtection()->setSort(true);
        $spreadsheet->getActiveSheet()->getProtection()->setInsertRows(true);
        $spreadsheet->getActiveSheet()->getProtection()->setFormatCells(true);

        $spreadsheet->getActiveSheet()->getProtection()->setPassword('misEXCELpwd');

        return $spreadsheet;
    }

}