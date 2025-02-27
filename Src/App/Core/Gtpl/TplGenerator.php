<?php

/*
 * This class generates tpl
 */

namespace App\Core\Gtpl;

use App\Core\Application;

class TplGenerator {


    private $coreModel;
    private $ctypeObj;
    private $gpsPanelInitialization;
    private $gpsFields = array();

    public function __construct($ctypeObj) {
        $this->ctypeObj = $ctypeObj;

        $this->coreModel = Application::getInstance()->coreModel;
    }

    /**
     * generate
     *
     * @return string
     *
     * This is the entry function
     */
    public function generate() : string {

        $fields = $this->ctypeObj->getFields();

        $file = APP_ROOT_DIR . DS . "Views" . DS . "Node" . DS . "Show.php";

        //get the template
        $content = file_get_contents($file);

        //append buttons at the top
        $content = _str_replace("%%generateUITplButtons%%", $this->generateActionButtons($fields), $content);

        //append fields
        $content = _str_replace("%%generateUITpl%%", $this->generateFields($fields,"\$nodeData"), $content);

        //append button actions
        $content = _str_replace("%%buttons_actions%%", $this->generateButtonActions($fields), $content);

        //append gps initialization
        $content = _str_replace("%%gpsPanelInitialization%%", $this->gpsPanelInitialization, $content);
        
        
        //return back the generated template
        return $content;
    }

    


        
    /**
     * getButtons
     *
     * @param  array $fields
     * @return array
     *
     * Returns buttons only from fields array
     */
    private function getButtons($fields) : array {
            
        $result = array();

        foreach($fields as $field){

            if($field->field_type_id == "button"){
                $result[] = array("name" => $field->name, "title" => $field->title, "method" => $field->method);
            }
        }

        return $result;
    }




        
    /**
     * generateActionButtons
     *
     * @param  array $fields
     * @return string
     *
     * Generates actions button at the top
     */
    private function generateActionButtons($fields) : string {
            
        /*  Template

            <div class="btn-group mb-2">
                <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu">   
                    <button class="dropdown-item" @click="run_button1()">Button 1</button>
                    <button class="dropdown-item" @click="run_button2()">Button 2</button>
                </div>
            </div>

        */

        $buttons = $this->getButtons($fields);

        if(sizeof($buttons) == 0){
            return "";
        }

        ob_start();

        ?>

    <div class="btn-group mb-2">
        <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
        <div class="dropdown-menu">
                
            <?php foreach($buttons as $button){ ?>
                    
            <button class="dropdown-item" @click="run_<?=$button["name"]?>()"><?=$button["title"]?></button>
                    
            <?php } ?>

        </div>
    </div>

        <?php
    
        return ob_get_clean();
    }



    
        
    /**
     * generateFields
     *
     * @param  array $fields
     * @param  string $prefix
     * @param  bool $insideFc
     * @return string
     *
     * Generate fields based on fields array
     */
    private function generateFields($fields, $prefix, $insideFc = false) : string {
            
        $return_value = "";
        $current_group = "-EMPTY-";

        foreach($fields as $field){

            if($field->is_hidden || $field->is_hidden_updated_read)
                continue;

            if(intval($field->size) <= 0 || intval($field->size) > 3 )
                $field->size = 1;
            
            $group_name = (is_null($field->group_name) ? Application::getInstance()->settings->get('TPL_DEFAULT_GROUP_NAME') : $field->group_name);

                    
            if( $current_group != $group_name) {

                
                if($current_group != "-EMPTY-"){
                    $return_value .= $this->closeGroup($insideFc);
                }
                
                $current_group = $group_name;

                $return_value .= $this->openGroup($group_name, $insideFc);
                
            }
            
            switch($field->field_type_id){
                case 1: //Text
                case 4: //Date
                case 6: //Number
                    $return_value .= $this->generateBasic($prefix, $field);
                    break;
                case 2: //ComboBox
                    $return_value .= $this->generateCombobox($prefix, $field);
                    break;
                case 3: //FieldCollection
                    $return_value .= $this->generateFieldCollection($prefix, $field);
                        break;
                case 5: //Attachment
                    $return_value .= $this->generateAttachment($prefix, $field);
                    break;
                case 7: //Decimal
                    $return_value .= $this->generateDecimal($prefix, $field);
                    break;
                case 9: //Boolean
                    $return_value .= $this->generateBoolean($prefix, $field);
                    break;
                default:
                    break;  
                    
            }
        }

        
        $return_value .= $this->closeGroup($insideFc);

        return $return_value;
    }





