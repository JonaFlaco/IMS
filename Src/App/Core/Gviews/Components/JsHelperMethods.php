<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class jsHelperMethods {

    public function __construct() {
        
    }

    public function generate(){
        
        ob_start(); ?>

        
        dep_selected(fc_name, field_name, value){
            
            if(fc_name.length > 0) {
            
                let target_value = this[fc_name][field_name];

                if(target_value != null && typeof target_value == 'object'){
                    target_value = target_value.id;
                }
                if(value == '' && target_value == null){
                    return true;
                }
                
                if(Array.isArray(target_value)){
                    
                    if(target_value.includes(value))
                        return true;
                    else
                        return false;
                } else {
                    
                    if(target_value == value)
                        return true;
                    else
                        return false;
                }

            } else {

                let target_value = this[field_name];
                
                if(target_value != null && typeof target_value == 'object'){
                    target_value = target_value.id;
                }

                if(value == '' && target_value == null){
                    return true;
                }

                if(Array.isArray(target_value)){
                    
                    if(target_value.includes(value))
                        return true;
                    else
                        return false;
                } else {
                    
                    if(target_value == value)
                        return true;
                    else
                        return false;
                }
            }
        },

        <?php

        return ob_get_clean();
    }

}