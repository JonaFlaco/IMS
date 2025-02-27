<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;
use App\Core\Gviews\Components\FilterComponents\DateComponent;
use App\Core\Gviews\Components\FilterComponents\TextComponent;
use App\Core\Gviews\Components\FilterComponents\BooleanComponent;
use App\Core\Gviews\Components\FilterComponents\NumberComponent;
use App\Core\Gviews\Components\FilterComponents\ComboboxComponent;

class FilterationPanelComponent {

    private $ctypeObj;
    private $viewData;
    
    private $coreModel;
    private bool $hasFilter = false;
    
    public function __construct($ctypeObj, $viewData = null) {
        $this->viewData = $viewData;
        $this->ctypeObj = $ctypeObj;
        
        $this->coreModel = Application::getInstance()->coreModel;
    }

    public function hasFilter(){
        return $this->hasFilter;
    }

    public function generateModal(){
        
        $filtration_panel = $this->generateUIViewFilter();
        
        if(!empty($filtration_panel)){
            $this->hasFilter = true;
            
            ob_start(); ?>

            <!-- Dark Header Modal -->
            <div id="filtrationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
                <div style="height: 75%;" class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header modal-colored-header p-1 ps-2 bg-primary">
                            <h4 class="modal-title" id="info-header-modalLabel">
                                <i class="mdi mdi-filter-variant"></i>
                                <?= t("Filtration Panel") ?>
                            </h4>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <?= $filtration_panel ?>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="pagination_records_per_page"><?= t("Records Per Page") ?>:</label>
                                    <select name="pagination_records_per_page" v-model="pagination_records_per_page" class="form-select">
                                        <option value="" selected> - <?= t("Default") ?> - </option>
                                        <?php foreach(Application::getInstance()->globalVar->get('PAGINATION_PAGE_SIZE_ARRAY') as $item){ ?>
                                            <option value="<?= $item ?>"><?= $item ?></option>
                                        <?php } ?>
                                        
                                    </select>
                                </div>
                                    
                            </div>
                        </div>
                        <div class="modal-footer p-1">
                            <button data-bs-dismiss="modal" type="button" @click='filter()' class="btn btn-primary">
                                <i class="mdi mdi-filter-variant"></i>
                                <?= t("Aplicar") ?>
                            </button>

                            <?= $this->generateResetFilterButton() ?>

                            <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?= t("Cerrar") ?></button>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php

            return ob_get_clean();
        }
    }

    public function generateQuickAccess() : ?string {

        $filtration_panel = $this->generateUIViewFilter(true);
        if(!empty($filtration_panel)){
            return sprintf('<div class="col-sm-12"> <h4>%s</h4> <div class="row"> %s </div></div>',t("Filtros Rápidos"), $filtration_panel);
        }

        return null;
        
    }

    public function generateFilterButton(){
        ob_start();

        if($this->hasFilter): ?>
        
            <button data-bs-toggle="modal" data-bs-target="#filtrationModal" type="button" class="btn btn-secondary">
                <i class="mdi mdi-filter-variant"></i>
                <?= t("Filtros avanzados") ?>
            </button>
        
        <?php endif; 
        
        return ob_get_clean();
    }

    public function generateResetFilterButton(){
        ob_start();

        if($this->hasFilter): ?>
            
            <button 
                type="button" 
                @click="resetFilter" class="btn btn-danger"
                >
                <i class="mdi mdi-window-close"></i> 
                <span><?= t("Remover filtros") ?></span>
            </button>
            
        <?php endif; 
        
        return ob_get_clean();
    }


