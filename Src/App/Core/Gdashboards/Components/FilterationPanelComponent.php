<?php

namespace App\Core\Gdashboards\Components;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;
use App\Core\Gdashboards\Components\FilterComponents\TextComponent;
use App\Core\Gdashboards\Components\FilterComponents\ComboboxComponent;
use App\Core\Gdashboards\Components\FilterComponents\NumberComponent;
use App\Core\Gdashboards\Components\FilterComponents\DateComponent;
use App\Core\Gdashboards\Components\FilterComponents\BooleanComponent;

class FilterationPanelComponent {

    private $dashboardObj;

    private $coreModel;

    public function __construct($dashboardObj) {
        
        $this->dashboardObj = $dashboardObj;

        $this->coreModel = Application::getInstance()->coreModel;
    }
    
    public function generateModal() 
    {
        ob_start(); 

        $filtration_panel = $this->generateUIViewFilter();
        $this->have_filtration_modal = false;
        if(_strlen($filtration_panel) > 0):
            $this->have_filtration_modal = true;
            
            ?>
                <!-- Dark Header Modal -->
                <div id="filtrationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
                    <div style="height: 75%;" class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header modal-colored-header p-1 ps-2 bg-info">
                                <h4 class="modal-title" id="info-header-modalLabel">
                                    <i class="mdi mdi-filter-variant"></i>
                                    <?= ("Filtration Panel") ?>
                                </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <?= $filtration_panel ?>
                                </div>
                            </div>
                            <div class="modal-footer p-1">
                                <button data-bs-dismiss="modal" type="button" @click='get_data()' class="btn btn-primary">
                                    <i class="mdi mdi-filter-variant"></i>
                                    <?= $this->coreModel->getKeyword("Apply") ?>
                                </button>
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
               
            <?php 
        endif;

        return ob_get_clean();
    }

    
    private function generateUIViewFilter(){

        $return_value = "";
        
        foreach($this->dashboardObj->filters as $filter){
            if($filter->is_hidden == true)
                continue;
                
            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
            
            if(isset($filter->ctype_id) && _strlen($filter->ctype_id) > 0){
                                            
                $ctypeRel = (new Ctype)->load($filter->ctype_id);

                $return_value .= $this->generateUIViewFilter_sub($thisField, $ctypeRel,$filter);
                
            } else {

                $ctypeRel = (new Ctype)->load($filter->ctype_id);

                $return_value .= $this->generateUIViewFilter_sub($thisField, $ctypeRel,$filter);

            }
        }

        return $return_value;
    }

    
    private function generateUIViewFilter_sub($field, $ctypeObj,$filter){

        $return_value = "";

        if($field->field_type_id == "text"){
            $return_value .= (new TextComponent($filter, $field))->generate();
        } else if ($field->field_type_id == "relation" && (!isset($filter->field_type_id) || $filter->field_type_id == "relation")){
            $return_value .= (new ComboboxComponent($filter, $field, $this->dashboardObj->filters))->generate();
        } else if ($field->field_type_id == "boolean"){
            $return_value .= (new BooleanComponent($filter, $field))->generate();
        } else if ($field->field_type_id == "date"){
            $return_value .= (new DateComponent($filter, $field))->generate();
        } else if ($field->field_type_id == "number" || $field->field_type_id == "decimal"){
            $return_value .= (new NumberComponent($filter, $field))->generate();
        } 

        return $return_value;
    }






