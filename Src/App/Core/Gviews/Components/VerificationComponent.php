<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class VerificationComponent {


    private $ctypeObj;

    public function __construct($ctypeObj) {
        $this->ctypeObj = $ctypeObj;    
    }

    public function generateMethod(){
        
        ob_start(); ?>

        update_verification(value){
            
            let self = this;
      
            let count = this.records.filter(x => x.is_selected).length;

            if(count == 0) {
                $.toast({
                    heading: 'Error',
                    text: 'No record is selected',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
                return;
            }

            if(confirm('Are you you want to ' + (value ? 'verify' : 'unverify') + ' ' + count + ' record(s)?') != true) {
                return;
            }

            self.show_loading_popup((value ? 'Verifying' : 'Unverifying') + ' ' + count + ' record(s)');

            this.records.filter(x => x.is_selected).forEach(function(itm){
            
                if(itm.<?=  $this->ctypeObj->id ?>_is_verified == value)
                    return;

                axios({
                    method: 'post',
                    url: '/InternalApi/updateverification/' + itm.<?=  $this->ctypeObj->id ?>_id_main + '?value=' + value + '&ctype_id=<?= $this->ctypeObj->id ?>&response_format=json',
                    data:null,
                    headers: {
                        'Content-Type': 'form-data',
                        'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("verification") ?>',
                    }
                })
                .then(function(response){
                    if(response.data.status == 'success'){
                    
                        $.toast({
                            heading: 'Success',
                            text: 'Status changed successfuly',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });

                        itm.<?=  $this->ctypeObj->id ?>_is_verified = value;

                    } else if(response.data.status == 'failed') {
                                                    
                        $.toast({
                            heading: 'Failed',
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

            });
  
        },
        
        <?php

        return ob_get_clean();
    }

    public function generateButtons($button){

        ob_start(); ?>

        <a @click='update_verification(<?= ($button->method == "[VERIFY]" ? "1" : "0") ?>)' href="javascript: void(0);" class="dropdown-item <?= $button->style ?>">
            <i class="mdi mdi-check-circle<?= ($button->method == "[UNVERIFY]" ? "-outline" : "") ?> "></i>
            <?= $button->title ?>
        </a>
        
        <?php

        return ob_get_clean();

        
    }

}