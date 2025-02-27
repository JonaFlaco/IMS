<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class FooterActionsComponent {

    private $ctypeObj;
    private $ctypePermissionObj;
    private $isEditMode;
    private $isSurvey;
    private $recordData;

    private $langDirection;
    private $footerActionsComponent;

    public function __construct($ctypeObj, $ctypePermissionObj, $isEditMode, $isSurvey, $recordData) {
        $this->ctypeObj = $ctypeObj;
        $this->ctypePermissionObj = $ctypePermissionObj;
        $this->isEditMode = $isEditMode;
        $this->isSurvey = $isSurvey;
        $this->recordData = $recordData;

        $this->langDirection = Application::getInstance()->user->getLangDirection();



        if(_strpos($this->ctypeObj->extends, 'id="tpl-footer-actions-component"') !== false){
            $this->footerActionsComponent = "<footer-actions-component></footer-actions-component>";
        }
    }

    public function generate() : ?string{
        
        if($this->isSurvey) {
            ob_start();

            ?>
        
            <div class="row">
                <div class="col-12">
                    <div class="card ribbon-box">
                        <div class="card-body">
                            <div id="bottom_actions" class="ribbon-content row">
                                    
                                <div class="col-lg-12">
    
                                    <?= $this->generateButtons() ?>
                    
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
            </div>
                    
            <?php
                
                
            return ob_get_clean();

        } else {
            ob_start();

            ?>
        
            <div class="row">
                <div class="col-12">
                    <div class="card ribbon-box">
                        <div class="card-body">
                            <div class="ribbon ribbon-primary float-start"> 
                                
                                <i class="mdi mdi-menu"></i> <?= t("Acciones") ?>
                                
                            </div>
                            <div id="bottom_actions" class="ribbon-content row collapse pt-3 show">
                                    
                                <div class="col-lg-12">
    
                                    <?= $this->generateButtons() ?>
                    
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
            </div>
                    
            <?php
                
                
            return ob_get_clean();

        }
        
        
    }

    private function generateButtons(){

        ob_start() ?>

        
            <div class="btn-group">
                <button 
                    type="button" 
                    class="btn btn-success"
                    :disabled="SaveButtonLoading"
                    @click="postData()"
                    >
                    <i class="mdi me-1 mdi-content-save"></i>
                    <?=  ($this->isSurvey ? t("Enviar") : t("Guardar")) ?>
                </button>
                <?php if($this->isSurvey != true && ($this->ctypeObj->disable_add != true && $this->ctypePermissionObj->allow_add == true)){ ?>
                    <button :disabled="SaveButtonLoading" type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript: void(0);" @click="postData(1)">
                            <i class="mdi me-1 mdi-content-save"></i>
                            <?= t("Guardar & Crear Nuevo") ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
            
            <?php if($this->isEditMode && ($this->ctypeObj->disable_delete != true && $this->ctypePermissionObj->allow_delete == true)){ ?>
                
                <button @click="deleteRecord()" class="btn btn-danger">
                    <i class="dripicons-trash"></i> 
                    <?= t("Eliminar") ?>
                </button>
                
            <?php } ?>

            <?php if($this->isEditMode && ($this->ctypeObj->disable_add != true && $this->ctypePermissionObj->allow_add == true)){ ?>
            
                <button @click="addRecord()" class="btn btn-secondary">
                    <i class="dripicons-plus"></i> 
                    <?= t("Añadir Nuevo") ?>
                </button>
            
            <?php } ?>


            <?= $this->footerActionsComponent ?>

        <?php

        return ob_get_clean();
        
    }

}