    public function generateViewFilterRefereshList(){
        $return_value = "";
        foreach($this->dashboardObj->filters as $filter){
            
            if(isset($filter->data_source_filter_by_field_name) && _strlen($filter->data_source_filter_by_field_name) > 0){

                $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
                $filter_ctype = (new Ctype)->load($filter->ctype_id);

                $return_value .= "
                reload_" . $filter_ctype->id . "_" . $filter->name . "(val){

                    if(val != undefined && val != null && typeof val == 'object'){
                        val = val.id;
                    }
                    
                    this.loading_" .  $filter_ctype->id . "_" . "$filter->field_name = true;

                    var filters = [val];

                    var formData = new FormData();
                    formData.append('filters', filters);\n\t\t\t

                    var self = this;
                    axios({
                        method: 'POST',
                        url: '/InternalApi/genericPreloadList/' + val + '?field_id=$thisField->id&add_all=true&response_format=json',
                        data:formData,
                        headers: {
                            'Content-Type': 'form-data',
                        }
                    })
                    .then(function(response){
                        if(response.data.status == 'success'){
                            
                            self.pl_" . $filter_ctype->id . "_" . $filter->field_name . " = response.data.result;
                            self.loading_" .  $filter_ctype->id . "_" . "$filter->field_name = false;

                        } else {
                            self.pl_" . $filter_ctype->id . "_" . $filter->field_name . " = [];
                            $.toast({
                                heading: 'Error',
                                text: 'An error occured while loading preload list for $thisField->title',
                                showHideTransition: 'slide',
                                position: 'top-right',
                                icon: 'error'
                            });
                            self.loading_" .  $filter_ctype->id . "_" . "$filter->field_name = false;
                            return;
                        }
                    })
                    .catch(function(error){
                        $.toast({
                            heading: 'Error',
                            text: error,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        self.loading_" .  $filter_ctype->id . "_" . "$filter->field_name = false;
                    });
        
        

                },
                    ";
            }
        }

        return $return_value;
    }
    

    public function getDataObject() {

        $result = [];

        foreach($this->dashboardObj->filters as $filter){
            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
        
            if($thisField->field_type_id == "relation"){
    
                $filter_ctype = (new Ctype)->load($filter->ctype_id);

                $pl = $this->coreModel->getPreloadList($thisField->data_source_table_name,$thisField->data_source_value_column, $thisField->data_source_display_column, array("original_ctype_id" => $thisField->parent_id, "fixed_where_condition" => $thisField->data_source_fixed_where_condition, "sort_field_name" => $thisField->data_source_sort_column, "field_id" => $thisField->id));
                
                $v_value = [];
                foreach($pl as $item){

                    $value = $item->name;
                    $value = _str_replace('"','\\"', $value);
                    $v_value[] = (object)[
                        "id" => $item->id,
                        "name" => $value
                    ];
                }

                $result["pl_" . $filter_ctype->id . "_$filter->field_name"] = $v_value;
                $result["loading_" .  $filter_ctype->id . "_$filter->field_name"] = false;
                
            }
        }

        foreach($this->dashboardObj->filters as $filter){

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
            $varCtype = null;
            if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
                $varCtype = (new Ctype)->load($filter->ctype_id);
    
            if($filter->default_value == "[USERID]")
                $filter->default_value = Application::getInstance()->user->getId();
    
            if(empty($filter->operator_id)) {
                $filter->operator_id = 0;
            }

            $fieldFullName = $varCtype->id . "_" . $thisField->name;
            
            $result[$fieldFullName . "_operator_id"] = $this->getParamValue($fieldFullName . "_operator_id", $filter->operator_id);

            if($thisField->field_type_id == "relation"){    
                $result[$fieldFullName] = $this->getParamValue($fieldFullName, $filter->default_value);
            } else if($thisField->field_type_id == "date") {
                $result[$fieldFullName] = $this->getParamValue($fieldFullName, $filter->default_value);
                $result[$fieldFullName . "_2nd_value"] = $this->getParamValue($fieldFullName . "_2nd_value", $filter->default_value);
            } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal") {
                $result[$fieldFullName] = $this->getParamValue($fieldFullName, $filter->default_value);
                $result[$fieldFullName . "_2nd_value"] = $this->getParamValue($fieldFullName . "_2nd_value", $filter->default_value);
            } else {
                $result[$fieldFullName] = $this->getParamValue($fieldFullName, $filter->default_value);
            }
            
        }

        return $result;
    }


    public function finalizeParameterValues() {

        ob_start(); ?>

        <?php foreach($this->dashboardObj->filters as $filter){

        $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);

        $varCtype = null;
        if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
            $varCtype = (new Ctype)->load($filter->ctype_id);

            $alias = $varCtype->id;

            $fieldFullName = $alias . "_" . $thisField->name;
            ?>

            <?php if($thisField->field_type_id == "relation" && $filter->field_type_id != "text"): ?>
            
                if(this.<?= $fieldFullName ?> != null && this.<?= $fieldFullName ?>.length > 0) {
                    if(this.<?= $fieldFullName ?>.includes(',')){
                        this.<?= $fieldFullName ?> = this.<?= "pl_" . $varCtype->id . "_" . $filter->field_name ?>.filter(x => this.<?= $fieldFullName ?>.split(',').includes(x.id));
                    } else {
                        this.<?= $fieldFullName ?> = this.<?= "pl_" . $varCtype->id . "_" . $filter->field_name ?>.find(x => x.id == this.<?= $fieldFullName ?>);
                    }
                }

            <?php endif; ?>
            
        <?php } ?>

        <?php

        return ob_get_clean();

    }

