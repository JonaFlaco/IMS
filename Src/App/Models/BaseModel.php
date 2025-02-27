<?php 

/*
 *  This model handles the base functions which all the models will use them
 */

namespace App\Models;

use App\Core\Application;
use App\Core\Common\Singleton;

class BaseModel extends Singleton {
    
    public $db;

    public function __construct(){
        $this->db = new \App\Core\DAL\MainDatabase;
    }

    



    /**
     * node_load
     *
     * @param  string $ctype_name
     * @param  int $id
     * @param  array $settings
     * @return void
     *
     * Load record
     */
    public function node_load($ctype_id, $id = null, $settings = array()) : array {

        return \App\Models\Sub\SubNodeLoad::main($ctype_id, $id, $settings);

    }

    public function loadFirst($ctype_id, $id = null, $settings = array()) : object {

        $data = \App\Models\Sub\SubNodeLoad::main($ctype_id, $id, $settings);

        if(sizeof($data) == 0)
            throw new \App\Exceptions\NotFoundException("Data not found");
        else
            return $data[0];

    }



    public function loadFirstOrDefault($ctype_id, $id = null, $settings = array()) : ?object {

        $data = \App\Models\Sub\SubNodeLoad::main($ctype_id, $id, $settings);

        if(sizeof($data) == 0)
            return null;
        else
            return $data[0];

    }

    



    /**
     * delete
     *
     * @param  string $ctype_id
     * @param  int $id
     * @param  bool $ignore_if_not_found
     * @return void
     *
     * Delete a record
     */
    public function delete($ctype_id, $id, $ignore_if_not_found = false){

        \App\Models\Sub\SubNodeDelete::main($ctype_id, $id, $ignore_if_not_found);
    }

    

    /**
     * save
     *
     * @param  object $data
     * @param  array $settings
     * @return void
     *
     * Legacy Save record
     */
    public function save($data, $settings = array()){

        return \App\Models\Sub\SubNodeSave::legacy($data, $settings);
    }



    /**
     * node_save
     *
     * @param  object $data
     * @param  array $settings
     * @return void
     *
     * Save record
     */
    public function node_save($data, $settings = array()) {

        return \App\Models\Sub\SubNodeSave::main($data, $settings);
    }


        
    /**
     * getPreloadList
     *
     * @param  string $ctype_name
     * @param  string $data_source_value_column
     * @param  string $data_source_display_column
     * @param  string $settings
     * @return void
     *
     * This functions loads preload list for comobobox example list of gener, list of governorate, list of beneficiaries which include only two column, value and title
     */
    public function getPreloadList($ctype_id, $data_source_value_column, $data_source_display_column, $settings = array()) : array {
            
        return \App\Models\Sub\PreloadList::get($ctype_id, $data_source_value_column, $data_source_display_column, $settings);
    }



    public function getSetting($key, $defaultValue = null){
        
        
        $key = strtolower($key);

        
        $dataFromCache = Application::getInstance()->cache->get("get_setting.$key");
        if(isset($dataFromCache)) {
            return $dataFromCache;
        }
        
        $this->db->query("SELECT lower(id) as id, isnull(value,'') as value from settings t");
    
        foreach($this->db->resultSet() as $item) {
            Application::getInstance()->cache->set("get_setting.$item->id", $item->value, 600);    
        }

        $dataFromCache = Application::getInstance()->cache->get("get_setting.$key");
        if(isset($dataFromCache)) {
            return $dataFromCache;
        }

        return $defaultValue;

    }

    
    public function saveSetting($id, $value){

        $settingObj = $this->nodeModel("settings")
            ->where("m.id = :id")
            ->bindValue(":id", $id)
            ->loadFirst();

        if($settingObj->value_type_id != "boolean" && !$settingObj->allow_empty_value && (!isset($value) || _strlen($value) == 0)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Value is required for setting ($id)");
        }

        if(!empty($id) && $settingObj->value != $value){

            $settingObj->value = $value;
            $settingObj->save(array("justification" => "Updated"));
            
        }

    }
    
    


    public function getKeyword($keyword){

        
        $keyword = _trim($keyword);

        $lang = \App\Core\Application::getInstance()->user->getLangId();
        
        $field_name = "name";
        if(empty($lang) || $lang == "en"){
            return $keyword;
        } else {
            $field_name = "name_" . $lang;
        }

        $dataFromCache = Application::getInstance()->cache->get("get_keyword.$keyword");
        if(isset($dataFromCache)) {
            return $dataFromCache->{$field_name};
        }

        $keywords = $this->nodeModel("keywords")
            ->fields(["name", "name_ar", "name_ku", "name_fr", "name_es"])
            ->load();
        
        foreach($keywords as $item) {
            Application::getInstance()->cache->set("get_keyword.$item->name", $item, 600);
        }

        $dataFromCache = Application::getInstance()->cache->get("get_keyword.$keyword");
        if(isset($dataFromCache)) {
            return $dataFromCache->{$field_name};
        } 

        return $keyword;

    }


    public function nodeModel($ctype_id) {
        return new NodeModel($ctype_id);
    }

    public function newSelect(){
        return Application::getInstance()->queryFactory->newSelect();
    }

    public function newInsert(){
        return Application::getInstance()->queryFactory->newInsert();
    }

    public function newUpdate(){
        return Application::getInstance()->queryFactory->newUpdate();
    }

    public function newDelete(){
        return Application::getInstance()->queryFactory->newDelete();
    }
    
}



