<?php

/*
 * This class generates dashboards
 */
namespace App\views\Components;

use App\Core\Application;

class WidgetComponent {

    public function __construct() {

        $this->coreModel = Application::getInstance()->coreModel;

        
    }

    public function generate(){
        
        echo $this->generateTemplate();
        echo $this->generateScript();
    }


    private function generateTemplate(){
        
        ob_start() ?>

        <template id="tpl-widget-component">
            <div>
                <!-- <button class="btn btn-secondary" @click="reload">Refresh</button> -->
                <div :id="'chart_parent_' + id" class="col-xl-12"></div>
            </div>
            
        </template>

        <?php

        return ob_get_clean();
        
    }


    private function generateScript() {

        ob_start() ?>

        <script>

            var tplwidgetComponent = {
                template: '#tpl-widget-component',
                data() {
                    return {
                        widgetData: null,
                    }
                },
                mounted() {
                    this.reload();
                },
                props: ['id','type','size'],
                methods: {
                    <?= $this->generateMethod() ?>
                    getSize() {
                        return this.size ?? 12;
                    },
                    getName() {
                        return this.widgetData?.name ?? "N/A";
                    }
                }
            }

            Vue.component('widget-component', tplwidgetComponent);
        </script>

        <?php

        return ob_get_clean();
    }


