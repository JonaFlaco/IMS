<?php 

/**
 * This function converts node object to json format then we will pass it to legacy save function to save the data to database
 */

namespace App\Models\Sub;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use Exception;
use stdClass;
use TypeError;

class NodeJsonToOjbect {

    private $coreModel;
    private $app;


    public function __construct() {
        $this->app = Application::getInstance();
        $this->coreModel = $this->app->coreModel;
    }
    
    /**
     * main
     *
     * @param  string $ctypeId
     * @param  object $data
     * @param  string $justification
     * @return void
     *
     * Main function
     */
    public function main($ctypeId, $recordData, $justification = null, $token = null) : string {

        if(!isset($recordData) || $recordData == null){
            throw new \App\Exceptions\MissingDataFromRequesterException("Data is empty");
        }

        $ctypeObj = (new Ctype)->load($ctypeId);
        $fields = $ctypeObj->getFields();
    
        $recordData = object_set_property_to_lowercase($recordData);

        $data = new \StdClass();

        $data->justification = $justification;
        
        $data->sett_is_update = property_exists($recordData, "id") && _strlen($recordData->id) > 0;
        
        if(isset($recordData->sett_is_update))
            $data->sett_is_update = $recordData->sett_is_update;

        $data->token = $token;
        
        $data->tables = $this->mapFields($recordData, $ctypeObj, $fields);
    
        // 3. Field-Collection
        foreach($fields as $field){
            if($field->field_type_id == "field_collection" && isset($recordData->{$field->name})){
                $fc_obj = (new Ctype)->load($field->data_source_id);

                $fc_fields = $field->getFields();

                $fieldCollectionData = [];
                foreach($recordData->{$field->name} as $itm){
                    
                    $x = $this->mapFields($itm, $fc_obj, $fc_fields);
                    $fieldCollectionData = array_merge($fieldCollectionData, $x);

                }

                $fieldCollection = new stdClass();
                $fieldCollection->type = "field_collection";
                $fieldCollection->id = $fc_obj->id;
                $fieldCollection->data = new stdClass();
                $fieldCollection->data->type = "field_collection";
                $fieldCollection->data->id = $fc_obj->id;
                $fieldCollection->data->data = new stdClass();
                $fieldCollection->data->data->tables = $fieldCollectionData;

                $data->tables[] = $fieldCollection;
                

            }
        }

        return json_encode($data);

    }




    
    /**
     * generateJson
     *
     * @param  object $data
     * @param  array  $fields
     * @return string
     *
     * Generate json based on an object
     */
    private static function mapFields($recordData, $ctypeObj, $fields) : array {
        $result = [];

        $recordData = object_set_property_to_lowercase($recordData);

        $mainTableData = new \stdClass();
    
        foreach($fields as $field){
            if(     
                    ($field->field_type_id == "media") || 
                    ($field->field_type_id == "relation" && $field->is_multi == true) || 
                    $field->field_type_id == "field_collection" || 
                    $field->field_type_id == "button" || 
                    (!property_exists($recordData, $field->name) && $field->name != "id" && $field->name != "parent_id")
                ){
                continue;
            }
            
            $value = null;
            if(isset($recordData->{$field->name})){
                $value = $recordData->{$field->name};
            }
            
            if($field->field_type_id == "date"){
                
                if(_strlen($value) > 0 && _strtolower($value) != "null"){
                    
                    if(\DateTime::createFromFormat('d/m/Y H:i:s', $value)){
                        $value = \DateTime::createFromFormat('d/m/Y H:i:s', $value);
                    } else if (\DateTime::createFromFormat('Y-m-d H:i:s.v', $value)){
                        $value = \DateTime::createFromFormat('Y-m-d H:i:s.v', $value);
                    } else if (\DateTime::createFromFormat('Y/m/d', $value)){
                        $value = \DateTime::createFromFormat('Y/m/d', $value);
                    } else if (\DateTime::createFromFormat('Y-m-d', $value)){
                        $value = \DateTime::createFromFormat('Y-m-d', $value);
                    } else if (\DateTime::createFromFormat('d/m/Y', $value)){
                        $value = \DateTime::createFromFormat('d/m/Y', $value);
                    }
                    
                    try {
                        $value = date_format($value,"d/m/Y H:i:s");
                    } catch(Exception $exc) {
                        throw new \App\Exceptions\InvalidDateFormat('Invalid Date Format for "' . $field->title . '" (' . $value . ')');
                    } catch(TypeError $exc) {
                        throw new \App\Exceptions\InvalidDateFormat('Invalid Date Format for "' . $field->title . '" (' . $value . ')');
                    }
                }
            }

            $mainTableData->{$field->name} = $value;
        }

        //5. Attachment - Single
        foreach($fields as $field){
            if($field->field_type_id == "media" && $field->is_multi != true){

                if(property_exists($recordData, $field->name . "_name"))
                    $mainTableData->{$field->name . "_name"} = $recordData->{$field->name . "_name"};
                if(property_exists($recordData, $field->name . "_original_name"))
                    $mainTableData->{$field->name . "_original_name"} = $recordData->{$field->name . "_original_name"};
                if(property_exists($recordData, $field->name . "_size"))
                    $mainTableData->{$field->name . "_size"} = $recordData->{$field->name . "_size"};
                if(property_exists($recordData, $field->name . "_type"))
                    $mainTableData->{$field->name . "_type"} = $recordData->{$field->name . "_type"};
                if(property_exists($recordData, $field->name . "_extension"))
                    $mainTableData->{$field->name . "_extension"} = $recordData->{$field->name . "_extension"};
            }
        }

        
        $mainTable = new stdClass();
        $mainTable->type = "main_table";
        $mainTable->id = $ctypeObj->id;
        $mainTable->data = $mainTableData;

        $result[] = $mainTable;
        

        foreach($fields as $field){

            // 5. Attachment - Multi
            if($field->field_type_id == "media" && $field->is_multi == true){

                if(isset($recordData->{$field->name})){
                    $fileData = [];
                    
                    foreach($recordData->{$field->name} as $file){
                        
                        $obj_sub = new \stdClass();
                        
                        $obj_sub->name = $file->name;
                        $obj_sub->original_name = $file->original_name;
                        $obj_sub->size = $file->size;
                        $obj_sub->extension = $file->extension;
                        $obj_sub->type = $file->type;

                        $fileData[] = $obj_sub;
                    }

                    $file = new \stdClass();
                    $file->type = "file";
                    $file->id = $ctypeObj->id . "_" . $field->name;
                    $file->data = $fileData;

                    $result[] = $file;
                }

            }

            //2. Combobox Multi
            if($field->field_type_id == "relation" && $field->is_multi == true){

                if(isset($recordData->{$field->name})){
                    
                    $subTableData = [];
                    
                    foreach($recordData->{$field->name} as $item){
                        
                        if(is_object($item)){
                            $subTableData[] = $item->value;
                        } else {
                            $subTableData[] = $item;
                        }
                    }

                    $subTable = new \stdClass();
                    $subTable->type = "subtable";
                    $subTable->id = $ctypeObj->id . "_" . $field->name;
                    $subTable->data = $subTableData;

                    $result[] = $subTable;
                }

            }

        }

        return $result;
    }
    
}