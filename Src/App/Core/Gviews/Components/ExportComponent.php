<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;
use App\Core\Gctypes\Ctype;

class ExportComponent {

    private $viewData;
    private $permissions;
    private FilterationPanelComponent $filterationPanelComponent;
    private $coreModel;
    private $ctypeObj;
    public function __construct($viewData, $permissions, $filterationPanelComponent) {
        $this->viewData = $viewData;
        $this->permissions = $permissions;
        $this->filterationPanelComponent = $filterationPanelComponent;

        $this->coreModel = Application::getInstance()->coreModel;
        $this->ctypeObj = (new Ctype)->load($this->viewData->ctype_id);
    }

    public function generateMethods(){
        
        ob_start(); ?>
        
        
        exportxls(view_id, file_name, export_type){
            if(view_id == undefined)
                view_id = <?= $this->viewData->id ?>;
            this.exportXlsButtonLoading = true;
            this.exportXlsViewId = view_id;
            this.show_loading_popup('<?= t("Exporting, please wait") ?>...');
            
            let self = this;
            let file_extension = 'xlsx'
            if(export_type == '<?= EXPORT_CSV_ID ?>'){
                file_extension = 'csv';
            }

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
                    url: '/GViews/export/' + view_id + '?response_format=json',
                    data:formData,
                    
                    headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){
                    
                    //const url = window.URL.createObjectURL(new Blob([response.data]));
                    //const link = document.createElement('a');
                    //link.href = url;
                    //link.setAttribute('download', file_name + '.' + file_extension);
                    //document.body.appendChild(link);
                    //link.click();

                    $.toast({heading: 'Success',text: response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'success'});

                    self.exportXlsButtonLoading = false;
                    self.hide_loading_popup();

                    topBarVm.show_bg_tasks_modal();
                    
                                        
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
                    
                    self.exportXlsButtonLoading = false;
                    self.hide_loading_popup();
                });
                
        },
        
        <?php

        return ob_get_clean();
    }

    public function getDataObject(){
        
        $result = [];

        $result["exportXlsButtonLoading"] = false;
        $result["exportXlsViewId"] = false;

        return $result;
    }

}