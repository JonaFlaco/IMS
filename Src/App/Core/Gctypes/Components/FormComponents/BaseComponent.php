<?php

namespace App\Core\Gctypes\Components\FormComponents;

use App\Core\Application;
use App\Core\Gctypes\CtypesHelper;

class BaseComponent {

    protected object $mainCtypeObj;
    protected object $field;
    protected ?string $prefix;
    protected bool $isSurvey = false;
    protected bool $isEditMode;

    protected $coreModel;
    
    protected CtypesHelper $ctypesHelper;

    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        
        $this->mainCtypeObj = $mainCtypeObj;
        $this->field = $field;
        $this->isEditMode = $isEditMode;
        $this->isSurvey = $isSurvey;
        $this->prefix = $prefix;
        
        
        $this->coreModel = Application::getInstance()->coreModel;
        
        $this->ctypesHelper = new CtypesHelper();

        if(_strtolower($field->str_length) == "max"){
            $field->str_length = 1000000;
        } else {
            $field->str_length = (intval($field->str_length) > 0 ? $field->str_length : TEXT_DEFAULT_LENGTH);
        }
    }

    public function dependancy() : string {
        $result = "";
        if(!empty($this->field->dependencies)){
            $result = " v-if=\"computed_" . (!empty($this->prefix) ? $this->prefix . "_" : "") . $this->field->name . "\" ";
        }

        return $result;
    }

    public function validationMessage() : string {
        
        return (empty($this->field->validation_message) ? t("Please enter a valid data") : $this->field->validation_message);
    }

    public function addDeprecatedFlag() : ?string {
        if($this->field->is_deprecated)
            return '<i class="mdi mdi-information" v-tooltip="\'Deprecated field\'"></i>';

        return null;
    }

    public function requiredSign() : ?string {
        if($this->isRequired() != "false") 
            return sprintf('<span v-if="%s" class="ml-1 text-danger">&nbsp*</span>', $this->isRequired());
        else
            return null;
        
    }

    public function validationPattern() : string {
        if($this->field->field_type_id == "number" && empty($this->field->validation_pattern))
            return 'pattern="^[0-9]+$"';
        if($this->field->field_type_id == "decimal" && empty($this->field->validation_pattern))
            return 'pattern="^[0-9.\-]+$"';
        return  (!empty($this->field->validation_pattern) ? sprintf('pattern="%s"', $this->field->validation_pattern) : "");
        
    }
    
    private function refreshingCbx() {
        if(!empty($this->field->data_source_filter_by_field_name)){
            return sprintf('loading_%s_%s', $this->field->name, $this->field->data_source_table_name); 
        }
    }
    
    public function comboboxRefreshIcon() : ?string{
        
        if(!empty($this->field->data_source_filter_by_field_name)){
            return sprintf('<span v-if="%s" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>', $this->refreshingCbx()); 
        }

        return null;
    }

    public function comboboxAtChange(){
        
        if($this->field->data_source_value_column_is_text == true || $this->field->select2_async == true) {
            return null;
        }

        if($this->field->appearance_id == "2_select2"){
            return sprintf('@input="handleChange_%s"', $this->uniqueName());
        } else {
            return sprintf('@change="handleChange_%s"', $this->uniqueName());
        }

    }


    public function isReadOnly() : string {

        $result = "";

        if(!empty($this->prefix)){
            
            if($this->field->is_read_only){
                $result = "true";
            } else {
                if($this->field->is_read_only_updated_edit == true){
                    $result = "$this->prefix.sys_is_edit_mode == 1";
                } 
                if ($this->field->is_read_only_updated_add == true){
                    if(_strlen($result) > 0)
                        $result .= " || ";
                    $result .= "$this->prefix.sys_is_edit_mode == 0";
                }
            }
        } else {
            if($this->isEditMode && $this->field->is_read_only_updated_edit == true){
                $result = "true";
            } else if (!$this->isEditMode && $this->field->is_read_only_updated_add == true){
                $result = "true";
            }
        }

        if(!empty($this->field->read_only_condition)){
            $result = "computed_read_only_cond_" . (!empty($this->prefix) ? $this->prefix . "_" : "") . $this->field->name;
        }

        if(!empty($result) && !empty($this->refreshingCbx())) {
            return $result;// . " && " . $this->refreshingCbx();
        } else if (empty($result) && !empty($this->refreshingCbx())) {
            return "false";//$this->refreshingCbx();
        } else if (!empty($result) && empty($this->refreshingCbx())) {
            return $result;
        } else {
            return "false";
        }

    }


    public function isRequired() : string {

        if($this->field->field_type_id == "media" && $this->field->is_multi && $this->field->is_required) {
            return $this->dataPath() . ".length == 0";
        } if($this->field->is_required){
            return "true";
        } else if(empty($this->field->required_condition)){
           return "false";
        } else {
            return "computed_req_cond_" . (!empty($this->prefix) ? $this->prefix . "_" : "") . $this->field->name;
        }
        
    }

    public function uniqueName() : string {
        
        if($this->field->appearance_id == "7_map" && _strtolower(substr($this->field->name, _strlen($this->field->name) -3,_strlen($this->field->name))) == "lat"){
            $base_name = _strtolower(substr($this->field->name, 0,_strlen($this->field->name) - 4));
            return (empty($this->prefix) ? "" : $this->prefix . "_") . $base_name;
        }

        return (empty($this->prefix) ? "" : $this->prefix . "_") . $this->field->name;

    }


    public function dataPath() : string {

        if($this->field->appearance_id == "7_map" && _strtolower(substr($this->field->name, _strlen($this->field->name) -3,_strlen($this->field->name))) == "lat"){
            $base_name = _strtolower(substr($this->field->name, 0,_strlen($this->field->name) - 4));
            return (empty($this->prefix) ? "" : $this->prefix . ".") . $base_name;
        }

        return (empty($this->prefix)  ? "" : $this->prefix . "." ) . $this->field->name;

    }

    public function invalidFeedback (){
        
        return sprintf('<div class="invalid-feedback"> %s </div>', $this->validationMessage());
    }

    public function descriptionPanel(){
        
        if(!empty($this->field->description)) {

            return sprintf('<div class="pb-2"> <i class="mdi mdi-information"> %s </i></div>',$this->field->description);
        }
        return null;
    }


    public function charsRemaining(){
        
        //To-Do: Commented for now
        // if($this->isSurvey !== true && $this->field->str_length != 1000000){
        //     return sprintf(' ({{(%s == null ? 0 : %s.length + \'/%s\')}} " . t("chars remaining") . ")"', $this->dataPath(), $this->dataPath(), $this->field->str_length);
        // }

        return null;
        
    }

}