    private function getParamValue($key, $defaultValue) {
        
        $value = Application::getInstance()->request->getParam($key);
        if(_strlen($value) > 0)
            return $value;
        else 
            return $defaultValue;
    }

    
    public function updateFiltersInUrl() {

        $result = 'updateFiltersInUrl() {

            ';
                    
        foreach($this->dashboardObj->filters as $filter){

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
            $varCtype = null;

            if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
                $varCtype = (new Ctype)->load($filter->ctype_id);
                
            $result .= "\t\t\tthis.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "_operator_id', this." . $varCtype->id . "_" . $thisField->name . "_operator_id);\n";

            if($thisField->field_type_id == "relation" && $filter->field_type_id != "text"){
                if(isset($filter->default_value) && _strlen($filter->default_value) > 0 && $filter->is_hidden == true){
                    $result .= "\t\t\this.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n";
                } else {
                    $result .= "
                    if(Array.isArray(this." . $varCtype->id . "_" . $thisField->name . ")){
                        this.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . " != undefined && this." . $varCtype->id . "_" . $thisField->name . " != null ? this." . $varCtype->id . "_" . $thisField->name . ".map(x => x.id) : '');
        } else {
            this.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . " != undefined && this." . $varCtype->id . "_" . $thisField->name . " != null && this." . $varCtype->id . "_" . $thisField->name . ".id != undefined ? this." . $varCtype->id . "_" . $thisField->name . ".id : '');
        }\n";
                }
            } else if($thisField->field_type_id == "date") {
                $result .= "\t\t\tthis.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n";
                $result .= "\t\t\tthis.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "_2nd_value', this." . $varCtype->id . "_" . $thisField->name . "_2nd_value);\n";
            } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal") {
                $result .= "\t\t\tthis.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");\n";
                $result .= "\t\t\tthis.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "_2nd_value', this." . $varCtype->id . "_" . $thisField->name . "_2nd_value);\n";
            } else {
                $result .= "\t\t\tthis.prepareFilterCondition('" . $varCtype->id . "_" . $thisField->name . "', this." . $varCtype->id . "_" . $thisField->name . ");";
            }
                
        }
    
        $result .= "},";
        return $result;

    }


    public function generateResetFilterMethod() {
        
        echo 'resetFilter() {

            ';
            
            foreach($this->dashboardObj->filters as $filter){
                
                $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
                $varCtype = null;
                if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
                    $varCtype = (new Ctype)->load($filter->ctype_id);


                $alias = $varCtype->id;
        
                $fieldFullName = $alias . "_" . $thisField->name;

                if(empty($filter->operator_id)) {
                    $filter->operator_id = 0;
                }

                if($filter->default_value == "[USERID]")
                    $filter->default_value = Application::getInstance()->user->getId();
        
                if(empty($filter->operator_id)) {
                    $filter->operator_id = 0;
                }

                if(isset($filter->operator_id)) {
                    echo "this.{$fieldFullName}_operator_id = '{$filter->operator_id}';\n";
                } else {
                    echo "this.{$fieldFullName}_operator_id = null;\n";
                }

                if($thisField->field_type_id == "relation"){     
                    if(isset($filter->default_value)) {
                        echo "this.{$fieldFullName} = \"{$filter->default_value}\";\n";
                    } else {
                        echo "this.{$fieldFullName} = null\n";
                    }
                } else if($thisField->field_type_id == "date") {
                    if(isset($filter->default_value)) {
                        echo "this.{$fieldFullName} = \"{$filter->default_value}\";\n";
                        echo "this.{$fieldFullName}_2nd_value = \"{$filter->default_value}\";\n";
                    } else {
                        echo "this.{$fieldFullName} = null;\n";
                        echo "this.{$fieldFullName}_2nd_value = null;\n";
                    }
                } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal") {
                    if(isset($filter->default_value)) {
                        echo "this.{$fieldFullName} = {$filter->default_value};\n";
                        echo "this.{$fieldFullName}_2nd_value = {$filter->default_value};\n";
                    } else {
                        echo "this.{$fieldFullName} = null;\n";
                        echo "this.{$fieldFullName}_2nd_value = null;\n";
                    }
                } else {
                    if(isset($filter->default_value)) {
                        echo "this.{$fieldFullName} = \"{$filter->default_value}\";\n";
                    } else {
                        echo "this.{$fieldFullName} = null;\n";
                    }
                } 
                
        
            }

            echo $this->finalizeParameterValues();
            
        echo '
            this.get_data();
        },';
        
    }
    
    public function generateViewFiltersOperatorFieldVisibility() {

        $result = "";
        foreach($this->dashboardObj->filters as $filter) {
            $result .= $this->generateViewFiltersOperatorFieldVisibilityAction($filter);
        }

        return $result;
    }

    public function generateViewFiltersdependenciesComputedFields() {

        $result = "";
        foreach($this->dashboardObj->filters as $filter) {
            $result .= $this->generateViewFiltersdependenciesComputedFieldsAction($filter);
        }

        return $result;
    }

    
    private function generateViewFiltersOperatorFieldVisibilityAction($filter){
        
        $return_value = "";
        $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
        $varCtype = null;
        if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
            $varCtype = (new Ctype)->load($filter->ctype_id);

        if($thisField->field_type_id == "text" || ($thisField->field_type_id == "relation" && $filter->field_type_id == "text")){


            $return_value .= "computed_" . $varCtype->id . "_" . $thisField->name . "_value_visible: function(){
                if(
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id && 
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id.length > 0 && 
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != '' &&
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != 'text_is_empty' &&
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != 'text_is_not_empty &&
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != 'text_in' &&
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != 'text_no_in'

                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_value');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            ";

            $return_value .= "computed_" . $varCtype->id . "_" . $thisField->name . "_list_visible: function(){
                if(
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'text_in' ||
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'text_no_in'
                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_list');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            ";
        } else if($thisField->field_type_id == "relation"){


            $return_value .= "computed_" . $varCtype->id . "_" . $thisField->name . "_value_visible: function(){
                if(
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'relation_equal' ||
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'relation_not_equal' ||
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'relation_in' ||
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'relation_not_in' 

                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_value');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            ";

            $return_value .= "computed_" . $varCtype->id . "_" . $thisField->name . "_list_visible: function(){
                if(
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'text_in' ||
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'text_no_in'
                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_list');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            ";
            
        } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal"){


            $return_value .= "computed_" . $varCtype->id . "_" . $thisField->name . "_value_visible: function(){
                if(
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id && 
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id.length > 0 && 
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != 'number_empty' &&
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != 'number_not_empty' &&
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != 'number_in' &&
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id != 'number_not_in'

                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_value');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            
            computed_" . $varCtype->id . "_" . $thisField->name . "_2nd_value_visible: function(){
                if(
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'number_between' ||
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'number_not_between'
                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_2nd_value');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },

            computed_" . $varCtype->id . "_" . $thisField->name . "_list_visible: function(){
                if(
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'number_in' ||
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'number_not_in'
                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_list');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            ";
            
        } else if($thisField->field_type_id == "date"){


            $return_value .= "computed_" . $varCtype->id . "_" . $thisField->name . "_value_visible: function(){
                let self = this;
                if(
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'date_equal' ||
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'date_not_equal' ||
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'date_greater_than' ||
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'date_less_than' ||
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'date_between' ||
                        this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'date_not_between'

                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_value');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');

                        $(\"#" .  $varCtype->id . "_" . $thisField->name . "\").datepicker({
                            dateFormat: \"dd/mm/yy\",
                            onSelect:function(selectedDate, datePicker) {
                                self." .  $varCtype->id . "_" . $thisField->name . " = selectedDate;
                            },
                        });   
        
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            ";

            $return_value .= "computed_" . $varCtype->id . "_" . $thisField->name . "_2nd_value_visible: function(){
                let self = this;
                if(
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'date_between' ||
                    this." . $varCtype->id . "_" . $thisField->name . "_operator_id == 'date_not_between'
                ){

                    setTimeout(function () { 
                        var elem = $('#div_" . $varCtype->id . "_" . $thisField->name . "_2nd_value');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');

                        $(\"#" .  $varCtype->id . "_" . $thisField->name . "_2nd_value\").datepicker({
                            dateFormat: \"dd/mm/yy\",
                            onSelect:function(selectedDate, datePicker) {            
                                self." .  $varCtype->id . "_" . $thisField->name . "_2nd_value = selectedDate;
                            },
                        });   
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            ";
            
        }

        return $return_value;
    }
    
    private function generateViewFiltersdependenciesComputedFieldsAction($filter){
        
        $dependencies = $filter->dependencies;
        
        if(empty($dependencies))
            return "";
        
        $return_value = "";

        if(!empty($dependencies)){
            
            $dependencies = _str_replace("selected(","this.dep_selected('',",$dependencies);
            $dependencies = _str_replace(" and"," && ",$dependencies);
            $dependencies = _str_replace(" or"," || ",$dependencies);
            $dependencies = _str_replace(")and",")&&",$dependencies);
            $dependencies = _str_replace(")or",")||",$dependencies);

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);

            $return_value .= "computed_" . $thisField->ctype_id . "_" . "$filter->field_name: function(){
                if(($dependencies) == true){

                    setTimeout(function () { 
                        var elem = $('#div_" . $thisField->ctype_id . "_" . "$filter->field_name');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                    }, 0);

                    return true;
                } else {
                    return false;
                }
            },
            ";
        }

        return $return_value;
    }


}