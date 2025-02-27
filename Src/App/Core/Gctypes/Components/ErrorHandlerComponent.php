<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class ErrorHandlerComponent {

    public function __construct() {
        
    }

    public function generateModal() : ?string{
        
        ob_start(); ?>
        <!-- Error Modal -->
        <div id="PostErrorModal" class="modal fade" tabindex="-1" style="z-index:5000" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-filled bg-danger">
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <i class="dripicons-wrong h1"></i>
                            <h4 class="mt-2"><?= t("Ups, hubo un error") ?>!</h4>
                        </div>
                        
                        <div id="error-modal-body"></div>
                        <div class="text-center">
                            <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal"><?= t("Continuar") ?></button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php

        return ob_get_clean();
    }

    public function showErrorDialog() {
        ob_start() ?>

        showErrorDialog() {
            var myModal = new bootstrap.Modal(document.getElementById('PostErrorModal'), {})
            myModal.show();
        },

        <?php

        return ob_get_clean();
    }
}