    /**
     * openGroup
     *
     * @param  string $group_name
     * @param  bool $insideFc
     * @return string
     *
     * Append open group html code
     */
     private function openGroup($group_name, $insideFc = false) : string {

        /*  Group example outside Field-Collection

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3"><?=$group_name?></h4>
                    </div>
                </div>
            </div>

        
        /*  Group example outside Field-Collection

            <div class="row">
                <div class="col-md-12">
                </div>
            </div>

        */

        ob_start();

        if($insideFc != true){ ?>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-primary mb-3"><?=$group_name?></h4>
                    
        <?php } 
        // else { 
                    
        //             <div class="row">
        //                 <div class="col-md-12">

        // <?php }
                

        return ob_get_clean();

    }





    /**
     * closeGroup
     *
     * @param  bool $insideFc
     * @return string
     *
     * Append close group html code
     */
    private function closeGroup($insideFc = false) : string {

        ob_start();
        
        if($insideFc != true){ ?>
                        
                </div>
            </div>
        </div>
            
        <?php } 
        // else { 
        //         </div>
        // <?php } 

        return ob_get_clean();
    }

    


    
    
    /**
     * generateGpsScript
     *
     * @param  string $prefix
     * @param  string $gps_field_name
     * @return void
     *
     * Generate GPS Script for gps fields
     */
    private function generateGpsScript($prefix, $gps_field_name){

        /*  Example

            <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKJJhpyQ0jjnRWi2hnS7C7BIRddUSVUQM&libraries=visualization,drawing&callback=gps_initMap"></script>
            <script>

                function gps_initMap() {

                    var lat = <?= $nodeData->{'gps_lat'} ?? 0?>;
                    var lng = <?= $nodeData->{'gps_lng'} ?? 0?>;

                    if(lat > 0 && lng > 0){

                        var map = new google.maps.Map(document.getElementById('gps_map'), {
                            center: new google.maps.LatLng(lat,lng),
                            zoom: 12
                        });
                        
                        var marker = new google.maps.Marker({
                            map: map,
                            position: new google.maps.LatLng(lat, lng),
                            animation: google.maps.Animation.DROP   
                        });

                    }

                }

            </script>

        */

        ob_start(); 
        if(!empty($gps_field_name) == true){ ?>
        
            <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= Application::getInstance()->env->get('google_maps_api_key')?>&libraries=visualization,drawing&callback=<?=$gps_field_name?>_initMap"></script>
            <script>


                function <?=$gps_field_name?>_initMap() {

                    var lat = <?= "<?= $prefix->{'" . $gps_field_name . "_lat'} ?? 0?>"?>;

                    var lng = <?= "<?= $prefix->{'" . $gps_field_name . "_lng'} ?? 0?>"?>;

                    if(lat > 0 && lng > 0){

                        var map = new google.maps.Map(document.getElementById('<?=$gps_field_name?>_map'), {
                            center: new google.maps.LatLng(lat,lng),
                            zoom: 12
                        });
                        
                        var marker = new google.maps.Marker({
                            map: map,
                            position: new google.maps.LatLng(lat, lng),
                            animation: google.maps.Animation.DROP   
                        });

                    }

                }

            </script>

        <?php }

        $this->gpsPanelInitialization .= ob_get_clean();
    }



    
    
