<?php

namespace App\Core\Gctypes\Components\FormComponents;

class DateComponent extends BaseComponent {

    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        parent::__construct($mainCtypeObj, $field, $isEditMode, $isSurvey, $prefix);
    }

    public function generate() : ?string {
        
        switch($this->field->appearance_id) {
            
            case '4_separated':
                return $this->separated();

            default:
                return $this->regular();

        }

    }

    public function regular() : ?string {

        ob_start(); ?>
    
        <div 
            <?= $this->dependancy() ?> 
            class="mb-3 col-md-<?= $this->field->size ?>" 
            id="div_<?= $this->uniqueName() ?>"
            >
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
            </label>
            <input 
                :disabled="<?= $this->isReadOnly() ?>"
                type="text" 
                :required="<?= $this->isRequired() ?>"
                ref="<?= $this->uniqueName() ?>" 
                name="<?= $this->uniqueName() ?>"
                id="<?= $this->uniqueName() ?>"
                v-on:input="validate"
                class="form-control" 
                v-model="<?= $this->dataPath() ?>"
                >
            <?= $this->invalidFeedback() ?>
            
            <?= $this->descriptionPanel() ?>
            
        </div>

        <?php

        return ob_get_clean();

    }

    private function separated() : ?string{
        
        ob_start(); ?>
    
        <div 
            <?= $this->dependancy() ?> 
            class="mb-3 col-md-<?= $this->field->size ?>" 
            id="div_<?= $this->uniqueName() ?>">
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
            </label>
            <div class="form-inline">
                <input 
                    :disabled="<?= $this->isReadOnly() ?>"
                    type="hidden"
                    :required="<?= $this->isRequired() ?>"
                    ref="<?= $this->uniqueName() ?>" 
                    name="<?= $this->uniqueName() ?>"
                    id="<?= $this->uniqueName() ?>"
                    class="form-control" 
                    v-model="<?= $this->dataPath() ?>">
            </div>
            <?= $this->invalidFeedback() ?>
            <?= $this->descriptionPanel() ?>
        </div>
        
        <?php

        return ob_get_clean();

    }


}

