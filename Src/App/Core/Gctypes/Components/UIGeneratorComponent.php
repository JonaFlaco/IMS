<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;
use App\Core\Gctypes\Components\FormComponents\DateComponent;
use App\Core\Gctypes\Components\FormComponents\NoteComponent;
use App\Core\Gctypes\Components\FormComponents\TextComponent;
use App\Core\Gctypes\Components\FormComponents\BooleanComponent;
use App\Core\Gctypes\Components\FormComponents\NumberComponent;
use App\Core\Gctypes\Components\FormComponents\DecimalComponent;
use App\Core\Gctypes\Components\FormComponents\ComboboxComponent;
use App\Core\Gctypes\Components\FormComponents\AttachmentComponent;
use App\Core\Gctypes\Components\FormComponents\BaseComponent;
use App\Core\Gctypes\CtypesHelper;

class UIGeneratorComponent {

    private $mainCtypeObj;
    private $ctypePermissionObj;
    private $recordData;
    private array $fields;
    private $isEditMode;
    private $isSurvey;
    private $fcName;
    private $prefix;

    private ?string $lang;
    private $langDir;
    private $coreModel;
    private array $fieldCollectionUseDragDrop = [];
    private $loadVueDraggable;
    private CtypesHelper $ctypesHelper;
    
