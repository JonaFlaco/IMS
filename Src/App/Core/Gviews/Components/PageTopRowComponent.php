<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;
use App\Core\Gviews\Components\FilterationPanelComponent;

class PageTopRowComponent {

    private $viewData;
    private $permissions;
    private $ctypeObj;
    private $langDir;
    private FilterationPanelComponent $filterationPanelComponent;
    private TopExtraActionComponent $topExtraActionComponent;
    
    public function __construct($ctypeObj, $permissions, $filterationPanelComponent, $topExtraActionComponent, $viewData = null) {
        $this->viewData = $viewData;
        $this->ctypeObj = $ctypeObj;
        $this->permissions = $permissions;
        $this->filterationPanelComponent = $filterationPanelComponent;
        $this->topExtraActionComponent = $topExtraActionComponent;

        $this->langDir = Application::getInstance()->user->getLangDirection();
    }

    
    public function generate() : ?string {

        ob_start(); ?>
        
        <div class="col-sm-4">                            
            <?= $this->generateAddButton() ?>
        </div>

        <div class="col-sm-8">
            <div class="text-sm-end">
               
                
                <?= $this->generateRefreshButton() ?>

                <?= $this->filterationPanelComponent->generateResetFilterButton() ?>
                
                <div class="btn-group">
                <?= $this->filterationPanelComponent->generateFilterButton() ?>
                    <?= $this->generateDefaultExportButton() ?>
                    <?= $this->topExtraActionComponent->generate() ?>
                </div>
            </div>
        </div>
        
        <?php

        return ob_get_clean();

    }

    private function generateAddButton() : ? string {
        if($this->ctypeObj->disable_add == true || $this->permissions->allow_add != true){
            return null;
        }
        
        return sprintf('<a href="/%s/add/" id="btnAdd" target="_blank" class="btn btn-secondary mb-2"><i class="mdi mdi-plus-box me-1"></i><span>%s</span></a>', $this->ctypeObj->id,  t("Agregar nuevo registro"));
        
    }

    private function generateRefreshButton() : ? string {
        ob_start() ?>
            

        <button 
            type="button" 
            @click='filter()' 
            class="btn btn-primary"
            >
            <i class="mdi mdi-refresh"></i> 
            <span><?= t("Refrescar") ?></span>
        </button>

        <?php

        return ob_get_clean();
        
    }

    private function generateDefaultExportButton() : ? string {

        if(!isset($this->viewData) || $this->viewData->hide_default_export_btn == true){
            return null;
        }

        ob_start() ?>

        <button :disabled="exportXlsButtonLoading && exportXlsViewId == '<?= $this->viewData->id ?>'" type="button" @click="exportxls(null,'<?= (!empty($this->viewData->export_file_name) ? $this->viewData->export_file_name : $this->viewData->name) ?>','<?= $this->viewData->export_type_id ?>')" class="btn btn-secondary">
            <i v-if="!exportXlsButtonLoading && exportXlsViewId == '<?= $this->viewData->id?>'" class="mdi mdi-file-excel"></i> 
            <span v-if="exportXlsButtonLoading && exportXlsViewId == '<?= $this->viewData->id ?>'" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span><?= t("Export") ?></span>
        </button>

        <?php

        return ob_get_clean();
        
    }

    private function generateShowColumnSettingsButton() : ? string {
        
        if(!isset($this->viewData)){
            return null;
        }

        ob_start() ?>


        <button data-bs-toggle="modal" id="btnHideShowColumnsSettings" data-bs-target="#columnsModal" type="button" class="btn btn-secondary">
            <i class="mdi mdi-table-column-plus-after"></i>
        </button>

               
        <?php

        return ob_get_clean();
        
    }



}