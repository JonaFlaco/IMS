<?php

/**
 * This class is responsible to retrive data from ODK Aggregate without writing custom code for each form.
 * For that we have 'Crons' table in which field mapping for each form is stored. 
 * This class will get field mapping from 'Crons' and based on that it will retrive data from ODK Aggregate an save it in its Content-Type. 
 */

namespace App\Core\Crons;

use App\Core\Application;
use App\Core\DAL\MySQLDatabase;
use App\Core\Gctypes\Ctype;

Class SyncOdkForm extends BaseSyncOdkForm {

    private $connection_string_obj;
    
    public function __construct($id,array $params = array()) {
        
        $this->id = $id;
        $this->params = $params;

        $this->batchSize = 1;
        if(isset($params['batch_size']) && intval($params['batch_size']) > 0){
            $this->batchSize = intval($params['batch_size']);
        }

        $this->coreModel = Application::getInstance()->coreModel;
        
        $this->cronObj = $this->coreModel->nodeModel("crons")
            ->id($this->id)
            ->loadFirstOrFail();

        if(!empty($this->cronObj->ctype_id)){
			$this->ctypeObj = (new Ctype)->load($this->cronObj->ctype_id);
		}

        parent::__construct();
    }


    /**
    * sync
    *
    * @param  int $this->id
    * @param  array $params
    * @return void
    *
    * This is the public function, which receives a cron id and sync it.
    */
    public function run(){

        if(in_array($this->cronObj->status_id, [72,83])) {
            throw new \App\Exceptions\IlegalUserActionException("Unable to run archived or abandoned crons");
        }
        
        $fields = $this->ctypeObj->getFields();
    
        //Check if odk_auri field exist in fields of ctype or not, if not exist show error
        if(object_exist_in_array_of_objects($fields,"name", "odk_auri") == false){
            Application::getInstance()->pushNotification->add($this->ctypeObj->id . " odk_auri field not found",Application::getInstance()->user->getSystemUserId(), null, array('admin'), "crons", $this->id,"danger",true);
            throw new \App\Exceptions\NotFoundException("odk_auri field not found");
        }

        //Check if odk_form_version field exist in fields of ctype or not, if not exist show error
        if(object_exist_in_array_of_objects($fields,"name", "odk_form_version") == false){
            Application::getInstance()->pushNotification->add($this->ctypeObj->id . " odk_form_version field not found",Application::getInstance()->user->getSystemUserId(), null, array('admin'), "crons", $this->id,"danger",true);
            throw new \App\Exceptions\NotFoundException("odk_form_version field not found");
        }
    
        //Get connection string for the cron,if found the instanciate it else show error
        $this->connection_string_obj = $this->coreModel->nodeModel("db_connection_strings")
            ->id($this->cronObj->db_connection_string_id)
            ->loadFirstOrFail();
        
        $this->odkDb = new MySqlDatabase($this->connection_string_obj->name,$this->connection_string_obj->host,$this->connection_string_obj->db_name,$this->connection_string_obj->username,$this->connection_string_obj->password,$this->connection_string_obj->port);
        
        //Retrive data from ODK Aggregate server
        $results = $this->retirveDataFromODK($this->batchSize);

        //If the reseult is empty then return
        if(!isset($results) || sizeof($results) == 0){
            return;
        }
        
        $has_error = false;
        //Loop throw the result one by one and sync them
        foreach($results as $row){

            $this->ukey = \App\Helpers\MiscHelper::randomString(25);
            $this->MainRecordUri = $row->TheMainURI;

            //$this->coreModel->addCronLog($this->ukey, $this->id, "started", "Started");

            try {

                //get email address of the user submitted the this form
                if($this->getFormUserId($row->_CREATOR_URI_USER) != true) {
                    $has_error = true;
                    continue;
                }
                

                //Check if this record is already synced before or not
                if($this->checkIfFromAlreadySynced()) {
                    $has_error = true;
                    continue;
                }
                
                /**
                * If the we reach this line means the data passed based checking and we start creating a data object to send to node_save()
                * We devided cron strucutre into 4 parts
                *   1. Main record: This convers all fields which their value is not multi (text, number, date) and not complex like (image).
                *   2. Single Image: This covers single image fields.
                *   3. Field-Collections: This covers Field-Collections which means they are sub table for our main record.
                *   4. Multi Image: This covers multiple image fields which also they are sub table for our main record.
                */
            
                // We call generateData() to create the data object with later we pass to node_save()
                // This will cover point 1 in the above list.
                $data = $this->generateData($row);

                $synced_fc = array();
                //Here we will loop throw Field-Collections and multi-image fields
                foreach($this->cronObj->field_collections as $itm){

                    if(!empty($itm->repeat_name) != true || in_array($itm->repeat_name, $synced_fc))
                        continue;
                    
                    //Save the repeat name in synced_fc so in will not be synced again
                    $repeat_name[] = $itm->repeat_name;
                    
                    if($itm->tag == "image_repeat") {
                        //Here we will call generateImageMultiData() to create sub table from the multi-image
                        //Then assign the return value to the main data
                        //This will cover point 4 in the above list.
                        $data->{$itm->gc_name} = $this->generateImageMultiData($itm,$row->TheMainURI, false);
                    } else {
                        //Here we will call generateFCData() to create sub table from Field-Collection
                        //Then assign the return value to the main data
                        //This will cover point 3 in the above list.
                        

                        if(empty($row->{$itm->repeat_name . "_URI"})) continue;
                        
                        $data->{$itm->gc_fc_name} = $this->generateFCData($row,$itm->repeat_name);
                    }
                }

                /**
                * If we reach here means the data object is ready 
                * We will pass the $data to node_save() to save it to database
                * Will node_save we also send below paramters as setting
                *   1. dont_add_log as true so node_save() will not add log since we want to add long manually.
                *   2. dont_validate as true so node_save() will not do data validation for this data since we already did validation in odk.
                */
                
                $result = $this->coreModel->node_save($data,array("user_id" => $this->userId, "dont_add_log" => true, "dont_validate" => true));
                
                //If the data saved successfuly then we add URI of the record to a table inside ODK Aggregate database 
                //so for future sync this record will be falged as synced and will not come back again
                if(intval($result) == 0 || $this->markAsSynced(intval($result)) != true){
                    throw new \App\Exceptions\CriticalException("Something went wrong while saving cron record");
                }
                
            } catch(\Exception $exc) {

                $this->markAsHasIssue();

                Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "failed", $exc->getMessage());

                throw $exc;
            }
            

        }

        return $has_error == false;
    }
    


    
    
    /**
     * retirveDataFromODK
     *
     * @param  int $batch_size
     * @return array
     *
     * This function will create dynamic query to load data from ODK Aggregate Database and return backt the result
     */
    private function retirveDataFromODK($batch_size = 1) : array {
        
        $cron_id = $this->cronObj->id;

        //We will work with these data types only
        $filterd_types = array("text","geotrace","calculate","geopoint","decimal","integer","select_one","select_multiple","date","image","file","start","end");


        //If the form is too big ODK Aggregate will create multiple core tables (core, core2, core3)
        //So here with this query we will get list of all core tables for the form we are syncing 
        $this->odkDb->query("select table_name as table_name FROM information_schema.tables where table_schema='" . $this->connection_string_obj->db_name . "' and table_name like '{$cron_id}_core%' and table_name != '{$cron_id}_core'");

        $results = $this->odkDb->resultSet();
        
        $cores = array();
        foreach($results as $row){
            array_push($cores, $row->table_name);
        }


        //Start generating the dynamic query to load data
        //Here we will add all simple single-value fields like text, date, number, gps
        $query = "SELECT {$cron_id}_core._URI as TheMainURI, {$cron_id}_core.* \n";

        // Add all core tables
        foreach($cores as $core){
            $query .= ",$core.* ";
        }
		
		$fieldCollectionNames = [];
		foreach($this->cronObj->field_collections as $field){
			if(!in_array($field->repeat_name, $fieldCollectionNames))
				$fieldCollectionNames[] = $field->repeat_name;
			
			continue;
		}
		
		$fieldCollectionExtraTables = [];
		foreach($fieldCollectionNames as $fcName){
			//$this->odkDb->query("select table_name FROM information_schema.tables  where table_schema='" . $this->connection_string_obj->db_name . "' and table_name like '{$cron_id}_{$fcName}%' and table_name != '{$cron_id}_{$fcName}'");

            $qry = "select table_name as table_name FROM information_schema.tables  where table_schema='" . $this->connection_string_obj->db_name . "' and table_name like '{$cron_id}_{$fcName}%' 
            and table_name != '{$cron_id}_{$fcName}' and table_name not like '{$cron_id}_{$fcName}%_blb'
            and table_name != '{$cron_id}_{$fcName}' and table_name not like '{$cron_id}_{$fcName}%_bn'
           and table_name != '{$cron_id}_{$fcName}' and table_name not like '{$cron_id}_{$fcName}%ref'";
           
           $this->odkDb->query($qry);

			$results = $this->odkDb->resultSet();
			
			if(sizeof($results) > 0) {
				$item = new \stdClass();
				$item->name = $fcName;
				$item->list = [];
				foreach($results as $row){
					$item->list[] = $row->table_name;
				}
				
				$fieldCollectionExtraTables[] = $item;
			}
		}
		
        //Here we will load multiple select fields
        foreach($this->cronObj->fields as $field){

            $field->odk_name = strtoupper($field->odk_name);

            if ($field->data_type == "select_multiple"){
                $query .= " ,CONCAT((select group_concat(ifnull(VALUE,'') SEPARATOR '" . CRONS_VALUE_DELIMITER . "') as ee from {$cron_id}_{$field->odk_name}  where {$cron_id}_{$field->odk_name}._TOP_LEVEL_AURI={$cron_id}_core._URI order by {$cron_id}_{$field->odk_name}._URI desc)) as {$field->odk_name}";
            }

        }


        //Here we will load Field-Collection columns
        $current_fc = "";
        foreach($this->cronObj->field_collections as $field){

            $field->odk_name = strtoupper($field->odk_name);
            $field->repeat_name = strtoupper($field->repeat_name);
            
            //exclude not support data types
            if(in_array(_strtolower($field->data_type), $filterd_types) != true){
                return [];
            }

            //foreach Field-Collection add this once
            if($current_fc != $field->repeat_name){

                $current_fc = $field->repeat_name;
                $sub_table_name = "{$cron_id}_{$field->repeat_name}";
				
				$extra_joins = "";
				foreach($fieldCollectionExtraTables as $o) {
					
					if(_strtolower($o->name) == _strtolower($field->repeat_name)) {
						
						$e = 0;
						foreach($o->list as $t) {
							$extra_joins .= " LEFT JOIN {$t} c{$e} ON {$sub_table_name}._URI = c{$e}._PARENT_AURI ";
							$e++;
						}
					}
				}
				
                $query .= " ,CONCAT_WS(',',(select group_concat(ifnull(_URI,'') SEPARATOR '" . CRONS_VALUE_DELIMITER . "') as ee from $sub_table_name where $sub_table_name._TOP_LEVEL_AURI = {$cron_id}_core._URI order by $sub_table_name._URI desc)) as {$current_fc}_URI\n";
            }

            
            if($field->data_type == "image" || $field->data_type == "file"){
                
            } else if($field->data_type == "geopoint"){
                
                $query .= " ,CONCAT_WS(',',(select group_concat(ifnull({$field->odk_name}_LAT,'') SEPARATOR '" . CRONS_VALUE_DELIMITER . "') as ee from $sub_table_name where $sub_table_name._TOP_LEVEL_AURI = {$cron_id}_core._URI order by $sub_table_name._URI desc)) as {$current_fc}_{$field->odk_name}_LAT \n";
                
                $query .= " ,CONCAT_WS(',',(select group_concat(ifnull({$field->odk_name}_LNG,'') SEPARATOR '" . CRONS_VALUE_DELIMITER . "') as ee from $sub_table_name where $sub_table_name._TOP_LEVEL_AURI = {$cron_id}_core._URI order by $sub_table_name._URI desc)) as {$current_fc}_{$field->odk_name}_LNG \n";

            } else if ($field->data_type == "select_multiple"){
                
                $sub_table_name = "{$cron_id}_{$field->odk_name}";

                $query .= " ,CONCAT((select group_concat(concat(_PARENT_AURI,ifnull(VALUE,'')) SEPARATOR '" . CRONS_VALUE_DELIMITER . "') as ee from $sub_table_name where $sub_table_name._TOP_LEVEL_AURI = {$cron_id}_core._URI order by $sub_table_name._URI desc)) as {$current_fc}_{$field->odk_name} \n";

            } else {

                $sub_table_name = "{$cron_id}_{$field->repeat_name}";
                    
				$extra_joins = "";
				foreach($fieldCollectionExtraTables as $o) {
					
					if(_strtolower($o->name) == _strtolower($field->repeat_name)) {
						
						$e = 0;
						foreach($o->list as $t) {
							$extra_joins .= " LEFT JOIN {$t} c{$e} ON {$sub_table_name}._URI = c{$e}._PARENT_AURI ";
							$e++;
						}
					}
				}
				
                $query .= " ,CONCAT_WS(',',(select group_concat(ifnull($field->odk_name,'') SEPARATOR '" . CRONS_VALUE_DELIMITER . "') as ee from $sub_table_name $extra_joins where $sub_table_name._TOP_LEVEL_AURI = {$cron_id}_core._URI order by $sub_table_name._URI desc)) as {$current_fc}_{$field->odk_name} \n";
            }
        }
    
        $query .= " FROM {$cron_id}_core \n";
        $query .= " left join odk_records_with_issue iss on iss._URI = {$cron_id}_core._URI and iss.source_table='{$cron_id}_core' \n";
        //Add joins
        foreach($cores as $core){
            $query .= " left join $core on {$cron_id}_core._URI = $core._TOP_LEVEL_AURI \n";
        }
        
        $query .= " WHERE {$cron_id}_core._URI NOT in (select _URI from odk_synced_records where source_table = '{$cron_id}_core') \n";
        //Set batch size
        $query .= "ORDER BY iss._URI limit 0,$batch_size \n";
        
        $this->odkDb->query($query);
        
        //Return the result
        return $this->odkDb->resultSet();
    }
    


    
    
    /**
     * generateData
     *
     * @param  object $row
     * @return object
     *
     *This function will recieve row data from ODK Aggregate and create data object
     */
    private  function generateData($row) : object {

        //Create new object
        $data = new \stdClass();

        //Set Content-Type name
        $data->sett_ctype_id = $this->ctypeObj->id;

        $data->odk_auri = $row->TheMainURI;
        $data->odk_form_version = $this->cronObj->version;
        
        if(isset($row->START)){
            $data->created_date = date_format(date_create($row->START),"d/m/Y H:i:s");
        }

        foreach($this->cronObj->fields as $field){

            $odk_field_name = strtoupper($field->odk_name);
            
            if($field->data_type == "calculate" && isset($row->{$odk_field_name})) {

                if(_strpos($row->{$odk_field_name}, "T") !== false) {
                    $row->{$odk_field_name} = substr($row->{$odk_field_name}, 0, _strpos($row->{$odk_field_name}, "T"));
                }
                
            }

            if($field->data_type == "date" || $field->data_type == "start" || $field->data_type == "end"){
                
                if(isset($row->{$odk_field_name})){   
                    $data->{$field->gc_name} = date_format(date_create($row->{$odk_field_name}),"d/m/Y H:i:s");
                }

            } else if ($field->data_type == "geopoint"){

                $data->{$field->gc_name . "_lat"} = $row->{$odk_field_name . "_LAT"};
                $data->{$field->gc_name . "_lng"} = $row->{$odk_field_name . "_LNG"};
                
            } else if ($field->data_type == "select_multiple"){
                
                $data->{$field->gc_name} = array();

                foreach(_explode(CRONS_VALUE_DELIMITER,$row->{$odk_field_name}) as $item){
                                
                    if($item == CRONS_IGNORE_VALUE)
                        continue;

                    $data->{$field->gc_name}[] = $item;
                
                }

            } else if ($field->data_type == "image" || $field->data_type == "file"){

                //If it is single-image then call generateImageSingle() to handle it
                $data = $this->generateImageSingle($data, $field, $row->TheMainURI);
                
            } else {

                if(!isset($row->{$odk_field_name})){ 
                    continue;      
                }
                if($field->data_type == "select_one" && $row->{$odk_field_name} == CRONS_IGNORE_VALUE){
                    continue;
                }

                $data->{$field->gc_name} = $row->{$odk_field_name};
                
            }
        }

        return $data;

    }




    
    /**
     * generateFCData
     *
     * @param  object $row
     * @param  string $current_fc
     * @return void
     *
     * This function will recieve row data and generate sub table for each Field-Collection
     */
    private  function generateFCData($row, $current_fc = ""){
        
        //Create data array, since Field-Collection is sub table
        $data = array();
        
        //This will be a temp storage, which will store data and later put it to data object
        $storage = array();

        $current_fc = strtoupper($current_fc);

        $odk_field_name = strtoupper($current_fc . "_URI");
        $storage[$odk_field_name] = $row->$odk_field_name;

        $current_fc_size = sizeof(_explode(CRONS_VALUE_DELIMITER,$row->$odk_field_name));

        foreach($this->cronObj->field_collections as $field){
            if(strtoupper($field->repeat_name) != $current_fc)
                continue;

            if(_strtolower($field->data_type) == "image" || _strtolower($field->data_type) == "file")
                continue;
                
            $odk_field_name = strtoupper($current_fc . "_" . $field->odk_name);

            if($field->data_type == "geopoint"){
                $storage[$odk_field_name . "_LAT"] = $row->{$odk_field_name . "_LAT"};
                $storage[$odk_field_name . "_LNG"] = $row->{$odk_field_name . "_LNG"};
            } else {
                $storage[$odk_field_name] = $row->$odk_field_name;
            }
            
        }

        
        //Loop throw Field-Collection records one by one
        for($x = 0; $x < $current_fc_size; $x++){
            
            //Create sub object
            $sub = new \stdClass();

            foreach($this->cronObj->field_collections as $field){
                if(strtoupper($field->repeat_name) != $current_fc)
                    continue;
                
                $odk_field_name = strtoupper($current_fc . "_" . $field->odk_name);
                
                $current_auri = _explode(CRONS_VALUE_DELIMITER, $storage[$current_fc . "_URI"])[$x];
                
                if($field->data_type == "select_multiple"){
                    
                    if(isset($storage[$odk_field_name])){

                        $sub->{$field->gc_name} = array();

                        foreach(_explode(CRONS_VALUE_DELIMITER,$storage[$odk_field_name]) as $item){
                            
                            if($item == CRONS_IGNORE_VALUE)
                                continue;

                            if(_strpos($item, $current_auri) !== false){
                                $sub->{$field->gc_name}[] = _str_replace($current_auri,"",$item);
                            }
                            
                        }
                        
                    }
                    
                } else if ($field->data_type == "geopoint"){

                    $odk_field_name = strtoupper($current_fc . "_" . $field->odk_name);

                    $lat = _explode(CRONS_VALUE_DELIMITER,$storage[$odk_field_name . "_LAT"])[$x];
                    $lng = _explode(CRONS_VALUE_DELIMITER,$storage[$odk_field_name . "_LNG"])[$x];

                    $sub->{$field->gc_name . "_LAT"} = $lat;
                    $sub->{$field->gc_name . "_LNG"} = $lng;
                    
                } else if(_strtolower($field->data_type) == "image" || _strtolower($field->data_type) == "file"){

                    if($field->tag == "image_repeat"){
                        $fc_rec_auri = _explode(CRONS_VALUE_DELIMITER, $row->{$current_fc . "_URI"})[$x];
                        
                        $sub->{$field->gc_name} = $this->generateImageMultiData($field,$fc_rec_auri, true);
                        
                    } else {
                    
                        //If it is single-iamge then callgenerateImageSingle() to handle it
                        $sub = $this->generateImageSingle($sub,$field, $current_auri);
                        
                    }
                    
                } else if($field->data_type == "date"){
                    $value = _explode(CRONS_VALUE_DELIMITER,$storage[$odk_field_name])[$x];
                    if(isset($value)){
                        $sub->{$field->gc_name} = date_format(date_create($value),"d/m/Y H:i:s");
                    }
                    
                } else {
                    
                    $sub->{$field->gc_name} = _explode(CRONS_VALUE_DELIMITER,$storage[$odk_field_name])[$x];
                }

            }

            //Put it to data array
            $data[] = $sub;
        }


        //Return data array
        return $data;

    }
    


    
    
    /**
     * generateImageSingle
     *
     * @param  object $data
     * @param  object $field
     * @param  string $uri
     * @return void
     *
     *This function will put single-image fields to data
     */
    private  function generateImageSingle($data, $field, $uri){
        
        $base_table_name = $this->cronObj->id . "_" . $field->odk_name;

        //Get the image in database
        $query = "SELECT ";
        $query .= " {$base_table_name}_blb.value as VALUE, ";
        $query .= " {$base_table_name}_bn.unrooted_file_path as FILE_NAME ";
        $query .= " FROM {$base_table_name}_ref ";
        $query .= " LEFT JOIN {$base_table_name}_blb on {$base_table_name}_blb._URI = {$base_table_name}_ref._SUB_AURI  ";
        $query .= " LEFT JOIN {$base_table_name}_bn on {$base_table_name}_bn._URI = {$base_table_name}_ref._DOM_AURI  ";
        $query .= " WHERE {$base_table_name}_bn._PARENT_AURI = :id ";
        
        $this->odkDb->query($query);

        $this->odkDb->bind(':id', $uri);
        $sub_row = $this->odkDb->resultSingle();
        if(isset($sub_row)){

            
            //Send the image to UploadFile to copy the file to server and return object with required info
            $file = \App\Helpers\UploadHelper::uploadFile($sub_row->{"VALUE"}, $this->ctypeObj->id, $sub_row->{"FILE_NAME"}, null, true);

            if(isset($file)){
                //Assign single-image info to data object
                $data->{$field->gc_name . "_name"} = $file->name;
                $data->{$field->gc_name . "_original_name"} = $file->original_name;
                $data->{$field->gc_name . "_extension"} = $file->extension;
                $data->{$field->gc_name . "_size"} = $file->size;
                $data->{$field->gc_name . "_type"} = $file->type;
            }
        }

        //return data object;
        return $data;

    }
    
    /**
     * generateImageMultiData
     *
     * @param  object $field
     * @param  string $uri
     * @param  bool $is_inside_fc
     * @return void
     *
     *This function will create sub table for multi-image fields
     */
     


    
    
    private  function generateImageMultiData($field, $uri, $is_inside_fc = false){
    
        //create data array
        $data = array();
        
        if(!isset($field) || $field == array())
            return $data;

        $odk_field_name = strtoupper($field->odk_name);
        
        $base_table_name = $this->cronObj->id . "_" . $odk_field_name;

        //Get the images from ODK Aggregate database
        $query = "SELECT ";
        $query .= " {$base_table_name}_blb.value as VALUE, ";
        $query .= " {$base_table_name}_bn.unrooted_file_path as FILE_NAME ";

        if($is_inside_fc){
            
            $cron_id = $this->cronObj->id;

            $query .= " FROM {$cron_id}_{$field->image_multi_repeat_name}";
            $query .= " LEFT JOIN {$base_table_name}_bn on {$base_table_name}_bn._PARENT_AURI = {$cron_id}_{$field->image_multi_repeat_name}._URI";
            $query .= " LEFT JOIN {$base_table_name}_ref ON {$base_table_name}_ref._DOM_AURI = {$cron_id}_{$field->odk_name}_bn._URI ";
            $query .= " LEFT JOIN {$base_table_name}_blb on {$base_table_name}_blb._URI = {$base_table_name}_ref._SUB_AURI  ";
            $query .= " WHERE {$cron_id}_{$field->image_multi_repeat_name}._PARENT_AURI = :id and {$base_table_name}_bn.unrooted_file_path is not null";
        } else {
            $query .= " FROM {$base_table_name}_ref ";
            $query .= " LEFT JOIN {$base_table_name}_blb on {$base_table_name}_blb._URI = {$base_table_name}_ref._SUB_AURI  ";
            $query .= " LEFT JOIN {$base_table_name}_bn on {$base_table_name}_bn._URI = {$base_table_name}_ref._DOM_AURI  ";
            $query .= " WHERE {$base_table_name}_ref._TOP_LEVEL_AURI = :id ";
        }

        $this->odkDb->query($query);
        $this->odkDb->bind(':id', $uri);
        $results = $this->odkDb->resultSet();

        //Loop throw images one by one
        foreach($results as $sub_row){

            //Send the image to UploadHelper to save the image in serer and return back required info
            $file = \App\Helpers\UploadHelper::uploadFile($sub_row->{"VALUE"}, $this->ctypeObj->id, $sub_row->{"FILE_NAME"}, null, true);

            if(isset($file)){
                //create object for each image
                $item = new \stdClass();
                $item->name = $file->name;
                $item->original_name = $file->original_name;
                $item->extension = $file->extension;
                $item->size = $file->size;
                $item->type = $file->type;

                //Add it to data array
                $data[] = $item;
            }
        }

        //return back data array
        return $data;
    }
    
}