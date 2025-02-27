<?php

/*
 * This is base class for syncing ODK Form, contains some helper classes
 */

namespace App\Core\Crons;

use App\Core\Application;
use App\Exceptions\ForbiddenException;
use App\Models\CTypeLog;

class BaseSyncOdkForm extends BaseCron {

    public $odkDb;
    public $cronObj;
    public $ctypeObj;
    public $batchSize;
    public $userId;
    public $MainRecordUri;

    public function __construct() {
        
        parent::__construct();
        
        if(isset($this->cronObj) && isset($this->cronObj->db_connection_string_id)) {
            $this->odkDb = \App\Helpers\DbHelper::getMySQLDbObj($this->cronObj->db_connection_string_id);
        }
        
        if(Application::getInstance()->user->isNotAdmin() && Application::getInstance()->request->isLocal() != true && !Application::getInstance()->request->isCli()){
            throw new ForbiddenException();
        }

    }


    public function loadDb() {
        if(isset($this->cronObj) && isset($this->cronObj->db_connection_string_id)) {
            $this->odkDb = \App\Helpers\DbHelper::getMySQLDbObj($this->cronObj->db_connection_string_id);
        }
    }

    // Get id for the used who submitted the form
    public function getFormUserId($userUri) {

        $this->userId = null;

        $username = self::cronDecodeUserNameFromString($userUri);

        //If it is not anonymous
        if(_strtolower($username) != _strtolower("anonymoususer")){

            //If username is in ignore list, then mark it as synced and ignore
            if(in_array(_strtolower($username),Application::getInstance()->globalVar->get('IGNORE_ODK_USERNAMES'))){
                
                $this->odkDb->query("INSERT INTO odk_synced_records (_URI, source_table) VALUES ('$this->MainRecordUri','" . $this->cronObj->id . "_core')");
                $this->odkDb->execute();

                return false;
            }

                
            // $end = _strtolower(substr($email, _strlen($email) - 4, 4));
            // if($end == ".com") {
                
            // } else if (!_strpos($email,"@")){
            //     $email .= "@$default_email_domain_name";
            // }
            // echo $email;exit;

            $this->userId = $this->coreModel->getUserIdByName($username);

            if(empty($this->userId)){

                if(!empty($this->ukey)){
                    $this->coreModel->addCronLog($this->ukey, $this->cronObj->id, "failed", "$username not found in users");
                }

                Application::getInstance()->pushNotification->add("sync " . $this->cronObj->id . ": $username not found in users $this->MainRecordUri",Application::getInstance()->user->getSystemUserId(), null, array('admin'), "crons", $this->cronObj->id,"danger",true);
                
                return false;
            }

        } else {
            //if anonymous then use GUEST ID
            $this->userId = Application::getInstance()->user->getGuestUserId();
        }

        return true;
    }

    //This method extracts username from odk form
    public static function cronDecodeUserNameFromString($string)
    {
        if (_strpos($string,'|'))
        {
            $tmp = substr($string, 0, _strpos($string,'|'));
            if (_strlen($tmp)>=4)
                $tmp = substr($tmp,4);
            return $tmp;
        }
        return $string;
    }


    public static function cronGetData($string , $data_type = 'string'){
        if (isset($string) && _strlen($string) > 0 )
        { 
            if ($data_type == 'date')
                return "\"" . date_format(date_create($string),"d/m/Y H:i:s") . "\"";
            else {
                $string = \App\Helpers\MiscHelper::eJson($string);
                return "\"" . $string . "\"";
            }
        }
        else
            return "null";
    }


    //this methods check if the form you are trying to sync already synced before or not
    public function checkIfFromAlreadySynced($ctypeId = null) {
        
        if(empty($ctypeId) && isset($this->ctypeObj)){
            $ctypeId = $this->ctypeObj->id;
        }

        if($this->coreModel->checkAuriIfExist($ctypeId,$this->MainRecordUri)){

            if(!empty($this->ukey)){
                $this->coreModel->addCronLog($this->ukey, $this->cronObj->id, "failed", "Tried to re-sync same record",$this->MainRecordUri);
            }

            Application::getInstance()->pushNotification->add($this->cronObj->id . ": Tried to re-sync same record $this->MainRecordUri",Application::getInstance()->user->getSystemUserId(), null, array('admin'), "crons", $this->cronObj->id,"danger",true);

            echo e($ctypeId) . " already have " . e($this->MainRecordUri);
            $qry = "INSERT INTO odk_synced_records (_URI, source_table) VALUES ('" . $this->MainRecordUri . "','" . $this->cronObj->id . "_core')";
            $this->odkDb->query($qry);
            $this->odkDb->execute();

            return true;
        }

        return false;
    }

    

    //This method mark a form as synced
    public function markAsSynced($recordId, $ctypeId = null, $logMessage = "ODK Form Synced"){

        if(empty($ctypeId) && isset($this->ctypeObj)){
            $ctypeId = $this->ctypeObj->id;
        }

        $this->odkDb->query("INSERT INTO odk_synced_records (_URI, source_table) VALUES ('" . $this->MainRecordUri . "','" . $this->cronObj->id . "_core')");
        $this->odkDb->execute();
        $this->coreModel->addCronLog($this->ukey, $this->cronObj->id, "data_synced", "record synced",$this->MainRecordUri, $recordId);
        
        if(!empty($ctypeId)) {
            //Since we told node_save() to not add log for the newly inserted/updated record, here we will add it manualy with custom title and justification
            (new CTypeLog($ctypeId))
                ->setContentId($recordId)
                ->setUserId($this->userId)
                ->setTitle($logMessage)
                ->setGroupNam("cron")
                ->save();
        }

        return true;
    }