    private function generateUIViewFilter(bool $only_quick_access_filtrs = false){

        $return_value = "";
        foreach($this->viewData->filters as $filter){

            if($filter->is_hidden == true)
                continue;
                
            if($only_quick_access_filtrs == true && $filter->add_to_quck_access_panel != true)
                continue;

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
            
            if(isset($thisField) && $thisField->is_hidden_updated_read){
                continue;
            }
            
            if(isset($filter->ctype_id) && _strlen($filter->ctype_id) > 0){

                $ctypeRel = (new Ctype)->load($filter->ctype_id);

                $return_value .= $this->generateUIViewFilter_sub($thisField, $ctypeRel,$filter, $only_quick_access_filtrs);
                
            } else {

                $return_value .= $this->generateUIViewFilter_sub($thisField, $this->ctypeObj,$filter, $only_quick_access_filtrs);

            }
            
        }

        return $return_value;
    }

    
    private function generateUIViewFilter_sub($field, $ctypeObj, $filter, $only_quick_access_filtrs){

        $return_value = "";

        if($field->field_type_id == "text" || ($field->field_type_id == "relation" && $filter->field_type_id == "text")){
            $return_value .= (new TextComponent($filter, $field))->generate($only_quick_access_filtrs);
        } else if ($field->field_type_id == "relation" && (!isset($filter->field_type_id) || $filter->field_type_id == "relation")){
            $return_value .= (new ComboboxComponent($filter, $field, $this->viewData->filters))->generate($only_quick_access_filtrs );
        } else if ($field->field_type_id == "boolean"){
            $return_value .= (new BooleanComponent($filter, $field))->generate($only_quick_access_filtrs);
        } else if ($field->field_type_id == "date"){
            $return_value .= (new DateComponent($filter, $field))->generate($only_quick_access_filtrs);
        } else if ($field->field_type_id == "number" || $field->field_type_id == "decimal"){
            $return_value .= (new NumberComponent($filter, $field))->generate($only_quick_access_filtrs);
        } 

        return $return_value;
    }

    
    
