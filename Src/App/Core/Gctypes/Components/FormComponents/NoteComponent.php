<?php

namespace App\Core\Gctypes\Components\FormComponents;

class NoteComponent extends BaseComponent {

    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        parent::__construct($mainCtypeObj, $field, $isEditMode, $isSurvey, $prefix);
    }

    public function generate() : ?string {

        ob_start(); ?>

        <div class="col-md-<?= $this->field->size ?> pb-1 mb-3"
            <?= $this->dependancy() ?> 
            id="div_<?= $this->uniqueName() ?>">
            <label class="<?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title ?>
                <?= $this->addDeprecatedFlag() ?>
            </label>
            <?= $this->descriptionPanel() ?>
        </div>

        <?php

        return ob_get_clean();

    }


}

