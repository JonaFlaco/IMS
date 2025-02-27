<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class UpdateStatusComponent {

    private $ctypeObj;
    private $isEditMode;
    private $recordData;

    public function __construct($ctypeObj, $isEditMode, $recordData) {
        $this->ctypeObj = $ctypeObj;
        $this->isEditMode = $isEditMode;
        $this->recordData = $recordData;
    }

    public function generateModal() : ?string {
        
        
        if($this->isEditMode != true || $this->ctypeObj->use_generic_status != true){
            return null;
        }

        ob_start(); ?>

        <!-- Update Status Modal Modal -->
        <update-status-component 
            v-if="updateStatusItems.length > 0" 
            ctype-id="<?= $this->ctypeObj->id ?>"
            :records="updateStatusItems"
            @clean-up="updateStatusItems = []"
            @after-update="afterUpdateStatus"
            >
        </update-status-component>
        
        <?php

        return ob_get_clean();

    }

    public function generateButton() : ?string {
        
        
        if($this->isEditMode != true || $this->ctypeObj->use_generic_status != true){
            return "";
        }

        $statusTitle = t("Status");
        $changeTitle = t("Change");

        return <<<HTML

        <span class="p-1" :class="status.style">
            $statusTitle:
            {{ status.name }}
            <a 
                href="javascript: void(0);"
                class="ms-2 hide_on_print"
                @click="updateStatus()" 
                >
                <span class="text-white">
                    <strong>
                        <i class="mdi mdi-format-list-bulleted"> </i> 
                        $changeTitle
                    </strong>
                </span>
            </a>
        </span>
        
        HTML;

    }


    public function getDataObject(){
        $result = [];

        $result["updateStatusItems"] = [];

        return $result;
    }

    
    public function generateMethods() : ?string {
        
        if($this->isEditMode != true || $this->ctypeObj->use_generic_status != true) {
            return null;
        }
    
        ob_start();
        ?>

        updateStatus(){

            //check if form is valid
            this.$refs.form.classList.add('was-validated');
            this.form_validated = true;
            
            if (!this.$refs.form.checkValidity()) {
                $.toast({
                    heading: 'Error',
                    text: 'Please enter valid values',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
                
                return;
            }
            
            this.updateStatusItems = [];

            this.updateStatusItems.push({
                id: this.id, 
                title: this.<?= (!empty($this->ctypeObj->display_field_name) ? $this->ctypeObj->display_field_name : "id") ?>, 
            });
        },
        afterUpdateStatus(item) {
        
            this.status.id = item.status.id;
            this.status.name = item.status.name;
            this.status.style = item.status.style;

            this.status_id = item.status.id;
            this.status_id_display = item.status.name;
            
        },

        <?php

        return ob_get_clean();
    }


}