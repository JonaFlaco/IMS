<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class LogComponent {

    private $ctypeObj;
    private $isEditMode;
    private $recordData;
    private $ctypePermissionObj;

    public function __construct($ctypeObj, $isEditMode, $recordData, $ctypePermissionObj) {
        $this->ctypeObj = $ctypeObj;
        $this->isEditMode = $isEditMode;
        $this->recordData = $recordData;
        $this->ctypePermissionObj = $ctypePermissionObj;
    }

    private function showLog() {
        return $this->ctypePermissionObj->allow_view_log == true && $this->isEditMode == true && ($this->ctypeObj->is_system_object != true || Application::getInstance()->user->isAdmin() == true);
    }

    public function generate() : ?string {
        
        
        if(!$this->showLog()){
            return null;
        }

        ob_start();

        ?>

            <div class="row">
                <div class="col-12">
                    <div class="card ribbon-box">
                        <div class="card-body">
                            <div class="ribbon ribbon-primary float-start"> 
                                <a data-bs-toggle="collapse" href="#bottom_log" role="button" aria-expanded="false" aria-controls="bottom_log" class="text-white">
                                    <i class="mdi mdi-comment-outline"></i> <?= t("Log") ?>
                                </a>
                            </div>
                            <div id="bottom_log" class="ribbon-content row collapse pt-3 show">
                    
                                <log-component 
                                    ctype-id="<?= $this->ctypeObj->id ?>" 
                                    content-id="<?= $this->recordData->id ?>"
                                    csrf-token="<?= Application::getInstance()->csrfProtection->create("ctypes_logs") ?>"
                                    >
                                </log-component>

                            </div>
        
                        </div>
                    </div>
                </div>
            </div>


        <?php  

        return ob_get_clean();


    }

}