<?php

namespace App\Core\Gdashboards\Components;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;

class ActionsComponent {

    private $dashboardObj;

    private $coreModel;

    public function __construct($dashboardObj) {
        $this->dashboardObj = $dashboardObj;

        $this->coreModel = Application::getInstance()->coreModel;
    }


    private function getFormData() {

        $result = "
        var formData = new FormData();
        ";
                    
        foreach($this->dashboardObj->filters as $filter){

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
            $varCtype = null;

            if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
                $varCtype = (new Ctype)->load($filter->ctype_id);
                
            $result .= "\t\t\tformData.append( '" . $varCtype->id . "_" . $thisField->name . "_operator_id', this." . $varCtype->id . "_" . $thisField->name . "_operator_id);\n";

            if($thisField->field_type_id == "relation" && $filter->field_type_id != "text"){
                if(isset($filter->default_value) && _strlen($filter->default_value) > 0 && $filter->is_hidden == true){
                    $result .= "\t\t\tformData.append( '" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n";
                } else {
                    $result .= "
                    if(Array.isArray(this." . $varCtype->id . "_" . $thisField->name . ")){
                formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . " != undefined && this." . $varCtype->id . "_" . $thisField->name . " != null ? this." . $varCtype->id . "_" . $thisField->name . ".map(x => x.id) : '');
        } else {
            formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . " != undefined && this." . $varCtype->id . "_" . $thisField->name . " != null && this." . $varCtype->id . "_" . $thisField->name . ".id != undefined ? this." . $varCtype->id . "_" . $thisField->name . ".id : '');
        }\n";
                }
            } else if($thisField->field_type_id == "date") {
                $result .= "\t\t\tformData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n";
                $result .= "\t\t\tformData.append('" . $varCtype->id . "_" . $thisField->name . "_2nd_value', this." . $varCtype->id . "_" . $thisField->name . "_2nd_value);\n";
            } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal") {
                $result .= "\t\t\tformData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n";
                $result .= "\t\t\tformData.append('" . $varCtype->id . "_" . $thisField->name . "_2nd_value', this." . $varCtype->id . "_" . $thisField->name . "_2nd_value);\n";
            } else {
                $result .= "\t\t\tformData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");";
            }
                
        }
    
        return $result;

    }
    
    public function exportChartData() {

        ob_start(); ?>

        chart_export_data(widget_id){
            
            <?= $this->getFormData() ?>

            axios({
                method: 'post',
                url: '/InternalApi/GdashboardWidgetExportData/' + widget_id + '?response_format=json',
                data:formData,
                headers: {
                    'Content-Type': 'form-data',
                }
            })
            .then(function(response){
                    
                if(response.data.status == 'success'){
                    window.location.replace('/filedownload?temp=1&file_name=' + response.data.fileName  , '_blank')
                } else {
                    
                    if(response.data.message != null && response.data.message.length > 0){
    
                        $.toast({heading: 'Error',text: response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});
    
                    } else {
                        
                        $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
        
                    }
                    
                }
                                    
            })
            .catch(function(error){
                $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
            });
        },

        <?php

        return ob_get_clean();
    }


    public function exportChartAsImage() { 
        
        $script_generated = "";

        foreach($this->dashboardObj->widgets as $widget){

            if($widget->is_hidden == true || $widget->type == "html")
                continue;

                $script_generated .= "
        chart_$widget->id" . "_export_as_image(){
            var chart = $('#chart_$widget->id').highcharts();
            chart.exportChart();
        },
        ";
                
        }

        return $script_generated;
    }


    public function getData() {

        $script_generated = "
        
        prepareFilterCondition(key, value) {
            var url = new URL(window.location.href);
            if(value == null || value.length == 0 || value == \"null\") {
                url.searchParams.delete(key);
            } else {
                url.searchParams.set(key, value);
            }
        },

        prepareFilterConditionOnUrl() {
            
            var url = new URL(window.location.href);
            
            this.filtersList.forEach((itm) => {

            if(itm.value == null || itm.value.length == 0 || itm.value == \"null\") {
                url.searchParams.delete(itm.key);
            } else {
                url.searchParams.set(itm.key, itm.value);
            }
            });

            window.history.replaceState(null, null, url);
        },
        
        ";

        foreach($this->dashboardObj->widgets as $widget){

            if($widget->is_hidden == true)
                continue;

        $script_generated .= "
        get_data_$widget->id(){
                

            if(this.getCookie('" . $this->dashboardObj->id . "_widget_" . $widget->id . "') == \"0\"){
                return;
            }
            
            document.getElementById('chart_parent_$widget->id').innerHTML = `<div class=\"card\"><div class=\"card-body\"><h4 class=\"header-title mb-3\">$widget->name</h4><h5 class=\"text-secondary\"><span class=\"spinner-border spinner-border me-1\" role=\"status\" aria-hidden=\"true\"></span> Loading...</h5></div></div>`;

            let self = this;
        
            self." . $widget->id . "_series = [];
            self." . $widget->id . "_labels = [];
            self." . $widget->id . "_categories = [];
            self." . $widget->id . "_html = '';

            " . $this->getFormData() . "
            
            axios({
                method: 'post',
                url: '/InternalApi/GdashboardWidgetGetData/$widget->id&response_format=json',
                data:formData,
                headers: {
                    'Content-Type': 'form-data',
                }
            })
            .then(function(response){

                if(response.data.result == undefined){
                    
                    if(response.data.message != undefined){
                        
                        $.toast({
                            heading: 'error',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });

                        document.getElementById('chart_parent_$widget->id').innerHTML = `
                            <div class=\"card\">
                            <div class=\"card-body\">

                                <h4 class=\"header-title mb-3\">$widget->name</h4>

                                <h3 class=\"text-danger\"><i class=\" mdi mdi-block-helper\"></i> ` + response.data.message + `</h3>
                            </div>
                        </div>
                        `;
                    } else {
                        $.toast({
                            heading: 'error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });

                        document.getElementById('chart_parent_$widget->id').innerHTML = `
                            <div class=\"card\">
                            <div class=\"card-body\">

                                <h4 class=\"header-title mb-3\">$widget->name</h4>

                                <h3 class=\"text-danger\"><i class=\" mdi mdi-block-helper\"></i> Something went wrong</h3>
                            </div>
                        </div>
                        `;
                    }
                    
    
                } else {
    
    ";
    if($widget->type == "html"){
        $script_generated .= "
  const parentElement = document.getElementById('chart_parent_$widget->id');
        parentElement.innerHTML = response.data.result.html;

        const scripts = parentElement.querySelectorAll('script');
        scripts.forEach(script => {
            const newScript = document.createElement('script');
            if (script.src) {
                newScript.src = script.src;
                newScript.async = false;  // Mantener el orden de ejecución
            } else {
                newScript.textContent = script.innerHTML;
            }
            document.head.appendChild(newScript);  // Ejecuta el script
            script.remove();  
        });

        ";
    }
     else {
        $script_generated .= "    
        
        if(response.data.result.html != null && response.data.result.html.length > 0){
            document.getElementById('chart_parent_$widget->id').innerHTML = response.data.result.html;
        }
        self." . $widget->id . "_series = (response.data.result.series == null ? null : response.data.result.series);
        self." . $widget->id . "_drilldown = (response.data.result.drilldown == null ? null : response.data.result.drilldown);
        self." . $widget->id . "_labels = response.data.result.labels;
        self." . $widget->id . "_categories = response.data.result.categories;
        self." . $widget->id . "_html = response.data.result.html;
        

        if(self." . $widget->id . "_series == null || self." . $widget->id . "_series.length == 0){
            //document.getElementById('chart_$widget->id').innerHTML = '<h3 class=\"text-info\"><i class=\" mdi mdi-block-helper\"></i> No data to show</h3>';
            return;
        }
        ";
        
    }

        $script_generated .= $this->generateChart($widget);

        $script_generated .= "
        
                    }
                    
                })
                .catch(function(error){

                    document.getElementById('chart_parent_$widget->id').innerHTML = `
                        <div class=\"card\">
                        <div class=\"card-body\">

                            <h4 class=\"header-title mb-3\">$widget->name</h4>

                            <h3 class=\"text-danger\"><i class=\" mdi mdi-block-helper\"></i> Something went wrong</h3>
                        </div>
                    </div>
                    `;
                    console.log (error);
                    return;
                    
                });
            },
            
            ";
        }
        
        $count = 1;
        $script_generated .= "
        
            async get_data(){
                $('#cardfiltration').hide();
                this.updateFiltersInUrl();
                this.prepareFilterConditionOnUrl();
                ";

            foreach($this->dashboardObj->widgets as $widget){

                if($widget->is_hidden == true)
                    continue;
                $script_generated .= "
            


                await this.get_data_$widget->id();\n 
                
                ";
                
                
            }
            
            $script_generated .= "
                
            },

            ";

            return $script_generated;
    }


    private function generateChart($widget){

        switch ($widget->type){
            case "pie":
                return $this->generatePieChart($widget);
                break;
            case "bar":
                return $this->generateBarChart($widget);
                break;
            case "column":
                return $this->generateColumnChart($widget);
                break;
            case "area":
                return $this->generateAreaChart($widget);
                break;
            case "line":
                return $this->generateLineChart($widget);
                break;
            default:
                return "";
                break;
        }
    }

    
    private function generatePieChart($widget){

        $return_value = "
            
            Highcharts.setOptions({
                colors: [" . (isset($widget->colors) ? $widget->colors : WIDGET_COLORS) . "]
            });

            var chart = Highcharts.chart('chart_$widget->id', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                exporting: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: 'Value: <b>{point.y} - {point.percentage:.2f}%<b><br>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        " . ($widget->allow_drilldown == true || $widget->allow_pop_up_detail == true ? "cursor: 'pointer'," : "");
                        if($widget->allow_pop_up_detail == true){
                            $return_value .= "
                        point: {
                            events: {
                                click: function () {
                                    self.chart_$widget->id" . "_get_detail('$widget->name', $widget->id,this.name, this.series.name, this.category);
                                }
                            }
                        },";
                    }
                    $return_value .= "
                        showInLegend: " . ($widget->hide_lables == true ? "false" : "true") . "
                    }
                },
                series: self." . $widget->id . "_series,
                drilldown: self." . $widget->id . "_drilldown
            });
            
        ";


        return $return_value;

    }

    
    private function generateBarChart($widget){

        $return_value = "
            
        Highcharts.setOptions({
            colors: [" . (isset($widget->colors) ? $widget->colors : WIDGET_COLORS) . "]
        });

        Highcharts.chart('chart_$widget->id', {
            chart: {
                type: 'bar'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                " . ($widget->allow_drilldown != true ? "categories: self." . $widget->id . "_categories" : "type: 'category'") . ",
                crosshair: true
            },
            legend: {
                " . ($widget->allow_drilldown == true ? " enabled: false " : " enabled: true ") . "
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            exporting: {
                enabled: false
            },
            legend: {
            " . ($widget->allow_drilldown == true ? " enabled: false " : " enabled: true ") . "
            },
            tooltip: {
                headerFormat: '<span style=\"font-size:10px\">{point.key}</span><table>',
                pointFormat: '<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td>' +
                    '<td style=\"padding:0\"><b>{point.y:.0f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                series: {
                    " . (!empty($widget->stack_result) ? "stacking: '$widget->stack_result'," : "" ) . "
                    " . ($widget->allow_drilldown == true || $widget->allow_pop_up_detail == true ? "cursor: 'pointer'," : "");
                    if($widget->allow_pop_up_detail == true){
                        $return_value .= "
                    point: {
                        events: {
                            click: function () {
                                self.chart_$widget->id" . "_get_detail('$widget->name', $widget->id,this.name, this.series.name, this.category);
                            }
                        }
                    },";
                }
                $return_value .= "
                },
            },
            series: self." . $widget->id . "_series,
            drilldown: self." . $widget->id . "_drilldown
        });

        ";


        return $return_value;
    }

    private function generateColumnChart($widget){

        $return_value = "
        
        Highcharts.setOptions({
            colors: [" . (isset($widget->colors) ? $widget->colors : WIDGET_COLORS) . "]
        });

        Highcharts.chart('chart_$widget->id', {
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                " . ($widget->allow_drilldown != true ? "categories: self." . $widget->id . "_categories" : "type: 'category'") . ",
                crosshair: true
            },
            exporting: {
                enabled: false
            },
            legend: {
                " . ($widget->allow_drilldown == true ? " enabled: false " : " enabled: true ") . "
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            tooltip: {
                headerFormat: '<span style=\"font-size:10px\">{point.key}</span><table>',
                pointFormat: '<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td>' +
                    '<td style=\"padding:0\"><b>{point.y:.0f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                series: {
                    " . (!empty($widget->stack_result) ? "stacking: '$widget->stack_result'," : "" ) . "
                    " . ($widget->allow_drilldown == true || $widget->allow_pop_up_detail == true ? "cursor: 'pointer'," : "");
                    if($widget->allow_pop_up_detail == true){
                        $return_value .= "
                    point: {
                        events: {
                            click: function () {
                                self.chart_$widget->id" . "_get_detail('$widget->name',$widget->id,this.name, this.series.name, this.category);
                            }
                        }
                    },";
                }
                $return_value .= "
                },
            },
            series: self." . $widget->id . "_series,
            drilldown: self." . $widget->id . "_drilldown
        });

        
        ";

        return $return_value;
    }

    private function generateAreaChart($widget){

        $return_value = "
                
        Highcharts.setOptions({
            colors: [" . (isset($widget->colors) ? $widget->colors : WIDGET_COLORS) . "]
        });

        Highcharts.chart('chart_$widget->id', {
            chart: {
                type: 'area'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            exporting: {
                enabled: false
            },
            xAxis: {
                " . ($widget->allow_drilldown != true ? "categories: self." . $widget->id . "_categories" : "type: 'category'") . ",
                crosshair: true
            },
            legend: {
                " . ($widget->allow_drilldown == true ? " enabled: false " : " enabled: true ") . "
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            tooltip: {
                headerFormat: '<span style=\"font-size:10px\">{point.key}</span><table>',
                pointFormat: '<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td>' +
                    '<td style=\"padding:0\"><b>{point.y:.0f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    " . ($widget->allow_drilldown == true || $widget->allow_pop_up_detail == true ? "cursor: 'pointer'," : "");
                    if($widget->allow_pop_up_detail == true){
                        $return_value .= "
                    point: {
                        events: {
                            click: function () {
                                self.chart_$widget->id" . "_get_detail('$widget->name',$widget->id,this.name, this.series.name, this.category);
                            }
                        }
                    },";
                }
                $return_value .= "
                },
            },
            series: self." . $widget->id . "_series,
            drilldown: self." . $widget->id . "_drilldown
        });

        ";


        return $return_value;

    }

    private function generateLineChart($widget){

        $return_value = "
            
        Highcharts.setOptions({
            colors: [" . (isset($widget->colors) ? $widget->colors : WIDGET_COLORS) . "]
        });

        Highcharts.chart('chart_$widget->id', {
            chart: {
                type: 'line'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                " . ($widget->allow_drilldown != true ? "categories: self." . $widget->id . "_categories" : "type: 'category'") . ",
                crosshair: true
            },
            exporting: {
                enabled: false
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            legend: {
                " . ($widget->allow_drilldown == true ? " enabled: false " : " enabled: true ") . "
            },
            tooltip: {
                headerFormat: '<span style=\"font-size:10px\">{point.key}</span><table>',
                pointFormat: '<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td>' +
                    '<td style=\"padding:0\"><b>{point.y:.0f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    " . ($widget->allow_drilldown == true || $widget->allow_pop_up_detail == true ? "cursor: 'pointer'," : "");
                    if($widget->allow_pop_up_detail == true){
                        $return_value .= "
                    point: {
                        events: {
                            click: function () {
                                self.chart_$widget->id" . "_get_detail('$widget->name',$widget->id,this.name, this.series.name, this.category);
                            }
                        }
                    },";
                }
                $return_value .= "
                },
            },
            series: self." . $widget->id . "_series,
            drilldown: self." . $widget->id . "_drilldown
        });

        ";


        return $return_value;

    }

    

}