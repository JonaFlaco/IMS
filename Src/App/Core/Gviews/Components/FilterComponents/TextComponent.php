<?php

namespace App\Core\Gviews\Components\FilterComponents;

class TextComponent extends BaseComponent {

    public function __construct($filter, $field) {
        parent::__construct($filter, $field);
    }

    public function generate($only_quick_access_filtrs){
        
        ob_start(); ?>
        
        
        <div class="col-md-<?= $only_quick_access_filtrs ? 6 : 12 ?> mb-3"
            <?= $this->dependency(); ?> 
            id="div_<?= $this->uniqueName() ?>"
            >
            <label class="form-label" for="<?= $this->uniqueName() ?>"><?= $this->title() ?>:</label>
            <div class="row">
                <div v-if="<?= $this->filter->hide_operator ? "false" : "true" ?>" class="col">
                
                    <select v-model="<?= $this->uniqueName() ?>_operator_id" class="form-select">
                        <option value="0"> <?= t("Cualquiera") ?> </option>
                        
                        <?php foreach($this->filterChoices("text") as $itm){ ?>
                            <option value="<?= $itm->id ?>"><?= $itm->name ?></option>
                        <?php } ?>

                    </select>
                
                </div>
                <div id="div_<?= $this->uniqueName() ?>_value" v-if="computed_<?= $this->uniqueName() ?>_value_visible" class="col-8 ps-0">
                    
                    <input class="form-control" type="text" 
                        v-model="<?= $this->uniqueName() ?>" 
                        name="<?= $this->uniqueName() ?>" >
                    </input>

                </div>
                <div class="col-8 ps-0" id="div_<?= $this->uniqueName() ?>_list" 
                    v-if="computed_<?= $this->uniqueName() ?>_list_visible" >

                    <textarea class="form-control" style="resize:none;" 
                        v-model="<?= $this->uniqueName() ?>" 
                        name="<?= $this->uniqueName() ?>_2" >
                    </textarea>

                </div>
            </div>
        </div>

        <?php

            return ob_get_clean();
    }

}