    private function generateMethod() {

        ob_start() ?>

        reload(){
              
            let self = this;
      
            document.getElementById('chart_parent_' + self.id).innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">` +  self.getName() + `</h4>
                        <h5 class="text-secondary"><span class="spinner-border spinner-border me-1" role="status" aria-hidden="true"></span> Loading...</h5>
                    </div>
                </div>`;

            self.series = [];
            self.labels = [];
            self.categories = [];
            self.html = '';

            axios({
                method: 'post',
                url: '/InternalApi/GdashboardWidgetGetData/' + self.id + '&minimal=1&response_format=json',
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

                        document.getElementById('chart_parent_' + self.id).innerHTML = `
                            

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

                        document.getElementById('chart_parent_' + self.id).innerHTML = `
                            <div class="card">
                            <div class="card-body">

                                <h4 class="header-title mb-3">` + self.getName() + `</h4>

                                <h3 class="text-danger"><i class="mdi mdi-block-helper"></i> Something went wrong</h3>
                            </div>
                        </div>
                        `;
                    }
                    

                } else {

                    self.widgetData = response.data.result.meta;

                    if(this.type == "html") {
                    
                        document.getElementById('chart_parent_' + self.id).innerHTML = response.data.result.html;
                    
                    } else {
                    
                        if (response.data.result.html != null && response.data.result.html.length > 0) {
    const parent = document.getElementById('chart_parent_' + self.id);
    parent.innerHTML = response.data.result.html;

    // Buscar y ejecutar los scripts del HTML cargado
    const scripts = parent.getElementsByTagName('script');
    for (let script of scripts) {
        const newScript = document.createElement('script');
        if (script.src) {
            newScript.src = script.src; // Si es un script externo
        } else {
            newScript.textContent = script.innerHTML; // Si es un script inline
        }
        document.body.appendChild(newScript); // Agregarlo al DOM para ejecutarlo
        document.body.removeChild(newScript); // Opcional: limpiar el DOM
    }
}

                        self.series = (response.data.result.series == null ? null : response.data.result.series);
                        self.drilldown = (response.data.result.drilldown == null ? null : response.data.result.drilldown);
                        self.labels = response.data.result.labels;
                        self.categories = response.data.result.categories;
                        self.html = response.data.result.html;


                        if(self.series == null || self.series.length == 0){
                            return;
                        }
                    
                    }

                    self.generateChart();

                }
                                
            })
            .catch(function(error){

                document.getElementById('chart_parent_' + self.id).innerHTML = `
                    <div class="card">
                    <div class="card-body">

                        <h4 class="header-title mb-3">` + self.getName() + `</h4>

                        <h3 class="text-danger"><i class="mdi mdi-block-helper"></i> Something went wrong</h3>
                    </div>
                </div>
                `;
                console.log (error);
                return;
                
            });
        },
        generateChart(){

            switch (this.type){
                case "pie":
                    return this.generatePieChart();
                    break;
                case "bar":
                    return this.generateBarChart();
                    break;
                case "column":
                    return this.generateColumnChart();
                    break;
                case "area":
                    return this.generateAreaChart();
                    break;
                case "line":
                    return this.generateLineChart();
                    break;
                default:
                    return "";
                    break;
            }
        },
        generatePieChart(){

            let self = this;

            Highcharts.setOptions({
                colors: self.widgetData.colors
            });

            var plotOptions = {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: (this.widgetData.hide_lables ? false : true),
                    cursor: (this.widgetData.allow_drilldown || this.widgetData.allow_pop_up_detail ? 'pointer' : ''),
                }
            };

            if(this.widgetData.allow_pop_up_detail == true){
                this.pi.point = {
                    events: {
                        click: function () {
                            self.get_detail(this.widgetData.name, this.widgetData.id,self.id, this.series.name, this.category);
                        }
                    }
                };
            }
            

            var chart = Highcharts.chart('chart_' + self.id, {
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
                plotOptions: plotOptions,
                series: self.series,
                drilldown: self.drilldown
            });
        },
        generateColumnChart(){

            let self = this;

            Highcharts.setOptions({
                colors: this.widgetData.colors
            });

            var plotOptions = {
                series: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: (this.widgetData.hide_lables ? false : true),
                    cursor: (this.widgetData.allow_drilldown || this.widgetData.allow_pop_up_detail ? 'pointer' : ''),
                }
            };

            if(this.widgetData.stack_result == true){
                this.plotOptions.series.stacking = this.widgetData.stack_result
            }

            if(this.widgetData.allow_pop_up_detail == true){
                this.plotOptions.series.point = {
                    events: {
                        click: function () {
                            self.get_detail(this.widgetData.name, this.widgetData.id,self.id, this.series.name, this.category);
                        }
                    }
                };
            }
            

            var chart = Highcharts.chart('chart_' + self.id, {
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
                    point: {
                        valueSuffix: '%'
                    }
                },
                xAxis: {
                    categories: this.widgetData.allow_drilldown != true ? self.categories : null,
                    type: this.widgetData.allow_drilldown != true ? null : 'category',
                    crosshair: true,
                },
                exporting: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: 'Value: <b>{point.y} - {point.percentage:.2f}%<b><br>'
                },
                legend: {
                    enabled: this.widgetData.allow_drilldown ? false : true,
                },
                tooltip: {
                    headerFormat: `<span style="font-size:10px">{point.key}</span><table>`,
                    pointFormat: `<tr><td style="color:{series.color};padding:0">{series.name}: </td>` +
                        `<td style="padding:0"><b>{point.y:.0f}</b></td></tr>`,
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: plotOptions,
                series: self.series,
                drilldown: self.drilldown
            });
        },
        generateBarChart(){

            let self = this;

            Highcharts.setOptions({
                colors: this.widgetData.colors
            });

            var plotOptions = {
                series: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: (this.widgetData.hide_lables ? false : true),
                    cursor: (this.widgetData.allow_drilldown || this.widgetData.allow_pop_up_detail ? 'pointer' : ''),
                }
            };

            if(this.widgetData.stack_result == true){
                this.plotOptions.series.stacking = this.widgetData.stack_result
            }

            if(this.widgetData.allow_pop_up_detail == true){
                this.plotOptions.series.point = {
                    events: {
                        click: function () {
                            self.get_detail(this.widgetData.name, this.widgetData.id,self.id, this.series.name, this.category);
                        }
                    }
                };
            }


            var chart = Highcharts.chart('chart_' + self.id, {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                xAxis: {
                    categories: this.widgetData.allow_drilldown != true ? self.categories : null,
                    type: this.widgetData.allow_drilldown != true ? null : 'category',
                    crosshair: true,
                },
                exporting: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: 'Value: <b>{point.y} - {point.percentage:.2f}%<b><br>'
                },
                legend: {
                    enabled: this.widgetData.allow_drilldown ? false : true,
                },
                tooltip: {
                    headerFormat: `<span style="font-size:10px">{point.key}</span><table>`,
                    pointFormat: `<tr><td style="color:{series.color};padding:0">{series.name}: </td>` +
                        `<td style="padding:0"><b>{point.y:.0f}</b></td></tr>`,
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: plotOptions,
                series: self.series,
                drilldown: self.drilldown
                });
            },
            generateLineChart(){

                let self = this;

                Highcharts.setOptions({
                    colors: this.widgetData.colors
                });

                var plotOptions = {
                    series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: (this.widgetData.hide_lables ? false : true),
                        cursor: (this.widgetData.allow_drilldown || this.widgetData.allow_pop_up_detail ? 'pointer' : ''),
                    }
                };

                if(this.widgetData.stack_result == true){
                    this.plotOptions.series.stacking = this.widgetData.stack_result
                }

                if(this.widgetData.allow_pop_up_detail == true){
                    this.plotOptions.series.point = {
                        events: {
                            click: function () {
                                self.get_detail(this.widgetData.name, this.widgetData.id,self.id, this.series.name, this.category);
                            }
                        }
                    };
                }


                var chart = Highcharts.chart('chart_' + self.id, {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    xAxis: {
                        categories: this.widgetData.allow_drilldown != true ? self.categories : null,
                        type: this.widgetData.allow_drilldown != true ? null : 'category',
                        crosshair: true,
                    },
                    exporting: {
                        enabled: false
                    },
                    tooltip: {
                        pointFormat: 'Value: <b>{point.y} - {point.percentage:.2f}%<b><br>'
                    },
                    legend: {
                        enabled: this.widgetData.allow_drilldown ? false : true,
                    },
                    tooltip: {
                        headerFormat: `<span style="font-size:10px">{point.key}</span><table>`,
                        pointFormat: `<tr><td style="color:{series.color};padding:0">{series.name}: </td>` +
                            `<td style="padding:0"><b>{point.y:.0f}</b></td></tr>`,
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: plotOptions,
                    series: self.series,
                    drilldown: self.drilldown
                });
            },
            generateAreaChart(){

                let self = this;

                Highcharts.setOptions({
                    colors: this.widgetData.colors
                });

                var plotOptions = {
                    series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: (this.widgetData.hide_lables ? false : true),
                        cursor: (this.widgetData.allow_drilldown || this.widgetData.allow_pop_up_detail ? 'pointer' : ''),
                    }
                };

                if(this.widgetData.stack_result == true){
                    this.plotOptions.series.stacking = this.widgetData.stack_result
                }

                if(this.widgetData.allow_pop_up_detail == true){
                    this.plotOptions.series.point = {
                        events: {
                            click: function () {
                                self.get_detail(this.widgetData.name, this.widgetData.id,self.id, this.series.name, this.category);
                            }
                        }
                    };
                }


                var chart = Highcharts.chart('chart_' + self.id, {
                    chart: {
                        type: 'area'
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    xAxis: {
                        categories: this.widgetData.allow_drilldown != true ? self.categories : null,
                        type: this.widgetData.allow_drilldown != true ? null : 'category',
                        crosshair: true,
                    },
                    exporting: {
                        enabled: false
                    },
                    tooltip: {
                        pointFormat: 'Value: <b>{point.y} - {point.percentage:.2f}%<b><br>'
                    },
                    legend: {
                        enabled: this.widgetData.allow_drilldown ? false : true,
                    },
                    tooltip: {
                        headerFormat: `<span style="font-size:10px">{point.key}</span><table>`,
                        pointFormat: `<tr><td style="color:{series.color};padding:0">{series.name}: </td>` +
                            `<td style="padding:0"><b>{point.y:.0f}</b></td></tr>`,
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: plotOptions,
                    series: self.series,
                    drilldown: self.drilldown
                });
                },


        <?php

        return ob_get_clean();
        
    }


}