    //This method mark a form as synced
    public function markAsHasIssue(){

        $this->odkDb->query("INSERT IGNORE INTO odk_records_with_issue (_URI, source_table) VALUES ('" . $this->MainRecordUri . "','" . $this->cronObj->id . "_core')");
        $this->odkDb->execute();

    }




    //This class retrived data for syncing single photo
    public function syncSinglephoto($ctype_id,$field_name,$odk_form_id,$odk_field_name,$mainuri, $returnTypeIsObject = false)
    {
        
        if(!empty($ctype_id) &&
        !empty($field_name)  &&
        !empty($odk_form_id)  &&
        !empty($odk_field_name)  &&
        !empty($mainuri) )
        {
            $Query = "SELECT ";
            $Query .= " " . $odk_form_id . "_" . $odk_field_name . "_blb.value as " . strtoupper($odk_field_name. "_value ");
            $Query .= " ," . $odk_form_id . "_" . $odk_field_name . "_bn.unrooted_file_path as " . strtoupper($odk_field_name . "_file_name ");
            $Query .= " ," . $odk_form_id . "_" . $odk_field_name . "_bn.content_type as " . strtoupper($odk_field_name . "_type ");
            $Query .= " ," . $odk_form_id . "_" . $odk_field_name . "_bn.content_length as " . strtoupper($odk_field_name . "_size ");
            $Query .= " FROM " . $odk_form_id . "_" . $odk_field_name . "_ref ";
            $Query .= " LEFT JOIN " . $odk_form_id . "_" . $odk_field_name . "_blb on " . $odk_form_id . "_" . $odk_field_name . "_blb._URI = " . $odk_form_id . "_" . $odk_field_name . "_ref._SUB_AURI  ";
            $Query .= " LEFT JOIN " . $odk_form_id . "_" . $odk_field_name . "_bn on " . $odk_form_id . "_" . $odk_field_name . "_bn._URI = " . $odk_form_id . "_" . $odk_field_name . "_ref._DOM_AURI  ";
            $Query .= " WHERE " . $odk_form_id . "_" . $odk_field_name . "_bn._PARENT_AURI = '$mainuri' ";

            $this->odkDb->query($Query);
            $results = $this->odkDb->resultSet();
            if(isset($results[0])){
                $res = $results[0];
                
                
                $f = strtoupper($odk_field_name . "_file_name");
                $original_name = $res->$f;
                
                $file_extension = get_ext_from_file_name($original_name);

                $f = strtoupper($odk_field_name . "_size");
                $size = $res->$f;
                $f = strtoupper($odk_field_name . "_type");
                $type = $res->$f;
                $f = strtoupper($odk_field_name . "_value");
                $value = $res->$f;

                $new_file_name = sprintf("%s_%s.%s", 
                    pathinfo($original_name, PATHINFO_FILENAME),
					time(), 
                    $file_extension
                );

                if(!file_exists(UPLOAD_DIR_FULL . "\\$ctype_id")){
                    mkdir(UPLOAD_DIR_FULL . "\\$ctype_id", 0777, true);
                }
                $fileName = UPLOAD_DIR_FULL . '\\' . $ctype_id . '\\' . $new_file_name; // path to png image
                $thum_dest_path = UPLOAD_DIR_FULL . '\\' . $ctype_id . '\\thumbnails\\' . $new_file_name;

                
                $im = imagecreatefromstring($res->$f);
                        
                $resp = imagejpeg($im, $fileName,80);
                imagedestroy($im);
            
                \App\Helpers\UploadHelper::resizeImage($fileName, $fileName);
                $size = filesize($fileName);
            
            
                \App\Helpers\UploadHelper::resizeImage($fileName, $thum_dest_path, true);

                if( $returnTypeIsObject ) {

                    return (object) [
                        "name" => $new_file_name,
                        "original_name" => $original_name,
                        "size" => $size,
                        "extension" => $file_extension,
                        "type" => $type,
                    ];

                } else {

                    $return_value = "";
                    $return_value .= "\"" . $field_name . "_name\":" . (isset($new_file_name) && _strlen($new_file_name) > 0 ? "\"" . $new_file_name . "\"" : "null") . ",";
                    $return_value .= "\"" . $field_name . "_original_name\":" . (isset($original_name) && _strlen($original_name) > 0 ? "\"" . $original_name . "\"" : "null") . ",";
                    $return_value .= "\"" . $field_name . "_size\":" . (isset($size) && _strlen($size) > 0 ? "\"" . $size . "\"" : "null") . ",";
                    $return_value .= "\"" . $field_name . "_extension\":" . (isset($file_extension) && _strlen($file_extension) > 0 ? "\"" . $file_extension . "\"" : "null") . ",";
                    $return_value .= "\"" . $field_name . "_type\":" . (isset($type) && _strlen($type) > 0 ? "\"" . $type . "\"" : "null") . "";
                
                    return $return_value;
                }
            }

            
        }
        
        return null;
    }

}