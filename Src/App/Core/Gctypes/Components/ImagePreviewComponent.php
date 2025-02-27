<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class ImagePreviewComponent {

    private $ctypeObj;
    private $ctypePermissionObj;
    private $recordData;

    private $isGuest;

    public function __construct($ctypeObj, $ctypePermissionObj, $recordData) {
        $this->ctypeObj = $ctypeObj;
        $this->ctypePermissionObj = $ctypePermissionObj;
        $this->recordData = $recordData;

        $this->isGuest = Application::getInstance()->user->isGuest();

    }

    public function generateModal() : ?string{
        
        ob_start(); ?>



        <!-- Image preview Modal -->
        <div class="modal fade" id="ImagePreviewModal" tabindex="-1" style="z-index: 1060;" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h5 class="modal-title" ><?= t("Image Preview") ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    
                        <?php if(!$this->isGuest): ?>
                        <div class="alert alert-warning" role="alert">
                            <i class="dripicons-warning me-2"></i> <?= t("Rotation will take effect immeditely no need to re-save the content. but the change might appear after refreshing the page") ?>
                        </div>
                        <?php endif; ?>

                        <span v-if="loading_rotate" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <img id="imgPreview" style="max-width:100%;"/>

                    </div>
                    <div class="modal-footer">
                        <?php if(!$this->isGuest): ?>
                        <button type="button" class="btn btn-primary" @click="rotateImage(0)"><i class="mdi mdi-rotate-right"></i> <?= t("Rotate") ?></button>
                        <button type="button" class="btn btn-primary" @click="rotateImage(1)"><i class="mdi mdi-rotate-left"></i> <?= t("Rotate") ?></button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?= t("Cerrar") ?></button>
                    </div>
                </div>
            </div>
        </div>



        <?php

        return ob_get_clean();
    }

    public function generateMethods(){

        ob_start(); ?>

        previewImage: function(ctype_id, field_name, file_name) {
            
            this.rotate_image_name = file_name;
            this.rotate_image_ctype_id = ctype_id;
            this.rotate_image_field_name = field_name;
            this.showRotationWarning = false;
            img = document.getElementById("imgPreview");
            img.src = '/filedownload?ctype_id=' + ctype_id + '&field_name=' + field_name + '&file_name=' + file_name;

            var myModal = new bootstrap.Modal(document.getElementById('ImagePreviewModal'), {})
            myModal.show();
        },
        
        rotateImage(direction){
            let self = this
            self.loading_rotate = true;

            axios.post( '/fileserver/rotate/' + self.rotate_image_name + '?ctype_id=<?= $this->ctypeObj->id ?>&direction=' + direction + '&response_format=json',
            ).then(function(response){
                if(response.data.status == 'success'){
                    self.showRotationWarning = true;
                    img = document.getElementById("imgPreview");
                    img.src = '/filedownload?ctype_id=' + self.rotate_image_ctype_id + '&field_name=' + self.rotate_image_field_name + '&file_name=' + self.rotate_image_name;
                    
                    self.loading_rotate = false;    
                } else {
                    $.toast({
                        heading: 'Error',
                        text: response.data.message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });                            }
                    self.loading_rotate = false;
                    
            }).catch(function(error){

                if(error.response != undefined && error.response.data.status == 'failed'){
                    document.getElementById('error-modal-body').innerHTML = '<p>' + error.response.data.message + '</p>';
                } else {
                    document.getElementById('error-modal-body').innerHTML = '<p>' + error + '</p>';
                }

                this.showErrorDialog();
                self.loading_rotate = false;
            });
        },
        
        <?php

        return ob_get_clean();
    }

    public function getDataObject(){

        $result = [];

        $result["loading_rotate"] = false;
        $result["rotate_image_name"] = '';
        $result["rotate_image_field_id"] = '';

        return $result;
        
    }

}