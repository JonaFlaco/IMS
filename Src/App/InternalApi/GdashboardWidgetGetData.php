<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
class GdashboardWidgetGetData extends BaseInternalApi {
    
    private $minimal = false;
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $this->minimal = !empty($params['minimal']);
        
        $widget = $this->coreModel->nodeModel("widgets")
            ->id($id)
            ->loadFirstOrFail();
        
        $data = null;
        if(!empty($widget->query)) {
            $data = $this->coreModel->runChartDataSource($widget, $_POST);
        }
        
        $isEmpty = false;
        if($widget->type != "html"){
            $isEmpty = true;
            foreach($data as $itm){
            if($itm->value <> 0 ){
                $isEmpty = false;
            }
            }

        }

        $html_code = "";
        if($widget->force_render_widget_even_if_result_is_empty != true && empty($data) || $isEmpty == true){
            $html_code = "
            <div id=\"chart_$widget->id\" class=\"col-xl-12\">
                <div class=\"card\">
                    <div class=\"card-body\">

                        <h4 class=\"header-title mb-3\">$widget->name</h4>

                        <h3 class=\"text-info\"><i class=\" mdi mdi-block-helper\"></i> No data to show</h3>
                    </div>
                </div>
            </div>
            ";

            
            echo "{\"status\":\"success\",\"result\":{\"html\":\"" . \App\Helpers\MiscHelper::eJson($html_code) . "\", \"labels\":[], \"series\":[],\"categories\":[]}}";
            exit;
        }

