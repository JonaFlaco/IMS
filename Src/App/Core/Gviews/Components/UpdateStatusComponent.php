<?php

namespace App\Core\Gviews\Components;

class UpdateStatusComponent {

    private $viewData;
    private $ctypeObj;
    public function __construct($viewData, $ctypeObj) {
        $this->viewData = $viewData;    
        $this->ctypeObj = $ctypeObj;
    }

    public function generateModal(){
        
        ob_start(); ?>

        <!-- Update Status Modal Modal -->
        <update-status-component 
            v-if="updateStatusItems.length > 0" 
            ctype-id="<?= $this->ctypeObj->id ?>"
            :records="updateStatusItems"
            :update-to="updateStatusToStatus"
            @clean-up="updateStatusItems = []"
            @after-update="afterUpdateStatus"
            >
        </update-status-component>


        <?php

        return ob_get_clean();
    }

    public function generateMethods() : ?string {

        ob_start(); ?>

        updateStatus(item){

            this.updateStatusItems = [];

            this.updateStatusItems.push({
                    id: item.<?= $this->ctypeObj->id ?>_id_main, 
                    title: item.<?= $this->ctypeObj->id . "_" . (empty($this->ctypeObj->display_field_name) || $this->ctypeObj->display_field_name == "id" ? "id_main" : $this->ctypeObj->display_field_name) ?>, 
                });

            this.updateStatusToStatus = null;
            this.updateStatusIsSingle = true;
        },
        afterUpdateStatus(item) {
            
            this.records.filter((x) => x.<?= $this->ctypeObj->id ?>_id_main == item.id).forEach((x) => {
                x.<?= $this->ctypeObj->id ?>_status_id_id = item.status.id;
                x.<?= $this->ctypeObj->id ?>_status_id = item.status.name;
                x.<?= $this->ctypeObj->id ?>_status_id_style = item.status.style;
            });
            
        },
            
        <?php

        return ob_get_clean();

    }

    public function getDataObject(){
        
        $result = [];

        $result["updateStatusItems"] = [];
        $result["updateStatusIsSingle"] = null;
        $result["updateStatusToStatus"] = null;
        
        return $result;

    }

    public function generateButtonActions(){

        ob_start();
        foreach($this->viewData->actions as $button){
            if(empty($button->update_status_id)){
                continue;
            }
            ?>
            
            update_status_<?= $button->name ?>(is_justification_required,is_actual_date_required){
                
                let found = 0;
                this.updateStatusItems = [];

                this.records.filter((itm) => itm.is_selected).forEach((itm) => {

                    this.updateStatusItems.push(
                        {
                            id: itm.<?= $this->ctypeObj->id ?>_id_main, 
                            title: itm.<?= $this->ctypeObj->id . "_" . (empty($this->ctypeObj->display_field_name) || $this->ctypeObj->display_field_name == "id" ? "id_main" : $this->ctypeObj->display_field_name) ?>, 
                        }
                    );

                    found++;

                });

                if(found == 0)
                    $.toast({
                        heading: 'Error',
                        text: 'Please select records first',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                else {
                    
                    this.updateStatusToStatus = <?= $button->update_status_id ?>;
                    this.updateStatusIsSingle = false;
                    
                }
                
                
            },
                
            <?php
            
        }

        return ob_get_clean();
    }
}