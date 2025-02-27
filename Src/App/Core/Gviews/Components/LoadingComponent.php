<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class LoadingComponent {

    public function __construct() {
        
    }

    public function generate(){
        
        ob_start(); ?>


        <div class="alert alert-info my-1" v-if="is_loading == 1">
            <?= t("Loading, please wait") ?>...
        </div>

        <?php

        return ob_get_clean();
    }

    public function generateModal(){
        
        ob_start(); ?>

        
        <!-- Loading Modal -->
        <div id="loadingModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    
                    <div class="modal-body">
                    
                        <h4 class="text-primary text-center">
                            <span class="spinner-border"></span>
                            {{loading_modal_message}}
                        </h4>
                        
                    </div>
                    
                </div>
            </div>
        </div>


        <?php

        return ob_get_clean();
    }

    public function generateMethods(){

        ob_start(); ?>

        
        show_loading_popup(message){
            if(message == null || message.length == 0){
                message = 'loading';
            }
            this.loading_modal_message = message;

            var myModal = new bootstrap.Modal(document.getElementById('loadingModal'), {
                backdrop: 'static',
                keyboard: false,
            })
            myModal.show();
        },
        hide_loading_popup(message){
            setTimeout(function(){
                var logModal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'))
                logModal.hide();
            }, 1000);
        },

        <?php

        return ob_get_clean();
    }

    public function getDataObject(){
        
        $result = [];

        $result["loading_modal_message"] = '';
        $result["is_loading"] = false;

        return $result;
    }

}