        if($widget->type == "html"){
            echo $this->getChartHtmlData($widget, $data);
        
        } else if($widget->type == "pie")
            echo $this->getChartPieData($widget, $data);
        else 
            echo $this->getChartColumnData($widget, $data);


    }

    private function getColors($widget) {
        
        $colors = (isset($widget->colors) ? $widget->colors : WIDGET_COLORS);

        $colors = _str_replace("'", "", $colors);

        return _explode(",", $colors);
    }
    private function getMetaData($widget) {
        
        return json_encode((object)[
            "id" => $widget->id,
            "name" => $widget->name,
            "colors" => $this->getColors($widget),
            "hide_lables" => $widget->hide_lables,
            "allow_pop_up_detail" => $widget->allow_pop_up_detail,
            "allow_drilldown" => $widget->allow_drilldown,
        ]);
    }

    private function getChartPieData($widget, $data){
        
        $labels = "";
        $series = "";
        $categories = "";
        $table = "";
        
        $total = 0;
        $drilldown = "";
        $html_code = "";
        $i = 0;
        if(isset($data) && sizeof($data) > 0){
            $series .= "{\"name\": \"" . (isset($data[0]->{"label_2"}) ? $data[0]->label_2 : $data[0]->label) . "\", \"colorByPoint\": 1,\"data\": [";
            foreach($data as $itm){

                $itm->value = floatval($itm->value);

                if($i++ > 0)
                    $series .= "},";
                $total += intval($itm->value);
                $series .= "{\"name\":\"$itm->label\",\"y\":" . intval($itm->value) . ($widget->allow_drilldown == true ? ",\"drilldown\":\"" . get_machine_name($itm->label) . "\"" : "");
                
            }

            $series = $series . "}]}";

            

            $current_group = "-EMPTY-";
            if($widget->allow_drilldown == true){
                $data2 = $this->coreModel->runChartDataSource($widget, $_POST,true);

                foreach($data2 as $itm){

                    if($itm->value == 0){
                        $itm->value = "0";
                    }  else if ($itm->value < 0) {
                        $itm->value = "0" . $itm->value;
                    }

                    $total += intval($itm->value);
                    if($current_group != $itm->label){
                        $current_group = $itm->label;
                        if(!empty($drilldown)){
                            $drilldown .= "]},";
                        }
                        
                        $drilldown .= "{\"name\": \"" . get_machine_name($itm->label). "\",\"id\": \"" . get_machine_name($itm->label) . "\",\"data\":[";

                        $drilldown .= "[\"$itm->label_2\", $itm->value]";
                    } else {
                        $drilldown .= ",[\"$itm->label_2\", $itm->value]";
                    }
                    

                }
                if(!empty($drilldown))
                $drilldown = "{\"series\":[$drilldown]}]}";
            }

            if(intval($total) == 0){
                $series = "}";
            }

            
            $file =  APP_ROOT_DIR . "\\Views\\CustomWidgets\\" . toPascalCase($widget->template_file_name) . ".php";
            
            if(!is_file($file)){
                $file =  EXT_ROOT_DIR . "\\Views\\CustomWidgets\\" . toPascalCase($widget->template_file_name) . ".php";
            }
            
            if(!is_file($file)){
                $html_code = "
                <div class=\"card\">
                    <div class=\"card-body\">

                        ";
                        if($this->minimal != true) { 
                        $html_code .= "
                        <div class=\"dropdown float-end\">
                            <a href=\"javascript: void(0);\" class=\"dropdown-toggle arrow-none card-drop\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                                <i class=\"mdi mdi-dots-vertical\"></i>
                            </a>
                            <div class=\"dropdown-menu dropdown-menu-right\">
                                <a onclick=\"vm.chart_export_data('$widget->id')\" href=\"javascript: void(0);\" class=\"dropdown-item\">Export As Excel</a>
                                <button onclick=\"vm.chart_$widget->id" . "_export_as_image()\" class=\"dropdown-item\">Export As Image</button>
                            </div>
                        </div>
                        ";
                        }
                        $html_code .= "
                        <h4 class=\"header-title mb-3\">$widget->name</h4>

                        <div id=\"chart_$widget->id\">
                    </div>
                </div>
                ";
            } else {
                ob_start();
                require($file);
                $html_code = ob_get_clean();

            }
        }
        
        
        if(empty($drilldown)){
            $drilldown = "{}";
        }

        return "{\"status\":\"success\",\"result\":{\"meta\":" . $this->getMetaData($widget) . ",\"html\":\"" . \App\Helpers\MiscHelper::eJson($html_code) . "\", \"labels\":[$labels], \"series\":[$series],\"categories\":[$categories],\"drilldown\":$drilldown}}";
    }
    

    private function getChartColumnData($widget, $data){
        
        $labels = "";
        $series = "";
        $categories = "";
        $categories_is_set= null;
        $table = "";
        $drilldown = "";

        $current_group = "-EMPTY-";

        $total = 0;

        if($widget->allow_drilldown != true){
            
            foreach($data as $itm){
                
                $itm->value = floatval($itm->value);
                
                $total += intval($itm->value);
                if($current_group != $itm->label){
                    $current_group = $itm->label;
                    if(!empty($series)){
                        $series .= "]},";
                        $categories_is_set = true;
                    }
                    
                    $series .= "{\"name\": \"$itm->label\",\"data\":[$itm->value";
                } else {
                    
                    $series .= ",$itm->value";
                }

                if($categories_is_set != true){
                    if(!empty($categories))
                        $categories .= ",";
                    $categories .= "\"" . (isset($itm->label_2) ? $itm->label_2 : "") . "\"";
                }

            }

            if(!empty($series))
            $series .= "]}";
            
        
        } else {

            foreach($data as $itm){

                $itm->value = floatval($itm->value);

                $total += intval($itm->value);
                if($current_group != $itm->label){
                    $current_group = $itm->label;
                    if(!empty($series)){
                        $series .= "]},";
                    }
                    
                    $series .= "{\"name\": \"$itm->label\",\"colorByPoint\": 1,\"data\":[";
                    
                    $series .= "{\"name\":\"$itm->label_2\",\"y\":$itm->value,\"drilldown\":\"" . get_machine_name($itm->label_2) . "\"}";
                } else {
                    $series .= ",{\"name\":\"$itm->label_2\",\"y\":$itm->value,\"drilldown\":\"" . get_machine_name($itm->label_2) . "\"}";
                }

            }

            if(!empty($series))
            $series .= "]}";

            $data2 = $this->coreModel->runChartDataSource($widget, $_POST,true);

            foreach($data2 as $itm){

                $itm->value = floatval($itm->value);

                $total += intval($itm->value);
                if($current_group != $itm->label){
                    $current_group = $itm->label;
                    if(!empty($drilldown)){
                        $drilldown .= "]},";
                    }
                    
                    $drilldown .= "{\"name\": \"" . get_machine_name($itm->label). "\",\"id\": \"" . get_machine_name($itm->label) . "\",\"data\":[";

                    $drilldown .= "[\"$itm->label_2\", $itm->value]";
                } else {
                    $drilldown .= ",[\"$itm->label_2\", $itm->value]";
                }
                

            }
            if(!empty($drilldown))
            $drilldown = "{\"series\":[$drilldown]}]}";

        }

        if(empty($labels))
            $labels = "";

        if(empty($series))
            $series = "";

        if(empty($categories))
            $categories = "";

        $html_code = null;
            
        if(isset($data) && isset($data[0])){
            $data = $data[0];
        }

        
        $file =  APP_ROOT_DIR . "\\Views\\CustomWidgets\\" . toPascalCase($widget->template_file_name) . ".php";
        
        if(!is_file($file)){
            $file =  EXT_ROOT_DIR . "\\Views\\CustomWidgets\\" . toPascalCase($widget->template_file_name) . ".php";
        }
        if(!is_file($file)){
            $html_code = "
            <div class=\"card\">
                <div class=\"card-body\">
                ";
                if($this->minimal != true) { 
                $html_code .= "
                    <div class=\"dropdown float-end\">
                        <a href=\"javascript: void(0);\" class=\"dropdown-toggle arrow-none card-drop\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                            <i class=\"mdi mdi-dots-vertical\"></i>
                        </a>
                        <div class=\"dropdown-menu dropdown-menu-right\">
                            <a onclick=\"vm.chart_export_data('$widget->id')\" href=\"javascript: void(0);\" class=\"dropdown-item\">Export As Excel</a>
                            <button onclick=\"vm.chart_$widget->id" . "_export_as_image()\" class=\"dropdown-item\">Export As Image</button>
                        </div>
                    </div>
                    ";
                }
                $html_code .= "
                    <h4 class=\"header-title mb-3\">$widget->name</h4>

                    <div id=\"chart_$widget->id\">
                </div>
            </div>
            ";
        } else {
            ob_start();
            require($file);
            $html_code = ob_get_clean();
        }

        
        if(empty($drilldown))
            $drilldown = "{}";

        if($total == 0) {
            return "{\"status\":\"success\",\"result\":{\"meta\":" . $this->getMetaData($widget) . ",\"html\":\"" . \App\Helpers\MiscHelper::eJson($html_code) . "\",\"labels\":[], \"series\":[],\"categories\":[],\"drilldown\":{}}}";
        } else {
            return "{\"status\":\"success\",\"result\":{\"meta\":" . $this->getMetaData($widget) . ",\"html\":\"" . \App\Helpers\MiscHelper::eJson($html_code) . "\", \"table\":[$table], \"labels\":[$labels], \"series\":[$series],\"categories\":[$categories],\"drilldown\":$drilldown}}";
        }
    }



    private function getChartHtmlData($widget, $data){

        if(!empty($widget->colors)){
            $colors = _explode(",",$widget->colors);
        } else {
            $colors = _explode(",",WIDGET_COLORS);
        }

        $html_code = "";
        $color = isset($colors) && isset($colors[0]) ? _str_replace("'","",$colors[0]) : "'#4855f1'";

        if($widget->force_render_widget_even_if_result_is_empty || ((isset($data) && isset($data[0])))){

            $file =  APP_ROOT_DIR . "\\Views\\CustomWidgets\\" . toPascalCase($widget->template_file_name) . ".php";
            if(!is_file($file)){
                $file =  EXT_ROOT_DIR . "\\Views\\CustomWidgets\\" . toPascalCase($widget->template_file_name) . ".php";
            }
            if(!is_file($file)){

                $html_code = "
        <div class=\"card\">
                <div class=\"card-body\">
                ";
                if($this->minimal != true) {
                $html_code .= "
                    <div class=\"dropdown float-end\">
                        <a href=\"javascript: void(0);\" class=\"dropdown-toggle arrow-none card-drop\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                            <i class=\"mdi mdi-dots-vertical\"></i>
                        </a>
                        <div class=\"dropdown-menu dropdown-menu-right\">
                            <a onclick=\"vm.chart_export_data('$widget->id')\" href=\"javascript: void(0);\" class=\"dropdown-item\">Export As Excel</a>
                        </div>
                    </div>
                    ";
                }
                    $html_code .= "
                    <h4 class=\"header-title mb-3\">$widget->name</h4>

                    ";

                    if(empty($data)) {
                        $html_code .= "
                            <h3 class=\"text-info\"><i class=\" mdi mdi-block-helper\"></i> No data to show</h3>
                        ";
                    } else {
                        $html_code .= "
                        <table class=\"table table-bordered table-sm\"><thead><tr style=\"background-color:" . $color . " !important;\" class=\"text-white\">";


                    foreach($data[0] as $key => $value){
                        $title = ucfirst($key);
                        $title = str_replace("_", " ", $title);
                        $html_code .= "<th>$title</th>";
                    }
                    
                    $html_code .= "</tr></thead><tbody>";

                    $current_first_column_value = "-EMPTY-";
                    $pending_merge_row = 0;
                    foreach($data as $itm){
                        $html_code .= "<tr>";
                        
                        $itm = (array)$itm;

                        

                        $first_column = array_keys($itm)[0];
                        
                        foreach(array_keys($itm) as $head){

                            

                            if($head == $first_column){
                                
                                //echo $current_first_column_value . " - " . $itm[$head] . " $pending_merge_row<br>";

                                if($current_first_column_value != $itm[$head]){
                                    $pending_merge_row = 0;
                                    
                                    
                                    foreach($data as $itm2){
                                        $itm2 = (array)$itm2;

                                        if($itm2[$head] == $itm[$head]){
                                            $pending_merge_row++;
                                        }
                                    }
                                    

                                    $current_first_column_value = $itm[$head];
                                } else {
                                    if($pending_merge_row > 0){
                                        continue;
                                    }
                                }
                            
                                $html_code .= "<td class=\"align-middle\" " . ($head == $first_column && $pending_merge_row > 1 ? " rowspan=\"" . $pending_merge_row . "\" " : "" ) . ">" . $itm[$head] . "</td>";
                                $pending_merge_row--;
                            } else {
                                //$html_code .= "<td class=\"align-middle\">" . ($itm[$head] == "0" ? "" : $itm[$head]) . "</td>";
                                $html_code .= "<td class=\"align-middle\">" . $itm[$head] . "</td>";
                                
                            }
                            

                            
                        }


                        $html_code .= "</tr>";
                        
                        //echo "<br>";
                    }

                    
                    $html_code .= "</tbody></table>";
                }
                $html_code .= "
                </div>
            </div>";

            } else {

                ob_start();
                require($file);
                $html_code = ob_get_clean();
            }

        } else {
            // $html_code = "
            // <div id=\"chart_$widget->id\" class=\"col-xl-12\">
            //     <div class=\"card\">
            //         <div class=\"card-body\">

            //             <h4 class=\"header-title mb-3\">$widget->name</h4>

            //             <h3 class=\"text-info\"><i class=\" mdi mdi-block-helper\"></i> No data to show</h3>
            //         </div>
            //     </div>
            // </div>
            // ";
        }
            
        
        return "{\"status\":\"success\",\"result\":{\"html\":\"" . \App\Helpers\MiscHelper::eJson($html_code) . "\", \"labels\":[], \"series\":[],\"categories\":[]}}";
    }
}