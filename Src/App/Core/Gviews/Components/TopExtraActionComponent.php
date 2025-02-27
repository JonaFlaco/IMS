<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class TopExtraActionComponent {

    private $viewData;
    private $ctypeObj;
    private $permissions;
    private ?VerificationComponent $verificationComponent;

    private $coreModel;
    private $langDir;
    private $app;
    

    public function __construct($ctypeObj, $permissions, $viewData = null, $verificationComponent = null) {
        $this->viewData = $viewData;
        $this->ctypeObj = $ctypeObj;
        $this->permissions = $permissions;
        $this->verificationComponent = $verificationComponent;

        $this->app = Application::getInstance();
        $this->coreModel = $this->app->coreModel;
        $this->langDir = $this->app->user->getLangDirection();
    }

    public function generate(){
        
        ob_start() ?>

        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= t("Opciones") ?>
        </button>

        <div class="dropdown-menu custom-dropdown-menu" aria-labelledby="dropdownMenuButton" <?= $this->langDir == "rtl" ? 'style="text-align: right !important;"' : ''?>>

            <?php if(isset($this->viewData) && $this->app->user->isAdmin() && (!$this->viewData->is_system_object || $this->app->user->isSuperAdmin())): ?>
                <button class="dropdown-item" @click="editView()">
                    <i class="mdi mdi-open-in-new me-1"></i>
                    <?= t('Editar Vista') ?>
                </button>
            <?php endif; ?>

            <?php if($this->app->user->isAdmin() && (!$this->ctypeObj->is_system_object || $this->app->user->isSuperAdmin())): ?>
                <button class="dropdown-item" @click="editCtype()">
                    <i class="mdi mdi-open-in-new me-1"></i>
                    <?= t('Editar Content-Type') ?>
                </button>
            <?php endif; ?>

            <?php if(isset($this->viewData)): ?>
                <button data-bs-toggle="modal" id="btnHideShowColumnsSettings" data-bs-target="#columnsModal" type="button" class="dropdown-item">
                    <i class="mdi mdi-table-column-plus-after"></i>
                    <?= t('Modificar Columnas') ?>
                </button>
            <?php endif; ?>

            <?php if($this->permissions->allow_generic_export == true || $this->permissions->allow_generic_import_add == true || $this->permissions->allow_generic_import_edit == true): ?>
                
                <div class="dropdown-divider"></div>
                
            <?php endif; ?>
    
    
            
            <?php if($this->permissions->allow_generic_export == true): ?>
                    
                <a class="dropdown-item" @click='exportgenericxls()' href="javascript: void(0);">
                    <i class="mdi mdi-file-excel"></i> 
                    <span><?= t("Exportar a Excel") ?></span>
                </a>

            <?php endif; ?>

            <?php if($this->permissions->allow_generic_import_add == true || $this->permissions->allow_generic_import_edit == true): ?>
            
                <a target="_blank" class="dropdown-item" href="/dataimport/">
                <i class="mdi mdi-file-excel"></i> 
                    <span><?= t("Importar desde Excel") ?></span>
                </a>
                            
            <?php endif; ?>

            <?php if(isset($this->viewData)) { echo (new BulkDeleteComponent($this->ctypeObj, $this->permissions))->generateButton(); }?>
            
            <?php
            if(isset($this->viewData)) {

                $btnCount = 0;
                foreach($this->viewData->actions as $button){
                    
                    if(!empty($button->roles)) {
            
                        $has_access = false;
                        
                        foreach($button->roles as $role) {
                            if(in_array($role->value, explode(",", $this->app->user->getRoles())))
                                $has_access = true;
                            
                        }
            
                        if(Application::getInstance()->user->isAdmin() != true && !$has_access){
                            continue;
                        }
            
                    }

                    
                    if($btnCount++ == 0){ ?>
                        <div class="dropdown-divider"></div>
                    <?php }

                    if(isset($button->download_view_id)):
                        
                        $button_view_data = $this->coreModel->nodeModel("views")
                            ->id($button->download_view_id)
                            ->loadFirstOrFail();
                        
                        ?>
                        <a href="javascript: void(0);" class="dropdown-item <?= $button->style ?>" @click='exportxls("<?= $button->download_view_id ?>","<?= (!empty($button_view_data->export_file_name) ? $button_view_data->export_file_name : $button_view_data->name) ?>", "<?=$button_view_data->export_type_id ?>")'>
                            <i class="mdi mdi-file-excel"></i>
                            <?= $button->title ?>
                        </a>
                        
                    <?php elseif (isset($button->update_status_id)):

                        $button_status = $this->coreModel->nodeModel("status_list")
                            ->id($button->update_status_id)
                            ->loadFirstOrFail();
                        
                        $status_settings = $this->coreModel->getStatus($button_status->id, $this->ctypeObj->id);
                        if(sizeof($status_settings) > 0){
                            $status_settings = $status_settings[0];
                        } else {
                            $status_settings = $button_status;
                        }

                        ?>
                        <a href="javascript: void(0);" class="dropdown-item <?= $button_status->style ?>" @click='update_status_<?= $button->name ?>(<?= $status_settings->is_justification_required ?>,<?= $status_settings->is_actual_date_required ?>)'>
                            <i class="dripicons-gear"></i>
                            <?= $button->title ?>
                        </a>
                        
                    <?php else: 
                        
                        if($button->method == "[VERIFY]" || $button->method == "[UNVERIFY]"): ?>
                            <?= $this->verificationComponent->generateButtons($button) ?>
                        <?php else: ?>
                            
                            <a href="javascript: void(0);" ref="run_<?= $button->name ?>" class="dropdown-item <?= $button->style ?>" id="run_<?= $button->name ?>" <?= (!empty($button->method) && $button->method != "#" ? " @click='run_$button->name()' " : "") ?>>
                                <i class="dripicons-gear"></i>
                                <?= $button->title ?>
                            </a>
                            
                        <?php endif; ?>
                    <?php endif; ?>
                <?php } ?>
            <?php } ?>
        </div>
    
        <?php
      
        return ob_get_clean();
        
    }

    public function generateMethods() : ?string {

        $result = "";
            
        $verification_button_found = false;
        if(isset($this->viewData)) {
            
            foreach($this->viewData->actions as $button){
                if(!empty($button->method)){
                    
                    if($button->method == "[VERIFY]" || $button->method == "[UNVERIFY]"){
                        $verification_button_found = true;
                    } else if(!empty($button->method) && $button->method != "#") {
                        
                        if(_strtolower($button->method) == "@click")
                            continue;

                        $result .= sprintf("run_%s(){ window.open('/%s', '_blank'); },", $button->name, $button->method);
                    }
                }
            }

        }

        if($verification_button_found == true && isset($this->verificationComponent)){

            $result .= $this->verificationComponent->generateMethod();
        
        }

        if(isset($this->viewData) && $this->app->user->isAdmin() && (!$this->viewData->is_system_object || $this->app->user->isSuperAdmin())) {
            $result .= "
                editView() {
                    window.open('/views/edit/" . $this->viewData->id . "' , '_blank');
                },
            ";
        }

        if($this->app->user->isAdmin() && (!$this->ctypeObj->is_system_object || $this->app->user->isSuperAdmin())) {
            $result .= "
                editCtype() {
                    window.open('/ctypes/edit/" . (isset($this->viewData) ? $this->viewData->ctype_id : $this->ctypeObj->id) . "' , '_blank');
                },
            ";
        }

        
        return $result;
    }


}