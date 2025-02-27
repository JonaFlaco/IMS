<?php

/*
 * This class is responsible to render Add and Edit interface
 * It breaks down the process into sub components to make it easier to maintaine.
 */

namespace App\Core\Gctypes;

use \App\Core\Application;
use \App\Models\CoreModel;
use App\Exceptions\ForbiddenException;
use App\Core\Gctypes\Components\LogComponent;
use App\Core\Gctypes\Components\MapComponent;
use App\Core\Gctypes\Components\jsHelperMethods;
use App\Core\Gctypes\Components\PageHeaderComponent;
use App\Core\Gctypes\Components\TopActionsComponent;
use App\Core\Gctypes\Components\UIGeneratorComponent;
use App\Core\Gctypes\Components\UpdateStatusComponent;
use App\Core\Gctypes\Components\ErrorHandlerComponent;
use App\Core\Gctypes\Components\FooterActionsComponent;
use App\Core\Gctypes\Components\ImagePreviewComponent;
use App\Core\Gctypes\Components\JustificationForEditComponent;
use App\Core\Gctypes\Components\KeyBindingComponent;
use App\Core\Gctypes\CtypesHelper;

class AddEditGen {

    private Application $app;
    private CoreModel $coreModel;
    private object $ctypeObj;
    private array $fields;

    private ?object $surveyObj;
    private ?string $lang;
    private ?object $ctypePermissionObj;

    private bool $allowOpenTpl = true;
    private $id = null;
    private ?object $recordData = null;

    private $js_system_variables = [];

    private UpdateStatusComponent $UpdateStatusComponent;
    private JustificationForEditComponent $justificationForEditComponent;
    private KeyBindingComponent $keyBindingComponent;
    private ErrorHandlerComponent $errorHandlerComponent;
    private TopActionsComponent $topActionsComponent;
    private LogComponent $logComponent;
    private FooterActionsComponent $footerActionsComponent;
    private PageHeaderComponent $pageHeaderComponent;
    private MapComponent $mapComponent;
    private UIGeneratorComponent $uiGeneratorComponent;
    private ImagePreviewComponent $imagePreviewComponent;
    private CtypesHelper $ctypesHelper;

    private $loadRichTextEditor = false;

    public function __construct(object $ctypeObj, $id = null, ?object $surveyObj = null) {
        
        $this->app = Application::getInstance();
        $this->coreModel = $this->app->coreModel;

        $this->ctypeObj = $ctypeObj;
        $this->surveyObj = $surveyObj;
        $this->id = $id;

        $this->ctypesHelper = new CtypesHelper();

        $this->ctypeObj->extends = $this->loadExtends();
    }


    public function generate() : array {
        
        $this->lang = Application::getInstance()->user->getLangId();


        if(Application::getInstance()->user->isGuest() && $this->surveyObj->type_id == "internal"){ 
            Application::getInstance()->user->checkAuthentication();
        }

        if(!empty($this->ctypeObj->{"name_" . $this->lang})){
            $this->ctypeObj->name = $this->ctypeObj->{"name_" . $this->lang};
        }


        $this->ctypePermissionObj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);

        $this->allowOpenTpl = true;
        if($this->isAddMode() || empty($this->ctypePermissionObj) || $this->ctypePermissionObj->allow_read != true || $this->ctypeObj->disable_read == true) {
            $this->allowOpenTpl = false;
        }

        $this->fields = $this->ctypesHelper->getFields($this->isAddMode(), $this->ctypeObj->id); 

        foreach($this->getFieldByType($this->fields, "text", false) as $field) {
            if($field->appearance_id == "1_rich_text") {
                $this->loadRichTextEditor = true;
            }
        }

        if($this->isEditMode()) {

            $this->recordData = $this->coreModel->nodeModel($this->ctypeObj->id)
                ->id($this->id)
                ->loadFirstOrFail();

            $this->checkPermissions();

        }

        $this->UpdateStatusComponent = new UpdateStatusComponent($this->ctypeObj, $this->isEditMode(), $this->recordData);
        $this->justificationForEditComponent = new JustificationForEditComponent($this->ctypeObj, $this->isEditMode());
        $this->keyBindingComponent = new KeyBindingComponent($this->ctypeObj, $this->isEditMode());
        $this->errorHandlerComponent = new ErrorHandlerComponent();
        $this->topActionsComponent = new TopActionsComponent($this->ctypeObj, $this->fields, $this->isEditMode(), $this->recordData, $this->allowOpenTpl, $this->ctypePermissionObj);
        $this->logComponent = new LogComponent($this->ctypeObj, $this->isEditMode(), $this->recordData, $this->ctypePermissionObj);
        $this->footerActionsComponent = new FooterActionsComponent($this->ctypeObj, $this->ctypePermissionObj, $this->isEditMode(), $this->isSurvey(), $this->recordData);
        $this->pageHeaderComponent = new PageHeaderComponent($this->ctypeObj, $this->isEditMode(), $this->recordData, $this->isSurvey());
        $this->mapComponent = new MapComponent($this->fields);
        $this->imagePreviewComponent = new ImagePreviewComponent($this->ctypeObj, $this->ctypePermissionObj, $this->recordData);

        $this->uiGeneratorComponent = new UIGeneratorComponent($this->ctypeObj, $this->fields, $this->ctypePermissionObj, $this->isEditMode(), $this->isSurvey(), $this->recordData);

        $data = [
            'title' => ($this->isEditMode() ? "Edit " : "Add ") . $this->ctypeObj->name ,
            'script' => $this->getPageScript(),
            'sett_load_rich_text_editor' => $this->loadRichTextEditor
        ];
        
