<?php

namespace App\Core\Gctypes\Components\FormComponents;

use App\Core\Application;

class ComboboxComponent extends BaseComponent {

    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        parent::__construct($mainCtypeObj, $field, $isEditMode, $isSurvey, $prefix);
    }

    public function generate() : string {

        switch($this->field->appearance_id) {
            
            case '2_list':
                return $this->List();

            case '2_select2':
                return $this->Select2();
                
            default:
                return $this->regular();

        }

        return null;
    }

    public function regular() : ?string {

        $noneOption = "";
        if($this->isRequired() != "false") {
            $noneOption = "<option v-if=\"!" . $this->isRequired() . "\" value=\"\">- " . t("None") . " -</option>";
        }
        ob_start(); ?>
        
        <div 
            class="mb-3 col-md-<?= $this->field->size ?>"
            <?= $this->dependancy() ?>  
            id="div_<?= $this->uniqueName() ?>">
    
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title ?><?= $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
                <?= $this->comboboxRefreshIcon(); ?>
            </label>
            
            <select class="form-select" 
                :disabled="<?= $this->isReadOnly() ?>"
                v-bind:class="{'is-invalid': <?= $this->uniqueName() ?>_has_error}"
                v-on:input="validate" 
                ref="<?= $this->uniqueName() ?>" 
                name="<?= $this->uniqueName() ?>"
                id="<?= $this->uniqueName() ?>"
                <?= $this->comboboxAtChange() ?>
                <?= ($this->field->is_multi == true ? 'multiple data-actions-box="true"' : '') ?>
                v-model="<?= $this->dataPath() ?>" 
                :required="<?= $this->isRequired() ?>"
                >

        <?php if(!empty($this->field->data_source_filter_by_field_name)): ?>
            <option v-for="opt in pl_<?= $this->field->name . '_' . $this->field->data_source_table_name ?>" :value="opt.id">{{opt.name}}</option>
        <?php else: ?>

            <?= $noneOption ?>

            <?php foreach($this->ctypesHelper->getComboboxOptions($this->field) as $data){ ?>
                <option value="<?= $data->id ?>"><?= e($data->name) ?></option>
            <?php } ?>

        <?php endif; ?>

            </select>
            <?= $this->invalidFeedback() ?>
            <?= $this->descriptionPanel() ?>
        </div>
        
        <?php

        return ob_get_clean();
    }

    private function generateSelectAllForList($items = []) {
        
        $result = "";
        if(empty($items)) {
            

            $result = '
            <button class="ms-3 btn-select-all btn btn-sm btn-outline-dark" 
                v-tooltip="\'Select All\'"
                @click="' . $this->dataPath() . ' = pl_' . $this->field->name . "_" . $this->field->data_source_table_name . '.map((itm) => itm.id)">
                <i class="mdi mdi-checkbox-marked-outline"></i>
            </button>
            
            <button class="btn-unselect-all btn btn-sm btn-outline-dark" 
                v-tooltip="\'Unselect All\'"
                @click="' . $this->dataPath() . ' = []">
                <i class="mdi mdi-checkbox-blank-outline"></i>
            </button>
            ';


        } else {
            $value = "";
            foreach($items as $item) {
                if(_strlen($value) > 0) $value .= ",";
                $value .= "'" . $item->id . "'";
            }

            $result = '
            <button class="ms-3 btn-select-all btn btn-sm btn-outline-dark" 
                v-tooltip="\'Select All\'"
                @click="' . $this->dataPath() . ' = [' . $value . ']">
                <i class="mdi mdi-checkbox-marked-outline"></i>
            </button>
            
            <button class="btn-unselect-all btn btn-sm btn-outline-dark" 
                v-tooltip="\'Unselect All\'"
                @click="' . $this->dataPath() . ' = []">
                <i class="mdi mdi-checkbox-blank-outline"></i>
            </button>
            ';
        }

        return $result;
    }


    public function List() : ?string {


        ob_start(); ?>

        <div 
            <?= $this->dependancy() ?> 
            class="mb-3 col-md-<?= $this->field->size ?>" 
            id="div_<?= $this->uniqueName() ?>"
            >
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title ?><?= $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
                <?= $this->comboboxRefreshIcon(); ?>
            </label>
            <div>
            <div data-simplebar style="max-height: 250px;">

        <?php if(!empty($this->field->data_source_filter_by_field_name)): 

            if($this->field->is_multi) {
                echo $this->generateSelectAllForList();
            }

            ?>

            <div v-for="opt in pl_<?= $this->field->name . "_" . $this->field->data_source_table_name ?>" class="custom-control <?= ($this->field->is_multi ? "custom-checkbox" : "custom-radio") ?> ms-3">
                <input 
                    :disabled="<?= $this->isReadOnly() ?>"
                    :required="(<?= $this->isRequired() ?>) && (!<?= $this->dataPath() ?> || <?= $this->dataPath() ?>.length == 0)"
                    <?= $this->comboboxAtChange() ?>
                    type="<?= ($this->field->is_multi ? "checkbox" : "radio") ?>" class="form-check-input"
                    name="<?= $this->uniqueName() ?>"
                    v-model="<?= $this->dataPath() ?>"
                    v-on:input="validate"
                    :id="'<?= $this->dataPath() ?>_' + opt.id"
                    :value="opt.id"
                >
                <label class="form-check-label" :for="'<?= $this->uniqueName() ?>_' + opt.id">{{opt.name}}</label>
            </div>
            
        <?php else: 
        
            $list = $this->ctypesHelper->getComboboxOptions($this->field);
            
            // if($this->field->is_multi) {
            //     $item = new \stdClass();
            //     $item->id = 0;
            //     $item->name = "Toggle Selection";
            //     array_unshift($list, $item);                
            // }
            
            if($this->field->is_multi) {
                echo $this->generateSelectAllForList($list);
            }

            
            $i = 0;
            foreach($list as $item){ ?>
                
                <div class="custom-control <?= ($this->field->is_multi ? "custom-checkbox" : "custom-radio") ?> ms-3">
                    <input 
                        :required="(<?= $this->isRequired()?>) && (!<?= $this->dataPath() ?> || <?= $this->dataPath() ?>.length == 0)"  
                        :disabled="<?= $this->isReadOnly() ?>"
                        <?= $this->comboboxAtChange() ?>
                        type="<?= ($this->field->is_multi ? "checkbox" : "radio") ?>" class="form-check-input"
                        name="<?= $this->uniqueName() ?>"
                        v-model="<?= $this->dataPath() ?>"
                        v-on:input="validate"
                        id="<?= $this->uniqueName() . '_' . $i ?>"
                        value="<?= $item->id ?>"
                    >
                    <label class="form-check-label" for="<?= $this->uniqueName() . '_' . $i ?>"><?= ($item->name) ?></label>
                </div>
                
            <?php $i++; } ?>

        <?php endif; ?>

        </div></div></div>
        
        <?php

        return ob_get_clean();
    }

    public function Select2() : ?string {

        $form_validity = (empty($this->prefix) ? '' : $this->prefix . '_') . "form_validated";
        
        ob_start(); ?>

        <div class="mb-3 col-md-<?= $this->field->size ?>"
            id="div_<?= $this->uniqueName() ?>"
            <?= $this->dependancy() ?>  
            >
            
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
                <?= $this->comboboxRefreshIcon() ?>
            </label>

            <div :class="{'valid-border': <?= $form_validity ?> && <?= $this->uniqueName() ?>_has_error_c != true ,'invalid-border': <?= $form_validity ?> && <?= $this->uniqueName() ?>_has_error != true}">
                <multiselect 

                    v-model="<?= $this->dataPath() ?>" 
                    :multiple="<?= ($this->field->is_multi == true ? "true" : "false") ?>" 
                    
                    <?= $this->field->is_multi ? ':group-select="true"' : '' ?>
                    placeholder="<?= t("Seleccionar Opción") ?>" 
                    track-by="id" 
                    label="name"
                    :hide-selected="false"
                    :searchable="true"
                    :options="pl_<?= $this->field->name . '_' . $this->field->data_source_table_name ?>" 
                    <?= $this->comboboxAtChange() ?>

                    <?php if($this->field->select2_async == true): ?>
                        data-name="<?= $this->field->name . '_' . $this->field->data_source_table_name ?>"
                        data-field-id="<?= $this->field->id?>"
                        data-v-model="<?= $this->dataPath() ?>"
                        data-extra-keyword="<?= $this->getExtraKeyword() ?>"
                        :loading="loading_<?= $this->field->name . '_' . $this->field->data_source_table_name ?> == true"
                        
                        @search-change="select2SearchChange"
                        @open="select2Open"
                    <?php endif; ?>
                    
                    ref="<?= $this->uniqueName() ?>"
                    name="<?= $this->uniqueName() ?>"
                    id="<?= $this->uniqueName() ?>"
                    v-on:input="validate"
                    :disabled="<?= $this->isReadOnly() ?>"
                    :required="<?= $this->isRequired() ?>"
                    >
                    <?php if($this->field->is_multi && $this->field->select2_async != true): ?>

                        <span slot="beforeList">
                            <div class="p-1 row">
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="<?= $this->dataPath() ?> = pl_<?= $this->field->name . '_' . $this->field->data_source_table_name ?>">
                                        <i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                        Select All
                                    </button>
                                </div>
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="<?= $this->dataPath() ?> = []">
                                        <i class="mdi mdi-checkbox-multiple-blank-outline"></i>
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </span>

                    <?php elseif($this->field->select2_async): ?>

                        <span slot="noResult">
                            <span v-if="loading_<?= $this->field->name . '_' . $this->field->data_source_table_name ?> != true && keyword_<?= $this->field->name . '_' . $this->field->data_source_table_name ?>.length > 0">
                                <i class="mdi mdi-information text-danger"></i>
                                No results were found for "{{keyword_<?= $this->field->name . '_' . $this->field->data_source_table_name ?>}}".
                            </span>
                        </span>
                        <span slot="beforeList" class="p-2">
                            <span v-if="keyword_<?= $this->field->name . '_' . $this->field->data_source_table_name ?>.length == 0">
                                <i class="mdi mdi-information text-info"></i>
                                Please write something to search
                            </span>
                            
                            <span v-else-if="pl_<?= $this->field->name . '_' . $this->field->data_source_table_name ?>.length > 0">
                                <i class="mdi mdi-information text-info"></i>
                                Showing top 100 results only
                            </span>
                            
                        </span>

                    <?php endif; ?>
                </multiselect>

                <?= $this->descriptionPanel() ?>

            </div>
            
            <div v-if="<?= $form_validity ?> && <?= $this->uniqueName() ?>_has_error_c" class="was-validated-not form-control:invalid invalid-feedback d-block">
                <?= $this->validationMessage() ?>
            </div>
        
        </div>
    
        <?php

        return ob_get_clean();

    }



    private function getExtraKeyword() {

        if($this->field->select2_async != true) {
            return null;
        }
            
        $final_value = "";
        $final_value_p = "";
        
        foreach(_explode(",", $this->field->data_source_filter_by_field_name) as $itm){
            if(!empty($final_value))
                $final_value .= ",";
            
            $has_place_holder = false;
            if(_strtolower($itm) == _strtolower("[CTYPEID]")){
                
                if(!empty($final_value_p))
                    $final_value_p .= ",";
                $final_value_p .= _strtolower(_str_replace(array("]","["),array("",""),$itm));

                $final_value = $this->mainCtypeObj->id;
                $has_place_holder = true;

            } else if(_strtolower($itm) == _strtolower("[RECORDID]")){
                
                
                if(!empty($final_value_p))
                    $final_value_p .= ",";
                $final_value_p .= _strtolower(_str_replace(array("]","["),array("",""),$itm));

                $final_value = isset($this->recordData) ? $this->recordData->id : "";
                $has_place_holder = true;

            } else {
                
                if(_strpos($itm, ".") !== false && $has_place_holder == false){
                    $arr = _explode(".",$itm);
                    $final_value =  $arr[sizeof($arr) - 1];

                    if(!empty($final_value_p))
                        $final_value_p .= ",";
                    $final_value_p .= _strtolower(_str_replace(array("]","["),array("",""),$final_value));

                    
                } else {
                
                    $final_value .= "$itm";

                    if(!empty($final_value_p))
                        $final_value_p .= ",";
                    $final_value_p .= _strtolower(_str_replace(array("]","["),array("",""),$itm));

                }

            }
            
        }

        return $final_value_p;

    }

}

