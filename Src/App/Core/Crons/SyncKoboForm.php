<?php

namespace App\Core\Crons;

use App\Core\Application;
use App\Core\DAL\MySQLDatabase;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;

Class SyncKoboForm extends BaseSyncOdkForm {

    private $id;
    private $params;
    private $kobo_url;
    private $kobo_api_token;
    
    public function __construct(int $id,array $params = array()) {
        
        $this->id = $id;
        $this->params = $params;

        $this->kobo_url = "https://kobo.humanitarianresponse.info/api/v2";
        $this->kobo_api_token = "21b9e988085db6bf099590aeabcaee6901b6617c";

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
		} else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Content-Type is empty for cron (" . $this->cronObj->id . ")");
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

        if($this->ctypeObj)
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
        $connection_string_obj = $this->coreModel->nodeModel("db_connection_strings")
            ->id($this->cronObj->db_connection_string_id)
            ->loadFirstOrFail();
        
        $this->odkDb = new MySqlDatabase($connection_string_obj->name,$connection_string_obj->host,$connection_string_obj->db_name,$connection_string_obj->username,$connection_string_obj->password,$connection_string_obj->port);
        
        //Retrive data from ODK Aggregate server
        $results = $this->retirveDataFromKoboServer($this->batchSize);

        //If the reseult is empty then return
        if(!isset($results) || sizeof($results) == 0){
            return;
        }
        
        //Loop throw the result one by one and sync them
        foreach($results as $row){

            $this->ukey = \App\Helpers\MiscHelper::randomString(25);
            $this->MainRecordUri = $row->_uuid;

            //$this->coreModel->addCronLog($this->ukey, $this->id, "started", "Started");

            //get email address of the user submitted the this form
            // if($this->getFormUserId($row->_CREATOR_URI_USER) != true) {
            //     continue;
            // }

            if(isset($row->_submitted_by)) {
                
                $email = $row->_submitted_by;
                $default_email_domain_name = Application::getInstance()->env->get('default_email_domain_name');
                //If username is in ignore list, then mark it as synced and ignore
                //TODO: Ignore this record if the user is in ignore list

                if (!_strpos($email,"@")){
                    $email.="@$default_email_domain_name";
                }

                $this->userId = $this->coreModel->getUserIdByEmail($email);

                if(empty($this->userId)){

                    echo "email '" . e($email) . "' not found in users";
                    
                    if(!empty($this->ukey)){
                        $this->coreModel->addCronLog($this->ukey, $this->cronObj->id, "failed", "$email not found in users");
                    }

                    Application::getInstance()->pushNotification->add("sync " . $this->cronObj->id . ": $email not found in users $this->MainRecordUri",Application::getInstance()->user->getSystemUserId(), null, array('admin'), "crons", $this->cronObj->id,"danger",true);
                    exit;
                }
                
            } else {
                $this->userId = Application::getInstance()->user->getGuestUserId();
            }
            

            //Check if this record is already synced before or not
            // if($this->checkIfFromAlreadySynced()) {
            //     continue;
            // }
            
            /**
            * If the we reach this line means the data passed based checking and we start creating a data object to send to node_save()
            * We devided cron strucutre into 4 parts
            *   1. Main record: This convers all fields which their value is not multi (text, number, date) and not complex like (image).
            *   2. Single Image: This covers single image fields.
            *   3. Field-Collection: This covers Field-Collection which means they are sub table for our main record.
            *   4. Multi Image: This covers multiple image fields which also they are sub table for our main record.
            */
          
            // We call generateData() to create the data object with later we pass to node_save()
            // This will cover point 1 in the above list.
            $data = $this->generateData($row);

            $synced_fc = array();
            //Here we will loop throw Field-Collection and multi-image fields
            foreach($this->cronObj->field_collections as $itm){

                if(!empty($itm->repeat_name) != true || in_array($itm->repeat_name, $synced_fc))
                    continue;
                
                //Save the repeat name in synced_fc so in will not be synced again
                $synced_fc[] = $itm->repeat_name;
                
                if($itm->tag == "image_repeat") {
                    //Here we will call generateImageMultiData() to create sub table from the multi-image
                    //Then assign the return value to the main data
                    //This will cover point 4 in the above list.
                    $data->{$itm->gc_name} = $this->generateImageMultiData($itm,$row->_uuid, false);
                } else {
                    //Here we will call generateFCData() to create sub table from Field-Collection
                    //Then assign the return value to the main data
                    //This will cover point 3 in the above list.
                    $data->{$itm->repeat_name} = $this->generateFCData($row,$itm->repeat_name);
                }
            }

            /**
            * If we reach here means the data object is ready 
            * We will pass the $data to node_save() to save it to database
            * Will node_save we also send below paramters as setting
            *   1. dont_add_log as true so node_save() will not add log since we want to add long manually.
            *   2. dont_validate as true so node_save() will not do data validation for this data since we already did validation in odk.
            */
            
            print_r($data);
            exit;
            $result = $this->coreModel->node_save($data,array("user_id" => $this->userId, "dont_add_log" => true, "dont_validate" => true));
            
            //If the data saved successfuly then we add URI of the record to a table inside ODK Aggregate database 
            //so for future sync this record will be falged as synced and will not come back again
            if(intval($result) == 0 || $this->markAsSynced(intval($result)) != true){
                throw new \App\Exceptions\CriticalException("Something went wrong while saving cron record");
            }
            
            

        }

    }
    

    private function retirveDataFromKoboServer($batch_size) {


        $curl = curl_init();
        
        curl_setopt_array($curl, 
            array(
                CURLOPT_URL => sprintf("%s/assets/%s/data.json",$this->kobo_url, $this->cronObj->id),
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Authorization:Token ' . $this->kobo_api_token
                )
            )
        );

        $response = curl_exec($curl);

        if ($response === false) 
            $response = curl_error($curl);
        
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl); 

        return json_decode($response)->results;
    }

    private function getSslPage($url) {
   
        $curl = curl_init();
        
        curl_setopt_array($curl, 
            array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    'Authorization:Token ' . $this->kobo_api_token
                )
            )
        );

        $response = curl_exec($curl);

        if ($response === false) 
            $response = curl_error($curl);
        
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
    
        return $response;
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

        $row = (object)array_change_key_case((array)$row, CASE_LOWER);

        //Create new object
        $data = new \stdClass();

        //Set Content-Type name
        $data->sett_ctype_id = $this->ctypeObj->id;

        $data->odk_auri = $row->_uuid;
        $data->odk_form_version = $this->cronObj->version;
        
        if(isset($row->start)){
            $data->created_date = date_format(date_create($row->start),"d/m/Y H:i:s");
        }
        $data->created_user_id = $this->userId;

        foreach($this->cronObj->fields as $field){

            $odk_field_name = _strtolower($field->odk_name);

            if($field->data_type == "calculate" && isset($row->{$odk_field_name})) {

                if(_strpos($row->{$odk_field_name}, "T00:") !== false) {
                    $row->{$odk_field_name} = substr($row->{$odk_field_name}, 0, _strpos($row->{$odk_field_name}, "T00:"));
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
                //TODO
                //$data = $this->generateImageSingle($data, $field, $row->_uuid);

                $orginal_file_name = _str_replace(" ", "_", $row->{$odk_field_name});
                $url = "";

                foreach($row->_attachments as $attachment) {
                    $ex = _explode("/", $attachment->filename);
                    if($ex[sizeof($ex) - 1] == $orginal_file_name) {
                        $url = $attachment->download_medium_url;
                        break;
                    }
                    
                }

                if(empty($url)) {
                    throw new \App\Exceptions\CriticalException("Attachment not found for cron (" . $this->cronObj->id . ")");
                }

                if(!file_exists(UPLOAD_DIR_FULL . "\\" . $this->ctypeObj->id)){
                    mkdir(UPLOAD_DIR_FULL . "\\" . $this->ctypeObj->id, 0777, true);
                }
                
                $file_extension = "jpg";

                $new_file_name = sprintf("%s.%s", 
                    time(), 
                    $file_extension
                );

                $file_path = UPLOAD_DIR_FULL . "\\" . $this->ctypeObj->id . "\\" . $new_file_name;
                $thum_dest_path = UPLOAD_DIR_FULL . "\\" . $this->ctypeObj->id . "\\thumbnails\\" . $new_file_name;
                
                $res = $this->getSslPage($url);
                
            
                $im = imagecreatefromstring($res);
            
                $resp = imagejpeg($im, $file_path,80);

                imagedestroy($im);
                                    
                \App\Helpers\UploadHelper::resizeImage($file_path, $thum_dest_path, true);
                
                $file_size = filesize($file_path);
                $file_type = "image/jpeg";

                $data->{$field->gc_name . "_name"} = $new_file_name;
                $data->{$field->gc_name . "_original_name"} = $orginal_file_name;
                $data->{$field->gc_name . "_size"} = $file_size;
                $data->{$field->gc_name . "_type"} = $file_type;
                $data->{$field->gc_name . "_extension"} = $file_extension;
                
                
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
            
            $cron_name = $this->cronObj->id;

            $query .= " FROM {$cron_name}_{$field->image_multi_repeat_name}";
            $query .= " LEFT JOIN {$base_table_name}_bn on {$base_table_name}_bn._PARENT_AURI = {$cron_name}_{$field->image_multi_repeat_name}._URI";
            $query .= " LEFT JOIN {$base_table_name}_ref ON {$base_table_name}_ref._DOM_AURI = {$cron_name}_{$field->odk_name}_bn._URI ";
            $query .= " LEFT JOIN {$base_table_name}_blb on {$base_table_name}_blb._URI = {$base_table_name}_ref._SUB_AURI  ";
            $query .= " WHERE {$cron_name}_{$field->image_multi_repeat_name}._PARENT_AURI = :id and {$base_table_name}_bn.unrooted_file_path is not null";
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