<?php

namespace App\Core\Gctypes\Components\FormComponents;

class NumberComponent extends BaseComponent {

    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        parent::__construct($mainCtypeObj, $field, $isEditMode, $isSurvey, $prefix);
    }

    public function generate() : ?string {

        ob_start() ?>

        <div class="mb-3 col-md-<?= $this->field->size ?>" 
            <?= $this->dependancy() ?>
            id="div_<?= $this->uniqueName() ?>">
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
            </label>
            <input type="text" 
                :disabled="<?= $this->isReadOnly() ?>"
                v-bind:class="{ 'is-invalid': <?= $this->uniqueName() ?>_has_error }"
                v-on:input="validate" 
                pattern="^[0-9]+$"
                name="<?= $this->uniqueName() ?>"
                ref="<?= $this->uniqueName() ?>"
                id="<?= $this->uniqueName() ?>"
                class="form-control" v-model="<?= $this->dataPath() ?>" 
                :required="<?= $this->isRequired() ?>"
                >
            <?= $this->invalidFeedback() ?>
            <?= $this->descriptionPanel() ?>
        </div>

        <?php

        return ob_get_clean();

    }


}

