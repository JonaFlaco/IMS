<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class JustificationForEditComponent {

    private $ctypeObj;
    private $isEditMode;

    public function __construct($ctypeObj, $isEditMode) {
        $this->ctypeObj = $ctypeObj;
        $this->isEditMode = $isEditMode;
    }

    public function generateModal() : ?string {
        
        if($this->isEditMode !== true){
            return null;
        }

        ob_start(); ?>


        <!-- Edit Justification Modal -->
        <div id="editJustificationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
            <form id='form_edit_justification' ref="form_edit_justification" v-on:submit.prevent class="was-validated" enctype="multipart/form-data" novalidate  autocomplete="off">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header modal-colored-header bg-dark">
                            <h4 class="modal-title" id="dark-header-modalLabel"><?= t("Justificación") ?></h4>
                        </div>
                        <div class="modal-body">

                            <div>
                                <label class="form-label" for="editJustification"><?= t("Por favor, escribe la justificación para esta edición.") ?></label>
                                <textarea :disabled="SaveButtonLoading" <?= ($this->ctypeObj->justification_for_edit_is_required == true ? "required" : "") ?>
                                    v-model="editJustification" class="form-control rounded-0" ref="editJustification" row="5"id="editJustification"></textarea>
                                <div class="invalid-feedback">
                                <?= t("Justificacion") . t("ㅤrequerida") ?>
                                </div>
                            </div>
                            

                        </div>
                        <div class="modal-footer">
                            <button :disabled="SaveButtonLoading" type="button" class="btn btn-light" data-bs-dismiss="modal"><?= t("Cerrar") ?></button>
                            <button v-if="goToNew != 1" :disabled="SaveButtonLoading" @click="postDataAction(0)" class="btn btn-success">
                                <i v-if="!SaveButtonLoading" class="mdi mdi-content-save"></i> 
                                <span v-if="SaveButtonLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <?= t("Guardar") ?>
                                </button>
                            <button v-if="goToNew == 1" :disabled="SaveButtonLoading" @click="postDataAction(1)" class="btn btn-success">
                                <i v-if="!SaveButtonLoading" class="mdi mdi-content-save"></i> 
                                <span v-if="SaveButtonLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <?= t("Guardar & Crear Nuevo") ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>



        <?php

        return ob_get_clean();


    }

    public function getDataObject(){
        
        $result = [];

        $result['editJustification'] = '';

        return $result;
    }

}