    public function generateFilterFieldVisibility(){
        
        ob_start(); 
        
        foreach($this->viewData->filters as $filter){

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
            $varCtype = $this->ctypeObj;
            if(isset($filter->ctype_id) && _strlen($filter->ctype_id))
                $varCtype = (new Ctype)->load($filter->ctype_id);

            $fieldFullName = $varCtype->id . "_" . $thisField->name;

            if($thisField->field_type_id == "text" || ($thisField->field_type_id == "relation" && $filter->field_type_id == "text")){

                ?>
                computed_<?= $fieldFullName ?>_value_visible: function(){
                    
                    if(
                        this.<?= $fieldFullName ?>_operator_id &&
                        this.<?= $fieldFullName ?>_operator_id.length > 0 && 
                        this.<?= $fieldFullName ?>_operator_id != "" &&
                        this.<?= $fieldFullName ?>_operator_id != 'text_is_empty' &&
                        this.<?= $fieldFullName ?>_operator_id != 'text_is_not_empty' &&
                        this.<?= $fieldFullName ?>_operator_id != 'text_in' &&
                        this.<?= $fieldFullName ?>_operator_id != 'text_not_in'

                    ){
                        setTimeout(function () {
                            var elem = $('#div_<?= $fieldFullName ?>_value');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
                
                computed_<?= $fieldFullName ?>_list_visible: function(){
                    if(
                        this.<?= $fieldFullName ?>_operator_id == 'text_in' ||
                        this.<?= $fieldFullName ?>_operator_id == 'text_not_in'
                    ){

                        setTimeout(function () { 
                            var elem = $('#div_<?= $fieldFullName ?>_list');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
                
            <?php } else if($thisField->field_type_id == "relation"){ ?>


                computed_<?= $fieldFullName ?>_value_visible: function(){
                    if(
                            this.<?= $fieldFullName ?>_operator_id == 'relation_equal' ||
                            this.<?= $fieldFullName ?>_operator_id == 'relation_not_equal' ||
                            this.<?= $fieldFullName ?>_operator_id == 'relation_in' ||
                            this.<?= $fieldFullName ?>_operator_id == 'relation_not_in' 

                    ){

                        setTimeout(function () { 
                            var elem = $('#div_<?= $fieldFullName ?>_value');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
               
                computed_<?= $fieldFullName ?>_list_visible: function(){
                    if(
                        this.<?= $fieldFullName ?>_operator_id == 'text_in' ||
                        this.<?= $fieldFullName ?>_operator_id == 'text_not_in'
                    ){

                        setTimeout(function () { 
                            var elem = $('#div_<?= $fieldFullName ?>_list');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
                
            <?php } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal"){ ?>


                computed_<?= $fieldFullName ?>_value_visible: function(){
                    if(
                            this.<?= $fieldFullName ?>_operator_id &&
                            this.<?= $fieldFullName ?>_operator_id.length > 0 && 
                            this.<?= $fieldFullName ?>_operator_id != 'number_empty' &&
                            this.<?= $fieldFullName ?>_operator_id != 'number_not_empty' &&
                            this.<?= $fieldFullName ?>_operator_id != 'number_in' &&
                            this.<?= $fieldFullName ?>_operator_id != 'number_not_in'

                    ){

                        setTimeout(function () { 
                            var elem = $('#div_<?= $fieldFullName ?>_value');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
                
                computed_<?= $fieldFullName ?>_2nd_value_visible: function(){
                    if(
                        this.<?= $fieldFullName ?>_operator_id == 'number_between' ||
                        this.<?= $fieldFullName ?>_operator_id == 'number_not_between'
                    ){

                        setTimeout(function () { 
                            var elem = $('#div_<?= $fieldFullName ?>_2nd_value');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },

                computed_<?= $fieldFullName ?>_list_visible: function(){
                    if(
                        this.<?= $fieldFullName ?>_operator_id == 'number_in' ||
                        this.<?= $fieldFullName ?>_operator_id == 'number_not_in'
                    ){

                        setTimeout(function () { 
                            var elem = $('#div_<?= $fieldFullName ?>_list');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
                
            <?php } else if($thisField->field_type_id == "date"){ ?>


                computed_<?= $fieldFullName ?>_value_visible: function(){
                    let self = this;
                    if(
                            this.<?= $fieldFullName ?>_operator_id == 'date_equal' ||
                            this.<?= $fieldFullName ?>_operator_id == 'date_not_equal' ||
                            this.<?= $fieldFullName ?>_operator_id == 'date_greater_than' ||
                            this.<?= $fieldFullName ?>_operator_id == 'date_less_than' ||
                            this.<?= $fieldFullName ?>_operator_id == 'date_between' ||
                            this.<?= $fieldFullName ?>_operator_id == 'date_not_between'

                    ){

                        setTimeout(function () { 
                            var elem = $('#div_<?= $fieldFullName ?>_value');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');

                            $("#<?= $fieldFullName ?>").datepicker({
                                dateFormat: "dd/mm/yy",
                                onSelect:function(selectedDate, datePicker) {
                                    self.<?= $fieldFullName ?> = selectedDate;
                                },
                            });   
            
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
                
                computed_<?= $fieldFullName ?>_2nd_value_visible: function(){
                    let self = this;
                    if(
                        this.<?= $fieldFullName ?>_operator_id == 'date_between' ||
                        this.<?= $fieldFullName ?>_operator_id == 'date_not_between'
                    ){

                        setTimeout(function () { 
                            var elem = $('#div_<?= $fieldFullName ?>_2nd_value');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');

                            $("#<?= $fieldFullName ?>_2nd_value").datepicker({
                                dateFormat: "dd/mm/yy",
                                onSelect:function(selectedDate, datePicker) {            
                                    self.<?= $fieldFullName ?>_2nd_value = selectedDate;
                                },
                            });   
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
                <?php
            }
        }


        return ob_get_clean();
    }


    

    public function generateFilterFieldDependency(){
        
        ob_start();

        foreach($this->viewData->filters as $filter){
            $dependencies = $filter->dependencies;
            
            if(empty($dependencies))
                continue;
            

            if(!empty($dependencies)){
                
                $dependencies = _str_replace("selected(","this.dep_selected('',",$dependencies);
                $dependencies = _str_replace(" and"," && ",$dependencies);
                $dependencies = _str_replace(" or"," || ",$dependencies);
                $dependencies = _str_replace(")and",")&&",$dependencies);
                $dependencies = _str_replace(")or",")||",$dependencies);

                $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);

                ?>

                computed_<?= $thisField->ctype_id . "_" . $filter->field_name ?>: function(){
                    if((<?= $dependencies ?>) == true){

                        setTimeout(function () { 
                            var elem = $('#div_<?=  $thisField->ctype_id . "_" . $filter->field_name ?>');
                            elem.removeClass('highlight');
                            elem.addClass('highlight');
                        }, 0);

                        return true;
                    } else {
                        return false;
                    }
                },
                
            <?php }

        }
        
        return ob_get_clean();

    }


    
    public function generateViewFilterRefereshList(){
        
        ob_start();

        foreach($this->viewData->filters as $filter){
            
            if(empty($filter->data_source_filter_by_field_name) ){
                continue;
            }

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
            $filter_ctype = (new Ctype)->load($filter->ctype_id);

            $fieldFullName = $filter_ctype->id . "_" . $filter->field_name;
            ?>

            
            reload_<?= $fieldFullName?>(val){

                if(val != undefined && val != null && typeof val == 'object'){
                    val = val.id;
                }
                
                this.loading_<?= $fieldFullName ?> = true;

                var filters = [val];

                var formData = new FormData();
                formData.append('filters', filters);

                var self = this;
                axios({
                    method: 'POST',
                    url: '/InternalApi/genericPreloadList/' + val + '?field_id=<?= $thisField->id ?>&add_all=true&response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){
                    if(response.data.status == 'success'){
                        
                        self.pl_<?= $fieldFullName ?> = response.data.result;
                        self.loading_<?= $fieldFullName ?> = false;

                    } else {
                        self.pl_<?= $fieldFullName ?> = [];
                        $.toast({
                            heading: 'Error',
                            text: 'An error occured while loading preload list for <?= $thisField->title ?>',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        self.loading_<?= $fieldFullName ?> = false;
                        return;
                    }
                })
                .catch(function(error){
                      
                    if(error.response != undefined && error.response.data.status == 'failed') {
                        $.toast({
                            heading: 'Error',
                            text: error.response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    } else {
                        $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    }
                    self.hide_loading_popup();
                    
                    self.loading_<?= $fieldFullName ?> = false;
                });
    
    

            },

            <?php
            
        }

        return ob_get_clean();

    }

    public function prepareFilterConditionMethod() {
        ob_start(); ?>

        prepareFilterCondition(formData, key, value) {
            this.filtersList.push({'key': key, 'value': value});
            
            if(formData) {
                formData.append(key, value);
            }
            
            return formData;
        },

        prepareFilterConditionOnUrl() {
            
            var url = new URL(window.location.href);
            
            this.filtersList.forEach((itm) => {

            
            if(itm.value == null || itm.value.length == 0 || itm.value == "null") {
                url.searchParams.delete(itm.key);
            } else {
                url.searchParams.set(itm.key, itm.value);
            }
            });

            window.history.replaceState(null, null, url);
        },

        <?php
        return ob_get_clean();
    }

    public function getFilterObject(){
        
        ob_start(); ?>

        var formData = new FormData();
        formData.append('selected_ids', selected_ids);

        formData = this.prepareFilterCondition(formData, 'pagination_records_per_page', this.pagination_records_per_page);
        
        <?php foreach($this->viewData->filters as $filter){

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
            
            $varCtype = $this->ctypeObj;
            if(isset($filter->ctype_id) && _strlen($filter->ctype_id)){
                $varCtype = (new Ctype)->load($filter->ctype_id); 
            }

            $fieldFullName = $varCtype->id . "_" . $thisField->name;
            ?>
        
            formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>_operator_id', (this.<?= $fieldFullName ?>_operator_id == "0" ? null : this.<?= $fieldFullName ?>_operator_id));

            <?php if($thisField->field_type_id == "relation" && $filter->field_type_id != "text"):
                if(isset($filter->default_value) && _strlen($filter->default_value) > 0 && $filter->is_hidden == true): ?>
                    
                    formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>', this.<?= $fieldFullName ?>);

                <?php else: ?>

                    if(Array.isArray(this.<?= $fieldFullName ?>)){
                        formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>', this.<?= $fieldFullName ?> != undefined && this.<?= $fieldFullName ?> != null ? this.<?= $fieldFullName ?>.map(x => x.id) : '');
                    } else {
                        formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>', this.<?= $fieldFullName ?> != undefined && this.<?= $fieldFullName ?> != null && this.<?= $fieldFullName ?>.id != undefined ? this.<?= $fieldFullName ?>.id : '');
                    }

                <?php endif; ?>
            <?php elseif ($thisField->field_type_id == "date"): ?>
                
                formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>', this.<?= $fieldFullName ?>);
                formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>_2nd_value', this.<?= $fieldFullName ?>_2nd_value);

            <?php elseif($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal"): ?>
                
                formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>', this.<?= $fieldFullName ?>);
                formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>_2nd_value', this.<?= $fieldFullName ?>_2nd_value);

            <?php else : ?>
                formData = this.prepareFilterCondition(formData, '<?= $fieldFullName ?>', this.<?= $fieldFullName ?>);
            <?php endif; ?>
            
        <?php } ?>

        <?php

        return ob_get_clean();

    }

    public function finalizeParameterValues() {

        ob_start(); ?>

        <?php foreach($this->viewData->filters as $filter){

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
            
            $varCtype = $this->ctypeObj;
            if(isset($filter->ctype_id) && _strlen($filter->ctype_id)){
                $varCtype = (new Ctype)->load($filter->ctype_id); 
            }

            $fieldFullName = $varCtype->id . "_" . $thisField->name;
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

    public function generateResetFilterMethod() {
        
        echo 'resetFilter() {

            this.page = 1;
            this.rowsPerPage = 50;
            ';
            
            foreach($this->viewData->filters as $filter){
                
                $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
                $varCtype = $this->ctypeObj;

                if(isset($filter->ctype_id) && _strlen($filter->ctype_id)){
                    $varCtype = (new Ctype)->load($filter->ctype_id);
                }

                $fieldFullName = $varCtype->id . "_" . $thisField->name;

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
            this.filter(1);
        },';
        
    }
    public function generateFilterMethod(){

        $afterSuccessFilter = null;
        $file =  APP_ROOT_DIR . DS . "Views" . DS . "GviewExtends" . DS . toPascalCase($this->viewData->id) . "AfterSuccessFilter.js.php";

        if(!is_file($file)){
            $file =  EXT_ROOT_DIR . DS . "Views" . DS . "GviewExtends" . DS . toPascalCase($this->viewData->id) . "AfterSuccessFilter.js.php";
        }
        
        if(is_file($file)){
            ob_start();
            require($file);
            $afterSuccessFilter = ob_get_clean();    
        }


        ob_start(); ?>

        filter(page = null, ignore_selected_ids = false){
            
            this.toggle_checkbox = false;
            this.is_loading = 1;
            this.show_loading_popup('<?= t("Loading, please wait") ?>...');
            
            let self = this;
            
            let selected_ids = '';
            if(ignore_selected_ids != true){
                this.records.forEach(function(itm){
                    
                    if(itm.is_selected == true){
                        if(selected_ids.length > 0)
                            selected_ids += ',';
        
                        selected_ids += itm.<?= $this->ctypeObj->id ?>_id_main;
                    }
                });
            }

            <?php if ( $this->hasFilter() ): ?>

                logModal = bootstrap.Modal.getInstance(document.getElementById('filtrationModal'))
                if(logModal) {
                    logModal.hide();
                }
                
            <?php endif; ?>
            

            this.records = [];
            
            
            if(page == null) {
                page = <?= $this->getParamValue("page", "page") ?>;
            }
            <?= $this->getFilterObject(); ?>
            formData = this.prepareFilterCondition(formData, 'page', page);

            this.prepareFilterConditionOnUrl();

            axios({
                    method: 'post',
                    url: '/GViews/loadData/<?= $this->viewData->id ?>?load_based_ctype=0&page=' + page + '&response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){

                    if(response.data.status != 'success'){

                        $.toast({
                            heading: 'error',
                            text: (response.data.message != null ? response.data.message : 'something went wrong'),
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
        
                        self.paginationButtons = [];
                        self.footerNoOfRecords = null;

                        self.paginationButtonLoading = false;

                    } else {
                        self.records = response.data.records;
                        self.all_ids = response.data.all_ids;
                        self.paginationButtons = response.data.paginationButtons;
                        self.footerNoOfRecords = response.data.footerNoOfRecords;

                        <?= $afterSuccessFilter ?>

                        self.paginationButtonLoading = false;
                    }

                    self.is_loading = 0;
                    self.hide_loading_popup();
                    
                })
                .catch(function(error){
                    
                    
                    if(error.response != undefined && error.response.data.type == 'needs_login'){
                        window.location.href = '/user/login?needs_login=1&destination=/<?= Application::getInstance()->request->getUrlStr() ?>';
                        return false;
                    } else if(error.response != undefined && error.response.data.status == 'failed') {
                        $.toast({
                            heading: 'Error',
                            text: error.response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    } else {
                        $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    }
                    self.hide_loading_popup();

                    self.paginationButtonLoading = false;
                    self.is_loading = 0;
                    self.hide_loading_popup();
                });
        },
        
        <?php

        return ob_get_clean();
        
    }

    public function getDataObject(){

        $result = [];

        $result["all_ids"] = $this->getParamValue("all_ids", "");

        foreach($this->viewData->filters as $filter){

            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);

            if($thisField->field_type_id == "relation"){

                $filter_ctype = (new Ctype)->load($filter->ctype_id);

                $list = [];
                foreach($this->coreModel->getPreloadList($thisField->data_source_table_name,$thisField->data_source_value_column, $thisField->data_source_display_column, array("original_ctype_id" => $thisField->parent_id, "fixed_where_condition" => $thisField->data_source_fixed_where_condition, "sort_field_name" => $thisField->data_source_sort_column, "field_id" => $thisField->id)) as $item){
                    
                    $obj = new \StdClass();
                    $obj->id = $item->id;
                    $obj->name = $item->name;

                    $list[] = $obj;
                }

                $result["pl_" . $filter_ctype->id . "_" . $filter->field_name] = $list;
                $result["loading_" .  $filter_ctype->id . "_" . "$filter->field_name"] = false;
                
                
            }
        }
    
    
        foreach($this->viewData->filters as $filter){
            
            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
            $varCtype = $this->ctypeObj;

            if(isset($filter->ctype_id) && _strlen($filter->ctype_id)){
                $varCtype = (new Ctype)->load($filter->ctype_id);
            }

            $fieldFullName = $varCtype->id . "_" . $thisField->name;

            if($filter->default_value == "[USERID]")
                $filter->default_value = Application::getInstance()->user->getId();
    
            if(empty($filter->operator_id)) {
                $filter->operator_id = 0;
            }
            
            if($thisField->field_type_id == "boolean" && _strlen($filter->default_value) == 0){
                $filter->default_value = "null";
            }

            if($thisField->field_type_id == "relation"){
                $result[$fieldFullName . "_operator_id"] = $this->getParamValue($fieldFullName . "_operator_id", $filter->operator_id);
                $result[$fieldFullName] = $this->getParamValue($fieldFullName, $filter->default_value);
            } else if($thisField->field_type_id == "date") {
                $result[$fieldFullName . "_operator_id"] = $this->getParamValue($fieldFullName . "_operator_id", $filter->operator_id);
                $result[$fieldFullName] = $this->getParamValue($fieldFullName, $filter->default_value);
                $result[$fieldFullName . "_2nd_value"] = $this->getParamValue($fieldFullName . "_2nd_value", $filter->default_value);
            } else if($thisField->field_type_id == "number" || $thisField->field_type_id == "decimal") {
                $result[$fieldFullName . "_operator_id"] = $this->getParamValue($fieldFullName . "_operator_id", $filter->operator_id);
                $result[$fieldFullName] = $this->getParamValue($fieldFullName, $filter->default_value);
                $result[$fieldFullName . "_2nd_value"] = $this->getParamValue($fieldFullName . "_2nd_value", $filter->default_value);
            } else {
                $result[$fieldFullName . "_operator_id"] = $this->getParamValue($fieldFullName . "_operator_id", $filter->operator_id);
                $result[$fieldFullName] = $this->getParamValue($fieldFullName, $filter->default_value);
            }
            
    
        }
    
        return $result;

    }

    private function getParamValue($key, $defaultValue) {
        $value = Application::getInstance()->request->getParam($key);
        if(_strlen($value) > 0)
            return $value;
        else 
            return $defaultValue;
    }

    public function generateFilterMethodBasedOnCtype(){

        ob_start(); ?>

        filter(page = null){
                
            this.records = [];
            this.is_loading = 1;
        
            let self = this;
            
            var formData = new FormData();
        
            axios({
                method: 'post',
                url: '/GViews/loadData/<?= $this->ctypeObj->id ?>?load_based_ctype=1&page=' + page + '&response_format=json',
                data:formData,
                headers: {
                    'Content-Type': 'form-data',
                }
            })
            .then(function(response){
                    
                if(response.data.status != 'success'){
    
                    $.toast({
                        heading: 'error',
                        text: (response.data.message != null ? response.data.message : 'something went wrong'),
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
    
                    self.paginationButtons = [];
                    self.footerNoOfRecords = null;
    
                    self.paginationButtonLoading = false;
    
                } else {
                    self.records = response.data.records;
                    self.paginationButtons = response.data.paginationButtons;
                    self.footerNoOfRecords = response.data.footerNoOfRecords;
    
                    self.paginationButtonLoading = false;
                }
    
                self.is_loading = 0;
                    
            })
            .catch(function(error){
                    
                    if(error.response != undefined && error.response.data.type == 'needs_login'){
                        window.location.href = '/user/login?needs_login=1&destination=/<?= Application::getInstance()->request->getUrlStr() ?>';
                        return false;
                    } else if(error.response.data.status == 'failed') {
                        $.toast({
                            heading: 'Error',
                            text: error.response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    } else {
                        $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    }
                    self.hide_loading_popup();
        
                    self.paginationButtonLoading = false;
                    self.is_loading = 0;
            });
        },

        <?php

        return ob_get_clean();
        
    }
}