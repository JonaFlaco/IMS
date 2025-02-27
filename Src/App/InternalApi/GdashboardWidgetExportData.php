<?php 

namespace App\InternalApi;

use App\Core\Response;
use App\Core\BaseInternalApi;
class GdashboardWidgetExportData extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $widget = $this->coreModel->nodeModel("widgets")
            ->id($id)
            ->loadFirstOrFail();
        
        $data = $this->coreModel->runChartDataSource($widget, $_POST);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        $file =  APP_ROOT_DIR . "/Views/CustomWidgets/" . toPascalCase($widget->export_function_name) . ".php";
        
        if(!is_file($file)){
            $file =  EXT_ROOT_DIR . "/Views/CustomWidgets/" . toPascalCase($widget->export_function_name) . ".php"; 
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

            $new_file_name = _str_replace("&", "", $new_file_name);

            
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
            save_file_to_browser("PhpSpreadSheet_Xlsx", $widget->name, $writer);
        }
        

    }
}
