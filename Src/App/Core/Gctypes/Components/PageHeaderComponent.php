<?php

namespace App\Core\Gctypes\Components;

class PageHeaderComponent {

    private $ctypeObj;
    private $isEditMode;
    private $recordData;
    private $isSurvey;

    public function __construct($ctypeObj, $isEditMode, $recordData, $isSurvey) {
        $this->ctypeObj = $ctypeObj;
        $this->isEditMode = $isEditMode;
        $this->recordData = $recordData;
        $this->isSurvey = $isSurvey;
    }

    public function generate(){

        if($this->isSurvey){
            return null;
        }
        
        ob_start(); ?>

        <div class="row">
            <div class="col-12">
                
                <div class="page-title-box">
                    <?= $this->generateBreadCrumb() ?>
                    <h4 class="page-title"> <?= $this->getPageTitle() ?></h4>
                </div>

                <?= $this->getCtypeStatus() ?>
            </div>
        </div> 
        

        <?php

        return ob_get_clean();    
    }

    private function getCtypeStatus() {

        if($this->ctypeObj->status_id == 20) {

            return <<<HTML
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Advertencia - </strong> Este Content-Type aún no ha sido publicado
                </div>
            HTML;

        } else if($this->ctypeObj->status_id == 72) {

            return <<<HTML
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Advertencia - </strong> Este Content-Type ha sido archivado
                </div>
            HTML;

        } else if($this->ctypeObj->status_id == 83) {

            return <<<HTML
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Advertencia - </strong> Este Content-Type ha sido abandonado
                </div>
            HTML;

        }

        return "";
    }

    private function getPageTitle() : string {

        if($this->isEditMode) {
            return t("Editar") . " " . $this->recordData->{$this->ctypeObj->display_field_name ?? "id"};
        } else {
            return t("Añadir Nuevo") . " " . $this->ctypeObj->name;
        }
        
    }


    public function generateBreadCrumb() : ?string {
        
        
        if($this->isSurvey){
            return null;
        }
            
        ob_start(); ?>


        <div class="page-title-right mt-0">
            <ol class="breadcrumb m-0">
                <?= sprintf('<li class="breadcrumb-item"><a href="/"> %s </a></li>', t("Home")) ?>
                <?= empty($this->ctypeObj->module_id) ? '' : sprintf('<li class="breadcrumb-item"><a href="/%s"> %s </a></li>', $this->ctypeObj->module_id, $this->ctypeObj->module_name) ?>
                <?= sprintf('<li class="breadcrumb-item"><a href="/%s"> %s </a></li>', $this->ctypeObj->id, $this->ctypeObj->name) ?>
                <?= sprintf('<li class="breadcrumb-item active"> %s </li>', $this->getPageTitle(false)) ?>
                
            </ol>
        </div>

        <?php

        return ob_get_clean();

    }


}