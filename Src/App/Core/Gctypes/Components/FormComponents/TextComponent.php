<?php

namespace App\Core\Gctypes\Components\FormComponents;

class TextComponent extends BaseComponent {

    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        parent::__construct($mainCtypeObj, $field, $isEditMode, $isSurvey, $prefix);
    }

    public function generate() : ?string {
        
        if($this->field->appearance_id == "1_long_text") {
            return $this->longText();
        } else if ($this->field->appearance_id == "1_rich_text") {
            return $this->richText();
        }
        
        $type = "text";
        switch($this->field->appearance_id) {
            case "1_color_picker":
                $type = "color";
                break;
            case "1_email":
                $type = "email";
                break;
            case "1_password":
                $type = "1_password";
                break;
            default:
                $type = "text";
                break;
        }

        ob_start(); ?>

        <div 
            <?= $this->dependancy() ?>
            key="cont_<?= $this->uniqueName() ?>"
            class="mb-3 col-md-<?=$this->field->size ?>" 
            id="div_<?= $this->uniqueName() ?>"
            >
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title . $this->charsRemaining() . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
            </label>
            <input type="<?= $type ?>" class="form-control" 
                
                ref="<?= $this->uniqueName() ?>" 
                name="<?= $this->uniqueName() ?>"
                id="<?= $this->uniqueName() ?>"
                
                :disabled="<?= $this->isReadOnly() ?>"
                <?= $this->validationPattern() ?>
                v-on:input="validate"
                v-model="<?= $this->dataPath() ?>" 
                maxlength="<?= $this->field->str_length ?>" data-toggle="maxlength" 
                :required="<?= $this->isRequired() ?>"
                >
                
            <?= $this->invalidFeedback() ?>
            <?= $this->descriptionPanel() ?>
            

        </div>

        <?php

        return ob_get_clean();

    }

    public function longText() : ?string {

        ob_start(); ?>

        <div class="mb-3 col-md-<?= $this->field->size ?>"
            <?= $this->dependancy() ?> 
            id="div_<?= $this->uniqueName() ?>">
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->field->name ?>">
                <?=$this->field->title . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
            </label>
            <textarea rows="5" class="form-control" 
                :disabled="<?= $this->isReadOnly() ?>"
                ref="<?= $this->uniqueName() ?>" 
                name="<?= $this->uniqueName() ?>"
                id="<?= $this->uniqueName() ?>"
                v-model="<?= $this->dataPath() ?>" 
                v-on:input="validate"
                maxlength="<?= $this->field->str_length ?>" 
                :required="<?= $this->isRequired() ?>"
                ></textarea>
            <?= $this->invalidFeedback() ?>
            <?= $this->descriptionPanel() ?>

        </div>

        <?php

        return ob_get_clean();

    }

    public function richText() : ?string {

        ob_start(); ?>

        <div class="mb-3 col-md-<?= $this->field->size ?>" style="padding-bottom: 40px"
            <?= $this->dependancy() ?> 
            id="div_<?= $this->uniqueName() ?>">
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->field->name ?>">
                <?=$this->field->title . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
            </label>
            <rich-text-editor-component rows="5" class="form-control" 
                ref="<?= $this->uniqueName() ?>" 
                name="<?= $this->uniqueName() ?>"
                id="<?= $this->uniqueName() ?>"
                v-model="<?= $this->dataPath() ?>" 
                ></rich-text-editor-component>
            <?= $this->invalidFeedback() ?>
            <?= $this->descriptionPanel() ?>

        </div>

        <?php

        return ob_get_clean();

    }

}