    /**
     * generateBasic
     *
     * @param  string $prefix
     * @param  object $field
     * @return string
     *
     * Generate basic fields like, text, date, number
     */
    private function generateBasic($prefix, $field) : string {

        /*  Example

            <p class="card-p"><strong>Full Name:</strong> <span class="ml-2"><?=$nodeData->full_name?></span></p>

        */

        ob_start();

        ?>

                    <p class="card-p"><strong><?=$field->title?>:</strong> <span class="ml-2"><?="<?={$prefix}->{$field->name}?>"?></span></p><?php 
        return ob_get_clean();
    }



    
    
    /**
     * generateCombobox
     *
     * @param  string $prefix
     * @param  object $field
     * @return string
     * 
     * Generate combobox
     */
    private function generateCombobox($prefix, $field) : string {

        /*  Example Single

            <p class="card-p"><strong>Gender:</strong> <span class="ml-2"> <?=$nodeData->gender_id_display?></span></p>

        /*  Example Multi

            <p class="card-p"><strong>Standard Vulnerability Indicators:</strong> <span class="ml-2">          
            <?php if(!empty($nodeData->standard_vulnerabilities_display)){ ?>
                <ul>
                    <?php foreach(preg_split('/[\r\n]+/', $nodeData->standard_vulnerabilities_display) as $cbx_itm){ ?>
                        <li> <?= e($cbx_itm) ?> </li>
                    <?php } ?>
                </ul>
            <?php } ?>

        */

        if($field->data_source_value_column_is_text) {
            return $this->generateBasic($prefix, $field);
        }

        ob_start(); 

        if($field->is_multi != true){ ?>

                    <p class="card-p"><strong><?=$field->title?>:</strong> <span class="ml-2"> <?="<?={$prefix}->{$field->name}_display?>"?></span></p>
        <?php } else { ?>

                    <p class="card-p"><strong><?=$field->title?>:</strong> <span class="ml-2">          
                        <?= "<?php if(!empty($prefix->{$field->name}_display)){ ?> "?>

                            <ul>

                                <?= "<?php foreach(preg_split('/[\\r\\n]+/', $prefix->{$field->name}_display) as \$cbx_itm){ ?>" ?>

                                    <li><?=" <?= e(\$cbx_itm) ?>"?> </li>

                                <?= "<?php } ?>" ?>

                            </ul>

                        <?= "<?php } ?>" ?>

        <?php } 
        return ob_get_clean();
    }


        


    /**
     * generateFieldCollection
     *
     * @param  string $prefix
     * @param  object   $field
     * @return string
     *
     * Generate Field-Collection
     */
    private function generateFieldCollection($prefix, $field) : string {
        
        /*  Example

            <?php foreach($nodeData->items as $fc_itm){ ?>
                    
                <div class="row">
                    <div class="col-md-12">
                        // Fields
                    </div>
                </div>
                <hr>
            <?php } ?>

        */

        $result = "";

        $result .= "<?php if( sizeof(\$nodeData->" . $field->name . ") > 0): ?>\n";

        $result .= "<div class=\"row\">\n";
        $result .= "<div class=\"col-md-12\">\n";
        $result .= "<h4 class=\"header-title text-primary mb-3\">" . $field->name . "</h4>\n";

        $result .= "<?php foreach(\$nodeData->" . $field->name . " as \$fc_itm){ ?>\n";
            
        $result .= $this->generateFields($field->getFields(), "\$fc_itm",true);

        $result .= "
            <hr>
        <?php } ?>
        ";

        $result .= "</div>\n</div>";
        $result .= "<?php endif; ?>\n";
        