    public function __construct($mainCtypeObj, $fields, $ctypePermissionObj, $isEditMode, $isSurvey, $recordData = null, $fcName = null, $prefix = null) {
        
        $this->mainCtypeObj = $mainCtypeObj;
        $this->fields = $fields;
        $this->ctypePermissionObj = $ctypePermissionObj;
        
        $this->isEditMode = $isEditMode;
        $this->isSurvey = $isSurvey;
        $this->recordData = $recordData;
        $this->fcName = $fcName;
        $this->prefix = $prefix;

        $this->ctypesHelper = new CtypesHelper();
        
        $this->langDir = Application::getInstance()->user->getLangDirection();
        $this->lang = Application::getInstance()->user->getLangId();

        $this->coreModel = Application::getInstance()->coreModel;
        

        foreach($this->fields as $field){
            
            if($field->field_type_id != "field_collection")
                continue;

            $fcObj = $this->coreModel->nodeModel("ctypes")
                ->id($field->data_source_id)
                ->loadFirstOrFail();

            array_push($this->fieldCollectionUseDragDrop, array("id" => $field->data_source_id, "allow_drag_drop_to_sort" => $fcObj->allow_drag_drop_to_sort));

            if($fcObj->allow_drag_drop_to_sort == true){
                $this->loadVueDraggable = true;
            }
        }
    }



    
    public function generate() : string {
        
        $result = "";

        $form_name = "form" . (empty($this->fcName) ? "" : "_" . $this->fcName);
        
        $result .= "<form ref=\"" . $form_name . "\" v-on:submit.prevent enctype=\"multipart/form-data\" novalidate autocomplete=\"off\">";
        

        $tabs = $this->getTabs();
        
        if(sizeof($tabs) > 1) {
            $result .= $this->generateTabs($tabs);
            $result .= "<div class=\"tab-content\">";
        }


        $current_group = "-EMPTY-";
        $current_tab = "-EMPTY-";
        $current_location = "-EMPTY-";
        
        
        $top = "";
        $left = "";
        $right = "";
        $bottom = "";

        $tmp = "";
        $tmp2 = "";

        $tab_index = 0;

        foreach($this->fields as $field){

            $tmp = "";
            $tmp2 = "";

            if($this->isEditMode && $field->is_hidden_updated_edit == true)
                continue;
            if($this->isEditMode !== true && $field->is_hidden_updated_add == true)
                continue;
            if($field->is_hidden_updated == true || $field->field_type_id == "button")
                continue;

            $tab_name = (is_null($field->tab_name) ? TPL_DEFAULT_TAB_NAME : $field->tab_name);
            $group_name = (is_null($field->group_name) ? TPL_DEFAULT_GROUP_NAME : $field->group_name);
            $location = is_null($field->location) ? 'top' : $field->location;

            if(sizeof($tabs) > 1){

                if( $current_tab != $tab_name) {
                    
                    $current_group = "-EMPTY-";
                    $current_location = "-EMPTY-";

                    if($current_tab != "-EMPTY-"){
                        
                        $result .= $this->generateui_sub($top, $left, $right, $bottom);
                        $top = "";
                        $left = "";
                        $right = "";
                        $bottom = "";

                        $result .= "</div>";
                        
                    }

                    $current_tab = $tab_name;
                    
                    $result .= "
                        <div class=\"tab-pane" . ($tab_index == 0 ? " show active" : "") . "\" id=\"" . (!empty($this->prefix) ? $this->prefix . "_" : "") . _strtolower(_str_replace(" ","_",$tab_name)) . "\">
                            ";

                    $tab_index++;
                }
            }

        
            if( $current_group != $group_name || $current_location != $location) {
                
                if($current_group != "-EMPTY-" || $current_location != "-EMPTY-"){
                    
                    $x = "";

                    $x .= "</div></div></div></div></div>";
                    
                    if($current_location != $location)
                        $tmp2 .= $x;
                    else
                        $tmp .= $x;
                }

                $current_group = $group_name;
                $current_location = $location;

                $current_group_display = t($current_group);


                
                $x = "";
            
                $current_group_visibility = "";
                foreach($this->fields as $xField){
                    
                        
                    if($this->isEditMode && $xField->is_hidden_updated_edit == true)
                        continue;
                    if($this->isEditMode !== true && $xField->is_hidden_updated_add == true)
                        continue;


                    $xFieldTabName = is_null($xField->tab_name) ? TPL_DEFAULT_TAB_NAME : $xField->tab_name;
                    $xFieldGroupName = is_null($xField->group_name) ? Application::getInstance()->settings->get('TPL_DEFAULT_GROUP_NAME') : $xField->group_name;
                    $xFieldLocationName = is_null($xField->location) ? 'top' : $xField->location;

                    $xCurrentTabName = ($current_tab == "-EMPTY-" ? TPL_DEFAULT_TAB_NAME : $current_tab);
                    $xCurrentGroupName = ($current_group == "-EMPTY-" ? Application::getInstance()->settings->get('TPL_DEFAULT_GROUP_NAME') : $current_group);

                    if($xFieldTabName == $xCurrentTabName && $xFieldLocationName == $location && $xFieldGroupName == $xCurrentGroupName){
                        
                        if(!empty($xField->dependencies)){
                            if(!empty($current_group_visibility))
                                $current_group_visibility .= " || ";
                            $current_group_visibility .= "computed_" . (!empty($this->prefix) ? $this->prefix . "_" : "") . $xField->name;
                        } else {
                            $current_group_visibility = "";
                            break;
                        }
                    }
                    
                }
                

                $x .= "
                    <div class=\"row\" " . (!empty($current_group_visibility) ? "v-if=\"$current_group_visibility\"" : "") . ">
                        <div class=\"col-12\">
                            <div class=\"card ribbon-box\">
                                <div class=\"card-body\">
                                    
                                    <div id=\"card_title" . get_machine_name($current_tab . $current_location . $current_group) . "\" class=\"ribbon ribbon-primary float-start\"> 
                                        <a class=\"text-white p-0 m-0\" data-bs-toggle=\"collapse\" href=\"#" . get_machine_name($current_tab . $current_location . $current_group) . "\" role=\"button\" aria-expanded=\"false\" aria-controls=\"" . get_machine_name($current_tab . $current_location . $current_group) . "\">
                                            " . (!empty($current_group_display) ? $current_group_display : "Uncategorized") . "
                                        </a>
                                    </div>
                                    
                                    <div id=\"" . get_machine_name($current_tab . $current_location . $current_group) . "\" class=\"ribbon-content row collapse " . (!empty($current_group) ? " pt-3 " : "") . " show\">
                                
                    ";
                
                    $tmp .= $x;
                
            }
            
            
            $isCustom = false;
            if(_strlen($this->mainCtypeObj->extends) > 0) {
                
                $prefixReady = _strlen($this->prefix) > 0 ? _str_replace('current_', '', $this->prefix) . '-' : '';
                
                $componentName = to_snake_case($prefixReady . $field->name) . '-component';
                
                if(_strpos($this->mainCtypeObj->extends, 'id="tpl-' . $componentName . '"') !== false){
                    
                    $dataPath = (empty($this->prefix)  ? "" : $this->prefix . "." ) . $field->name;
                    if($field->field_type_id == "media") {
                        $dataPath = 'var_' . $dataPath;
                    }

                    $baseComponent = new BaseComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix);

                    $tmp .= sprintf('<div %s class="col-md-%s pb-1 mb-3"> 
                                        <%s 
                                            ref="%s" 
                                            title="%s"
                                            name="%s"
                                            v-model="%s" 
                                            :is-required="%s"
                                            :is-read-only="%s"
                                            v-if="%s"
                                            >
                                        </%s> 
                                    </div>', 
                        $baseComponent->dependancy(),
                        $field->size, 
                        $componentName, 
                        (_str_replace('-','_',$prefixReady) . $field->name) . 'Component', 
                        $field->title,
                        (_str_replace('-','_',$prefixReady) . $field->name), 
                        $baseComponent->dataPath(),
                        $baseComponent->isRequired(),
                        $baseComponent->isReadOnly(true),
                        empty($this->prefix) ? "true" : "$this->prefix && Object.keys($this->prefix).length > 0",
                        $componentName);

                    $isCustom = true;
                }
            }

            if(!$isCustom) {

                switch($field->field_type_id){
                    case "text": //Text
                        $tmp .= (new TextComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix))->generate();
                        break;
                    case "relation": //ComboBox
                        $tmp .= (new ComboboxComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix))->generate();
                        break;
                    case "field_collection": //FieldCollection
                        $tmp .= $this->generateFieldCollection($field);
                        break;
                    case "date": //Date
                        $tmp .= (new DateComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix))->generate();
                        break;
                    case "media": //Attachment
                        $tmp .= (new AttachmentComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix))->generate();
                        break;
                    case "number": //Number
                        $tmp .= (new NumberComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix))->generate();
                        break;
                    case "decimal": //Decimal
                        $tmp .= (new DecimalComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix))->generate();
                        break;
                    case "boolean": //Boolean
                        $tmp .= (new BooleanComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix))->generate();
                        break;
                    case "button": //Button
                        break;  
                    case "note": //Note
                        $tmp .= (new NoteComponent($this->mainCtypeObj, $field, $this->isEditMode, $this->isSurvey, $this->prefix))->generate();
                        break;
                }
            }

            if($location == "left")
                $left .= $tmp;
            else if ($location == "right")
                $right .= $tmp;
            else if ($location == "bottom")
                $bottom .= $tmp;
            else
                $top .= $tmp;

        }

        $result .= $this->generateui_sub($top, $left, $right, $bottom);

        if(sizeof($tabs) > 1) { 
            $result .= "</div></div>";
        }

        $result .= "</form>";

        return $result;

    }


    private function generateui_sub($top, $left, $right,$bottom){
        
        $return_value = "
                <div begin_row class=\"row\">";

        if(!empty($top)){
            $return_value .= "
                    <div col_top class=\"col-md-12\">
                        $top
                        " ;

            $return_value .= "</div></div></div></div></div>";
        
                        $return_value .= "
                    
            </div>
            ";
        }

        if(!empty($left) || !empty($right)){
            $return_value .= "
            <div left_right_row class=\"col-md-12\"><div class=\"row\">
            ";
        }


        if(!empty($left) || !empty($right)){

            $return_value .= "
            <div left_col class=\"col-md-6\">
                
                        $left
                        " ;

            if(!empty($left)){
            
                    $return_value .= "</div></div></div></div></div>";
                
            }

                        $return_value .= "
            </div>
            ";
        }

    
        if(!empty($left) || !empty($right)){

            $return_value .= "
            <div right_col class=\"col-md-6\">
                
                        $right
                        " ;

            if(!empty($right)){
            
                    $return_value .= "</div></div></div></div></div>";
            
            }

                        $return_value .= "
                    
            </div>
            ";
        }


        if(!empty($left) || !empty($right)){
            $return_value .= "
            </div></div>
            ";
        }

        if(!empty($bottom)){
            $return_value .= "
            <div bottom_col class=\"col-md-12\">
                
                        $bottom
                        " ;

       
            $return_value .= "</div></div></div></div></div>";
       
                        $return_value .= "
                    
            </div>
        ";
        }


        $return_value .= "
        </div>
        ";

        return $return_value;
    }

    
    private function generateTabs(array $tabs = []) : string {
        
        ob_start();

        $tab_index = 0;
        ?>

        <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
        
        <?php foreach($tabs as $tab){
            
            
            $current_tab_visibility = "";
            foreach($this->fields as $xField){
                
                if($this->isEditMode && $xField->is_hidden_updated_edit == true)
                    continue;
                if($this->isEditMode !== true && $xField->is_hidden_updated_add == true)
                    continue;

                $xFieldTabName = is_null($xField->tab_name) ? TPL_DEFAULT_TAB_NAME : $xField->tab_name;
                $xCurrentTabName = ($tab == "-EMPTY-" ? TPL_DEFAULT_TAB_NAME : $tab);

                if($xFieldTabName == $xCurrentTabName){
                    
                    if(!empty($xField->dependencies)){
                        if(!empty($current_tab_visibility))
                            $current_tab_visibility .= " || ";
                        $current_tab_visibility .= "computed_" . (!empty($this->prefix) ? $this->prefix . "_" : "") . $xField->name;
                    } else {
                        $current_tab_visibility = "";
                        break;
                    }
                }
                
            }
            
            ?>
            
            <li class="nav-item" <?= (!empty($current_group_visibility) ? "v-if=\"$current_group_visibility\"" : "") ?>>
                <a  href="#<?= (!empty($this->prefix) ? $this->prefix . "_" : "") . _strtolower(_str_replace(" ","_",$tab))?>" 
                    data-bs-toggle="tab" aria-expanded="false" 
                    class="nav-link rounded-0 <?= $tab_index == 0 ? " active" : ""?>">
                    <i class="mdi mdi-home-variant d-lg-none d-block me-1"></i>
                    <span class="d-none d-lg-block"><?= $tab ?></span>
                </a>
            </li>
            
            <?php $tab_index++ ?>

        <?php } ?>
        
        </ul>

        <?php

        return ob_get_clean();
    }



    private function getTabs() : array {

        $result = [];
        
        foreach($this->fields as $field){

            if($this->isEditMode && $field->is_hidden_updated_edit == true)
                continue;
            if($this->isEditMode !== true && $field->is_hidden_updated_add == true)
                continue;

            $tab_name = (!empty($field->tab_name) ? $field->tab_name : TPL_DEFAULT_TAB_NAME);
            
            if(in_array($tab_name, $result) !== true){
                $result[] = $tab_name;
            }
        }

        return $result;
    }


    private function generateFieldCollection($field) : string {


        if($field->use_parent_permissions == true){
            $FcPermissionObj = $this->ctypePermissionObj;
        } else {
            $FcPermissionObj = Application::getInstance()->user->getCtypePermission($field->data_source_id);
        }

        $canAdd = $this->isSurvey || $FcPermissionObj->allow_add;
        $canEdit = $this->isSurvey || $FcPermissionObj->allow_edit;
        $canDelete = $this->isSurvey || $FcPermissionObj->allow_delete || $FcPermissionObj->allow_edit;

        $use_drag_drop_to_sort = false;
        foreach($this->fieldCollectionUseDragDrop as $itm){
            if($itm["id"] == $field->data_source_id){
                $use_drag_drop_to_sort = $itm["allow_drag_drop_to_sort"];
            }
        }

        $dependency = empty($field->dependencies) ? '' : sprintf(' v-if="computed_%s" ',(!empty($this->prefix) ? $this->prefix . "_" : "") . $field->name);
        
        ob_start(); ?>

        <div class="col-md-<?= $field->size ?> pt-1 pb-1" 
            <?= $dependency ?> 
            id="div_<?= (!empty($this->prefix) ? $this->prefix . "_" : "") . $field->name ?>">

            <?php

            $isCustom = false;
            if(_strlen($this->mainCtypeObj->extends) > 0) {

                $componentName = to_snake_case($field->name) . '-list-component';
                
                if(_strpos($this->mainCtypeObj->extends,'id="tpl-' . $componentName . '"') !== false){
                    
                    // return sprintf('<div class="col-md-%s p-0"> <%s v-model="%s" title="%s"></%s> </div>', 
                    //     $field->size, 
                    //     $componentName, 
                    //     $field->name,
                    //     $field->title,
                    //     $componentName);

                    ?>
                    
                    <div class="col-md-<?= $field->size ?> p-0"> <<?= $componentName ?> ref="<?= $field->name . "Component" ?>" v-model="<?= $field->name ?>" title="<?= $field->title ?>"></<?= $componentName ?>> </div>
                    
                    <?php

                    $isCustom = true;
                }
            }

            if(!$isCustom) { ?>
                
                <div class="card border border-other p-0">
                    <div class="row pt-1 pe-1">
                        <div class="col-sm-8">

                        
                            <a data-bs-toggle="collapse" href="#tbl_<?= $field->name ?>" role="button" aria-expanded="false" aria-controls="tbl_<?= $field->name ?>" class="p-0 m-0 collapsed">
                                <div class="card-widgets float-start">
                                    <a data-bs-toggle="collapse" href="#tbl_<?= $field->name ?>" role="button" aria-expanded="true" aria-controls="tbl_<?= $field->name ?>">
                                        <h5 class="ps-1"><i class="mdi mdi-minus"></i><?= $field->title ?></h5>
                                    </a>
                                </div>    
                                
                            </a>
                        </div>
                        <div class="col-sm-4">
                            <div class="float-end">
                                
                                <p>&nbsp</p>

                            </div>
                        </div>
                        
                    </div>
                    
                    <hr v-if="<?= $field->name ?>.length == 0" class="p-0 m-0 mt-3 mb-1">

                    <div class="card-body p-0 pt-0">
                    
                        <div id="tbl_<?= $field->name ?>" class="table-responsive m-0 show">
                        
                            <div v-if="<?= $field->name ?>.length == 0" class="text-center p-2">
                                <span><i class="mdi mdi-information"></i> <?= t("No hay datos para mostrar") ?></span>
                            </div>
                            <table v-if="<?= $field->name ?>.length > 0" class="table table-hover table-striped table-centered mb-0">
                            <thead>
                            <tr>
                                <th class="p-1"><?= t("Actiones") ?></th>
                            
                            <?php foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $ff){

                                if(($this->isEditMode && $ff->is_hidden_updated_edit == true) || ($this->isEditMode !== true && $ff->is_hidden_updated_add == true) || $ff->hide_in_fc_summary == true || $ff->field_type_id == "media" || $ff->field_type_id == "field_collection") {
                                    continue;
                                }

                                ?>

                                <th class="p-1"><?= $ff->title ?></th>

                            <?php } ?>

                            </tr>
                            </thead>
                            
                            <?php if($use_drag_drop_to_sort == true): ?>
                                <draggable tag="tbody" v-model="<?= $field->name ?>" ghost-class="vuedraggable-ghost" @start="drag=true" handle=".xhandler" @end="onEnd_fc_<?= $field->name ?>">
                            <?php else: ?>
                                <tbody>
                            <?php endif; ?>

                            <tr v-for="item in <?= $field->name . (Application::getInstance()->user->isSuperAdmin() !== true && Application::getInstance()->user->isAdmin() != true ? ".filter((e)=>e.is_system_field !== 1)" : "") ?>" 
                                :key="item.ind" class="col-md-<?= $field->size ?> m-1">
                            
                                <td scope="row" class="table-action p-1">
                                
                                    <?php if($use_drag_drop_to_sort == true): ?>
                                        <i class="text-dark mdi mdi-pan-vertical xhandler" style="cursor: grab;"></i>
                                    <?php endif; ?>
                                    
                                    <?php if($canEdit): ?>
                                        <a href="javascript: void(0);" @click="editRecord<?= $field->name ?>(item.sett_index)" class="action-icon text-primary"><i class="mdi mdi-pencil"></i></a>    
                                    <?php endif; ?>

                                    <?php if($canDelete): ?>
                                        <a href="javascript: void(0);" @click="deleteRecord<?= $field->name ?>(item.sett_index)" class="action-icon text-danger"><i class="mdi mdi-delete"></i></a>   
                                    <?php endif; ?>
                                </td>
                                    
                                <?= $this->generateFieldCollectionRecordsList($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), "item."); ?>
                    
                                </tr>
                            
                            <?php if($use_drag_drop_to_sort == true): ?>
                                </draggable>
                            <?php else: ?>
                                </tbody>
                            <?php endif; ?>
                                
                            </table>
                        </div>
                        <hr class="p-0 m-0 mt-1 mb-1">
                        <div class="row pb-1 pe-1">
                            <div class="col-sm-8" style="margin-top: auto !important; margin-bottom: auto !important;">
                                <span class="ps-1 pt-1"><?= t("Total de Registros") ?>: <span class="text-primary"><strong>{{<?= $field->name ?>.length}}</strong></span></span>
                            </div>
                            <div class="col-sm-4">
                                <div class="float-end">
                                
                                <?php if($canAdd): ?>
                                    <button type="button" @click="addNew<?= $field->name ?>()" class="btn btn-primary">
                                        <i class="dripicons-plus"></i> <?= t("Agregar Registro") ?>
                                    </button>
                                <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <?php } ?>

        </div>

        <?php

        return ob_get_clean();

    }


    private function generateFieldCollectionRecordsList($fields, $prefix=null){

        $result = "";
        
        foreach($fields as $field){

            if(($this->isEditMode && $field->is_hidden_updated_edit == true) || ($this->isEditMode !== true && $field->is_hidden_updated_add == true) || $field->hide_in_fc_summary == true || $field->field_type_id == "media" || $field->field_type_id == "field_collection") {
                continue;
            }

            $fieldFullName = $prefix .  $field->name;

            switch($field->field_type_id){
                case "text": //Text
                    $result .= sprintf('<td class="p-1">{{%s}}</td>', $fieldFullName);
                    break;
                case "decimal": //Decimal
                case "number": //Number
                    $result .= sprintf('<td class="p-1">{{%s == null ? "" : Number(%s).toLocaleString()}}</td>', $fieldFullName, $fieldFullName);
                    break;
                case "relation": //ComboBox
                    
                    if($field->appearance_id == "2_select2"){
                        if($field->is_multi == true){ //Multi
                            $result .= sprintf('<td class="p-1"><span v-for="itm in %s">{{itm.name}}, </span></td>', $fieldFullName);
                        } else { // Single
                            $result .= sprintf('<td class="p-1">{{%s == null ? null : %s.name}}</td>', $fieldFullName, $fieldFullName);
                        }
                    } else {
                        if($field->data_source_value_column_is_text == true){
                            $result .= sprintf('<td class="p-1">{{%s}}</td>', $fieldFullName);
                        } else {
                            if($field->is_multi == true){ //Multi
                                $result .= sprintf('<td class="p-1">{{%s_display}}</td>', $fieldFullName);
                            } else { // Single
                                $result .= sprintf('<td class="p-1">{{%s_display}}</td>', $fieldFullName);
                            }
                        }
                    }
                    break;
                    
                case "field_collection": //FieldCollection
                    break;
                case "date": //Date
                    
                    $result .= sprintf('<td class="p-1">{{%s}}</td>', $fieldFullName);
                    break;
                case "media": //Attachment

                    break;
                case "boolean": //Boolean
                    $result .= sprintf('<td class="p-1" v-if="%s == 1">Si</td>', $fieldFullName);   
                    $result .= sprintf('<td class="p-1" v-if="%s != 1">No</td>', $fieldFullName);   
                
                    break;
                default:
                    break;
        
            }

        }

        

        
        return $result;
       
    }


    public function generateFieldCollectionModal(){
        
        $result = "";

        foreach($this->getFieldByType($this->fields, "field_collection") as $field){
            
            if($field->is_hidden_updated == true)
                continue;

            if($field->use_parent_permissions == true){
                
                $fcPermissionObj = $this->ctypePermissionObj;

            } else {
                
                $fcPermissionObj = Application::getInstance()->user->getCtypePermission($field->data_source_id);

            }

            if($this->isSurvey !== true && $fcPermissionObj->allow_add == false && $fcPermissionObj->allow_edit == false) {
                continue;
            }

            
            $isCustom = false;
            if(_strlen($this->mainCtypeObj->extends) > 0) {
                
                $prefixReady = _strlen($this->prefix) > 0 ? _str_replace('current_', '', $this->prefix) . '-' : '';
                
                $componentName = to_snake_case($prefixReady . $field->name) . '-component';
                
                if(_strpos($this->mainCtypeObj->extends, 'id="tpl-' . $componentName . '"') !== false){
                    $isCustom = true;
                }
            }

            if(!$isCustom) {
                $result .= $this->generateFieldCollectionModalScript($field);
            }
            
        }

        return $result;
    }


    private function generateFieldCollectionModalScript(object $field) : string {
        
        ob_start();

        ?>
        
        <!-- FieldCollection Modal -->
        <div id="<?= $field->name ?>Modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-full-width modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" ><?= $field->title ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true" @click="closeModal<?= $field->name ?>"></button>
                    </div>
                    <div class="modal-body">
                    
                        <?= (new UIGeneratorComponent($this->mainCtypeObj, $this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), $this->ctypePermissionObj, $this->isEditMode, $this->isSurvey, $this->recordData, $field->name, "current_" . $field->name))->generate() ?>

                    </div>
                    <div class="modal-footer">
                        
                        <button :disabled="!<?= $field->name ?>_modal_btn_active" type="button" class="btn btn-secondary" @click="closeModal<?=$field->name ?>()" >
                            <i class="mdi mdi-close"></i> 
                            <?= t("Cerrar") ?>
                        </button>
                        
                        <button :disabled="!<?= $field->name ?>_modal_btn_active" type="button" class="btn btn-danger" @click="deleteRecord<?=$field->name ?>(<?= "current_" . $field->name ?>?.sett_index)" >
                            <i class="mdi mdi-delete"></i> 
                            <?= t("Eliminar") ?>
                        </button>

                        <div class="btn-group">
                            <button 
                                type="button" 
                                class="btn btn-success"
                                :disabled="!<?= $field->name ?>_modal_btn_active"
                                @click="saveModal<?= $field->name ?>()"
                                >
                                <i class="mdi me-1 mdi-content-save"></i>
                                <?= t("Guardar") ?>
                            </button>
                            <button type="button" :disabled="!<?= $field->name ?>_modal_btn_active" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript: void(0);"  @click="saveModal<?= $field->name ?>(1)">
                                    <i class="mdi me-1 mdi-content-save"></i>
                                    <?= t("Save & New") ?>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    
        <?php

        return ob_get_clean();

    }



    public function getDataObject() : array {

        $result = [];

        $result['form_validated'] = false;
        $result['sett_is_update'] = $this->isEditMode();
        foreach($this->fields as $field){

            if($field->name == "id")
                continue;

            if($field->field_type_id == "field_collection"){

                if($field->field_type_id == "field_collection"){
                    $result[$field->name ."_modal_is_open"] = false;
                    $result[$field->name ."_modal_btn_active"] = false;
                }

                if($field->name != "id" && $field->name != "parent_id" && $field->is_system_field == true){
                    continue;
                }

                $result['current_' . $field->name . '_form_validated'] = false;

                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
                    if($fc->field_type_id == "relation" && $fc->appearance_id == "2_select2" && (empty($fc->data_source_filter_by_field_name))){
                        $result['pl_' . $fc->name . '_' . $fc->data_source_table_name] = ($fc->select2_async ? [] : $this->ctypesHelper->getComboboxOptions($fc));
                        $result['keyword_' . $fc->name . '_' . $fc->data_source_table_name] = "";
                        $result['loading_' . $fc->name . '_' . $fc->data_source_table_name] = "";
                    }

                    if(isset($fc->data_source_filter_by_field_name) && _strlen($fc->data_source_filter_by_field_name) > 0){
                        $result['pl_' . $fc->name . '_' . $fc->data_source_table_name] = [];
                        $result['keyword_' . $fc->name . '_' . $fc->data_source_table_name] = "";
                        $result['loading_' . $fc->name . '_' . $fc->data_source_table_name] = false;
                    }
                }
            } else if($field->field_type_id == "relation" && $field->appearance_id == "2_select2" && (empty($field->data_source_filter_by_field_name))){
                $result['pl_' . $field->name . '_' . $field->data_source_table_name] = ($field->select2_async ? [] : $this->ctypesHelper->getComboboxOptions($field));
                $result['keyword_' . $field->name . '_' . $field->data_source_table_name] = "";
                $result['loading_' . $field->name . '_' . $field->data_source_table_name] = "";
            }

        }
        
        foreach($this->fields as $field){

            if($field->id == "id")
                continue;

            if($field->name != "id" && $field->name != "parent_id" && $field->is_system_field == true){
                continue;
            }
            if(isset($field->data_source_filter_by_field_name) && _strlen($field->data_source_filter_by_field_name) > 0){
                $result['pl_' . $field->name . '_' . $field->data_source_table_name] = [];
                $result['keyword_' . $field->name . '_' . $field->data_source_table_name] = false;
                $result['loading_' . $field->name . '_' . $field->data_source_table_name] = false;
            }

            if($field->field_type_id == "media"){
                $result['uploading_' . $field->name] = '';
            }

            if($field->field_type_id == "field_collection"){
                
                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
                    if($fc->name != "id" && $fc->name != "parent_id" && $fc->is_system_field == true){
                        continue;
                    }
                    if($fc->field_type_id == "media"){
                        $result['uploading_current_' . $field->name . '_' . $fc->name] = '';
                    }

                    $result['current_' . $field->name . '_' . $fc->name . '_is_required'] = $fc->is_required;
                    
                    $result['current_' . $field->name . '_' . $fc->name . '_has_error'] = false;
                    
                }
            }

            $result[$field->name . '_is_required'] = $field->is_required;

            $result[$field->name . '_has_error'] = false;
            
        }
        
        foreach($this->fields as $field){

            if($field->name == "id")
                continue;
                
            if($field->name != "id" && $field->name != "parent_id" && $field->name != "status_id"  && $field->is_system_field == true){
                continue;
            }
            
            if(($field->field_type_id == "relation" && $field->is_multi == true) ) {
                
                $result[$field->name] = $this->getValue($this->recordData, $field) ?? [];

            } else if ($field->field_type_id == "field_collection"){

                $use_drag_drop_to_sort = false;
                foreach($this->fieldCollectionUseDragDrop as $itm){
                    if($itm["id"] == $field->data_source_id){
                        $use_drag_drop_to_sort = $itm["allow_drag_drop_to_sort"];
                    }
                }

                $result[$field->name] = $this->getFieldCollectionObject($field->data_source_id, $this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), $use_drag_drop_to_sort);

                $obj = new \StdClass();
                
                foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
                    if($fc->field_type_id == "relation" and $fc->is_multi == true)
                        $obj->{$fc->name} = [];
                }

                $result['current_' . $field->name] = $obj;

                    
            } else if($field->field_type_id == "media" && $field->is_multi == true){
                $result[$field->name] = $this->getValue($this->recordData, $field) ?? [];
            } else if($field->field_type_id == "media" && $field->is_multi != true){
                $result[$field->name] = $this->getValue($this->recordData, $field) ?? [];
            } else if($field->field_type_id == "boolean"){
                
                $result[$field->name] = $this->getValue($this->recordData, $field);

            } else if($field->field_type_id == "date"){
                $result[$field->name] = $this->getValue($this->recordData, $field);
            } else if($field->field_type_id == "relation" && $field->is_multi != true){
                $result[$field->name] = $this->getValue($this->recordData, $field);
            } else {
                $result[$field->name] = $this->getValue($this->recordData, $field);
            }

            if($this->mainCtypeObj->use_generic_status == true && $field->name == "status_id"){
                
                $status_id = $result[$field->name] = $this->getValue($this->recordData, $field);
                
                $statusObj = $this->coreModel->nodeModel("status_list")
                    ->id($status_id)
                    ->loadFirstOrDefault();
                    
                if(isset($statusObj)){
                    $itm = new \stdClass();
                    $itm->id = $status_id;
                    $itm->name = $statusObj->name;
                    $itm->style = $statusObj->style;
                    $result["status"] = $itm;
                }
            }
        }
        
        
        
        foreach($this->fields as $field){
            if($field->name != "id" && $field->name != "parent_id" && $field->name != "status_id" && $field->is_system_field == true){
                continue;
            }
            if($field->field_type_id == "field_collection" ){
                $result['var_' . $field->name] = '';
            } else if($field->field_type_id == "media" ){
                $result['var_' . $field->name] = '';
            }
        }

        return $result;
        
    }

    

    


    private function getValue($data, $field, $show_display = false){

        if($this->isEditMode != true){
            $value = $this->decodeDefaultValue($field, $field->default_value_updated);
            
            if($field->field_type_id == "boolean") {
                if($field->appearance_id == "9_combobox" || $field->appearance_id == "9_list") {
                    return $value;
                } else {
                    return $value == true ? 1 : 0;
                }
            }
                
            return $value;
        }

        // 1. Text
        // 6. Number
        // 7. Decimal
        // 9. Boolean
        if($field->field_type_id == "text" || $field->field_type_id == "number" || $field->field_type_id == "decimal"){
            
            return $data->{$field->name};

        // 2. ComboBox
        } else if ($field->field_type_id == "relation") {
            
            $value = $data->{$field->name};

            if( $field->is_multi == true) {
                    
                if($field->appearance_id == "2_select2"){
                    
                    $result = [];

                    $exp_list = _explode("\n",$data->{$field->name . "_display"});
                    
                    $i = 0;
                    foreach($value as $v){
                    
                        $obj = new \StdClass();
                        $obj->id = $v->value;
                        $obj->name = $exp_list[$i];

                        $result[] = $obj;

                        $i++;

                    }

                } else {

                    if($show_display == true){
                        
                        $result = _str_replace("\n",", ",$data->{$field->name . "_display"});
                        
                    } else {

                        $result = [];

                        foreach(array($value) as $v){

                            if(isset($v)){
                                foreach($v as $e){
                                    $result[] = $e->value;
                                }
                            }
                        }
                    }
                }


                return $result;
            } else {
                
                            
                if($field->appearance_id == "2_select2"){

                    if($show_display == true && $field->data_source_value_column_is_text != true){
                    
                        $result = ($data->{$field->name . "_display"}) ;
                        
                    } else {
                        if(_strlen($value) > 0){

                            $result = new \StdClass();
                            $result->id = $value;
                            $result->name = ($field->data_source_value_column_is_text == true ? $data->{$field->name} : $data->{$field->name . "_display"} );
                            
                        } else {
                            $result = null;
                        }
                    }

                } else {
                    
                    if($show_display == true && $field->data_source_value_column_is_text != true){
                    
                        $result = ($data->{$field->name . "_display"}) ;
                        
                    } else {
                        
                        if(_strlen($value) > 0){

                            $result = \App\Helpers\MiscHelper::eJson($value,true);

                            // if($field->data_source_value_column_is_text == true){
                            //     $value = "'$value'";
                            // }
                        } else {
                            $result = null;
                        }

                    }

                }

                return $result;

            }

            
        
        // 4. Date
        } else if ($field->field_type_id == "date"){
            
            $value = $data->{$field->name};

            if(!empty($value)){

                if($field->appearance_id == "4_separated") {
                    $value = date_format(date_create($value),'Y-m-d');
                } else {
                    $value = date_format(date_create($value),'d/m/Y H:i:s');
                }
            } 

            $result = $value;

            return $result;
        
        // 5. Attachment
        } else if($field->field_type_id == "media"){
            
            $result = [];
            
            if($field->is_multi != 1) {
                
                if(!empty($data->{$field->name . '_name'})) {
                
                    // $result->name = $data->{$field->name . '_name'};
                    // $result->original_name = $data->{$field->name . '_original_name'};
                    // $result->size = $data->{$field->name . '_size'};
                    // $result->extension = $data->{$field->name . '_extension'};
                    // $result->type = $data->{$field->name . '_type'};
                    
                    $obj = new \StdClass();
                        
                    $obj->tmp_name = '';
                    $obj->original_name = $data->{$field->name . '_original_name'};
                    $obj->size = $data->{$field->name . '_size'};
                    $obj->type = $data->{$field->name . '_type'};
                    $obj->extension = $data->{$field->name . '_extension'};
                    $obj->name = $data->{$field->name . '_name'};
                    
                    $result[] = $obj;

                }

                return $result;
            } else {
                
                $value = $data->{$field->name};

                foreach(array($value) as $v){
                    foreach($v as $itm){
                        
                        $obj = new \StdClass();
                        
                        $obj->tmp_name = '';
                        $obj->original_name = $itm->original_name;
                        $obj->size = $itm->size;
                        $obj->type = $itm->type;
                        $obj->extension = $itm->extension;
                        $obj->name = $itm->name;
                        
                        $result[] = $obj;

                    }
                }

                return $result;
            }
        
        // 9. Boolean
        } else if($field->field_type_id == "boolean") {

            
            if($field->appearance_id == "9_combobox" || $field->appearance_id == "9_list") {
                return $data->{$field->name};
            } else {
                return ($data->{$field->name} == true ? 1 : 0); 
            }

        }

        return null;
    }

    
    private function getFieldCollectionObject($ctype_id, $fields, $allow_drag_drop_to_sort){

        
        if($this->isEditMode != true) {
            return [];
        }

        $result = [];
        
        $data = $this->coreModel->nodeModel($ctype_id)
            ->id($this->recordData->id)
            ->load();
        
        $i = 0;
        foreach($data as $itm){
            
            $obj = new \StdClass();
            
            if($allow_drag_drop_to_sort == true){
                $obj->sort = $i;
            }

            $obj->sett_index = $i++;
            
            foreach($fields as $field){
                if($allow_drag_drop_to_sort == true && $field->name == "sort"){
                    continue;
                }
                
                if($field->field_type_id == "relation" && $field->is_multi == true){
                    $obj->{$field->name} = $this->getValue($itm, $field);
                    
                    if($field->data_source_value_column_is_text != true){

                        $obj->{$field->name . '_display'} = '';
                    }
                } else if($field->field_type_id == "media" && $field->is_multi == true){
                    $obj->{$field->name} = $this->getValue($itm, $field);
                } else if($field->field_type_id == "media" && $field->is_multi != true){
                    $obj->{$field->name} = $this->getValue($itm, $field);
                } else if($field->field_type_id == "boolean"){

                    $obj->{$field->name} = $this->getValue($itm, $field,"",true);

                } else if(($field->field_type_id == "relation" && $field->is_multi != true) ) {

                    $obj->{$field->name} =  $this->getValue($itm, $field,"");

                    if($field->data_source_value_column_is_text != true){
                    
                        $obj->{$field->name . '_display'} = _str_replace("'","\'", $this->getValue($itm, $field ,true));

                    }
                    
                } else if($field->field_type_id != "field_collection" && $field->field_type_id != "media") {
                    
                    $obj->{$field->name} =  $this->getValue($itm, $field,true);

                } 


                if(!empty($field->data_source_filter_by_field_name)){
                    
                    $obj->{'pl_' . $field->name . '_' . $field->data_source_table_name} = [];

                }
                
            }

            $result[] = $obj;
        }

        return $result;

    }

    
    private function generateFieldCollectionDeleteRecordMethodScript($field, $use_drag_drop_to_sort){

        ob_start();

        ?>
        
        <?php
            $componentName = to_snake_case($field->name) . '-list-component';
            
            $hasCustomComponent = false;

            if(_strpos($this->mainCtypeObj->extends,'id="tpl-' . $componentName . '"') !== false){
                $hasCustomComponent = true;
            }

            ?>

            deleteRecord<?= $field->name ?>(sett_index){
     
                <?php
                        
                    foreach($field->getFields() as $fcField){
                        $componentNameField = to_snake_case($field->name) . '-' . to_snake_case($fcField->name) . '-component';
                        
                        $hasCustomComponentField = false;
    
                        if(_strpos($this->mainCtypeObj->extends,'id="tpl-' . $componentNameField . '"') !== false){
                            $hasCustomComponentField = true;
                        }
    
                        if($hasCustomComponentField):?>
                        if(this.$refs.<?= $field->name ?>_<?= $fcField->name ?>Component && typeof this.$refs.<?= $field->name ?>_<?= $fcField->name ?>Component.beforeDelete == "function")
                            if(this.$refs.<?= $field->name ?>_<?= $fcField->name ?>Component.beforeDelete(sett_index) == false) {
                            return;
                        }
                    <?php endif; 
                }?>


                this.closeModal<?= $field->name ?>();
                
                if(sett_index == null) return;
                
                this.<?= $field->name ?> = this.<?= $field->name ?>.filter((e)=>e.sett_index !== sett_index );
                      
                var i = 0;
                this.<?= $field->name ?>.forEach((itm) => {
                    itm.sett_index = i++;
                });

                <?php if($use_drag_drop_to_sort == true){ ?>

                    var i = 0;
                    this.<?= $field->name ?>.forEach((itm) => {
                        itm.sort = i++;
                    });
                    
                <?php } ?>

                <?php if($hasCustomComponent): ?>
                    if(this.$refs.<?= $field->name ?>Component && typeof this.$refs.<?= $field->name ?>Component.refresh == "function")
                        this.$refs.<?= $field->name ?>Component.refresh(this.<?= $field->name ?>);
                <?php endif; ?>
                
            },
        
        <?php

        return ob_get_clean();
    
    }

    public function generateFieldCollectionDeleteRecordMethod(){
        
        $result = "";

        foreach($this->fields as $field){
        
            if($field->field_type_id != "field_collection")
                continue;

            if($field->use_parent_permissions == true){
                $FcPermissionObj = $this->ctypePermissionObj;
            } else {
                $FcPermissionObj = Application::getInstance()->user->getCtypePermission($field->data_source_id);
            }

            $use_drag_drop_to_sort = false;
            foreach($this->fieldCollectionUseDragDrop as $itm){
                if($itm["id"] == $field->data_source_id){
                    $use_drag_drop_to_sort = $itm["allow_drag_drop_to_sort"];
                }
            }

            if($this->isSurvey || $FcPermissionObj->allow_delete || $FcPermissionObj->allow_edit){
                
                $result .= $this->generateFieldCollectionDeleteRecordMethodScript($field, $use_drag_drop_to_sort);
                
            }

        }

        return $result;

    }


    private function generateFieldCollectionAddRecordMethodScript($field){
        
        
        $obj = new \stdClass();
    
        $removeQuotesArray = [];

        $obj->sys_is_edit_mode = false;
        $obj->sort = 99999;

        foreach($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id) as $fc){
            if($fc->name == "id"){
                $obj->{$fc->name} = null;
            } else if($fc->field_type_id == "relation" && $fc->is_multi == true) {
                $obj->{$fc->name} = [];
            } else if ($fc->field_type_id == "media" && $fc->is_multi == true) {
                $obj->{'var_' . $fc->name} = [];
                $obj->{$fc->name} = [];
            } else if ($fc->field_type_id == "media" && $fc->is_multi != true) {
                $obj->{'var_' . $fc->name} = [];
                $obj->{$fc->name} = [];
            } else {
                $value = $fc->default_value_updated;

                if(_strpos($value, "$") !== false){
                    $value = _str_replace("$","",$value);
                    $value = "this." . $value;

                    $obj->{$fc->name} = $value;
                    
                    $removeQuotesArray[$fc->default_value_updated] = $value;
                } else {
                    $obj->{$fc->name} = $this->decodeDefaultValue($field, $value);
                }

            }

        }

        $reloadScript = "";
        foreach($this->getFieldByType($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), "relation") as $fc){
            if(!empty($fc->data_source_filter_by_field_name)){

                $reloadScript .= $this->reloadFieldListScript($fc, 'current_' . $field->name . '_' . $fc->name, $field->name);

            }
        }
        

        $objStr = json_encode($obj);

        foreach($removeQuotesArray as $key => $value) {
            $objStr = _str_replace("\"$value\"", $value, $objStr);
        }

        ob_start();

        ?>

        addNew<?= $field->name ?>()
        {
            var self = this;
            this.current_<?= $field->name ?> = <?= $objStr ?>;
            
            self.<?= $field->name ?>_modal_btn_active = false;
            
            setTimeout(function(){
                self.<?= $field->name ?>_modal_btn_active = true;
            }, 1000);
            
            <?= $reloadScript ?>

            this.validate();
            this.$refs.form_<?= $field->name ?>.classList.remove('was-validated');
            
            var modal = new bootstrap.Modal(document.getElementById('<?= $field->name ?>Modal'), {
                backdrop: 'static',
                keyboard: false,
            })
            modal.show();
            
            this.<?=$field->name ?>_modal_is_open = true;
            if (typeof initMaps() === "initMaps") { 
                initMaps();
            }

        },
        
        <?php

        return ob_get_clean();


    }

    
    public function generateFieldCollectionAddRecordMethod(){
        
        $result = "";

        foreach($this->getFieldByType($this->fields, "field_collection") as $field){
        
            if($field->use_parent_permissions == true){
                $FcPermissionObj = $this->ctypePermissionObj;
            } else {
                $FcPermissionObj = Application::getInstance()->user->getCtypePermission($field->data_source_id);
            }

            if($this->isSurvey || $FcPermissionObj->allow_add){
                $result .= $this->generateFieldCollectionAddRecordMethodScript($field);
            }

        }

        return $result;

    }


    private function generateFieldCollectionEditRecordMethodScript($field){
        
        $reloadScript = "";
        $refreshMapFunc = "";
        foreach($this->getFieldByType($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), "relation") as $fc){
            if(!empty($fc->data_source_filter_by_field_name)){
               
                $reloadScript .= $this->reloadFieldListScript($fc, 'current_' . $field->name . '_' . $fc->name, $field->name);

            }
        }

        foreach($this->getFieldByType($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id), "decimal") as $fc){
            if($fc->appearance_id == "7_map" && _strtolower(substr($fc->name, _strlen($fc->name) -3,_strlen($fc->name))) == "lat"){
                                        
                $base_name = _strtolower(substr($fc->name, 0,_strlen($fc->name) - 4));

                $refreshMapFunc .= "
                this.current_" . $field->name . "_" . $base_name . "_set_location_manually();
                ";
            }
        }

        ob_start();

        ?>

        editRecord<?= $field->name ?>: function(sett_index)
        { 
            var self = this;
            this.current_<?= $field->name ?> = JSON.parse(JSON.stringify(this.<?= $field->name ?>.filter((e)=>e.sett_index == sett_index )[0]));
            
            self.<?= $field->name ?>_modal_btn_active = false;
            
            setTimeout(function(){
                self.<?= $field->name ?>_modal_btn_active = true;
            }, 1000);
            
            <?= $reloadScript ?>

            this.validate();
            this.$refs.form_<?= $field->name ?>.classList.remove('was-validated');

            var modal = new bootstrap.Modal(document.getElementById('<?= $field->name ?>Modal'), {
                backdrop: 'static',
                keyboard: false,
            })
            modal.show();
            this.<?=$field->name ?>_modal_is_open = true;
            this.current_<?= $field->name ?>.sys_is_edit_mode = 1;
            
            <?= $refreshMapFunc ?>
        },
        
        <?php

        return ob_get_clean();


    }

    public function generateFieldCollectionEditRecordMethod(){

        $result = "";

        foreach($this->getFieldByType($this->fields, "field_collection") as $field){
        
            if($field->use_parent_permissions == true){
                $FcPermissionObj = $this->ctypePermissionObj;
            } else {
                $FcPermissionObj = Application::getInstance()->user->getCtypePermission($field->data_source_id);
            }

            if($this->isSurvey || $FcPermissionObj->allow_add || $FcPermissionObj->allow_edit){
                $result .= $this->generateFieldCollectionEditRecordMethodScript($field);
            }

        }


        return $result;
        
    }


    private function generateCheckIfAttachmentIsLoading($field) {

        $result = "";
        foreach($this->getFieldByType($field->getFields(), "media") as $field_inside_fc){

            $result .= "if(this.uploading_current_{$field->name}_{$field_inside_fc->name}) {
                $.toast({
                    heading: 'Error',
                    text: 'Attachment ($field_inside_fc->title) is loading, please wait',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
                return;
            }
            ";
        }

        return $result;

    }

    public function generateFieldCollectionCloseModalMethod(){
        $result = "";

        ob_start();

        foreach($this->getFieldByType($this->fields, "field_collection") as $field){
        
            if($field->use_parent_permissions == true){
                $FcPermissionObj = $this->ctypePermissionObj;
            } else {
                $FcPermissionObj = Application::getInstance()->user->getCtypePermission($field->data_source_id);
            }

            if($this->isSurvey || $FcPermissionObj->allow_add || $FcPermissionObj->allow_edit){

                
                ?>

                closeModal<?= $field->name ?>()
                {

                    <?= $this->generateCheckIfAttachmentIsLoading($field) ?>

                    var modal = document.getElementById('<?= $field->name ?>Modal')
                    modal = bootstrap.Modal.getInstance(modal)
                    if(modal != null) {
                        modal.hide();
                    }
                    this.<?=$field->name ?>_modal_is_open = false;
                    this.current_<?= $field->name ?> = {};
                },

                <?php
                
            }

        }

        return ob_get_clean();

    }



    public function generateFieldCollectionSaveModalMethod(){ 

        ob_start();

        foreach($this->getFieldByType($this->fields, "field_collection") as $field){
        
            if($field->use_parent_permissions == true){
                $FcPermissionObj = $this->ctypePermissionObj;
            } else {
                $FcPermissionObj = Application::getInstance()->user->getCtypePermission($field->data_source_id);
            }

            if($this->isSurvey || $FcPermissionObj->allow_add || $FcPermissionObj->allow_edit){

                $componentName = to_snake_case($field->name) . '-list-component';
                
                $hasCustomComponent = false;

                if(_strpos($this->mainCtypeObj->extends,'id="tpl-' . $componentName . '"') !== false){
                    $hasCustomComponent = true;
                }

                ?>

                saveModal<?= $field->name ?>(goToNew)
                {               
                    var self = this;
                    if(!this.<?= $field->name ?>_modal_btn_active)
                        return;

                    this.$refs.form_<?= $field->name ?>.classList.add('was-validated');
                    this.current_<?= $field->name ?>_form_validated = true;
                    
                    if (!this.validate_<?= $field->name ?>() || !this.$refs.form_<?= $field->name ?>.checkValidity()) {
                        $.toast({
                            heading: 'Error',
                            text: 'Por favor complete los campos correctamente',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        return;
                    }

                    this.<?= $field->name ?>_modal_btn_active = false;
                    
                    <?= $this->generateCheckIfAttachmentIsLoading($field) ?>

                    <?php
                        
                        foreach($field->getFields() as $fcField){
                            $componentNameField = to_snake_case($field->name) . '-' . to_snake_case($fcField->name) . '-component';
                            
                            $hasCustomComponentField = false;

                            if(_strpos($this->mainCtypeObj->extends,'id="tpl-' . $componentNameField . '"') !== false){
                                $hasCustomComponentField = true;
                            }

                            if($hasCustomComponentField):?>
                            if(this.$refs.<?= $field->name ?>_<?= $fcField->name ?>Component && typeof this.$refs.<?= $field->name ?>_<?= $fcField->name ?>Component.beforeSave == "function")
                                if(this.$refs.<?= $field->name ?>_<?= $fcField->name ?>Component.beforeSave() == false) {
                                    this.<?= $field->name ?>_modal_btn_active = true;
                                return;
                            }

                        <?php endif; 
                    }?>

                    if(this.current_<?= $field->name ?>.sett_index == null){
                        this.current_<?= $field->name ?>.sett_index = this.ind++;
                        this.<?= $field->name ?>.push(this.current_<?= $field->name ?>);
                    } else {
                        
                        this.$set(this.<?= $field->name ?>, this.<?= $field->name ?>.findIndex((e)=>e.sett_index == this.current_<?= $field->name ?>.sett_index), this.current_<?= $field->name ?>); 
                    }

                    var i = 0;
                    this.<?= $field->name ?>.forEach((itm) => {
                        itm.sett_index = i++;
                    });

                    <?php if($hasCustomComponent): ?>
                        if(this.$refs.<?= $field->name ?>Component && typeof this.$refs.<?= $field->name ?>Component.refresh == "function")
                            this.$refs.<?= $field->name ?>Component.refresh(this.<?= $field->name ?>);
                    <?php endif; ?>
                    
                    <?php if($this->loadVueDraggable) { ?>
                        this.onEnd_fc_<?= $field->name ?>();
                    <?php } ?>

                    $('#<?= $field->name ?>Modal').modal('hide');
                    this.current_<?= $field->name ?> = {};
                    setTimeout(function(){
                        const elements = document.getElementsByClassName("modal-backdrop fade show");
                        while(elements.length > 0){
                            elements[0].parentNode.removeChild(elements[0]);
                        }

                        if(goToNew == 1){
                            self.addNew<?= $field->name ?>();
                        }
                    }, 500);

                    


                },

            <?php

            }

        }

        return ob_get_clean();
    }

    private function getFieldByType(array $fields, string $type, $return_hidden = true) : array {
        
        $data = [];
        foreach($fields as $field) {

            if($field->field_type_id == $type && ($return_hidden == true || $field->is_hidden_updated !== true)) {
                $data[] = $field;
            }

        }

        return $data;
    }

    
    private function decodeDefaultValue($field, $value){

        if(!isset($value) || _strlen($value) == 0)
            return $value;
        
        if($field->field_type_id == "relation" && $field->is_multi == true) {
            return _explode(",", $value);
        }

        if(_strpos($value, "$") !== false){
            $value = _str_replace("$","",$value);
            $value = "this." . $value;
        } else {
            if($value == "[USER_ID]")
                $value = _str_replace("[USER_ID]", Application::getInstance()->user->getId(),$value);
            
            if($value == "[NOW]")
                $value = _str_replace("[NOW]", date('d/m/Y H:i:s'), $value);

            if($value == "[TODAY]")
                $value = _str_replace("[TODAY]", date('d/m/Y'), $value);
        }

        return $value;
    }

    
    private function isSurvey() : bool {
        return $this->isSurvey;
    }

    private function isNotSurvey() : bool {
        return !$this->isSurvey();
    }


    private function isAddMode() : bool {
        return !$this->isEditMode();
    }


    private function isEditMode() : bool {
        return $this->isEditMode;
    }
    

    private function reloadFieldListScript($field, $fieldFullName, $fcName = false){
        
        if($field->select2_async == true) {
            return;
        }

        $result = "";
        
        $value = "";
        foreach(_explode(",", $field->data_source_filter_by_field_name) as $itm){

            $has_place_holder = false;
            if(!empty($itm)){
                if(_strtolower($itm) == _strtolower("[CTYPEID]")){
                    $itm = $this->mainCtypeObj->id;
                    $has_place_holder = true;
                }

                if(_strtolower($itm) == _strtolower("[RECORDID]")){
                    $itm = (isset($this->recordData) ? $this->recordData->id : null);
                    $has_place_holder = true;
                }
                
            
                if(!empty($value))
                    $value .= ",";

                if($has_place_holder){
                    $value .= "'$itm'";
                } else {
                    $value .= "this.$itm";
                }
                
            }

        }

        if(!empty($value)){
            if(empty($fcName)){
                $value = _str_replace("self.", "",$value);
            } else {
                $value = _str_replace("self.", "current_$fcName.",$value);
            }
            $result .= "this.reload_$fieldFullName($value);";
        }
        
        return $result;
        
    }


    public function reloadFieldList(){
        
        $result = "";
        
        foreach($this->getFieldByType($this->fields, "relation") as $field){
            if(!empty($field->data_source_filter_by_field_name)){
                
                $result .= $this->reloadFieldListScript($field, $field->name, null);
                
            }
        }

        return $result;

    }


    
    
    private function generateFieldRefreshListScript($fields, $prefix){
        
        
        $result = "";
        foreach($fields as $field){
            if(!empty($field->data_source_filter_by_field_name) && $field->select2_async != true){

                $final_value = "";
                $final_value_p = "";
                
                foreach(_explode(",", $field->data_source_filter_by_field_name) as $itm){
                    if(!empty($final_value))
                        $final_value .= ",";
                    
                    $has_place_holder = false;
                    if(_strtolower($itm) == _strtolower("[CTYPEID]")){
                        
                        if(!empty($final_value_p))
                            $final_value_p .= ",";
                        $final_value_p .= _strtolower(_str_replace(array("]","["),array("",""),$itm));

                        $final_value = $this->mainCtypeObj->id;
                        $has_place_holder = true;

                    } else if(_strtolower($itm) == _strtolower("[RECORDID]")){
                        
                        
                        if(!empty($final_value_p))
                            $final_value_p .= ",";
                        $final_value_p .= _strtolower(_str_replace(array("]","["),array("",""),$itm));

                        $final_value = isset($this->recordData) ? $this->recordData->id : "";
                        $has_place_holder = true;

                    } else {
                        
                        if(_strpos($itm, ".") !== false && $has_place_holder == false){
                            $arr = _explode(".",$itm);
                            $final_value =  $arr[sizeof($arr) - 1];

                            if(!empty($final_value_p))
                                $final_value_p .= ",";
                            $final_value_p .= _strtolower(_str_replace(array("]","["),array("",""),$final_value));

                            
                        } else {
                        
                            $final_value .= "$itm";

                            if(!empty($final_value_p))
                                $final_value_p .= ",";
                            $final_value_p .= _strtolower(_str_replace(array("]","["),array("",""),$itm));

                        }

                    }
                    
                }

                $result .= "
                reload_" . (isset($prefix) && _strlen($prefix) > 0 ? $prefix . "_" : "") . "$field->name($final_value_p){
                    
                    var formData = new FormData();
                    
                    this.loading_" . $field->name . "_" . "$field->data_source_table_name = true;
                    var x_final_value = null;
                    
                    ";

                    if(_strpos($final_value_p, ",") !== false){
                        
                        foreach(_explode(",", $final_value_p) as $itm){
                            $result .= "
                            var x_$itm = $itm;
                            
                            if(x_$itm == undefined || x_$itm == null) {
                                x_$itm = null;
                            } else {
                                
                                if(Array.isArray(x_$itm)) {
                                    x_$itm = x_$itm;
                                } else if(typeof x_$itm == 'object' && x_$itm?.length >= 0){
                                    x_$itm = x_$itm.map((x) => x.id);
                                } else if (typeof x_$itm == 'object') {
                                    x_$itm = x_$itm.id
                                }
                            }
                            
                            ";
                        }

                        $i = 0;
                        foreach(_explode(",", $final_value_p) as $itm){
                            if($i++ == 0){
                                $result .= "formData.append('filters', x_$itm);";
                            } else {
                                $result .= "formData.append('filters_$i', x_$itm);";
                            }

                        }
                    } else {
                        $result .= "
                        var filters = $final_value_p;
                        
                        
                        if(filters == undefined || filters == null) {
                            filters = null;
                        } else {
                            if(Array.isArray(filters)) {
                                filters = filters;
                            } else if(typeof filters == 'object' && filters?.length >= 0){
                                filters = filters.map((x) => x.id);
                            } else if (typeof filters == 'object') {
                                filters = filters.id
                            }
                        }
                        
                        formData.append('filters', filters);
                        ";
                    }

                    
                    $result .= "
                
                    var self = this;
                    axios({
                        method: 'post',
                        url: '/" . ($this->isNotSurvey() ? "InternalApi" : "externalapi") . "/genericPreloadList/0?field_id=$field->id&lang=" . $this->lang . "&response_format=json',
                        data:formData,
                        headers: {
                            'Content-Type': 'form-data',
                        }
                    })
                    .then(function(response){
                        if(response.data.status == 'success'){
                            ";
                            
                                $result .= "
                            self.pl_" . $field->name . "_" . "$field->data_source_table_name = response.data.result;
                            "; 
                            
                            $result .= "
                            self.loading_" . $field->name . "_" . "$field->data_source_table_name = false;
                        } else {
                            self.pl_" . $field->name . "_" . "$field->data_source_table_name = [];
                            $.toast({
                                heading: 'Error',
                                text: 'An error occured while loading preload list for $field->title',
                                showHideTransition: 'slide',
                                position: 'top-right',
                                icon: 'error'
                            });
                            self.loading_" . $field->name . "_" . "$field->data_source_table_name = false;
                            return;
                        }
                    })
                    .catch(function(error){
                        $.toast({
                            heading: 'Error',
                            text: error,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        self.loading_" . $field->name . "_" . "$field->data_source_table_name = false;
                    });
        
        

                },
                    ";
            }
        }

        return $result;
    

    }

    public function generateFieldRefreshList(){
        
        $result = $this->generateFieldRefreshListScript($this->fields,"");


        foreach($this->getFieldByType($this->fields, "field_collection") as $field){

            $result .= $this->generateFieldRefreshListScript($this->ctypesHelper->getFields($this->isAddMode(), $field->data_source_id),"current_" . $field->name);

        }

        return $result;

    }

}