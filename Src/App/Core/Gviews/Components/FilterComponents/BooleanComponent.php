<?php

namespace App\Core\Gviews\Components\FilterComponents;

class BooleanComponent extends BaseComponent {

    public function __construct($filter, $field) {
        parent::__construct($filter, $field);
    }

    public function generate($only_quick_access_filtrs){
        
        ob_start(); ?>
        
        <div class="col-md-<?= $only_quick_access_filtrs ? 6 : 12 ?> mb-3"
            <?= $this->dependency() ?>
            id="div_<?= $this->uniqueName() ?>">
            <label class="form-label" for="<?= $this->uniqueName() ?>"><?= $this->title() ?>:</label>
            <select v-model="<?= $this->uniqueName() ?>" name="<?= $this->uniqueName() ?>" class="form-select">
                <option value="null"> <?= t("Cualquiera") ?> </option>\n
                <option value="1"> <?= t("Si") ?> </option>\n
                <option value="0"> <?= t("No") ?> </option>\n
            </select>
        </div>

        <?php

        return ob_get_clean();

    }

}