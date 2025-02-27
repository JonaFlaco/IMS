<?php

namespace App\Core\Gviews\Components;

class BulkDeleteComponent {

    private $ctypeObj;
    private $permissions;

    public function __construct($ctypeObj, $permissions) {
        $this->ctypeObj = $ctypeObj;
        $this->permissions = $permissions;
    }

    public function generateModal(){
        
        ob_start(); ?>

        <!-- Bulk Delete -->
        <div id="bulkDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="danger-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-danger">
                        <h4 class="modal-title" id="danger-header-modalLabel">Eliminar</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>

                    <div class="modal-body">
                        <h5 class="mt-0">Estas seguro de eliminar {{records.filter((e)=>e.is_selected == true).length}} registros en <?= $this->ctypeObj->name ?></h5>    
                    </div>

                    <div class="modal-footer">
                        <button type="button" :disabled="deleteLoading == 1" class="btn btn-light" data-bs-dismiss="modal">No, Cancelar</button>
                        <button :disabled="deleteLoading == 1" type="button" @click="deleteSelected(1)" class="btn btn-danger">
                            <span v-if="deleteLoading == 1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Si, Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php

        return ob_get_clean();
    }

    public function generateMethod(){

        if($this->permissions->allow_delete !== true){
            return null;
        }
        
        ob_start();

        ?>
            
        deleteSelected(confirmed = 0){
            let selected_ids = this.records.filter(x=>x.is_selected == true).map(x=>x.<?= $this->ctypeObj->id ?>_id_main);
            
            if(selected_ids == null || selected_ids.length == 0){
                alert('<?= t("Please select records first") ?>');
                return;
            } else {
    
                if(confirmed != 1){
                    $("#bulkDeleteModal").modal("show");
                } else {
    
                    let self = this;
        
                    self.deleteLoading = true;
    
                    var formData = new FormData();
                    formData.append('id_list', selected_ids);
                    formData.append('ctype_id', '<?= $this->ctypeObj->id ?>');
                    axios({
                        method: 'post',
                        url: '/InternalApi/deleteRecord/?response_format=json',
                        data:formData,
                        headers: {
                            'Content-Type': 'form-data',
                            'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("delete_" . $this->ctypeObj->id) ?>',
                        }
                    })
                    .then(function(response){
    
                        self.deleteLoading = false;
    
                        if(response.data.status == 'success'){
                            
                            $.toast({
                                heading: 'success',
                                text: 'Finished',
                                showHideTransition: 'slide',
                                position: 'top-right',
                                icon: 'success'
                            });
    
                            $("#bulkDeleteModal").modal("hide");
                            self.records.filter(x=>x.is_selected == true).forEach(function(itm){
                                itm.is_selected = false;
                            });
                            self.filter();
                        } else {
                            
                            self.loadingLog = false;
            
                            if(response.data.message != null && response.data.message.length > 0){
    
                                $.toast({
                                    heading: 'error',
                                    text: response.data.message,
                                    showHideTransition: 'slide',
                                    position: 'top-right',
                                    icon: 'error'
                                });
            
                            } else {
                                $.toast({
                                    heading: 'error',
                                    text: 'Something went wrong',
                                    showHideTransition: 'slide',
                                    position: 'top-right',
                                    icon: 'error'
                                });
                
                            }
                        }
                        
                    })
                    .catch(function(error){
            
                        self.deleteLoading = false;
                        
                            
                        if(error.response != undefined && error.response.data.status == 'failed') {
                            $.toast({
                                heading: 'Error',
                                text: error.response.data.message,
                                showHideTransition: 'slide',
                                position: 'top-right',
                                icon: 'error'
                            });
                        } else {
                            $.toast({heading: 'Error',text: '<?= t("Something went wrong") ?>',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                        }
                        self.hide_loading_popup();
                    });
    
                }
            }
        },
        
        <?php
        
        return ob_get_clean();
    }

    public function generateButton(){

        ob_start();

        if($this->permissions->allow_delete == true): ?>
                
            <div class="dropdown-divider"></div>

            <a class="dropdown-item text-danger" @click='deleteSelected(0)' href="javascript: void(0);">
                <i class="mdi mdi-trash-can"></i> 
                <span><?= t("Eliminar Seleccionados") ?></span>
            </a>

        <?php endif;

        return ob_get_clean();

    }

    public function getDataObject(){
        $result = [];

        $result["deleteLoading"] = false;

        return $result;
        
    }

}