        return $data;
    }

    private function loadExtends() {
         
        $file =  APP_ROOT_DIR . DS . "Views" . DS ."CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . "Extends.php";
        
        if(!is_file($file)){
            $file =  EXT_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . "Extends.php"; 
        } 

        if(is_file($file)){
            ob_start();
            require $file;
            return ob_get_clean();
        }

        return null;
    }

    private function getJsSystemVariables() {
        $this->js_system_variables = [
            "sys_is_survey" => $this->isSurvey()
        ];

        return $this->js_system_variables;
    }

    private function getPageScript() : string {

        ob_start();
        
        echo $this->ctypeObj->extends; ?>

        <?php if($this->isEditMode()): ?>
            <?= Application::getInstance()->view->renderView('Components/LogComponent') ?>
            <?= Application::getInstance()->view->renderView('Components/UpdateStatusComponent') ?>
        <?php endif; ?>

        <?php if($this->loadRichTextEditor) { Application::getInstance()->view->renderView('components/RichTextEditorComponent'); } ?>

        <script type="text/x-template" id="tpl-main">
            <div>
                <?= $this->imagePreviewComponent->generateModal() ?>
                <?= $this->UpdateStatusComponent->generateModal() ?>
                <?= $this->justificationForEditComponent->generateModal() ?>
                <?= $this->errorHandlerComponent->generateModal() ?>
                <?= $this->pageHeaderComponent->generate() ?>
                <?= Application::getInstance()->session->flash() ?>
                <?= $this->uiGeneratorComponent->generateFieldCollectionModal() ?>
                <?= $this->topActionsComponent->generate() ?>
                <?= $this->uiGeneratorComponent->generate() ?>
                <?= $this->logComponent->generate() ?>
                <?= $this->footerActionsComponent->generate() ?>
            </div>
        </script>

        <script>

            var vm = new Vue({
                mixins: [(typeof mix == 'undefined' ? [] : mix)],
                el: '#vue-cont',
                components: {
                    Multiselect: window.VueMultiselect.default
                },
                template:'#tpl-main',
                data: <?= $this->getDataObject(); ?>,
                beforeDestroy() {
                    <?= $this->keyBindingComponent->destroy() ?>
                },
                mounted(){

                    var self = this;

                    <?= $this->keyBindingComponent->generate() ?>

                    <?= $this->initializeDatePicker() ?>

                    <?= $this->uiGeneratorComponent->reloadFieldList() ?>

                    this.validate();
                    
                },
                
                methods: {
                    <?= $this->imagePreviewComponent->generateMethods() ?>
                    <?= $this->generateValidateMethod() ?>
                    <?= $this->generateFieldValidationCondition() ?>
                    <?= $this->generateComboboxHandleChangeMethod() ?>
                    <?= $this->generateFieldButtonMethods() ?>
                    <?= $this->generateFieldAttachmentMethods() ?>
                    <?= $this->uiGeneratorComponent->generateFieldCollectionDeleteRecordMethod() ?>
                    <?= $this->uiGeneratorComponent->generateFieldCollectionAddRecordMethod() ?>
                    <?= $this->uiGeneratorComponent->generateFieldCollectionEditRecordMethod() ?>
                    <?= $this->uiGeneratorComponent->generateFieldCollectionCloseModalMethod() ?>
                    <?= $this->uiGeneratorComponent->generateFieldCollectionSaveModalMethod() ?>
                    <?= $this->uiGeneratorComponent->generateFieldRefreshList() ?>
                    <?= $this->UpdateStatusComponent->generateMethods() ?>
                    <?= $this->generateDeleteRecordMethod() ?>
                    <?= $this->generateAddRecordMethod() ?>
                    
                    <?= $this->mapComponent->generateMapPinMerkerMethods() ?>
                    <?= $this->mapComponent->generateMapGetCurrentLocationMethods() ?>
                    <?= $this->generatePostDataMethod() ?>
                    <?= $this->generatePostDataActionMethod() ?>
                    <?= $this->generatePostDataObject() ?>
                    <?= $this->topActionsComponent->generateMethods() ?>
                    <?= (new jsHelperMethods)->generate($this->getJsSystemVariables()) ?>
                    <?= $this->errorHandlerComponent->showErrorDialog() ?>
                    
                    select2SearchChange(keyword, id) {
                        let self = this;

                        var obj = document.getElementById(id).parentElement.parentElement;
                        var data_name = obj.getAttribute("data-name");
                        var field_id = obj.getAttribute("data-field-id");
                        var extra_keyword = obj.getAttribute("data-extra-keyword");

                        self['keyword_' + data_name] = keyword;
                        self['pl_' + data_name] = [];

                        if(!keyword || keyword.length == 0) {
                            return;
                        }

                        var formData = new FormData();

                        //get extra keyword
                        
                        if(extra_keyword.includes(",")) {
                        
                            var tempValues = [];
                            let i = 0;
                            extra_keyword.split(",").forEach((itm) => {
                                
                                tempValues[i] = self[itm];
                                
                                if(tempValues[i] == undefined) {
                                    tempValues[i] = null;
                                } else {
                                    
                                    if(Array.isArray(tempValues[i])) {
                                        tempValues[i] = tempValues[i];
                                    } else if(typeof tempValues[i] == 'object' && tempValues[i]?.length >= 0){
                                        tempValues[i] = tempValues[i].map((x) => x.id);
                                    } else if (typeof tempValues[i] == 'object') {
                                        tempValues[i] = tempValues[i].id
                                    }
                                }

                                i++;
                                
                            });

                            i = 0;
                            tempValues.forEach((itm) => {
                                let postfix = i > 0 ? '_' + i : '';
                                formData.append('filters' + postfix, itm);
                                i++;
                            });

                        } else {
                            var result = "";
                            var value = self[extra_keyword];
                            
                            
                            if(value == undefined || value == null) {
                                value = null;
                            } else {
                                if(Array.isArray(value)) {
                                    value = value;
                                } else if(typeof value == 'object' && value?.length >= 0){
                                    value = value.map((x) => x.id);
                                } else if (typeof value == 'object') {
                                    value = value.id
                                }
                            }
                            formData.append('filters', value);
                            
                        }




                        self['loading_' + data_name] = true;
                        
                        axios.post(
                            '/InternalApi/GenericPreloadList?field_id=' + field_id + '&keyword=' + keyword + '&response_format=json',
                            formData,
                        )
                        .then(function(response){
                            
                            self['pl_' + data_name] = response.data.result;
                            
                            self['loading_' + data_name] = false;
                            
                        })
                        .catch(function(error){
                            $.toast({heading: 'Error',text: 'Someting went wrong while loading choices',showHideTransition: 'slide',position: 'top-right',icon: 'error'});

                            self['loading_' + data_name] = false;
                        });

                    },
                    select2Open(id) {
                        let self = this;
                        
                        var obj = document.getElementById(id).parentElement.parentElement;
                        var data_name = obj.getAttribute("data-name");
                        var field_id = obj.getAttribute("data-field-id");
                        var v_model = obj.getAttribute("data-v-model");

                        var parts = v_model.split(".");
                        
                        if(parts.length == 0 || parts.length > 2) {
                            return;
                        }
                        main_part = parts[0];
                        sub_part = parts[1];

                        if(parts.length == 1) {
                            if(self[main_part] == null || self[main_part].length == 0) {
                                self['pl_' + data_name] = [];
                            } else {
                                if(Array.isArray(self[main_part])) {
                                    self['pl_' + data_name] = self[main_part];
                                } else {
                                    self['pl_' + data_name] = [self[main_part]];
                                }
                            }
                        } else {
                            if(self[main_part][sub_part] == null || self[main_part][sub_part].length == 0) {
                                self['pl_' + data_name] = [];
                            } else {
                                if(Array.isArray(self[main_part][sub_part])) {
                                    self['pl_' + data_name] = self[main_part][sub_part];
                                } else {
                                    self['pl_' + data_name] = [self[main_part][sub_part]];
                                }
                            }
                        }
                    },
                },
                
                computed: {

                    <?= $this->generateHasErrorComputed() ?>
                    <?= $this->generateFieldDependancyComputed() ?>
                    <?= $this->generateFieldRequiredConditionComputed() ?>
                    <?= $this->generateFieldReadOnlyConditionComputed() ?>

                }
            });
                    

            <?= $this->mapComponent->generateFieldInitMapFunctions() ?>
            <?= $this->mapComponent->generateInitMapFunctions() ?>

        </script>

        <?= $this->mapComponent->includeGoogleMapsScript() ?>
        

        <?php
        return ob_get_clean();

    }
    
    

    private function isSurvey() : bool {
        return !empty($this->surveyObj);
    }

    private function isNotSurvey() : bool {
        return !$this->isSurvey();
    }

    private function isPublic() : bool {
        return $this->isSurvey() && $this->surveyObj->type_id == "public";
    }
    
    private function isNotPublic() : bool {
        return !$this->isPublic();
    }


    private function isAddMode() : bool {
        return empty($this->id);
    }


    private function isEditMode() : bool {
        return !empty($this->id);
    }


    private function getFieldByType(array $fields, $type, $return_hidden = true) : array {
        
        $data = [];
        foreach($fields as $field) {

            if($field->field_type_id == $type && ($return_hidden == true || $field->is_hidden_updated !== true)) {
                $data[] = $field;
            }

        }

        return $data;
    }


    private function checkPermissions() {

        if(property_exists($this->recordData,"is_system_object") && $this->recordData->is_system_object && !Application::getInstance()->user->isSuperAdmin()) {
            throw new ForbiddenException("You don't have permission to work on system objects");
        }

        if(Application::getInstance()->user->isAdmin()) {
            return;
        }
        
        if($this->isNotPublic()){
            if(
                ($this->ctypeObj->id == "ctypes" || $this->ctypeObj->id == "users" || $this->ctypeObj->id == "roles") && 
                $this->recordData->is_system_object == true && Application::getInstance()->user->isSuperAdmin() != true){
                throw new ForbiddenException("You don't have permission to this Content-Type");
            }

            $this->app->user->checkCtypeExtraPermission($this->ctypeObj, $this->recordData, "allow_edit");

        }

        if($this->ctypePermissionObj->allow_edit_only_your_own_records == true && $this->recordData->created_user_id != Application::getInstance()->user->getId()){
            throw new ForbiddenException();
        }
    

    }




    private function getDataObject() : string {

        $result = [];

        $result['id'] = $this->id;
        $result['SaveButtonLoading'] = false;
        $result['isAddMode'] = $this->isAddMode();
        $result['isEditMode'] = $this->isEditMode();
        
        
        $result = array_merge($result, $this->UpdateStatusComponent->getDataObject());
        $result = array_merge($result, $this->justificationForEditComponent->getDataObject());
        $result = array_merge($result, $this->uiGeneratorComponent->getDataObject());
        $result = array_merge($result, $this->imagePreviewComponent->getDataObject());


        $result['fieldValidationErrors'] = [];
        $result['goToNew'] = false;
        $result['drag'] = false;
        

        
        $result['ind'] = 0;

        
        
        return json_encode($result);
    }


    
    
    private function initializeDatePickerScript($field, $fieldFullName, $fieldDataPath){

        if($field->appearance_id == "4_separated"){
            ?>

            var dte_<?= $fieldFullName ?> = document.getElementById('<?= $fieldFullName ?>');
            if(dte_<?= $fieldFullName ?> != null){
                dte_<?= $fieldFullName ?>.onchange = function() {
                    self.<?= $field->name ?> = dte_<?= $fieldFullName ?>.value;
                }
            }
            
            $("#<?= $fieldFullName ?>").dateDropdowns({
                submitFormat: "dd/mm/yyyy",
                required: <?= $field->is_required == true ? "true" : "false" ?>,
                dropdownClass: "form-control",
                yearLabel: '<?= t("Year") ?>',
                monthLabel: '<?= t("Month") ?>',
                dayLabel: '<?= t("Day") ?>',
                displayFormat: 'ymd',
                <?php if($this->isEditMode() == true) { ?>
                    defaultDate: moment(self.<?= $fieldFullName ?>,"YYYY-M-D").format('YYYY-MM-DD'),
                <?php } ?>

            });
            <?php
        } else {
            ?>
            $("#<?= $fieldFullName ?>").datepicker({
                dateFormat: "dd/mm/yy",
                onSelect:function(selectedDate, datePicker) {            
                    self.<?= $fieldDataPath ?>  = selectedDate;
                },

            });
            <?php
        }
        
    }

    private function initializeDatePicker(){
        
        $result = "";

        foreach($this->fields as $field){
        
            if($field->field_type_id == "field_collection") {
                    
                foreach($this->getFieldByType($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), "date") as $fc){

                    if($fc->is_hidden != true && $fc->is_system_field != true){
                        
                        $result .= $this->initializeDatePickerScript($fc, 'current_' . $field->name . '_' . $fc->name, 'current_' . $field->name . '.' . $fc->name);
                        
                    }
                
                }

            } else if($field->field_type_id == "date" && $field->is_hidden != true && $field->is_system_field != true){
                
                $result .= $this->initializeDatePickerScript($field, $field->name, $field->name);

            }
        }

        return $result;
    }

   

    private function generateValidateMethod(){
        
        
        ob_start();

        $val_list = "";
        foreach($this->fields as $field){
            
            if($field->is_hidden == true)
                continue;

            if(!empty($field->validation_condition)){
                //$script_generated .= self::generateFieldValidationCondition($field);
                $val_list .= "this.val_cond_$field->name();\n";
            }
            
            if($field->field_type_id == "relation" && $field->appearance_id == "2_select2") {
                $val_list .= "if(this.$field->name" . "_has_error_c) { return false; };\n";
            }
            
        }

        ?>

        validate: function(){
            <?= $val_list ?>
            return true;
        },

        <?php

        foreach($this->fields as $field){
            
            if($field->is_hidden == true)
                continue;

            if($field->field_type_id == "field_collection"){

                $val_list = "";
                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
                    
                    if(!empty($fc->validation_condition)){
                        if($field->is_hidden == true)
                            continue;
                        //$script_generated .= self::generateFieldValidationCondition($fc, $field->name);
                        $val_list .= "this.val_cond_current_$field->name" . "_$fc->name();\n";
                    }

                    if($fc->field_type_id == "relation" && $fc->appearance_id == "2_select2") {
                        $val_list .= "if(this.current_$field->name" . "_$fc->name" . "_has_error_c) { return false; };\n";
                    }
                }
   
                ?>

                validate_<?= $field->name ?>: function(){
                    <?= $val_list ?>
                    return true;
                },

                <?php

            } 

        }

        return ob_get_clean();
    }

    private function generateFieldValidationCondition(){
        
        $result = "";
        foreach($this->fields as $field){
            
            if($field->is_hidden == true)
                continue;

            if($field->field_type_id == "field_collection"){

                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
                    if(!empty($fc->validation_condition)){
                        if($field->is_hidden == true)
                            continue;
                        $result .= self::generateFieldValidationConditionScript($fc, $field->name);
                    }
                }
                if(!empty($field->validation_condition)){
                    $result .= self::generateFieldValidationConditionScript($field);
                }

            } else {

                if(!empty($field->validation_condition)){
                    $result .= self::generateFieldValidationConditionScript($field);
                }
                
            }
        }

        return $result;
    }

    private static function generateFieldValidationConditionScript($field, $fc_name = ""){
        
        $prefix = "";

        if(!empty($fc_name))
            $prefix = "current_$fc_name";
        
        $validation_condition = $field->validation_condition;
        
        if(empty($validation_condition))
            return "";

        $result = "";

        if(!empty($validation_condition)){
            
            $validation_condition = _str_replace("\n"," ",$validation_condition);
            $validation_condition = _str_replace("selected(self","selected('$prefix'",$validation_condition);
            $validation_condition = _str_replace("selected(","this.val_selected(",$validation_condition);
            $validation_condition = _str_replace(" and"," && ",$validation_condition);
            $validation_condition = _str_replace(" or"," || ",$validation_condition);
            $validation_condition = _str_replace(")and",")&&",$validation_condition);
            $validation_condition = _str_replace(")or",")||",$validation_condition);


            $result .= "val_cond_" . (isset($prefix) && _strlen($prefix) > 0 ? $prefix . "_" : "") . "$field->name: function(){
                if(($validation_condition) == true){
                    this." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name" . "_has_error = false;
                    ";
                    if($field->field_type_id != "field_collection"){
                        $result .= "
                        var fld = this.\$refs." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name;
                        if(fld != undefined){
                            fld.setCustomValidity('');
                        }
                    ";
                    }

                    $result .= "
                    
                }
                else {
                    this." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name" . "_has_error = true;
                    ";
                    if($field->field_type_id != "field_collection"){
                        $result .= "
                    var fld = this.\$refs." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name;
                    if(fld != undefined){
                        fld.setCustomValidity('has_error');
                    }
                    ";
                    }
                    $result .= "

                }
            },
            
            ";

            return $result;
            
        }

        
    }



    private function generateComboboxHandleChangeMethod(){

        $result = "";

        foreach($this->fields as $field){

            if($field->field_type_id == "relation" && $field->data_source_value_column_is_text != true && $field->select2_async != true){
                $result .= "
                handleChange_$field->name(e){
                    
                    ";

                    // if($field->is_multi == true && $field->appearance_id == "2_list") {
                    //     $result .= "
                    //     if(e.target.value == 0) {
                    //         alert('toggle clicked');
                    //     }
                    //     ";
                    // }
            
                    foreach($this->fields as $fieldx){
                        if($fieldx->field_type_id == "relation" && !empty($fieldx->data_source_filter_by_field_name) && $fieldx->select2_async != true){
                            $found = false;
                            $value = "";
                            
                            foreach(_explode(",",$fieldx->data_source_filter_by_field_name) as $itm){
                                
                                
                                if(_strpos($itm, ".") !== false){
                                    $arr = _explode(".",$itm);
                                    $new_value =  $arr[sizeof($arr) - 1];
            
                                    if($new_value == $field->name)
                                        $found = true;
                                    
                                }
            
                                if($itm == $field->name)
                                    $found = true;
            
                                if(!empty($value))
                                    $value .= ",";
            
                                
                                $value .= "this.$itm";

                            }
            
                            if($found == true){
                                $value = _str_replace("self.", "",$value);
                                $result .= "this.reload_$fieldx->name($value);\n";
                            }
            
                        }
                    }
            
                    $result .= "
                },
                ";
            }
        }


        foreach($this->fields as $field){


            if($field->field_type_id == "field_collection"){
        
                $result .= "
                onEnd_fc_$field->name: function(){
                    drag = false;
                    
                    var i = 0;
                    this.$field->name.forEach((itm) => {
                        itm.sort = i++;
                    });
                    
                },
                ";
        
                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc) {
        
                    
                    if($fc->field_type_id == "relation" && $fc->data_source_value_column_is_text != true){
                        $result .= "
                        handleChange_current_" . $field->name . "_" . $fc->name . "(e){
                            
                            ";
                            
                            if($fc->appearance_id == "2_list"){
                                $result .= "
                                
                                var result = '';
                                if(e && e.target){
                                    e.target.value;
                                }
                                
                                this.current_" . $field->name . "." . $fc->name . "_display  = result;
                                ";
        
                                    foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fieldx){
                                        if($fieldx->field_type_id == "relation" && isset($fieldx->data_source_filter_by_field_name) && _strlen($fieldx->data_source_filter_by_field_name) > 0){
                                            $found = false;
                                            $final_value = "";
                                            
                                            foreach(_explode(",",$fieldx->data_source_filter_by_field_name) as $itm){
                                                
                                                if(_strpos($itm, ".") !== false){
                                                    $arr = _explode(".",$itm);
                                                    $new_value =  $arr[sizeof($arr) - 1];
        
                                                    if($new_value == $fc->name)
                                                        $found = true;
                                                    
                                                }
        
                                                if(!empty($final_value))
                                                    $final_value .= ",";
        
                                                
                                                $final_value .= "this.$itm";
                                                
        
        
                                            }
        
                                            if($found == true){
                                                $final_value = _str_replace("self.", "current_$field->name",$final_value);
                                                $result .= "this.reload_current_$field->name" . "_$fieldx->name($final_value);";
                                            }
        
                                        }
                                    }
        
        
                            } else {
                            $result .= "
                            
                            var result = '';
                                
                            if(e && e.target){
                                var cnt = e.target?.selectedOptions.length;
                                
                                
                                
                                for(var i =0; i < cnt; i++){
                                    if(i > 0)
                                        result += ', ';
                                    result += e.target.selectedOptions[i].text;
                                }
                            }
                                
                            this.current_" . $field->name . "." . $fc->name . "_display  = result;
                            ";
        
                                foreach($this->getFieldByType($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), "relation") as $fieldx){
                                    if(!empty($fieldx->data_source_filter_by_field_name) ){

                                        $found = false;
                                        $final_value = "";
                                        
                                        foreach(_explode(",",$fieldx->data_source_filter_by_field_name) as $itm){
                                            
                                            if(_strpos($itm, ".") !== false){
                                                $arr = _explode(".",$itm);
                                                $new_value =  $arr[sizeof($arr) - 1];
        
                                                if($new_value == $fc->name)
                                                    $found = true;
                                                
                                            }
        
                                            if(!empty($final_value))
                                                $final_value .= ",";
        
                                            
                                            $final_value .= "this.$itm";
                                            
        
        
                                        }
        
                                        if($found == true){
                                            $final_value = _str_replace("self.", "current_$field->name.",$final_value);
                                            $result .= "this.reload_current_$field->name" . "_$fieldx->name($final_value);";
                                        }
        
                                    }
                                }
        
        
                            }
        
                            $result .= "
                        },
                        ";
                    }
                }
            }
        }
        

        return $result;
    }

    

    private function generateFieldButtonMethods(){
        
        $result = "";

        foreach($this->getFieldByType($this->fields, "button") as $field) {

            if(($field->field_type_id == "button")){
                $result .= "
                run$field->name(){
                    ";

                    if(isset($this->id)){
                        $method = $field->method;
                        $method = _str_replace("[CTYPEID]",$this->ctypeObj->id, $method);
                        $method = _str_replace("[ID]",$this->id,$method);
                        
                        $result .= "
                        window.open('/$method', '_blank');

                        ";
                    } else {
                        $result .= "
                        alert('Id not found');
                    ";
                    }
                    $result.= "
                },";

            }
        }

        return $result;

    }

    private function generateFieldAttachmentMethods(){
        
        $result = "";

        foreach($this->getFieldByType($this->fields, "media") as $field){
        
            $fieldFullName = $field->ctype_id . "_" . $field->name;
            $fieldFullName2 = $field->name;
            $fieldDataPath = $field->name;
            $fieldDataPath2 = 'var_' . $field->name;
            $result .= $this->generateFieldAttachmentMethodsScript($field, $field->name, $fieldFullName, $fieldFullName2, $fieldDataPath, $fieldDataPath2);
            
        }

        foreach($this->getFieldByType($this->fields, "field_collection") as $field){
        
            foreach($this->getFieldByType($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), "media") as $fc){
            
                $fieldFullName = $fc->ctype_id . "_" . $fc->name;
                $fieldFullName2 = 'current_' . $field->name . '_' . $fc->name;
                $fieldDataPath = 'current_' . $field->name . '.' . $fc->name;
                $fieldDataPath2 = 'current_' . $field->name . '.var_' . $fc->name;
                $result .= $this->generateFieldAttachmentMethodsScript($fc, $fc->name, $fieldFullName, $fieldFullName2, $fieldDataPath, $fieldDataPath2);

            }
            
        }

        return $result;

    }

    private function generateFieldAttachmentMethodsScript($field, $fieldName, $fieldFullName, $fieldFullName2 = null, $fieldDataPath = null, $fieldDataPath2 = null){
        
        ob_start();

        ?>
        prepareToUpload_<?= $fieldFullName ?>(){
            let self = this;
            self.<?= $fieldDataPath2 ?> = this.$refs.<?= $fieldFullName2 ?>.files;
            self.upload_<?= $fieldFullName ?>('<?= $field->file_type_id ?>');
        },
        remove_<?= $fieldFullName ?>(name){

            if(!confirm('Estas seguro que quieres eliminar?'))
                return;

            let self = this;
            self.<?= $fieldDataPath ?> = self.<?= $fieldDataPath ?>.filter((e)=>e.name !== name );
            $("#<?= $fieldDataPath ?>").val('');
            self.validate();
        },
        
        upload_<?= $fieldFullName ?>(fileType){
            
            let self = this
            let formData = new FormData();
            this.uploading_<?= $fieldFullName2 ?> = true;
            for( var i = 0; i < this.<?= $fieldDataPath2 ?>.length; i++ ){
                let file = this.<?= $fieldDataPath2 ?>[i];
                formData.append('<?= $fieldName ?>[' + i + ']', file);
            }

            axios.post( '/fileupload/upload/<?= $this->ctypeObj->id ?>?file_type=' + fileType + '&field_name=<?= $fieldName ?>&response_format=json',
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
            ).then(function(response){
                if(response.data.status == 'success'){
                    
                    response.data.images.forEach(function(itm){
                        self.<?= $fieldDataPath ?>.push(itm);
                    });
                    self.<?= $fieldFullName2 ?>_is_required = false;
                    self.uploading_<?= $fieldFullName2 ?> = false;
                    self.validate();
                } else {
                    $.toast({
                        heading: 'Error',
                        text: response.data.message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });                            }
                    self.uploading_<?= $fieldFullName2 ?> = false;
                    $("#<?= $fieldFullName2 ?>").val('');
            }).catch(function(error){

                if(error.response != undefined && error.response.data.status == 'failed'){
                    document.getElementById('error-modal-body').innerHTML = '<p>' + error.response.data.message + '</p>';
                } else {
                    document.getElementById('error-modal-body').innerHTML = '<p>' + error + '</p>';
                }
                self.uploading_<?= $fieldFullName2 ?> = false;
                self.showErrorDialog();
                $("#<?= $fieldName ?>").val('');
            });
        },
    
        <?php

        return ob_get_clean();

    }

    

    



    private function generateDeleteRecordMethod() : ?string{
        
        if($this->isAddMode()) {
            return null;
        }

        ob_start(); ?>

        deleteRecord(){
            window.location.href = '/<?= $this->ctypeObj->id ?>/delete/<?= $this->id ?>';
        },

        <?php
        return ob_get_clean();

    }

    private function generateAddRecordMethod() : ?string{
        
        if($this->isAddMode()) {
            return null;
        }
        
        ob_start(); ?>

        addRecord(){
            window.location.href = '/<?= $this->ctypeObj->id ?>/add';
        },

        <?php
        return ob_get_clean();
        
    }

    

    
    private function generatePostDataMethod(){
        
        ob_start();

        ?>

        async postData(goToNew){

            <?php foreach($this->fields as $field){
                    
                $hasCustomComponent = false;

                if($field->is_hidden_updated) {
                    continue; 
                }

                if($field->field_type_id == "field_collection") {
                    if($field->use_parent_permissions == true){
                        $FcPermissionObj = $this->ctypePermissionObj;
                    } else {
                        $FcPermissionObj = Application::getInstance()->user->getCtypePermission($field->data_source_id);
                    }

                    
                    if($this->isSurvey() || $FcPermissionObj->allow_add || $FcPermissionObj->allow_edit){

                        $componentName = to_snake_case($field->name) . '-list-component';
                        
                        $hasCustomComponent = false;

                        if(_strpos($this->ctypeObj->extends,'id="tpl-' . $componentName . '"') !== false){
                            $hasCustomComponent = true;
                        } else {
                            $componentName = to_snake_case($field->name) . '-component';
                        
                            if(_strpos($this->ctypeObj->extends,'id="tpl-' . $componentName . '"') !== false){
                                $hasCustomComponent = true;
                            }
                        }
                        
                    }
                } else {

                    $componentName = to_snake_case($field->name) . '-component';
                        
                    if(_strpos($this->ctypeObj->extends,'id="tpl-' . $componentName . '"') !== false){
                        $hasCustomComponent = true;
                    }
                    
                }

                if($hasCustomComponent): ?>
                    if(this.$refs.<?= $field->name ?>Component && typeof this.$refs.<?= $field->name ?>Component.beforeSave == "function") {
                        if(this.$refs.<?= $field->name ?>Component.beforeSave() == false) {
                            this.<?= $field->name ?>_modal_btn_active = true;
                            return;
                        }
                    };
                <?php endif; 
            }

            ?>

            this.$refs.form.classList.add('was-validated');
            this.form_validated = true;

            if (!this.validate() || !this.$refs.form.checkValidity()) {
                $.toast({
                    heading: 'Error',
                    text: 'Por favor complete los campos correctamente',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
                
                return;
            }
            
            
            
            <?php if($this->isEditMode()){ ?>
                
                this.goToNew = goToNew;
                
                var myModal = new bootstrap.Modal(document.getElementById('editJustificationModal'), {})
                myModal.show();
                
                setTimeout(function () { 
                    document.getElementById("editJustification").focus();
                }, 500);

            <?php } else { ?>
                
                await this.postDataAction(goToNew);
                
            <?php } ?>

        },

        <?php

        return ob_get_clean();

    }


    private function generateHasErrorComputedScript($field, $fieldFullName, $fieldDataPath) {

        $value = "false";
        if($field->is_required){                
            $value = "this.$fieldDataPath == null || this.$fieldDataPath.length == 0 ? true : false";
        } elseif (_strlen($field->required_condition) > 0) {
            $value = "this.computed_req_cond_$fieldFullName && (this.$fieldDataPath == null || this.$fieldDataPath.length < 1 || this.$fieldDataPath == undefined)";
        }
         
        return "{$fieldFullName}_has_error_c: function(){ return $value },\n";
        
    }


    private function generateHasErrorComputed() {

        $result = "";

        foreach($this->fields as $field){

            if ($field->field_type_id == "relation"){
                
                $result .= $this->generateHasErrorComputedScript($field, $field->name, $field->name);
            
            } else if($field->field_type_id == "field_collection"){

                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){

                    if($fc->field_type_id == "relation"){

                        $result .= $this->generateHasErrorComputedScript($fc, "current_" . $field->name . "_" . $fc->name, "current_$field->name.$fc->name");

                    }

                }
            }
        }

        return $result;

    }


    private function generateFieldDependancyComputedScript($field, $fc_name = "") {

        $prefix = "";

        if(!empty($fc_name))
            $prefix = "current_$fc_name";
        
        $dependencies = $field->dependencies;
        $required_condition = $field->required_condition;
        
        if(empty($dependencies))
            return "";

        $return_value = "";

        if(!empty($dependencies)){
            
            $required_condition = _str_replace("\$dependencies",$field->dependencies, $required_condition);

            $dependencies = _str_replace("\n"," ", $dependencies);
            $dependencies = _str_replace("selected(self","selected('$prefix'",$dependencies);
            $dependencies = _str_replace("selected(","this.dep_selected(",$dependencies);
            $dependencies = _str_replace(" and"," && ",$dependencies);
            $dependencies = _str_replace(" or"," || ",$dependencies);
            $dependencies = _str_replace(")and",")&&",$dependencies);
            $dependencies = _str_replace(")or",")||",$dependencies);

            
            $required_condition = _str_replace("\n"," ", $required_condition);
            $required_condition = _str_replace("selected(self","selected('$prefix'",$required_condition);
            $required_condition = _str_replace("selected(","this.dep_selected(",$required_condition);
            $required_condition = _str_replace(" and"," && ",$required_condition);
            $required_condition = _str_replace(" or"," || ",$required_condition);
            $required_condition = _str_replace(")and",")&&",$required_condition);
            $required_condition = _str_replace(")or",")||",$required_condition);

            $extra_cond_required = "";
            if($field->field_type_id == "media"){
                $extra_cond_required = " && (this." . (!empty($prefix) ? $prefix . "." : "") . "$field->name == null || this." . (!empty($prefix) ? $prefix . "." : "") . "$field->name.length < 1 || this." . (!empty($prefix) ? $prefix . "." : "") . "$field->name == undefined) ";
            }

            $return_value .= "computed_" . (isset($prefix) && _strlen($prefix) > 0 ? $prefix . "_" : "") . "$field->name: function(){
                let self = this;
                if(($dependencies) == true){
                    
                    ";
                    if(!empty($required_condition)){
                        $return_value .= "
                        if(($required_condition $extra_cond_required) == true){
                            this." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name" . "_is_required = true;
                        } else {
                            this." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name" . "_is_required = false;
                        }
                        ";
                    }
                    
                    $return_value .= "
                    
                    setTimeout(function () { 
                        var elem = $('#div_" . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name');
                        elem.removeClass('highlight');
                        elem.addClass('highlight');
                        
                        ";

                    if($field->field_type_id == "date"){// Date
                        
                        if($field->appearance_id == "4_separated"){
                            $return_value .= "
                            var dte_" . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name = document.getElementById('" . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name');
                            if(dte_" . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name != null){
                                dte_" . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name.onchange = function() {
                                    self." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name = dte_" . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name.value;
                                }
                            }
                            
                            $(\"#" . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name\").dateDropdowns({
                                submitFormat: \"dd/mm/yyyy\",
                                required: " . ($field->is_required == true ? "true" : "false") . ",
                                dropdownClass: \"form-control\",
                                yearLabel: '" . t("Year") . "',
                                monthLabel: '" . t("Month") . "',
                                dayLabel: '" . t("Day") . "',
                                displayFormat: 'ymd',
                                " . ($this->isEditMode() ? "defaultDate: moment(self." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name,\"YYYY-M-D\").format('YYYY-MM-DD')," : "") . "
    
                            });
                            ";
                        } else {
                            
                        $return_value .= "
                        
                            $(\"#" . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name\").datepicker({
                                dateFormat: \"dd/mm/yy\",
                                onSelect:function(selectedDate, datePicker) {
                                self." . (!empty($prefix) ? "$prefix" . "." : "" ) . "$field->name = selectedDate;
                            },

                        });
                        ";
                        }

                    }

                    
                    if($field->field_type_id == "decimal" && $field->appearance_id == "7_map"){
                        
                        if(_strtolower(substr($field->name, _strlen($field->name) -3,_strlen($field->name))) == "lat"){
                            $base_name = _strtolower(substr($field->name, 0,_strlen($field->name) - 4));

                            $return_value .= "
                            
                        let nothing = self." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$base_name" . "_InitMap_run();
                            ";

                        }
    
                    }
                    

                    $return_value .= "
                    }, 0);

                    ";


                    $return_value .= "
        
                    
                    return true;
                }
                else {
                    
                    ";
                    if(!empty($required_condition)){
                        $return_value .= "
                        if(($required_condition) != true){
                            this." . (!empty($prefix) ? "$prefix" . "_" : "" ) . "$field->name" . "_is_required = false;
                        }        
                        ";
                    }
                    
                    $return_value .= "
                    return false;
                }
            },
            ";

            return $return_value;
            
        }

        
    }


    private function generateFieldDependancyComputed(){
        
        $result = "";
        
        foreach($this->fields as $field){
            if($field->field_type_id == "field_collection"){
                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
                    $result .= $this->generateFieldDependancyComputedScript($fc, $field->name);
                }
                $result .= $this->generateFieldDependancyComputedScript($field);
            } else {
                
                $result .= $this->generateFieldDependancyComputedScript($field);
                
            }
        }

        return $result;
    }


    private function generateFieldRequiredConditionComputedScript($field, $prefix = ""){
        
        $required_condition = $field->required_condition;
        
        if(empty($required_condition))
            return "";

        $return_value = "";

        if(!empty($required_condition)){
            
            $required_condition = _str_replace("\$dependencies",$field->dependencies, $required_condition);

            $required_condition = _str_replace("\n"," ",$required_condition);
            $required_condition = _str_replace("selected(self","selected('$prefix'",$required_condition);
            $required_condition = _str_replace("selected(","this.dep_selected(",$required_condition);
            $required_condition = _str_replace(" and"," && ",$required_condition);
            $required_condition = _str_replace(" or"," || ",$required_condition);
            $required_condition = _str_replace(")and",")&&",$required_condition);
            $required_condition = _str_replace(")or",")||",$required_condition);

            $extra_cond_required = "";
            if($field->field_type_id == "media"){
                $extra_cond_required = " && (this." . (!empty($prefix) ? $prefix . "." : "") . "$field->name == null || this." . (!empty($prefix) ? $prefix . "." : "") . "$field->name.length < 1 || this." . (!empty($prefix) ? $prefix . "." : "") . "$field->name == undefined) ";
            }

            if(_strlen($required_condition) > 0) {
                $required_condition = "({$required_condition})";
            }
            $return_value .= "computed_req_cond_" . (isset($prefix) && _strlen($prefix) > 0 ? $prefix . "_" : "") . "$field->name: function(){
                let self = this;
                if(($required_condition $extra_cond_required) == true){
                    return true;
                } else {
                    return false;
                }
            },
            ";

            return $return_value;
            
        }

    }


    private function generateFieldRequiredConditionComputed(){

        $result = "";

        foreach($this->fields as $field){
            
            if($field->field_type_id == "field_collection"){

                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
                    $result .= $this->generateFieldRequiredConditionComputedScript($fc, "current_" . $field->name);
                }

                $result .= $this->generateFieldRequiredConditionComputedScript($field);
                
            } else {
                
                $result .= $this->generateFieldRequiredConditionComputedScript($field);
                
            }
        }

        return $result;

    }


    private function generateFieldReadOnlyConditionComputedScript($field, $fc_name = ""){
        
        $prefix = "";

        if(!empty($fc_name))
            $prefix = "current_$fc_name";
        
        $read_only_condition = $field->read_only_condition;
        
        if(empty($read_only_condition))
            return "";

        $return_value = "";

        if(!empty($read_only_condition)){
            
            $read_only_condition = _str_replace("\$dependencies",$field->dependencies, $read_only_condition);

            $read_only_condition = _str_replace("\n"," ",$read_only_condition);
            $read_only_condition = _str_replace("selected(self","selected('$prefix'",$read_only_condition);
            $read_only_condition = _str_replace("selected(","this.dep_selected(",$read_only_condition);
            $read_only_condition = _str_replace(" and"," && ",$read_only_condition);
            $read_only_condition = _str_replace(" or"," || ",$read_only_condition);
            $read_only_condition = _str_replace(")and",")&&",$read_only_condition);
            $read_only_condition = _str_replace(")or",")||",$read_only_condition);

            $extra_cond_required = "";
            if(isset($prefix) && _strlen($prefix) > 0){
                if($field->is_read_only_updated_edit == true){
                    $extra_cond_required = "this.$prefix.sys_is_edit_mode == 1 || ";
                }
                if ($field->is_read_only_updated_add == true){
                    $extra_cond_required = "this.$prefix.sys_is_edit_mode == 0 || ";
                }
            } else {
                if($this->isEditMode() && $field->is_read_only_updated_edit == true){
                    $extra_cond_required = " 1 == 1 || ";
                } else if ($this->isAddMode() && $field->is_read_only_updated_add == true){
                    $extra_cond_required = " 1 == 1 || ";
                }
            }
            

            $return_value .= "computed_read_only_cond_" . (isset($prefix) && _strlen($prefix) > 0 ? $prefix . "_" : "") . "$field->name: function(){
                let self = this;
                if($extra_cond_required($read_only_condition) == true){
                    return true;
                } else {
                    return false;
                }
            },
            
            ";

            return $return_value;
            
        }

        

    }

    private function generateFieldReadOnlyConditionComputed(){

        $result = "";

        foreach($this->fields as $field){
            if($field->field_type_id == "field_collection"){
                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
                    $result .= $this->generateFieldReadOnlyConditionComputedScript($fc, $field->name);
                }
                $result .= $this->generateFieldReadOnlyConditionComputedScript($field);
            } else {
                
                $result .= $this->generateFieldReadOnlyConditionComputedScript($field);
                
            }
        }

        return $result;

    }


    


    


    private function generatePostDataActionMethod(){

        $result = "";

        $result .= "
        async postDataAction(goToNew){
            
            ";

            if(isset($this->id) && $this->ctypeObj->justification_for_edit_is_required == true){
                $result .= "

                if(this.editJustification == null || this.editJustification.length == 0){
            
                    $.toast({
                        heading: 'Error',
                        text: 'Justification is required',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    return;
                }
                ";
            }

            
            foreach($this->fields as $field){
                if($field->field_type_id == "field_collection" && $field->is_required == true ){
                    $result .= "
                    if(this.$field->name.length == 0){
                        $.toast({
                            heading: 'Error',
                            text: '$field->name is required',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        return;
                    }
                    ";
                }
            }

            foreach($this->fields as $field){
                if($field->field_type_id == "field_collection" && !empty($field->validation_condition)){
                    $result .= "
                    if(this.$field->name" . "_has_error == true){
                        $.toast({
                            heading: 'Error',
                            text: '" . (isset($field->validation_message) ? $field->validation_message : "Please enter valid data") . "',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        return;
                    }
                    ";
                }
            }

            $result .= "
            
            let i = 0;
            this.SaveButtonLoading = true;
            self = this;
            
            await axios({
                method: 'POST',
                ";
                if($this->isSurvey()){
                    $result .= "url: '/surveys/" . (isset($id) ? "edit" : "add") . "/$this->id" . "&survey_id=" . $this->surveyObj->id . "&response_format=json',";
                } else {
                    $result .= "url: '/" . $this->ctypeObj->id . "/" . ($this->isEditMode() ? "edit" : "add") . "/$this->id&response_format=json',";
                }
                    $result .= "
                data: this.getPostDataObject(),
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Csrf-Token': '" . (Application::getInstance()->csrfProtection->create((isset($id) ? "edit" : "add") . "_" . $this->ctypeObj->id)) . "',
                }
            }).then(function(response){
                
                try{
                    if(response.data.status == 'success') {
                        if(goToNew == 1){
                            window.location.href = '/" . $this->ctypeObj->id . "/add';
                        } else {
                            ";
                            if($this->isSurvey()){

                                if(!empty($this->surveyObj->post_submit_page_id)){
                                    $result .= "window.location.href = '/pages/show/" . $this->surveyObj->post_submit_page_id . "?lang=" . Application::getInstance()->user->getLangId() . "'";
                                } else if($this->surveyObj->type_id == "protected") { 
                                    $result .= "window.location.href = '/surveyManagement/list/" . $this->surveyObj->id . "?lang=" . Application::getInstance()->user->getLangId() . "'";
                                } else {
                                    $result .= "location.reload();";
                                }
                            } else {
                                if(!empty($this->ctypeObj->redirect_after_save)){
                                    $result .= "window.location.href = '" . $this->ctypeObj->redirect_after_save . "'";
                                } else {
                                    $result .= "window.location.href = '/" . $this->ctypeObj->id . "/edit/' + response.data.id;";
                                }
                            }

                            $result .= "
                        }
                        
                    } else {
                        /*$.toast({
                            heading: 'Error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        */
                        
                        document.getElementById('error-modal-body').innerHTML = '<p>' + response.data + '</p>';
                        self.showErrorDialog();
                        
                    } 
                } catch(e){
                    /*$.toast({
                        heading: 'Error',
                        text: 'Something went wrong',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });*/

                    document.getElementById('error-modal-body').innerHTML = 'Something went wrong, please contact system administrator';
                    self.showErrorDialog();

                }
                
                /* self.SaveButtonLoading = false; */ 
            }).catch(function(error){
                
                if(error.response != undefined && error.response.data.status == 'failed') {
                    /*$.toast({
                        heading: 'Error',
                        text: 'Something went wrong',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    */
                    
                    document.getElementById('error-modal-body').innerHTML = '<p>' + error.response.data.message + '</p>';
                    self.showErrorDialog();
                    
                } else {
                    document.getElementById('error-modal-body').innerHTML = '<p>' + error.message;
                    self.showErrorDialog();
                }

                self.SaveButtonLoading = false;
            });
        },
        ";

        return $result;
        
    }


    private function generatePostDataObject(){
        
        $result = "";
            
        
        $result .= "
        getPostDataObject(){
                
                ";


                foreach($this->fields as $field){
                    if($field->field_type_id == "field_collection" ){
                        $result .= " this.var_" . $field->name . " = `[`;
                                var i = 0;
                                this.$field->name.forEach(function(itm){
                                    
                                    if(i++ > 0)
                                        this.var_" . $field->name . " += `,`;    
                                    this.var_" . $field->name . " += `" . $this->generatePostDataObject2("itm",$field->data_source_table_name, $field->data_source_id, $this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id));
                                    $result .= "`;

                                }.bind(this));
                                this.var_" . $field->name . " += `]`;

                        ";

                    }
                }

                $result .= "
                
                
                ";

                foreach($this->fields as $field){
                    if($field->field_type_id == "field_collection" ){
                        $result .= " 
                        
                        if (this.var_" . $field->name . ".substring(this.var_" . $field->name . ".trim().length-1) == ',')
                        {
                            this.var_" . $field->name . " = this.var_" . $field->name . ".trim().substring(0, this.var_" . $field->name . ".length-1);
                        }
                        
                        ";
                        
                    } 
                }

                $result .= " 
                
                let data = {";
            
                    $result .= "
                        sett_ctype_id: '" . $this->ctypeObj->id . "',
                        sett_is_update: this.sett_is_update,
                        id:this.id,
                        ";
                        foreach($this->fields as $field){
                            if($field->name != "id" && $field->name != "parent_id" && $field->is_system_field == true){
                                continue;
                            }
                            if($field->field_type_id == "text"){
                                $result .= "$field->name: (this.$field->name ?? '')
                                    .replace(/'/g, \"\\'\")
                                    .replace(/\"/g, \"\\\"\"),\n";
                            } else if ($field->field_type_id == "relation" && $field->is_multi != true){
                                if($field->appearance_id == "2_select2"){
                                    $result .= "$field->name: this.$field->name == null ? null : this.$field->name.id,\n";
                                } else {
                                    $result .= "$field->name: this.$field->name,\n";
                                }
                            
                            } else if($field->field_type_id != "field_collection" && $field->is_multi == false && $field->field_type_id != "media"){
                                $result .= "$field->name: (this.$field->name ?? null),\n";
                            } else if($field->field_type_id == "media" && $field->is_multi != true){
                                $result .= $field->name . "_name: (this.$field->name == null || this.$field->name[0] == null ? null : this.$field->name[0]['name']),\n";
                                $result .= $field->name . "_original_name: (this.$field->name == null || this.$field->name[0] == null ? null : this.$field->name[0]['original_name']),\n";
                                $result .= $field->name . "_type: (this.$field->name == null || this.$field->name[0] == null ? null : this.$field->name[0]['type']),\n";
                                $result .= $field->name . "_size: (this.$field->name == null || this.$field->name[0] == null ? null : this.$field->name[0]['size']),\n";
                                $result .= $field->name . "_extension: (this.$field->name == null || this.$field->name[0] == null ? null : this.$field->name[0]['extension']),\n";
                            }
                        }
                        
                        foreach($this->fields as $field){
                            
                            if($field->field_type_id == "relation" and $field->is_multi == true){
                                $result .= "
                                    $field->name: ";
                                    if($field->appearance_id == "2_select2"){
                                        $result .= "(this.$field->name == null ? null : this.$field->name.map(a => a.id)),";
                                    } else {
                                        $result .= "this.$field->name,";
                                    }
            
                            } else if($field->field_type_id == "field_collection" ){
                                $result .= "
                                $field->name: JSON.parse(this.var_" . $field->name . "),
                                ";
            
                            } else if($field->field_type_id == "media" && $field->is_multi == true ){
                                $result .= "
                                $field->name: this.$field->name,
                                ";
                            }
                        }
                    
                    $result .= "}
                    
                let formData = new FormData();
                formData.append('justification', this.editJustification);
                formData.append('token', \"" . ($this->recordData != null && isset($this->recordData) && isset($this->recordData->token) ? $this->recordData->token : null) . "\");\n\t\t\t
                formData.append('data', JSON.stringify(data));
                
                return formData;
                

            },
            ";

        return $result;

    }


    
    private static function generatePostDataObject2($prefix, $main_table_name, $main_table_id, $fields){
        $result = "{";
        
            $i = 0;
            foreach($fields as $field){
                if($field->name != "id" && $field->name != "parent_id" && $field->is_system_field == true){
                    continue;
                }
                
                if($field->field_type_id == "note"){
                    continue;
                }

                if($field->field_type_id == "relation" and $field->is_multi == true){
                    if($i++ > 0){
                        $result .= ",";
                    }
                    $result .= "\\\"$field->name\\\": ";
                        if($field->appearance_id == "2_select2"){
                            $result .= "[\\\"` + ($prefix.$field->name == null ? null : $prefix.$field->name.map(a => a.id)) + `\\\"]";
                        } else {
                            $result .= "[\\\"` + ($prefix.$field->name == null ? null : $prefix.$field->name.join('\',\'')) + `\\\"]";
                        }
                        
                } else if($field->field_type_id == "media" && $field->is_multi == true ){
                    if($i++ > 0){
                        $result .= ",";
                    }
                    $result .= "\\\"$field->name\\\": ` + JSON.stringify($prefix.$field->name) + `
                    ";

                }

                if($field->field_type_id == "media" && $field->is_multi != true ) {
                    if($i++ > 0){
                        $result .= ",";
                    }
                    $result .= "\\\"" . $field->name . "_name\\\": \\\"` + ($prefix.$field->name == null || $prefix.$field->name[0] == null ? null : $prefix.$field->name[0]['name']) + `\\\"\n";
                    $result .= ",\\\"" . $field->name . "_original_name\\\": \\\"` + ($prefix.$field->name == null || $prefix.$field->name[0] == null ? null : $prefix.$field->name[0]['original_name']) + `\\\"\n";
                    $result .= ",\\\"" . $field->name . "_type\\\": \\\"` + ($prefix.$field->name == null || $prefix.$field->name[0] == null ? null : $prefix.$field->name[0]['type']) + `\\\"\n";
                    $result .= ",\\\"" . $field->name . "_size\\\": \\\"` + ($prefix.$field->name == null || $prefix.$field->name[0] == null ? null : $prefix.$field->name[0]['size']) + `\\\"\n";
                    $result .= ",\\\"" . $field->name . "_extension\\\": \\\"` + ($prefix.$field->name == null || $prefix.$field->name[0] == null ? null : $prefix.$field->name[0]['extension']) + `\\\"\n";
                } else if ($field->is_multi != true) {
                    if($i++ > 0){
                        $result .= ",";
                    }
                    if($field->field_type_id == "text"){
                        $result .= "\\\"$field->name\\\": \\\"` + ($prefix.$field->name ?? '')
                            .replace(/'/g, \"\u0027\")
                            .replace(/\\\\/g, \"\\\\\\\\\")
                            .replace(/\"/g, \"\\\u0022\")
                            .replace(/\\t/g, \"\\\\t\")
                            .replace(/\\n/g, \"\\\\n\") + `\\\"";
                    } else if($field->field_type_id == "relation" && $field->appearance_id == "2_select2") {
                        $result .= "\\\"$field->name\\\": \\\"` + ($prefix.$field->name == null ? null : $prefix.$field->name.id) + `\\\"";
                    } else {
                        $result .= "\\\"$field->name\\\": ` + ($prefix.$field->name == null || $prefix.$field->name == `null` ? null : `\\\"` + $prefix.$field->name + `\\\"`) + `";
                    }

                }
        
            
            }
        
        $result .= "}";

        return $result;
    }




}
