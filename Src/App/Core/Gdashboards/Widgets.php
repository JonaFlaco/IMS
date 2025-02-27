<?php

/*
 * This class generates dashboards
 */
namespace App\Core\Gdashboards;

use App\Core\Application;

class Widgets {

    private $coreModel;
    private $widget;
    private $app;
    private $id;
    private $size;
    
    public function __construct(int $id, string $name, $size = 12) {
        
        $this->id = $name;
        $this->size = $size;

        $this->app = Application::getInstance();

        $this->coreModel = $this->app->coreModel;
        
        $this->widget = $this->coreModel->nodeModel("widgets")
            ->id($id)
            ->loadFirstOrFail();

    }

    public function generate(){
        
        ob_start() ?>

        <div id="chart_parent_<?= $this->id ?>" class="col-xl-<?= $this->size ?>">
            
        </div>

        <?php

        return ob_get_clean();
        
    }


    public function generateMethod(){
        
        ob_start() ?>

        <?= $this->id ?>(){
                    
            document.getElementById('chart_parent_<?= $this->id ?>').innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3"><?= $this->widget->id ?></h4>
                        <h5 class="text-secondary"><span class="spinner-border spinner-border me-1" role="status" aria-hidden="true"></span> Loading...</h5>
                    </div>
                </div>`;

            let self = this;

            self.<?= $this->widget->id ?>_series = [];
            self.<?= $this->widget->id ?>_labels = [];
            self.<?= $this->widget->id ?>_categories = [];
            self.<?= $this->widget->id ?>_html = '';

            axios({
                method: 'post',
                url: '/InternalApi/GdashboardWidgetGetData/<?= $this->widget->id ?>&response_format=json',
                data:null,
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

                        document.getElementById('chart_parent_<?= $this->id ?>').innerHTML = `
                            

                                <h3 class="text-danger"><i class="mdi mdi-block-helper"></i> ` + response.data.message + `</h3>
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

                        document.getElementById('chart_parent_<?= $this->id ?>').innerHTML = `
                            <div class="card">
                            <div class="card-body">

                                <h4 class="header-title mb-3"><?= $this->widget->name ?></h4>

                                <h3 class="text-danger"><i class="mdi mdi-block-helper"></i> Something went wrong</h3>
                            </div>
                        </div>
                        `;
                    }
                    

                } else {

                    <?php if($this->widget->type == "html"): ?>
                    
                    document.getElementById('chart_parent_<?= $this->id ?>').innerHTML = response.data.result.html;
                    
                    <?php else: ?>
                    
                    if(response.data.result.html != null && response.data.result.html.length > 0){
                        document.getElementById('chart_parent_<?= $this->id ?>').innerHTML = response.data.result.html;
                    }
                    self.<?= $this->widget->id ?>_series = (response.data.result.series == null ? null : response.data.result.series);
                    self.<?= $this->widget->id ?>_drilldown = (response.data.result.drilldown == null ? null : response.data.result.drilldown);
                    self.<?= $this->widget->id ?>_labels = response.data.result.labels;
                    self.<?= $this->widget->id ?>_categories = response.data.result.categories;
                    self.<?= $this->widget->id ?>_html = response.data.result.html;


                    if(self.<?= $this->widget->id ?>_series == null || self.<?= $this->widget->id ?>_series.length == 0){
                        return;
                    }
                    
                    <?php endif; ?>

                    <?= $this->generateChart($this->widget) ?>

                }
                                
            })
            .catch(function(error){

                document.getElementById('chart_parent_<?= $this->id ?>').innerHTML = `
                    <div class="card">
                    <div class="card-body">

                        <h4 class="header-title mb-3"><?= $this->widget->name ?></h4>

                        <h3 class="text-danger"><i class="mdi mdi-block-helper"></i> Something went wrong</h3>
                    </div>
                </div>
                `;
                console.log (error);
                return;
                
            });
        },
                

        <?php

        return ob_get_clean();
        
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
