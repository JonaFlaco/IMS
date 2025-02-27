<?php

namespace App\Core\Gctypes\Components;

class jsHelperMethods {

    public function __construct() {
     
    }

    public function generate($js_system_variables = []){
        
        ob_start(); ?>

        dep_selected: function(fc_name, field_name, value, operator = '='){
            
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

                //check system variables
                let sys_vars = <?= json_encode($js_system_variables) ?>;
                

                let target_value = null;
                
                if(this[field_name] != undefined) {
                    target_value = this[field_name];
                }
                
                if(sys_vars[field_name] != undefined) {
                    target_value = sys_vars[field_name]
                }

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
            }
        },
        cus_eval(p1, p2, operator = '='){
            
            if(operator == '='){
                return p1 == p2;
            } else if(operator == '!='){
                return p1 != p2;
            } else if(operator == '>'){
                return p1 > p2;
            } else if(operator == '>='){
                return p1 >= p2;
            } else if(operator == '<'){
                return p1 < p2;
            } else if(operator == '<='){
                return p1 <= p2;
            }

        },

        val_selected(fc_name, field_name, value, operator = '=', fn = 'value'){
        
            if(fc_name.length > 0) {
                
                let target_value = this[fc_name][field_name];

                if(target_value != null && typeof target_value == 'object'){
                    target_value = target_value.id;
                }

                if(Array.isArray(target_value)){
                    
                    if(fn == 'value'){
                        if(target_value.includes(value))
                            return true;
                        else
                            return false;
                    } else if (fn == 'count'){
                        alert('');
                    }
                } else {
                    
                    var field_value = target_value;
                    if(!isNaN(field_value)){
                        field_value = Number(field_value);
                    }

                    if(this.cus_eval(field_value, value, operator)){
                        //alert(field_value + ' ' + operator + ' ' + value + ' is true');
                        return true;
                    } else {
                        //alert(field_value + ' ' + operator + ' ' + value + ' is false');
                        return false;
                    }

                }

            } else {

                let target_value = this[field_name];

                if(target_value != null && typeof target_value == 'object'){
                    target_value = target_value.id;
                }

                if(Array.isArray(target_value)){
                    
                    if(fn == 'value'){
                        if(target_value.includes(value))
                            return true;
                        else
                            return false;
                    } else if (fn == 'count'){
                        if(this.cus_eval(target_value.length, value, operator)){
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else {
                    
                    var field_value = target_value;
                    if(!isNaN(field_value)){
                        field_value = Number(field_value);
                    }

                    if(this.cus_eval(field_value, value, operator)){
                        //alert(field_value + ' ' + operator + ' ' + value + ' is true');
                        return true;
                    } else {
                        //alert(field_value + ' ' + operator + ' ' + value + ' is false');
                        return false;
                    }


                }
            }
        },

        <?php

        return ob_get_clean();
        
    }



}
