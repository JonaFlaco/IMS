<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class TopActionsComponent {

    private $ctypeObj;
    private $fields;
    private $isEditMode;
    private $allowOpenTpl;
    private $recordData;
    private $ctypePermissionObj;

    private $app;
    private $headerActionsComponent;

    public function __construct($ctypeObj, $fields, $isEditMode, $recordData, $allowOpenTpl, $ctypePermissionObj) {

        $this->ctypeObj = $ctypeObj;
        $this->fields = $fields;
        $this->isEditMode = $isEditMode;
        $this->recordData = $recordData;
        $this->allowOpenTpl = $allowOpenTpl;
        $this->ctypePermissionObj = $ctypePermissionObj;

        $this->app = Application::getInstance();

        if(_strpos($this->ctypeObj->extends, 'id="tpl-header-actions-component"') !== false){
            $this->headerActionsComponent = "<header-actions-component></header-actions-component>";
        }
    }

    public function generate() : ?string{
        
        $found = false;

        
        ob_start(); ?>

        <?php if($this->allowOpenTpl) : $found = true; ?>
            <button class="dropdown-item" @click="showTpl()"><i class="mdi mdi-open-in-new me-1"></i><?= t('Ver Registro') ?></button>
        <?php endif; ?>
        
        
        <?php if($this->app->user->isAdmin()) : $found = true; ?>
            <button class="dropdown-item" @click="editCtype()"><i class="mdi mdi-open-in-new me-1"></i><?= t('Editar Content-Type') ?></button>
        <?php endif; ?>

        
        <?php foreach($this->fields as $field):
            if($field->is_hidden_updated || $field->field_type_id != "button"):
                continue;
            endif;

            ?>

            <?php if($found): ?>
                <div class="dropdown-divider"></div>
            <?php $found = true; endif; ?>


            <button class="dropdown-item" @click="run<?= $field->name ?>()"><?= $field->title ?></button> 
        
        <?php endforeach; ?>
        
        <?= $this->headerActionsComponent ?>

        <?php

        $result = ob_get_clean();

        ob_start();

        if($found): ?>
            
            <div class="row">
                <div class="col-sm-4">
                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= t("Acciones") ?></button>
                        <div class="dropdown-menu">
                            <?= $result ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-8 text-end">
                    <?php if($this->isEditMode && $this->ctypeObj->use_generic_status == true): ?>

                        <?= (new UpdateStatusComponent($this->ctypeObj, $this->isEditMode, $this->recordData))->generateButton() ?>

                    <?php endif; ?>
                </div>
        </div>

        <?php endif; ?>
        
        <?php

        return ob_get_clean();

    }


    public function generateMethods() {

        ob_start();
        ?>

        <?php if($this->allowOpenTpl): ?>
        showTpl: function(){
            window.open('/<?= $this->ctypeObj->id ?>/show/<?= $this->recordData->id ?>','_blank');
        },
        <?php endif; ?>

        <?php if($this->app->user->isAdmin()): ?>
            
        editCtype: function(){
            window.open('/ctypes/edit/<?= $this->ctypeObj->id ?>','_blank');
        },
        <?php endif; ?>

        <?php

        return ob_get_clean();

    }

}