<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;

class GdashboardWidgetGetDetail extends BaseInternalApi {
    
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

        $color = isset($colors) && isset($colors[0]) ? _str_replace("'","",$colors[0]) : "'#4855f1'";

        
        if(isset($data) && isset($data[0])){

            $file =  APP_ROOT_DIR . "\\Views\\CustomWidgets\\" . toPascalCase($widget->pop_up_template_file_name) . ".php";
            
            if(!is_file($file)){
                $file =  EXT_ROOT_DIR . "\\Views\\CustomWidgets\\" . toPascalCase($widget->pop_up_template_file_name) . ".php"; 
            }
            if(!is_file($file)){

                $html_code = "
        
                    <table class=\"table table-bordered table-sm\"><thead><tr style=\"background-color:" . $color . " !important;\" class=\"text-white\">";


                foreach($data[0] as $key => $value){
                    $html_code .= "<th>" . e($key) . "</th>";
                }
                $html_code .= "</tr></thead><tbody>";

                foreach($data as $itm){
                    $html_code .= "<tr>";
                    
                    $itm = (array)$itm;

                    

                    $first_column = array_keys($itm)[0];
                    
                    foreach(array_keys($itm) as $head){
                        $html_code .= "<td class=\"align-middle\">" . e($itm[$head]) . "</td>";
                    }

                    $html_code .= "</tr>";
                    
                }

                
                $html_code .= "</tbody></table>
                ";

            } else {

                ob_start();
                require($file);
                $html_code = ob_get_clean();
            }

        } else {
            $html_code = "<h3 class=\"text-danger\"><i class=\" mdi mdi-block-helper\"></i>" . t("No data to show") . "</h3>";
        }

        echo $html_code;


    }

}