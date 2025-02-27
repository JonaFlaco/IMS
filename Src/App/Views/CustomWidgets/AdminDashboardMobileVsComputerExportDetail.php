<?php
    function admin_dashboard_mobile_vs_computer_export($obj_php_excel,$data){
    
        $row = 1;
        $col = 0;
        $sheet = $obj_php_excel->getSheet(0);
        
        if(isset($data) && isset($data[0])){

            foreach($data[0] as $key => $value){
                $sheet->setCellValueByColumnAndRow($col++, $row, $key);
                
            }
            $row++;
            $col = 0;

            foreach($data as $itm){
                
                $itm = (array)$itm;

                $first_column = array_keys($itm)[0];
                
                foreach(array_keys($itm) as $head){
                    $sheet->setCellValueByColumnAndRow($col++, $row, $itm[$head]);
                }
                $row++;
                $col = 0;
            }

            foreach($sheet->getColumnDimension() as $colObj) {
                $colObj->setAutoSize(true);
            }
            $sheet->calculateColumnWidths();

        }

        return $obj_php_excel;
    }
    
?>