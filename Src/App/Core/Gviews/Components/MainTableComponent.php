<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;

class MainTableComponent {

    private $viewData;
    private $permissions;

    private $ctypeObj;
    private $coreModel;
    private $fields;

    public function __construct($viewData, $ctypeObj, $permissions) {
        $this->viewData = $viewData;
        $this->permissions = $permissions;

        $this->coreModel = Application::getInstance()->coreModel;
        $this->ctypeObj = (new Ctype)->load($this->viewData->ctype_id);
        $this->fields = (new CtypeField)->loadByCtypeId($this->viewData->ctype_id); 
    }

    public function generate(){

        ob_start(); ?>

    <div v-if="records && records.length > 0" class="content-container">
        <div class="table-container">
            <table id="basic-datatable" class="table table-hover table-centered mb-0">
                <thead class="bg-primary text-white header-custom">
                    <tr>
                        <?= $this->generateTableHeaders() ?>
                    </tr>

                </thead>
                <tbody>
                    
                    <tr @click="do_toggle_single_checkbox(rec.row_number)" v-for="rec in records">
                        <?= $this->generateTableRecords() ?>
                    </tr>
                
                </tbody>
            </table>
        </div>
    </div>

        <?php

        return ob_get_clean();
    }


    private function generateTableHeaders() {

        $result = "";

        //Checkbox column header
        $result .= sprintf('<th class="pt-0 pb-0 ps-1 pe-1 text-center"><div class="form-check form-checkbox-primary"> <input type="checkbox" @change="do_toggle_checkbox" v-model="toggle_checkbox" class="form-check-input" id="customChecktoggle"> <label class="form-check-label" for="customChecktoggle"></label> </div></th>');

        //Row number column header
        // $result .= sprintf('<th class="pt-1 pb-1 ps-1 pe-1 text-center">#</th>');

        
        //Record actions column header
        $result .= sprintf('<th class="pt-1 pb-1 ps-1 pe-1 text-center"></th>');

        //Other column's header
        foreach($this->viewData->fields as $field){

            if($field->is_hidden == true){
                continue;
            }

            $visibility_style = 'style="display: table-cell;"';
            if(isset($_COOKIE[$this->viewData->id . "_col_" . $field->id]) && $_COOKIE[$this->viewData->id . "_col_" . $field->id] == "0"){
                $visibility_style = 'style="display: none;"';
            }

            if(isset($field->field_name)){
                $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($field->ctype_id, $field->field_name);
            } else {
                $thisField = null;
            }

            if(isset($thisField) && ($thisField->field_type_id == "field_collection" || $thisField->field_type_id == "button" || (\App\Core\Application::getInstance()->user->isAdmin() != true && $thisField->is_hidden_updated_read == true)))
                continue;

            // if(isset($thisField) && $thisField->is_hidden_updated_read){
            //     continue;
            // }

            $text_align_id = "";
            if($field->text_align_id == "center"){
                $text_align_id = "text-center";
            } else if($field->text_align_id == "right"){
                $text_align_id = "text-end";
            } else if($field->text_align_id == "justify"){
                $text_align_id = "text-justify";
            }

            $lang = \App\Core\Application::getInstance()->user->getLangId();
            
            $title_field_name = "title";
            
            if(empty($lang) && isset($field->custom_title)) {
                $title_field_name = "custom_title";
            } else {
                if(isset($field->{"custom_title_" . $lang}))
                    $title_field_name = "custom_title_" . $lang;
                else if(isset($thisField->{"title_" . $lang}))
                        $title_field_name = "title_" . $lang;
                
            }

            
            if(isset($field->{$title_field_name}) && _strlen($field->{$title_field_name}) > 0){
                $result .= sprintf('<th %s id="col_%s_head" class="pt-1 pb-1 ps-1 pe-1 %s"> %s </th>',$visibility_style, $field->id, $text_align_id, $field->{$title_field_name});
            } else 
                $result .= sprintf('<th %s id="col_%s_head" class="pt-1 pb-1 ps-1 pe-1 %s"> %s </th>', $visibility_style,$field->id, $text_align_id, $thisField->title);

        }


        return $result;

    }

