<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class MapComponent {

    private $fields;
    
    private $mapsFields = [];
    private $coreModel;
    public function __construct($fields) {
        $this->fields = $fields;

        $this->coreModel = Application::getInstance()->coreModel;
    }



    public function generateMapGetCurrentLocationMethods(){
        
        $result = "";
        
        foreach($this->fields as $field){

            if($field->field_type_id == "decimal" && $field->appearance_id == "7_map"){
                        
                if(_strtolower(substr($field->name, _strlen($field->name) -3,_strlen($field->name))) == "lat"){
                   
                    $fieldFullName = _strtolower(substr($field->name, 0,_strlen($field->name) - 4));

                    $result .= $this->generateMapGetCurrentLocationMethodsScript($fieldFullName, $fieldFullName, $field->is_read_only);
                }

            } else if ($field->field_type_id == "field_collection"){

                foreach($field->getFields() as $fc){
                    
                    if($fc->field_type_id == "decimal" && $fc->appearance_id == "7_map"){
                        
                        $baseName = _strtolower(substr($fc->name, 0,_strlen($fc->name) - 4));
                        $fieldFullName = "current_" . $field->name . "_" . $baseName;
                        $fieldDataPath = "current_" . $field->name . "." . $baseName;

                        $result .= $this->generateMapGetCurrentLocationMethodsScript($fieldFullName, $fieldDataPath, $field->is_read_only);
                        
                    }
                }

            }
        }
        
        return $result;
    }

    private function generateMapGetCurrentLocationMethodsScript($fieldFullName, $fieldDataPath, $isReadOnly){


        $this->mapsFields[] = $fieldFullName;

        ob_start(); ?>

        <?= $fieldFullName ?>_get_current_location(){
            var self = this;
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    self.<?= $fieldDataPath ?>_lat = position.coords.latitude;
                    self.<?= $fieldDataPath ?>_lng = position.coords.longitude;

                    self.pin_marker(<?= $fieldFullName ?>_markers, pos, <?= $fieldFullName ?>_map);

                }, function() {
                    alert('Error: The Geolocation service failed');
                });
            } else {
                // Browser doesn't support Geolocation
                alert('Error: Your browser doesn\'t support geolocation');
            }
        },
        <?= $fieldFullName ?>_set_location_manually(){

            <?php if($isReadOnly != true): ?>
            if(Number(this.<?= $fieldDataPath ?>_lat) == 0 || Number(this.<?= $fieldDataPath ?>_lng) == 0)
                return;
            var pos = {
                lat: Number(this.<?= $fieldDataPath ?>_lat),
                lng: Number(this.<?= $fieldDataPath ?>_lng)
            };
            
            this.pin_marker(<?= $fieldFullName ?>_markers, pos, <?= $fieldFullName ?>_map);

            <?php endif; ?>
            
        },
        <?= $fieldFullName ?>_InitMap_run: function(){
            <?= $fieldFullName ?>_InitMap();
            return null;
        },

        <?php

        return ob_get_clean();

    }

    



    public function generateFieldInitMapFunctions(){
        
        $result = "";
            
        foreach($this->fields as $field){

            if($field->field_type_id == "decimal" && $field->appearance_id == "7_map"){
                        
                if(_strtolower(substr($field->name, _strlen($field->name) -3,_strlen($field->name))) == "lat"){
                    $base_name = _strtolower(substr($field->name, 0,_strlen($field->name) - 4));

                    $result .= "
                    
                        var $base_name" . "_markers = [];
                        var $base_name" . "_map = null;


                        function $base_name" . "_InitMap() {
                            
                            var lat = this.vm.$base_name" . "_lat ?? 36;
                            var lng = this.vm.$base_name" . "_lng ?? 44;

                            var mapProp= {
                                center:new google.maps.LatLng(lat, lng),
                                zoom:5,
                            };
                            
                            var domMap = document.getElementById(\"$base_name" . "_map\");

                            if(domMap == undefined)
                                return;

                            $base_name" . "_map = new google.maps.Map(domMap,mapProp);
                    
                            vm.$base_name" . "_set_location_manually();
                            google.maps.event.addListener($base_name" . "_map, 'click', function(event) {
                                
                                vm.$base_name" . "_lat = event.latLng.lat();
                                vm.$base_name" . "_lng = event.latLng.lng();
                    
                                //vm.pin_marker($base_name" . "_markers, event.latLng, $base_name" . "_map);
                                for (var i = 0; i < $base_name" . "_markers.length; i++) {
                                    $base_name" . "_markers[i].setMap(null);
                                }
                
                                marker = new google.maps.Marker({position: event.latLng, map: $base_name" . "_map});
                                $base_name" . "_markers.push(marker);
                    
                            });
                    
                        }
                    
                    
                    
                    ";
                }
            } else if ($field->field_type_id == "field_collection"){
                foreach($field->getFields() as $fc){
                    if($fc->field_type_id == "decimal" && $fc->appearance_id == "7_map"){
                        
                        if(_strtolower(substr($fc->name, _strlen($fc->name) -3,_strlen($fc->name))) == "lat"){
                            $base_name = _strtolower(substr($fc->name, 0,_strlen($fc->name) - 4));
                            
                            $result .= "
                            
                                var current_$field->name" . "_$base_name" . "_markers = [];
                                var current_$field->name" . "_$base_name" . "_map = null;
        
        
                                function current_" . $field->name . "_" . $base_name . "_InitMap() {
                                    var lat = vm.current_$field->name.$base_name" . "_lat ?? 36;
                                    var lng = vm.current_$field->name.$base_name" . "_lng ?? 44;
                                    
                                    var mapProp= {
                                        center:new google.maps.LatLng(lat, lng),
                                        zoom:5,
                                    };
                                    
                                    var domMap = document.getElementById(\"current_" . $field->name . "_" . $base_name . "_map\");

                                    if(domMap == undefined)
                                        return;

                                    current_" . $field->name . "_" . $base_name . "_map = new google.maps.Map(domMap,mapProp);
                                    vm.current_" . $field->name . "_" . $base_name . "_set_location_manually();
                                    
                                    google.maps.event.addListener(current_" . $field->name . "_" . $base_name . "_map, 'click', function(event) {
                            
                                        vm.current_" . $field->name . "." . $base_name . "_lat = event.latLng.lat();
                                        vm.current_" . $field->name . "." . $base_name . "_lng = event.latLng.lng();
                            
                                        for (var i = 0; i < current_$field->name" . "_$base_name" . "_markers.length; i++) {
                                            current_" . $field->name . "_" . $base_name . "_markers[i].setMap(null);
                                        }
                        
                                        marker = new google.maps.Marker({position: event.latLng, map: current_" . $field->name . "_" . $base_name . "_map});
                                        current_" . $field->name . "_" . $base_name . "_markers.push(marker);
                            
                                    });
                            
                                }
                            
                            
                            ";
                        }
                    }
                }
            }
        }

        return $result;

    }

    public function includeGoogleMapsScript() : ?string{
        
        if(sizeof($this->mapsFields) == 0){
            return null;
        }

        $api_key = Application::getInstance()->env->get("GOOGLE_MAPS_API_KEY");
        
        return '<script src="https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&callback=initMaps"></script>';

    }

    public function generateInitMapFunctions(){
        
        $result = "";

        $result .= "
    
        function initMaps(){
            ";

            foreach($this->mapsFields as $base_name){
                $result .= "
                $base_name" . "_InitMap();
            ";
            
            }
            $result .= "
        }
        ";
    

        return $result;
    }
    

    public function generateMapPinMerkerMethods(){
        
        ob_start();

        ?>

            pin_marker(markers,pos, map){
            
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }

            marker = new google.maps.Marker({position: pos, map: map});
            markers.push(marker);

            map.setCenter(pos);

        },

        <?php

        return ob_get_clean();
    }
}