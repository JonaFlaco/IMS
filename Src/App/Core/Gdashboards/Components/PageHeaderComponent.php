<?php

namespace App\Core\Gdashboards\Components;

use App\Core\Application;

class PageHeaderComponent {

    private $dashboardObj;

    private $coreModel;

    public function __construct($dashboardObj) {
        
        $this->dashboardObj = $dashboardObj;

        $this->coreModel = Application::getInstance()->coreModel;
    }

    public function generate(){
        
        ob_start(); ?>

        

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right mt-0">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="/">Home</a>
                                </li> 

                                <?php if(isset($this->dashboardObj->moduleObj)): ?>
                                    <li class="breadcrumb-item">
                                        <a href="/<?= $this->dashboardObj->moduleObj->id ?>"><?= $this->dashboardObj->moduleObj->name ?></a>
                                    </li> 
                                <?php endif; ?>

                                <li class="breadcrumb-item active"><?= $this->dashboardObj->name ?></li>
                            </ol>
                        </div>

                        <h4 class="page-title"><?= $this->dashboardObj->name ?></h4>
                    </div>
                </div>
                <div class="col-12 mb-1">
                    <div class="page-title-box">
                        <div class="page-title-right mt-0">

                            <button type="button" @click='get_data' class="btn btn-primary">
                                <i class="mdi mdi-window-refresh"></i> 
                                <span><?= $this->coreModel->getKeyword("Refresh") ?></span>
                            </button>

                            <button type="button" @click='resetFilter' class="btn btn-danger">
                                <i class="mdi mdi-close"></i> 
                                <span><?= $this->coreModel->getKeyword("Reset") ?></span>
                            </button>
                            
                            <div class="btn-group">
                                <?php if(isset($this->dashboardObj->filters) && sizeof($this->dashboardObj->filters) > 0): ?>
                                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#filtrationModal" >
                                        <i class="mdi mdi-filter-variant"></i> Filter
                                    </button>
                                <?php endif; ?>
                                <button data-bs-toggle="modal" data-bs-target="#WidgetsModal" type="button" class="btn btn-secondary">
                                    <i class="mdi mdi-table-column-plus-after"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>     

        <?php

        return ob_get_clean();
    }

}