    private function generateRecordActions() {
        
        $result = "";
        foreach($this->viewData->fields as $field){
            if($field->is_hidden == true){
                continue;
            }
            $thisField = $this->coreModel->getFields($field->ctype_id,null,$field->field_name); 
            $thisField = $thisField[0];

            if($thisField->field_type_id == "button" && (\App\Core\Application::getInstance()->user->isAdmin() == true || $thisField->is_hidden_updated_read != true)){
                $result .= sprintf('<button class="dropdown-item" @click="run%s(rec.%s_id_main)">%s</button>', $thisField->name, $this->ctypeObj->id, $thisField->title);
            }
        }

        if(isset($result) && _strlen($result) > 0){
            $result = '<div class="dropdown-divider"></div> ' . $result;
        }

        return $result;
    }

    public function generateMethods(){

        ob_start(); ?>

        do_toggle_single_checkbox(row_number){
            //disabled
            return;
            this.records.forEach(function(itm){
                if(itm.row_number == row_number){
                    itm.is_selected = (itm.is_selected == true ? false : true);
                }
            });
        },
        do_toggle_checkbox(){
            let self = this;
            this.records.forEach(function(itm){
                itm.is_selected = self.toggle_checkbox == true ? true : false;
            });
        },

        <?php       
    
        foreach($this->viewData->fields as $field){

            if($field->is_hidden)
                continue;

            $thisField = null;
            if(isset($field->field_name)){
                $thisField = $this->coreModel->getFields($field->ctype_id,null,$field->field_name); 
                $thisField = $thisField[0];
            }

            if(!isset($thisField) || $thisField->field_type_id != "button")
                continue;


            $method = $thisField->method;
            $method = _str_replace("[CTYPEID]",$field->ctype_id ?? $this->ctypeObj->id,$method);
            
            ?>
            
            run<?= $thisField->name ?>(id){
                
                let method = '<?= $method ?>';
                method = method.replace('[ID]',id);
                
                window.open('/' + method, '_blank');

            },

        <?php } 

        return ob_get_clean();
        
    }

    
    private function generateRowActions(){
        ob_start(); ?>

        <td class="pt-0 pb-0 ps-1 pe-1 text-center table-action">
            <div>

                <div class="btn-group dropend">
                    <button type="button" class="btn text-primary link dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= t("Opciones") ?>
                    </button>
                    <ul class="dropdown-menu" role="menu" style="height:auto; max-height:100px; overflow-x:hidden;<?= (\App\Core\Application::getInstance()->user->getLangDirection() == "rtl" ? " text-align: right !important;" : "") ?>">
                        <?php if($this->ctypeObj->disable_read != true && ($this->permissions->allow_read == 1 || $this->permissions->allow_read_only_your_own_records == 1)): ?>
                            <button class="dropdown-item" @click="showRecord(rec.<?= $this->ctypeObj->id ?>_id_main)"><i class="mdi mdi-eye-outline"></i> <?= t("Ver Registro") ?></button>
                        <?php endif;
                        if($this->ctypeObj->disable_edit != true && ($this->permissions->allow_edit == 1 || $this->permissions->allow_edit_only_your_own_records == 1)): ?>
                            <button class="dropdown-item" @click="editRecord(rec.<?= $this->ctypeObj->id ?>_id_main)"><i class="mdi mdi-square-edit-outline"></i> <?= t("Editar Registro") ?> </button>
                        <?php endif; ?>

                        <?php if($this->permissions->allow_view_log): ?>
                            <button class="dropdown-item" @click="showLog(rec.<?= $this->ctypeObj->id ?>_id_main)"><i class="mdi mdi-history"></i> <?= t("Ver Log") ?></button>
                        <?php endif; ?>

                        <?php if($this->ctypeObj->disable_delete != true && ($this->permissions->allow_delete == 1 || $this->permissions->allow_delete_only_your_own_records == 1)): ?>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item text-danger" @click="deleteRecord(rec.<?= $this->ctypeObj->id ?>_id_main)"><i class="mdi mdi-delete"></i> <?= t("Eliminar Registro") ?></button>
                        <?php endif; ?>
                        
                        <?= $this->generateRecordActions() ?>
                        
                    </ul>
                </div>
                
            </div>
        </td>

        <?php

        return ob_get_clean();
        
    }

