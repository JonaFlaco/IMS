<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class PageHeaderComponent {

    private $ctypeObj;
    private $viewData;
    private bool $basedOnCtype;

    private $field_name;
    public function __construct($ctypeObj, $viewData = null,$basedOnCtype = true) {
        $this->ctypeObj = $ctypeObj;
        $this->viewData = $viewData;
        $this->basedOnCtype = $basedOnCtype;
    }

    
    public function generate() : ?string {

        

        ob_start(); ?>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <?= $this->generateBreadCrumb() ?>
                    <h4 class="page-title"><?= ($this->basedOnCtype ? $this->ctypeObj->name : $this->viewData->name) ?></h4>
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


    private function generateBreadCrumb() : ?string {
        
        ob_start(); ?>

        <div class="page-title-right mt-0">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="/"><?= t("Home") ?></a></li>
                
                <?php if($this->basedOnCtype && !empty($this->ctypeObj->module_id)): ?>
                    <li class="breadcrumb-item"><a href="/<?= $this->ctypeObj->module_id ?>"><?= $this->ctypeObj->module_name ?></a></li>
                <?php endif; ?>
                
                <li class="breadcrumb-item active"><?= ($this->basedOnCtype ? $this->ctypeObj->name : $this->viewData->name) ?></li>
            </ol>
        </div>

        <?php

        return ob_get_clean();
    }

}