<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class GenericExportComponent {

    private $viewData;
    private $permissions;
    private FilterationPanelComponent $filterationPanelComponent;
    private $ctypeObj;
    public function __construct($viewData, $ctypeObj, $permissions, $filterationPanelComponent = null) {
        $this->viewData = $viewData;
        $this->ctypeObj = $ctypeObj;
        $this->permissions = $permissions;
        $this->filterationPanelComponent = $filterationPanelComponent;
    }

    public function generateMethods(){
        
        if($this->permissions->allow_generic_export != true){
            return null;
        }

        ob_start(); ?>

        exportgenericxls(){
            this.show_loading_popup('<?= t("Exporting Generic Excel, please wait") ?>...');
            
            let self = this;
            
            let selected_ids = '';
            this.records.forEach(function(itm){
                
                if(itm.is_selected == true){
                    if(selected_ids.length > 0)
                        selected_ids += ',';

                    selected_ids += itm.<?= $this->ctypeObj->id ?>_id_main;
                }
            });

            <?= $this->filterationPanelComponent->getFilterObject(); ?>


            axios({
                    method: 'post',
                    url: '/GViews/gexport/<?= $this->viewData->id ?>?response_format=json',
                    data:formData,
                headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){
                    
                    if(response.data.status == 'success'){
                        //window.location.replace('/filedownload?temp=1&file_name=' + response.data.fileName  , '_blank')
                        $.toast({heading: 'Success',text: response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'success'});
                        topBarVm.show_bg_tasks_modal();
                    } else {
                        
                        self.loadingLog = false;
        
                        if(response.data.message != null && response.data.message.length > 0){

                            $.toast({heading: 'Error',text: response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});
        
                        } else {
                            
                            $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
            
                        }
                        
                    }

                    self.hide_loading_popup();
                                        
                })
                .catch(function(error){
                    
                    if(error.response != undefined && error.response.data.status == 'failed') {
                        $.toast({
                            heading: 'Error',
                            text: error.response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    } else {
                        $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    }
                    self.hide_loading_popup();
                });
        },
        
        <?php
        
        return ob_get_clean();
    }

    public function generateMethodsBasedOnCtype(){
        
        ob_start(); ?>


        exportgenericxls(){
            
            
            let self = this;
        
            this.show_loading_popup('<?= t("Exporting Generic Excel, please wait") ?>...');
            var formData = new FormData();
        
            axios({
                    method: 'post',
                    url: '/dataexport/export/<?= $this->ctypeObj->id ?>?response_format=json',
                    data:formData,
                headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){
                
                    if(response.data.status == 'success'){
                        //window.location.replace('/filedownload?temp=1&file_name=' + response.data.fileName  , '_blank')
                        $.toast({heading: 'Success',text: response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'success'});
                        topBarVm.show_bg_tasks_modal();
                    } else {
                        
                        self.loadingLog = false;
        
                        if(response.data.message != null && response.data.message.length > 0){
        
                            $.toast({heading: 'Error',text: response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});
        
                        } else {
                            
                            $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
            
                        }
                        
                    }
        
                    self.hide_loading_popup();
                                    
                })
                .catch(function(error){
                    $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    self.hide_loading_popup();
                });
        },

        <?php

        return ob_get_clean();

    }
}