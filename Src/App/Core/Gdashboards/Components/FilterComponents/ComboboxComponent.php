<?php

namespace App\Core\Gdashboards\Components\FilterComponents;

use \App\Core\Application;
use App\Core\Gctypes\Ctype;

class ComboboxComponent extends BaseComponent {

    private $filters;
    private $coreModel;

    public function __construct($filter, $field, $filters) {
        parent::__construct($filter, $field);

        $this->filters = $filters;

        $this->coreModel = Application::getInstance()->coreModel;
    }

    public function generate(){
        
        ob_start(); 

        $referesh_ds = null;
        foreach($this->filters as $filterx){
            if(!empty($filterx->data_source_filter_by_field_name) && $filterx->data_source_filter_by_field_name == $this->filter->field_name){
                
                $filter_ctype = (new Ctype)->load($this->filter->ctype_id);

                $alias = $filter_ctype->id;

                $referesh_ds = sprintf('@change="reload_%s(%s)"', $alias . "_" . $filterx->field_name, $filter_ctype->id . "_" . $this->field->name);
            }

        }
        
        $filterCtype = (new Ctype)->load($this->filter->ctype_id);

        $refresh_icon = "";
        if(!empty($this->filter->data_source_filter_by_field_name)){
            
            $filter_ctype = (new Ctype)->load($this->filter->ctype_id);

            $alias = $filter_ctype->id;

            $refresh_icon = sprintf('<span v-if="loading_%s" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>', $filter_ctype->id . "_" . $this->filter->field_name);
        }
    
        ?>
        <div class="col-md-6 mb-3"
            <?= $this->dependency() ?>
            id="div_<?= $this->uniqueName() ?>">
            <label class="form-label" for="<?= $this->field->name ?>"><?= $this->title() ?>: <?= $refresh_icon ?></label>
                <div class="row">
                <div class="col">
                
                    <select v-model="<?= $this->uniqueName() ?>_operator_id" class="form-select">
                        <option value="0"> <?= t("Any") ?> </option>
                    
                        <?php foreach($this->filterChoices("relation") as $itm){ ?>
                            <option value="<?= $itm->id ?>"><?= $itm->name ?></option>
                        <?php } ?>

                    </select>
                
                </div>
                <div id="div_<?= $this->uniqueName() ?>_value" v-if="computed_<?= $this->uniqueName() ?>_value_visible" class="col ps-0">
                    <multiselect 
                        <?= _str_replace("@change", "@input", $referesh_ds) ?>
                        v-model="<?= $this->uniqueName() ?>" 
                        :multiple="<?= $this->uniqueName() ?>_operator_id == 'relation_in' || <?= $this->uniqueName() ?>_operator_id == 'relation_not_in'" 
                        deselect-label="" 
                        select-label=""
                        selected-label="Selected"
                        track-by="id" 
                        label="name" 
                        placeholder="" 
                        :options="pl_<?= $filterCtype->id . "_" . $this->filter->field_name ?>" 
                        :searchable="true" 
                    > </multiselect>
                </div>
            </div>
        </div>


        <?php

        return ob_get_clean();

    }

}