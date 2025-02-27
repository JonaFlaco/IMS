<?php

namespace App\Core\Gdashboards\Components;

use App\Core\Application;

class jsHelperMethods {

    public function __construct() {
        
    }

    public function generate(){
        
        ob_start(); ?>

        
        dep_selected(fc_name, field_name, value, operator = '='){
            
            if(fc_name.length > 0) {
    
                let target_value = this[fc_name][field_name];
    
                if(target_value != null && typeof target_value == 'object' && target_value.id != undefined){
                    target_value = target_value.id;
                }
    
                if(value == '' && target_value == null){
                    value = null;
                }
                
                if(Array.isArray(target_value)){
                    if(target_value.length > 0 && target_value[0].id != undefined){
                        let value_map = target_value.map(a => a.id);
                        if(value_map != undefined && value_map != null && value_map.includes(value)){
                            return true;
                        } else {
                            return false;
                        }
                        
                    }
                    
                
                    if(target_value.includes(value))
                        return true;
                    else
                        return false;
                } else {
                    
                    if(operator == '='){
                        if(target_value == value)
                            return true;
                        else
                            return false;
                    } else if (operator == '!='){
                        if(target_value != value)
                            return true;
                        else
                            return false;
                    } else if (operator == '>'){
                        if(target_value > value)
                            return true;
                        else
                            return false;
                    } else if (operator == '<'){
                        if(target_value < value)
                            return true;
                        else
                            return false;
                    } else if (operator == '>='){
                        if(target_value >= value)
                            return true;
                        else
                            return false;
                    } else if (operator == '<='){
                        if(target_value <= value)
                            return true;
                        else
                            return false;
                    }
                }
    
            } else {
    
                
                let target_value = this[field_name];
                
                if(target_value != null && typeof target_value == 'object' && target_value.id != undefined){
                    target_value = target_value.id;
                }
    
                if(value == '' && target_value == null){
                    value = null;
                }
                
                if(Array.isArray(target_value)){
                    if(target_value.length > 0 && target_value[0].id != undefined){
                        let value_map = target_value.map(a => a.id);
                        if(value_map != undefined && value_map != null && value_map.includes(value)){
                            return true;
                        } else {
                            return false;
                        }
                        
                    } else {
                        target_value = '';
                    }
                
                    if(target_value.includes(value))
                        return true;
                    else
                        return false;
                } else {
                    
                    if(operator == '='){
                        if(target_value == value)
                            return true;
                        else
                            return false;
                    } else if (operator == '!='){
                        if(target_value != value)
                            return true;
                        else
                            return false;
                    } else if (operator == '>'){
                        if(target_value > value)
                            return true;
                        else
                            return false;
                    } else if (operator == '<'){
                        if(target_value < value)
                            return true;
                        else
                            return false;
                    } else if (operator == '>='){
                        if(target_value >= value)
                            return true;
                        else
                            return false;
                    } else if (operator == '<='){
                        if(target_value <= value)
                            return true;
                        else
                            return false;
                    }
    
                }
            }
        },

        <?php

        return ob_get_clean();
    }

}