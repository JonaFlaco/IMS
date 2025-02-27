<?php

namespace App\Core\Gdashboards\Components\FilterComponents;

class DateComponent extends BaseComponent {

    public function __construct($filter, $field) {
        parent::__construct($filter, $field);
    }

    public function generate(){
        
        ob_start(); ?>
        
        
        <div class="col-md-6 mb-3" 
            <?= $this->dependency() ?> 
            id="div_<?= $this->uniqueName() ?>">

            <label class="form-label" for="<?= $this->uniqueName() ?>"><?= $this->title() ?>:</label>
            <div class="row">
                <div class="col">
                
                    <select v-model="<?= $this->uniqueName() ?>_operator_id" class="form-select">
                        <option value="0"> <?= t("Cualquiera") ?> </option>
                        
                        <?php foreach($this->filterChoices("date") as $itm){
                            if($itm->name == "---"): ?>
                                <optgroup label=""></optgroup>
                            <?php else: ?>
                                <option value="<?= $itm->id ?>"><?= $itm->name ?></option>
                            <?php endif; ?>
                            <?php } ?>  
                    </select>
                
                </div>

                <div id="div_<?= $this->uniqueName() ?>_value" v-if="computed_<?= $this->uniqueName() ?>_value_visible" class="col ps-0">
                    <input type="text" v-model="<?= $this->uniqueName() ?>" name="<?= $this->uniqueName() ?>" id="<?= $this->uniqueName() ?>" class="form-control" >
                </div>
                <div id="div_<?= $this->uniqueName() ?>_2nd_value" v-if="computed_<?= $this->uniqueName() ?>_2nd_value_visible" class="col ps-0">
                    <input type="text" v-model="<?= $this->uniqueName() ?>_2nd_value" name="<?= $this->uniqueName() ?>_2nd_value" id="<?= $this->uniqueName() ?>_2nd_value" class="form-control">
                </div>
            </div>
        </div>

        <?php

        return ob_get_clean();
    }

}