    private function generateTableRecords(){

        $result = "";
         
        //Checkbox column
        $result .= sprintf('<td class="pt-0 pb-0 ps-1 pe-1 text-center">');
        $result .= sprintf('<div class="form-check form-checkbox-primary"> <input type="checkbox" v-model="rec.is_selected" class="form-check-input" :id="\'customCheck\' + rec.row_number"> <label class="form-check-label" :for="\'customCheck\' + rec.row_number"></label> </div>', $this->ctypeObj->id);
        $result .= sprintf('</td>');
        
        //Row Number column
        // $result .= sprintf('<td class="pt-0 pb-0 ps-1 pe-1 text-center">{{rec.row_number}}</td>');

        
        //Actions column
        $result .= $this->generateRowActions();

        //Generate the rest of columns
        foreach($this->viewData->fields as $field){
            if($field->is_hidden == true){
                continue;
            }

            $visibility_style = 'style="display: table-cell;"';
            if(isset($_COOKIE[$this->viewData->id . "_col_" . $field->id]) && $_COOKIE[$this->viewData->id . "_col_" . $field->id] == "0"){
                $visibility_style = 'style="display: none;"';
            }

            $text_align_id = "";
            if($field->text_align_id == "center"){
                $text_align_id = "text-center";
            } else if($field->text_align_id == "right"){
                $text_align_id = "text-end";
            } else if($field->text_align_id == "justify"){
                $text_align_id = "text-justify";
            }

            $thisField = null;
            if(isset($field->field_name)){
                $thisField = $this->coreModel->getFields($field->ctype_id,null,$field->field_name); 
                $thisField = $thisField[0];
            }

            if(isset($thisField) && ($thisField->field_type_id == "field_collection" || $thisField->field_type_id == "button" || (\App\Core\Application::getInstance()->user->isAdmin() != true && $thisField->is_hidden_updated_read == true)))
                continue;

            // if(isset($thisField) && $thisField->is_hidden_updated_read){
            //     continue;
            // }

            $varCtype = $this->ctypeObj;
            
            if(isset($field->ctype_id) && _strlen($field->ctype_id) > 0){
                $varCtype = (new Ctype)->load($field->ctype_id);
            }

            $fieldFullName = $varCtype->id . "_" . ($thisField == null ? get_machine_name($field->custom_title) : $thisField->name);

            $link_url_opening = "";
            $link_url_end = "";
            if($field->is_link == true){
                
                $mode = "show";
                if($field->link_to_ctype_mode == "edit"){
                    $mode = "edit";
                }
                
                if($field->link_to_ctype_type == "field" || $thisField->field_type_id == "relation"){
                    $link_url_opening = sprintf('<a class="text-normal" :href="\'%s/%s/\' + rec.%s_%s_id" target="_blank">', $thisField->data_source_table_name,$mode, $this->ctypeObj->id, $field->field_name);
                } else {
                    $link_url_opening = sprintf('<a class="text-primary" :href="\'/%s/%s/\' + rec.%s_id_main" target="_blank">', $this->ctypeObj->id, $mode,$this->ctypeObj->id);
                }

                $link_url_end = '<i v-if="rec.' . $fieldFullName . '" class="ms-1 mdi mdi-open-in-new"></i></a>';

            }


            
            $have_cond = false;
            $conditional_formatting_result = "";
            if(isset($field->conditional_formatting)){
                $conditional_formatting = json_decode($field->conditional_formatting);
                $count = 0;
                foreach($conditional_formatting as $cond){
                    if($cond->operator == "equal"){
                        $conditional_formatting_result .= "<div v-" . ($count++ > 0 ? "else-" : "") . "if=\"rec.$fieldFullName == '$cond->value'\" class=\"$cond->style ps-1 pe-1\" >
                        $link_url_opening
                        {{data}}
                        $link_url_end
                        </div>";
                    } else if($cond->operator == "between"){
                        $conditional_formatting_result .= "<div v-" . ($count++ > 0 ? "else-" : "") . "if=\"rec.$fieldFullName >= $cond->value && rec.$fieldFullName <= " . $cond->value_2 . "\" class=\"$cond->style  ps-1 pe-1\" >
                        $link_url_opening
                        {{rec.$fieldFullName}}
                        $link_url_end
                        </div>";
                    } else if($cond->operator == "greater"){
                        $conditional_formatting_result .= "<div v-" . ($count++ > 0 ? "else-" : "") . "if=\"rec.$fieldFullName > $cond->value\" class=\"$cond->style  ps-1 pe-1\" >
                        $link_url_opening
                        {{rec.$fieldFullName}}
                        $link_url_end
                        </div>";
                    } else if($cond->operator == "greater_or_equal"){
                        $conditional_formatting_result .= "<div v-" . ($count++ > 0 ? "else-" : "") . "if=\"rec.$fieldFullName >= $cond->value\" class=\"$cond->style  ps-1 pe-1\" >
                        $link_url_opening
                        {{rec.$fieldFullName}}
                        $link_url_end
                        </div>";
                    } else if($cond->operator == "less"){
                        $conditional_formatting_result .= "<div v-" . ($count++ > 0 ? "else-" : "") . "if=\"rec.$fieldFullName < $cond->value\" class=\"$cond->style  ps-1 pe-1\" >
                        $link_url_opening
                        {{rec.$fieldFullName}}
                        $link_url_end
                        </div>";
                    } else if($cond->operator == "less_or_equal"){
                        $conditional_formatting_result .= "<div v-" . ($count++ > 0 ? "else-" : "") . "if=\"rec.$fieldFullName <= $cond->value\" class=\"$cond->style  ps-1 pe-1\" >
                        $link_url_opening
                        {{rec.$fieldFullName}}
                        $link_url_end
                        </div>";
                    }


                    $have_cond = true;
                }
            }
                

            if(isset($thisField) && $thisField->data_source_table_name == "users" && $field->add_special_effects == true){
                
                $result .= sprintf('<td %s class="pt-0 pb-0 ps-1 pe-1 col_%s table-user">',$visibility_style, $field->id);
                $result .= sprintf('<a :href="\'/users/show/\' + rec.%s_id" class="text-normal" target="_blank">',$fieldFullName);
                $result .= sprintf('<img v-if="rec.%s_profile_picture_name" alt="" :src="\'/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=\' + rec.%s_profile_picture_name" alt="table-user" class="rounded-circle me-1">', $fieldFullName , $fieldFullName);
                $result .= sprintf('{{rec.%s}}', $fieldFullName);
                $result .= sprintf('<i class="ms-1 mdi mdi-open-in-new"></i>');
                $result .= sprintf('</a>');
                $result .= sprintf('</td>');

            } else if(isset($thisField) && $thisField->name == "is_verified" && $field->add_special_effects == true){
                $result .= sprintf('<td %s class="pt-0 pb-0 ps-1 pe-1 ps-0 col_%s %s\">', $visibility_style, $field->id, $text_align_id);
                $result .= $link_url_opening;
                $result .= sprintf('<span v-if="rec.%s == 1" class="ps-1 pe-1 text-success">', $fieldFullName);
                $result .= sprintf('<i class=" mdi mdi-check-circle"></i>');
                $result .= sprintf('Si');
                $result .= sprintf('</span>');
                $result .= sprintf('<span v-if="rec.%s != 1" class="ps-1 pe-1 text-danger">', $fieldFullName);
                $result .= sprintf('<i class=" mdi mdi-do-not-disturb"></i>');
                $result .= sprintf('No');
                $result .= sprintf('</span>');
                $result .= $link_url_end;
                $result .= sprintf('</td>');
            } else if(isset($thisField) && $thisField->name == "status_id" && $field->add_special_effects == true){
                
                ob_start(); ?>
                    <td <?= $visibility_style ?> class="pt-0 pb-0 ps-1 pe-1 col_<?= $field->id ?> <?= $text_align_id ?>">
                        
                        <button 
                            @click="updateStatus(rec)" 
                            class="btn btn-sm btn-link" 
                            :class="rec.<?= $this->ctypeObj->id ?>_status_id_style" 
                            role="button" 
                            type="button">
                            {{rec.<?= $this->ctypeObj->id ?>_status_id}}
                        </button>
                        
                    </td>

                    <?php

                    $result .= ob_get_clean();

            } else if(isset($thisField) && $thisField->field_type_id == "boolean" && $have_cond != true) {
                if($field->add_special_effects) {
                    $result .= sprintf('<td %s class="pt-0 pb-0 ps-1 pe-1 col_%s %s">', $visibility_style, $field->id, $text_align_id);
                    $result .= $link_url_opening;
                    $result .= sprintf('<span v-if="rec.%s == 1" class="badge bg-success">Si</span>', $fieldFullName);
                    $result .= sprintf('<span v-else class="badge bg-danger">No</span>', $fieldFullName);
                    $result .= $link_url_end;
                    $result .= sprintf('</td>');
                } else {
                    $result .= sprintf('<td %s class="pt-0 pb-0 ps-1 pe-1 col_%s %s">', $visibility_style, $field->id, $text_align_id);
                    $result .= $link_url_opening;
                    $result .= sprintf('{{ rec.%s == 1 ? "Si" : "No" }}', $fieldFullName);
                    $result .= $link_url_end;
                    $result .= sprintf('</td>');
                }

            } else if(isset($thisField) && $thisField->field_type_id == "boolean" && $have_cond) {

                $result .= sprintf('<td %s class="pt-0 pb-0 ps-1 pe-1 col_%s %s">', $visibility_style, $field->id, $text_align_id);
                
                $conditional_formatting_result = _str_replace("{{data}}", sprintf('{{ rec.%s == 1 ? "Si" : "No" }}', $fieldFullName), $conditional_formatting_result);
                
                $result .= $conditional_formatting_result;
                $result .= sprintf('<div %s>', ($have_cond == true ? "v-else" : ""));
                $result .= $link_url_opening;
                $result .= sprintf('<span style="white-space: pre-wrap;">{{ rec.%s == 1 ? "Si" : "No" }}</span>', $fieldFullName);
                $result .= $link_url_end;
                $result .= sprintf('</div>');
                $result .= sprintf('</td>');

            } else if(isset($thisField) && $thisField->field_type_id == "media" && $thisField->is_multi != true){

                $result .= sprintf('<td %s class="pt-0 pb-0 ps-1 pe-1 col_%s %s">', $visibility_style, $field->id, $text_align_id);

                if($field->is_link != true) {
                    $result .= sprintf('<a class="text-normal" :href="rec.%s_name" target="_blank">', $fieldFullName);
                } else {
                    $result .= sprintf('<a class="text-normal" :href="\'/%s/show/\' + rec.%s_id_main" target="_blank">', $this->ctypeObj->id, $this->ctypeObj->id);
                }
                
                $result .= sprintf('<img alt="" width="100" height="100" v-if="rec.%s_thumb != null" :src="rec.%s_thumb" >', $fieldFullName, $fieldFullName);
                $result .= sprintf('<img alt="" width="100" height="100" v-if="rec.%s_thumb == null" src="/assets/app/images/icons/image.png">', $fieldFullName);
                $result .= sprintf('</a>');
                $result .= sprintf('</td>');
            } else {
                $result .= sprintf('<td %s class="pt-0 pb-0 ps-1 pe-1 col_%s %s">', $visibility_style, $field->id, $text_align_id);
                
                $conditional_formatting_result = _str_replace("{{data}}", "{{rec.$fieldFullName}}", $conditional_formatting_result);
                
                $result .= $conditional_formatting_result;
                $result .= sprintf('<div %s>', ($have_cond == true ? "v-else" : ""));
                $result .= $link_url_opening;
                $result .= sprintf('<span style="white-space: pre-wrap;">{{rec.%s}}</span>', $fieldFullName);
                $result .= $link_url_end;
                $result .= sprintf('</div>');
                $result .= sprintf('</td>');
            }

        }


        return $result;
        
    }


    public function getDataObject(){

        $result = [];
        
        $result["toggle_checkbox"] = false;
        $result["records"] = [];

        return $result;
        
    }


    public function watchScript(){
        ob_start(); ?>

        records:{      
                handler: function(after, before){
                    let total = after.length;
                    let no_of_selected = 0;
                    
                    after.forEach(function(itm){
                        
                        if(itm.is_selected == true){
                            no_of_selected += 1;
                        }
                    });
                    
                    
                    if(no_of_selected == 0){
                        this.selected_status = '';
                    } else {
                        this.selected_status = no_of_selected + ' of ' + total;
                    }
                    
                },
                deep: true
            },

        <?php

        return ob_get_clean();
    }
    
}