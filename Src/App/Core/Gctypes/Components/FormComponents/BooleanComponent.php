<?php

namespace App\Core\Gctypes\Components\FormComponents;

class BooleanComponent extends BaseComponent {

    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        parent::__construct($mainCtypeObj, $field, $isEditMode, $isSurvey, $prefix);
    }

    public function generate() : ?string {

        switch($this->field->appearance_id) {
            
            case '9_list':
                return $this->list();

            case '9_combobox':
                return $this->combobox();

            default:
                return $this->regular();

        }

        return null;
    }

    private function regular() : ?string {

        ob_start(); ?>
        
        
        <div class="mt-1 mb-1 col-md-<?= $this->field->size ?>" 
            <?= $this->dependancy() ?>
            id="div_<?= $this->uniqueName() ?>">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" 
                    :disabled="<?= $this->isReadOnly() ?>"
                    v-model="<?= $this->dataPath() ?>" 
                    ref="<?= $this->uniqueName()?>"
                    name="<?= $this->uniqueName()?>"
                    v-on:input="validate"
                    id="<?= $this->uniqueName()?>"
                    :required="<?= $this->isRequired() ?>"
                    >
                <label class="form-check-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                    <?= $this->field->title . $this->requiredSign() ?>
                    <?= $this->addDeprecatedFlag() ?>
                </label>
                <?= $this->invalidFeedback() ?>
            </div>
            
            <?= $this->descriptionPanel() ?>
            
        </div>
        
        <?php

        return ob_get_clean();
        
    }


    private function list() : ?string {
    
        ob_start(); ?>
    

        <div class="mb-3 col-md-<?= $this->field->size ?> pb-1" 
            <?= $this->dependancy() ?>
            id="div_<?= $this->uniqueName() ?>">
            
                <label class="form-label" for="<?= $this->uniqueName() ?>">
                    <?= $this->field->title . $this->requiredSign() ?>
                    <?= $this->addDeprecatedFlag() ?>
                </label>
                <div class="custom-control custom-radio ms-3">
                    <input type="radio" class="form-check-input" 
                    required="required"
                    name="<?= $this->uniqueName() ?>"
                    id="<?= $this->uniqueName() ?>_1" 
                    v-on:input="validate"
                    value="1"
                    :disabled="<?= $this->isReadOnly() ?>"
                    v-model="<?= $this->dataPath() ?>"
                    > 
                    <label for="<?= $this->uniqueName() ?>_1" class="form-check-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>"><?= t("Si") ?></label>
                </div>
                <div class="custom-control custom-radio ms-3">
                    <input type="radio" class="form-check-input"
                        :disabled="<?= $this->isReadOnly() ?>"
                        required="required" 
                        name="<?= $this->uniqueName() ?>" 
                        id="<?= $this->uniqueName() ?>_2" 
                        v-on:input="validate"
                        value="0"
                        v-model="<?= $this->dataPath() ?>"
                    >
                    
                    <label for="<?= $this->uniqueName() ?>_2" class="form-check-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>"><?= t("No") ?></label>
                </div>
            
            <?= $this->descriptionPanel() ?>
            
        </div>

        <?php

        return ob_get_clean();
    }


    private function combobox() : ?string {
        
        ob_start(); ?>

        <div class="mb-3 col-md-<?= $this->field->size ?>" 
            <?= $this->dependancy() ?>
            id="div_<?= $this->uniqueName() ?>">
            
                <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                    <?= $this->field->title . $this->requiredSign() ?>
                    <?= $this->addDeprecatedFlag() ?>
                </label>
                <select class="form-select"
                    ref="<?= $this->uniqueName() ?>"
                    name="<?= $this->uniqueName() ?>"
                    id="<?= $this->uniqueName() ?>"
                    v-on:input="validate"
                    v-model="<?= $this->dataPath() ?>"
                    required="required"
                    :disabled="<?= $this->isReadOnly() ?>"
                    >
                    <option value="">- <?= t("None") ?> -</option> 
                    <option value="1"><?= t("Si") ?></option> 
                    <option value="0"><?= t("No") ?></option> 
                </select>
            
            <?= $this->descriptionPanel() ?>
            
        </div>
        
        <?php
        
        return ob_get_clean();

    }




}

