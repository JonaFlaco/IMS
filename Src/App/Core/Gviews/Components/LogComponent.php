<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class LogComponent {

    private $ctypeObj;
    private $ctypePermissionObj;

    public function __construct($ctypeObj, $ctypePermissionObj) {
        $this->ctypeObj = $ctypeObj;
        $this->ctypePermissionObj = $ctypePermissionObj;
    }

    private function showLog() {
        return $this->ctypePermissionObj->allow_view_log == true;
    }

    public function generateModal(){
        
        if(!$this->showLog()) {
            return false;
        }

        ob_start(); ?>

        
        
        <!-- Log Modal -->
        <div id="logModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-dark">
                        <h4 class="modal-title" id="dark-header-modalLabel">Log</h4>
                        <button type="button" class="btn-close" @click="hideLog" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">

                        <log-component 
                            v-if="log_id" 
                            :ctype-id="mainCtypeId" 
                            :content-id="log_id"
                            csrf-token="<?= Application::getInstance()->csrfProtection->create("ctypes_logs") ?>"
                            >
                        </log-component>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" @click="hideLog"><?= t("Cerrar") ?></button>
                    </div>
                </div>
            </div>
        </div>


        <?php

        return ob_get_clean();
    }


    public function generateMethods(){

        if(!$this->showLog()) {
            return false;
        }

        ob_start(); ?>
            
        showLog(id){
            
            this.log_id = id;
            var myModal = new bootstrap.Modal(document.getElementById('logModal'), {
                backdrop: 'static',
                keyboard: false,
            })
            myModal.show();

        },
        hideLog() {

            var logModal = document.getElementById('logModal')
            logModal = bootstrap.Modal.getInstance(logModal)
            logModal.hide();

            this.log_id = null;

        },
        
        
        <?php
        
        return ob_get_clean();
        
    }

    public function getDataObject(){
        
        if(!$this->showLog()) {
            return [];
        }

        return [
            "log_id" => null
        ];

    }

}