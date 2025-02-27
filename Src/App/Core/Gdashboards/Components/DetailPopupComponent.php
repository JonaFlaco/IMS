<?php

namespace App\Core\Gdashboards\Components;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;

class DetailPopupComponent {

    private $dashboardObj;

    private $coreModel;

    public function __construct($dashboardObj) {
        $this->dashboardObj = $dashboardObj;

        $this->coreModel = Application::getInstance()->coreModel;
    }

    public function generateModal(){
        
        ob_start(); ?>

            <!-- Full width modal content -->
            <div id="detailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="detailModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-full-width">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="detailModalTitle">Modal Heading</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body" id="detailModalBody">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>

                            <div id="div_btn_export_detail"></div>
                        </div>
                    </div>
                </div>
            </div>
        
        <?php

        return ob_get_clean();
    }


    

    public function ChartGetDetails() { 
        
        $script_generated = "";

        foreach($this->dashboardObj->widgets as $widget){

            if($widget->is_hidden == true)
                continue;

            if($widget->allow_pop_up_detail != true)
                continue;

                
            $script_generated .= "\t\t\tchart_$widget->id" . "_get_detail(widget_title, widget_id, name_value = null, series_name_value = null, category_value = null){
                document.getElementById('detailModalBody').innerHTML = 'Loading, please wait...';
                document.getElementById('detailModalTitle').innerHTML = widget_title;
                
                var myModal = new bootstrap.Modal(document.getElementById('detailModal'), {})
                myModal.show();
    
                var formData = new FormData();\n";
                
                foreach($this->dashboardObj->filters as $filter){
        
                    $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
                    $varCtype = null;

                    

                    if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
                        $varCtype = (new Ctype)->load($filter->ctype_id);
                        
                        $script_generated .= "\t\t\t\tformData.append('" . $varCtype->id . "_" . $thisField->name . "_operator_id', this." . $varCtype->id . "_" . $thisField->name . "_operator_id);\n";
        
                    if($thisField->field_type_id == "relation" && $filter->field_type_id != "text"){
                        if(isset($filter->default_value) && _strlen($filter->default_value) > 0 && $filter->is_hidden == true){
                            $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n\t\t\t";
                        } else {
                            $script_generated .= "
                            if(Array.isArray(this." . $varCtype->id . "_" . $thisField->name . ")){\n\t\t\t
                                formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . " != undefined && this." . $varCtype->id . "_" . $thisField->name . " != null ? this." . $varCtype->id . "_" . $thisField->name . ".map(x => x.id) : '');\n\t\t\t
                            } else {\n\t\t\t
                                formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . " != undefined && this." . $varCtype->id . "_" . $thisField->name . " != null && this." . $varCtype->id . "_" . $thisField->name . ".id != undefined ? this." . $varCtype->id . "_" . $thisField->name . ".id : '');\n\t\t\t
                            }\n\t\t\t";
                        }
                    } else if($thisField->field_type_id == "date") {
                        $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n\t\t\t";
                        $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "_2nd_value', this." . $varCtype->id . "_" . $thisField->name . "_2nd_value);\n\t\t\t";
                    } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal") {
                        $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n\t\t\t";
                        $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "_2nd_value', this." . $varCtype->id . "_" . $thisField->name . "_2nd_value);\n\t\t\t";
                    } else {
                        $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n\t\t\t";
                    }
                    
                }
        
                if(!empty($widget->detail_pop_up_name_filter)){
                    $script_generated .= "
                    formData.append('$widget->detail_pop_up_name_filter', name_value);\n
                    ";
                }
                if(!empty($widget->detail_pop_up_series_name_filter)){
                    $script_generated .= "
                    formData.append('$widget->detail_pop_up_series_name_filter', series_name_value);\n
                    ";
                }
                if(!empty($widget->detail_pop_up_category_filter)){
                    $script_generated .= "
                    formData.append('$widget->detail_pop_up_category_filter', category_value);\n
                    ";
                }

                $script_generated .= "

                document.getElementById('div_btn_export_detail').innerHTML = '<button type=\"button\" class=\"btn btn-secondary\" onclick=\"vm.chart_$widget->id" . "_export_detail(' + widget_id + ',\'' + name_value + '\',\'' + series_name_value + '\',\'' + category_value + '\')\" >Export</button>';

                axios({
                    method: 'post',
                    url: '/InternalApi/GdashboardWidgetGetDetail/' + widget_id + '&response_format=simple',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){

                    document.getElementById('detailModalBody').innerHTML = response.data;
                    
                })
                .catch(function(error){

                    document.getElementById('detailModalBody').innerHTML = '<h3 class=\"text-danger\"><i class=\" mdi mdi-block-helper\"></i> Something went wrong</h3>';
                    
                });
            },
        ";

        }

        return $script_generated;

    }


    public function ChartExportDetails() { 
        
        $script_generated = "";

        foreach($this->dashboardObj->widgets as $widget){

            if($widget->is_hidden == true)
                continue;

            if($widget->allow_pop_up_detail != true)
                continue;

                $script_generated .= "
                chart_$widget->id" . "_export_detail(widget_id, name_value = null, series_name_value = null, category_value = null){
                    //alert('Name: ' + name_value + ' Series ' + series_name_value + ' Category: ' + category_value);

                    var formData = new FormData();
                    
                    ";
            
            foreach($this->dashboardObj->filters as $filter){
    
                // if($filter->is_hidden == true)
                //     continue;
    
                $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
                $varCtype = null;

                

                if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
                    $varCtype = (new Ctype)->load($filter->ctype_id);
                    
                $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "_operator_id', this." . $varCtype->id . "_" . $thisField->name . "_operator_id);\n\t\t\t";
    
                if($thisField->field_type_id == "relation" && $filter->field_type_id != "text"){
                    if(isset($filter->default_value) && _strlen($filter->default_value) > 0 && $filter->is_hidden == true){
                        $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n\t\t\t";
                    } else {
                        $script_generated .= "
                        if(Array.isArray(this." . $varCtype->id . "_" . $thisField->name . ")){\n\t\t\t
                            formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . " != undefined && this." . $varCtype->id . "_" . $thisField->name . " != null ? this." . $varCtype->id . "_" . $thisField->name . ".map(x => x.id) : '');\n\t\t\t
                        } else {\n\t\t\t
                            formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . " != undefined && this." . $varCtype->id . "_" . $thisField->name . " != null && this." . $varCtype->id . "_" . $thisField->name . ".id != undefined ? this." . $varCtype->id . "_" . $thisField->name . ".id : '');\n\t\t\t
                        }\n\t\t\t";
                    }
                } else if($thisField->field_type_id == "date") {
                    $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n\t\t\t";
                    $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "_2nd_value', this." . $varCtype->id . "_" . $thisField->name . "_2nd_value);\n\t\t\t";
                } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal") {
                    $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n\t\t\t";
                    $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "_2nd_value', this." . $varCtype->id . "_" . $thisField->name . "_2nd_value);\n\t\t\t";
                } else {
                    $script_generated .= "formData.append('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n\t\t\t";
                }
                
            }
    


                    if(!empty($widget->detail_pop_up_name_filter)){
                        $script_generated .= "
                        formData.append('$widget->detail_pop_up_name_filter', name_value);\n
                        ";
                    }
                    if(!empty($widget->detail_pop_up_series_name_filter)){
                        $script_generated .= "
                        formData.append('$widget->detail_pop_up_series_name_filter', series_name_value);\n
                        ";
                    }
                    if(!empty($widget->detail_pop_up_category_filter)){
                        $script_generated .= "
                        formData.append('$widget->detail_pop_up_category_filter', category_value);\n
                        ";
                    }

                    $script_generated .= "

                    axios({
                        method: 'post',
                        url: '/InternalApi/GdashboardWidgetExportDetail/' + widget_id + '&response_format=json',
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




                ";
            }


            return $script_generated;
        }



}