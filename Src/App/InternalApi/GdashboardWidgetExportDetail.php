<?php 

namespace App\InternalApi;

use App\Core\Response;
use \App\Core\BaseInternalApi;

class GdashboardWidgetExportDetail extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $widget = $this->coreModel->nodeModel("widgets")
            ->id($id)
            ->loadFirstOrFail();
        
        if(!empty($widget->secondary_query)){
            $data = $this->coreModel->runChartDataSource($widget, $_POST,true);
        } else {
            $data = $this->coreModel->runChartDataSource($widget, $_POST);
        }
        
        if(!empty($widget->colors)){
            $colors = _explode(",",$widget->colors);
        } else {
            $colors = _explode(",",WIDGET_COLORS);
        }

        $color = isset($colors) && isset($colors[0]) ? _str_replace("'","",$colors[0]) : "'#727cf5'";

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        

        $file =  APP_ROOT_DIR . "/Views/CustomWidgets/" . toPascalCase($widget->export_detail_function_name) . ".php";
        
        if(!is_file($file)){
            $file =  EXT_ROOT_DIR . "/Views/CustomWidgets/" . toPascalCase($widget->export_detail_function_name) . ".php";
        }

        if(!is_file($file)){

            $sheet = $spreadsheet->getSheet(0);
            $row = 1;
            $col = 1;
            

            if(isset($data) && isset($data[0])){

                foreach($data[0] as $key => $value){
                    $sheet->setCellValueByColumnAndRow($col++, $row, $key);
                    
                }
                $row++;
                $col = 1;

                foreach($data as $itm){
                    
                    $itm = (array)$itm;

                    $first_column = array_keys($itm)[0];
                    
                    foreach(array_keys($itm) as $head){
                        $sheet->setCellValueByColumnAndRow($col++, $row, $itm[$head]);
                    }
                    $row++;
                    $col = 1;
                }

                \App\Helpers\PhpOfficeHelper::phpSpreadSheetAutosizeAllColumns($sheet);


            }
        } else {
            require_once $file;
            $fun_name = $widget->export_function_name;
            $spreadsheet = $fun_name($spreadsheet,$data);
        }

        if($this->app->response->getResponseFormat() == Response::$FORMAT_JSON){
            $new_file_name = $widget->name . " " . time() . '.xlsx';

            $fileName = TEMP_DIR . DS . $new_file_name; // path to png image
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $writer->save($fileName);
            
            $result = (object)[
                "status" => "success",
                "fileName" => $new_file_name
            ];

            return_json($result);
            
        } else {
            //\UrlHelper::redirect("/filedownload?temp=1&fname=" . $new_file_name);
            $spreadsheet->setActiveSheetIndex(0);
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            save_file_to_browser("PhpSpreadSheet_Xlsx", $widget->name, $writer);
        }
    }
}