        return $result;
    }




    
    /**
     * generateAttachment
     *
     * @param  string $prefix
     * @param  object $field
     * @return string
     *
     * Generate attachment
     */
    private function generateAttachment($prefix, $field) : string {

        /*  Example Single

            <?php if(isset($nodeData->national_id_photo_name)) {?>
                <div class="row">
                    <div class="col">
                        <p class="card-np"><strong>National ID Photo:</strong> <span class="ml-2"> </span></p>
                    </div>
                    <div class="col text-end">
                        <a href="/filedownload?fid=2069&size=orginal&fname=<?=$nodeData->national_id_photo_name?>" target="_blank">
                            <img height="100" width="100" src="<?= get_file_thumbnail($data->ctypeObj->name, $nodeData->national_id_photo_name)?>">
                        </a>
                    </div>
                </div>
                <p class="card-p"></p>
            <?php } ?>

        /*  Example Multi

            <?php if(sizeof($nodeData->photo_of_goods) > 0) {?>                        
                <p class="card-np"><strong>Photo of Goods:</strong> <span class="ml-2"> </span></p>

                <div class="row">
                    <?php foreach($nodeData->photo_of_goods as $att_itm){?>                                
                        <div class="col text-end">
                            <a href="/filedownload?fid=2555&size=orginal&fname=<?=$att_itm->name?>" target="_blank">
                                <img height="100" width="100" src="<?= get_file_thumbnail($data->ctypeObj->name, $att_itm->name)?>">
                            </a>
                        </div>
                        
                    <?php } ?>
                </div>

                <p class="card-p"></p>

            <?php } ?>

        */

        ob_start(); 
        if($field->is_multi != true){ ?>
              
                    <?= "<?php if(isset($prefix->{$field->name}_name)) {?>"?>

                    <div class="row">
                        <div class="col-md-12 mt-1">
                            <strong><?=$field->title?>:</strong>
                            <a href="/filedownload?ctype_id=<?= $field->ctype_id ?>&field_name=<?=$field->name?>&size=orginal&file_name=<?= "<?=" . $prefix?>-><?=$field->name?>_name?>" target="_blank">
                                <img height=<?php if ($field->file_type_id == 1) echo "100"; else echo "32"; ?> width=<?php if ($field->file_type_id == 1) echo "100"; else echo "32"; ?> alt="<?= "<?= $prefix->{$field->name}_original_name ?>" ?>" src="<?= "<?= get_file_thumbnail(\$data->ctypeObj->id, \"$field->name\", $prefix->{$field->name}_name)?>" ?>">
                                <?= "<?= $prefix->{$field->name}_original_name ?>" ?>
                            </a>
                        </div>
                    </div>
                    <p class="card-p"></p>
                    <?= "<?php } ?>" ?>

        <?php } else { ?>
                
                    <?= "<?php if(sizeof($prefix->$field->name) > 0) {?>"?>
                        
                        <p class="card-np mb-0"><strong><?=$field->title?>:</strong> <span class="ml-2"> </span></p>

                        <div class="row">
                            <?= "<?php foreach($prefix->$field->name as \$att_itm){?>" ?>
                                
                                <div class="col-md-12 mt-1">
                                    <a href="/filedownload?ctype_id=<?= $field->ctype_id ?>&field_name=<?=$field->name?>&size=orginal&file_name=<?= "<?=\$att_itm->name?>"?>" target="_blank">
                                        <img alt="<?= "<?= \$att_itm->original_name ?>" ?>" height=<?php if ($field->file_type_id == 1) echo "100"; else echo "32"; ?> width=<?php if ($field->file_type_id == 1) echo "100"; else echo "32"; ?> src="<?= "<?= get_file_thumbnail(\$data->ctypeObj->name, \"$field->name\", \$att_itm->name)?>" ?>">
                                        <?= "<?= \$att_itm->original_name ?>" ?>
                                    </a>
                                </div>
                                
                            <?= "<?php } ?>" ?>

                        </div>

                        <p class="card-p"></p>

                    <?= "<?php } ?>" ?>

        <?php
        }

        
        return ob_get_clean();

    }




    
    /**
     * generateDecimal
     *
     * @param  string $prefix
     * @param  object $field
     * @return string
     *
     * Generate decimal fields
     */
    private function generateDecimal($prefix, $field) : string {
        
        /*  Example if the field is GPS

            <p class="card-p"><strong>gps:</strong> <span class="ml-2"> 
            
            <?php 
            $temp_lat = $nodeData->{'gps_lat'};
            $temp_lng = $nodeData->{'gps_lng'};
            if($temp_lat > 0 &&$temp_lng > 0){ ?>
                <span class="ml-2"> <?= $temp_lat . ', ' . $temp_lng ?> </span>
                <div load="gps_initMap();" id="gps_map" style="height: 350px !important; position:relative !important; width:100% !important;"></div><p>

            <?php } else { ?>
                <span class="ml-2"> N/A</span>
                <div id="gps_map"></div><p>

            <?php } ?>

        /*  Exmaple if the field is not GPS

            <p class="card-p"><strong>Salary:</strong> <span class="ml-2"> <?=$nodeData->salary?></span></p>

        */

        ob_start(); 
        
        if((substr($field->name, -4) == "_lat" || substr($field->name, -4) == "_lng")){

            $base_name = substr($field->name, 0, _strlen($field->name) - 4);
            if(in_array($base_name, $this->gpsFields)){
                return ob_get_clean();
            }
            $this->gpsFields[] = $base_name

            ?>
            <p class="card-p"><strong><?=$base_name?>:</strong> <span class="ml-2"> 
            
                    <?="<?php 
                    \$temp_lat = $prefix->{'{$base_name}_lat'};
                    \$temp_lng = $prefix->{'{$base_name}_lng'};
                    if(\$temp_lat > 0 &&\$temp_lng > 0){ ?>" ?>

                        <span class="ml-2"> <?= "<?= \$temp_lat . ', ' . \$temp_lng ?>" ?> </span>
                        <div load="<?=$base_name?>_initMap();" id="<?=$base_name?>_map" style="height: 350px !important; position:relative !important; width:100% !important;"></div><p>

                    <?= "<?php } else { ?>" ?>

                        <span class="ml-2"> N/A</span>
                        <div id="<?=$base_name?>_map"></div><p>

                    <?= "<?php } ?>" ?>
                        
                    <?php 

            $this->generateGpsScript($prefix, $base_name);

        } else {
        ?>
            <p class="card-p"><strong><?=$field->title?>:</strong> <span class="ml-2"><?="<?={$prefix}->{$field->name}?>"?></span></p>
        <?php }
        

        return ob_get_clean();
    }

    




    /**
     * generateBoolean
     *
     * @param  string $prefix
     * @param  object $field
     * @return string
     *
     * Generate Boolean field
     */
    private function generateBoolean($prefix, $field) : string {

        /*  Example

            <p class="card-p"><strong>Are you employed?</strong> <span class="ml-2"><?=$nodeData->are_you_employed == true ? 'Yes' : 'No'?></span></p>

        */


        ob_start(); 
        ?>
                    <p class="card-p"><strong>*<?=$field->title?>:</strong> <span class="ml-2"><?="<?={$prefix}->{$field->name} == true ? 'Si' : 'No'?>"?></span></p>
        
        <?php 
        return ob_get_clean();
    }




    
    /**
     * generateButtonActions
     *
     * @param  array $fields
     * @return string
     *
     * Generate action for buttons
     */
    private function generateButtonActions($fields) : string {

        /*  Example
            
            run_button_name(){
                <?= get_button_method('actions/somethig/[ID]', $data->ctypeObj->id, $nodeData->id); ?>  
            },

        */

        $return_value = "";

        foreach($this->getButtons($fields) as $button){
            
            $return_value .= "
            run_" . $button['name'] . "(){
                <?= get_button_method('" . $button['method'] . "', \$data->ctypeObj->id, \$nodeData->id); ?>  
            },
            ";
        }

        return $return_value;
    }
   
}
