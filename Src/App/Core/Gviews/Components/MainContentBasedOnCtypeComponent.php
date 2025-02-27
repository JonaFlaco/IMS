<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class MainContentBasedOnCtypeComponent {

    private $ctypeObj;
    private $fields;
    private $permissions;

    public function __construct($ctypeObj, $fields, $permissions) {
        $this->ctypeObj = $ctypeObj;
        $this->fields = $fields;
        $this->permissions = $permissions;
    }

    public function generate(){
        
        ob_start(); ?>

        <div v-if="records && records.length > 0 " class="table-responsive">
            <table  id="basic-datatable" class="table table-hover table-centered mb-0">
                <thead>
                    <tr>
                        <!-- <th class="p-0 text-align">#</th> -->

                        <th class="p-0 text-center"></th> 
                                            
                        <?php foreach($this->fields as $field){

                            if(($field->field_type_id == "media" && $field->is_multi == true) || $field->field_type_id == "field_collection" || $field->field_type_id == "button"  || ($field->is_system_field == true && !in_array($field->name, ["id","created_date"])))
                                continue;
                            ?>

                            <th class="pt-1 pb-1 ps-1 pe-1"><?= $field->title ?></th>
                        <?php } ?>

                        

                    </tr>
                </thead>
                <tbody>
                    
                    <tr v-for="rec in records">

                
                    <?php
                    $custom_actions = "";
                    foreach($this->fields as $field){
                        if($field->field_type_id == "button"  && $field->is_hidden != true){
                            $custom_actions .= "<button class=\"dropdown-item\" @click=\"run$field->name(rec." . $this->ctypeObj->id . "_id)\">$field->title</button>\n";
                        }
                    }

                    if(isset($custom_actions) && _strlen($custom_actions) > 0){
                        $custom_actions = "<div class=\"dropdown-divider\"></div> " . $custom_actions;
                    }

                    ?>
                    
                    <td class="p-0  text-center table-action">
                        <div>

                            <div class="btn-group dropend">
                                <button type="button" class="btn text-primary link dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?= t("Optiones") ?>
                                </button>
                                <ul class="dropdown-menu" role="menu" style="height:auto; max-height:100px; overflow-x:hidden;<?= (\App\Core\Application::getInstance()->user->getLangDirection() == "rtl" ? " text-align: right !important;" : "") ?>">
                                    
                                    <?php if($this->ctypeObj->disable_read != true && $this->permissions->allow_read == 1): ?>
                                        <button class="dropdown-item" @click="showRecord(rec.id_main)"><i class="mdi mdi-eye-outline"></i> <?= t("Mirar Registro") ?></button>
                                    <?php endif; ?>

                                    <?php if($this->ctypeObj->disable_edit != true && $this->permissions->allow_edit == 1): ?>
                                        <button class="dropdown-item" @click="editRecord(rec.id_main)"><i class="mdi mdi-square-edit-outline"></i> <?= t("Editar Registro") ?></button>
                                    <?php endif; ?>
                                    
                                    <?php if($this->permissions->allow_view_log): ?>
                                        <button class="dropdown-item" @click="showLog(rec.<?= $this->ctypeObj->id ?>_id_main)"><i class="mdi mdi-history"></i> <?= t("Mirar Log") ?></button>
                                    <?php endif; ?>

                                    <div class="dropdown-divider"></div>

                                    <?php if($this->ctypeObj->disable_delete != true && $this->permissions->allow_delete == 1): ?>
                                        <button class="dropdown-item text-danger" @click="deleteRecord(rec.id_main)"><i class="mdi mdi-delete"></i> <?= t("Eliminar Registro") ?></button>
                                    <?php endif; ?>
                                    
                                    <?= $custom_actions ?>
                                </ul>
                            </div>
                            
                        </div>
                    </td>
                    
                    
                    <!-- <td class="p-0 text-align">{{rec.row_number}}</td> -->

                    <?php foreach($this->fields as $field){

                        if(($field->field_type_id == "media" && $field->is_multi == true) || $field->field_type_id == "field_collection" || $field->field_type_id == "button" || ($field->is_system_field == true && !in_array($field->name, ["id","created_date"])))
                            continue;

                        if(!$this->ctypeObj->disable_edit && $field->name == "id"): ?>
                            <td class="pt-0 pb-0 ps-1 pe-1">
                                <a :href="'/<?= $this->ctypeObj->id ?>/edit/' + rec.<?= $field->name ?>" target="_blank" class="text-primary">
                                    <span style="white-space: pre-wrap;"> {{ rec.<?= $field->name ?> }} </span>
                                    <i class="ms-1 mdi mdi-open-in-new"></i>
                            </a>
                        <?php elseif($field->field_type_id == "boolean"): ?>
                            <td class="pt-0 pb-0 ps-1 pe-1">
                                {{ rec.<?= $field->name ?> == 1 ? 'Si' : 'No' }}
                            </td>
                        <?php elseif ($field->field_type_id == "media" && $field->is_multi != true): ?>
                            <td class="pt-0 pb-0 ps-1 pe-1">{{rec.<?= $field->name ?>_name}}</td>
                        <?php else: ?>
                            <td class="pt-0 pb-0 ps-1 pe-1">{{rec.<?= $field->name ?>}}</td> 
                        <?php endif; ?>
                    <?php } ?>


                    

                </tr>
                
                </tbody>
            </table>
            
        </div>

        <?php

        return ob_get_clean();

    }

    public function getDataObject(){
        $result = [];

        $result["records"] = [];

        return $result;
    }

}