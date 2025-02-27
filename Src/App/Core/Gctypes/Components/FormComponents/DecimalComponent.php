<?php

namespace App\Core\Gctypes\Components\FormComponents;

use App\Core\Application;

class DecimalComponent extends BaseComponent {

    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        parent::__construct($mainCtypeObj, $field, $isEditMode, $isSurvey, $prefix);
    }

    public function generate() : ?string {

        switch($this->field->appearance_id) {
            
            case '7_map':
                return $this->map();

            default:
                return $this->regular();

        }

    }

    private function regular() : ?string {
        
       
        ob_start(); ?>
    
        <div class="mb-3 col-md-<?= $this->field->size ?>" 
            <?= $this->dependancy() ?>
            id="div_<?= $this->uniqueName() ?>">
            <label class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>">
                <?= $this->field->title . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
            </label>
            <input 
                :disabled="<?= $this->isReadOnly() ?>"
                type="text" 
                step="any" 
                v-bind:class="{ 'is-invalid': <?= $this->uniqueName() ?>_has_error }"
                v-on:input="validate" 
                ref="<?= $this->uniqueName() ?>" 
                name="<?= $this->uniqueName() ?>"
                pattern="^[0-9.]+$"
                id="<?= $this->uniqueName() ?>"
                class="form-control" 
                v-model="<?= $this->dataPath() ?>" 
                :required="<?= $this->isRequired() ?>"
                >
            <?= $this->invalidFeedback() ?>
            <?= $this->descriptionPanel() ?>
        </div>
        
        <?php

        return ob_get_clean();

    }

    private function map() : ?string{

        if(_strtolower(substr($this->field->name, _strlen($this->field->name) -3,_strlen($this->field->name))) == "lat"){
            
            $base_title = $this->field->title;
            
            $base_title = _str_replace(" Latitude","",$base_title);
            $base_title = _str_replace(" latitude","",$base_title);
            $base_title = _str_replace(" Lat","",$base_title);
            $base_title = _str_replace(" lat","",$base_title);
            
            ob_start(); ?>

            <div class="bg-light col-md-<?= $this->field->size ?> p-1 mb-2" 
                <?= $this->dependancy() ?>
                id="div_<?= $this->uniqueName() ?>">

                <label class="<?= ($this->field->is_deprecated ? "text-line-through" : "") ?>" for="<?= $this->uniqueName() ?>_lat">
                    <?= $base_title . $this->requiredSign() ?>
                    <?= $this->addDeprecatedFlag() ?>
                </label>
                <div class="row">

                    <div class="mb-3 col-md-3">
                        <label class="form-label" for="<?= $this->uniqueName() ?>_lat"> <?= t("Latitude") ?>:<?= $this->requiredSign() ?></label>
                        <input type="text" step="any" 
                            :disabled="<?= $this->isReadOnly() ?>"
                            ref="<?= $this->uniqueName() ?>_lat" 
                            name="<?= $this->uniqueName() ?>_lat"
                            id="<?= $this->uniqueName() ?>_lat"
                            v-on:input="validate"
                            class="form-control" v-model="<?= $this->dataPath() ?>_lat" 
                            :required="<?= $this->isRequired() ?>"
                            @change="<?= $this->uniqueName() ?>_set_location_manually()"
                            >
                        <br>
                        
                        <label class="form-label" for="<?= $this->uniqueName() ?>_lng"> <?= t("Longitude") ?>:<?= $this->requiredSign() ?></label>
                        <input type="text" step="any" 
                            :disabled="<?= $this->isReadOnly() ?>"
                            ref="<?= $this->uniqueName() ?>_lng" 
                            name="<?= $this->uniqueName() ?>_lng"
                            id="<?= $this->uniqueName() ?>_lng"
                            v-on:input="validate"
                            class="form-control" v-model="<?= $this->dataPath() ?>_lng" 
                            :required="<?= $this->isRequired() ?>"
                            @change="<?= $this->uniqueName() ?>_set_location_manually()"
                            >
                        <br>
                        <?= $this->invalidFeedback() ?>

                        <?php if(!$this->isReadOnly() && Application::getInstance()->request->isSecure() == true): ?>
                            <button class="btn btn-secondary" @click="<?= $this->uniqueName() ?>_get_current_location()"><i class=" mdi mdi-crosshairs-gps"></i></button>
                        <?php endif; ?>
                        
                        <?php if(!$this->isReadOnly()): ?>
                            <button class="btn btn-secondary" @click="<?= $this->uniqueName() ?>_set_location_manually()"><i class="mdi mdi-refresh"></i></button>
                        <?php endif; ?>
                        <br>
                        
                    </div>

                    <div class="col-md-9">
                        <div id="<?= $this->uniqueName() ?>_map" style="height:260px;"></div>
                    </div>

                </div>
                <?= $this->descriptionPanel() ?>
                    
            </div>
        
        <?php
        
            return ob_get_clean();

        }

        return null;
        
    }


}

