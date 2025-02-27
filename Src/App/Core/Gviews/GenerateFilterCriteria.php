<?php

/**
 * This class is will generate where clause based on a $_POST array received from the users
 */

namespace App\Core\Gviews;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;
use App\Models\CoreModel;

Class GenerateFilterCriteria {

    private $coreModel;
    private $viewData;
    private $ctypeObj;
    private $postData;
    private $useDefaultFiltersOnly;
    private $operatorsNotRequireValue;
    
    public function __construct($postData, $ctypeObj = null, $viewData = null, $useDefaultFiltersOnly = false) {
        $this->viewData = $viewData;
        $this->ctypeObj = $ctypeObj;
        $this->postData = $postData;
        $this->useDefaultFiltersOnly = $useDefaultFiltersOnly;

        $this->operatorsNotRequireValue = Application::getInstance()->globalVar->get("OPERATORS_NOT_REQUIRE_VALUE");
        $this->coreModel = CoreModel::getInstance();
    }



    /**
    * main
    *
    * @param  int $id
    * @param  array $params
    * @return void
    *
    * This is the public function, which receives a cron id and sync it.
    */
    public function main(){

        //If ctype_obj is not set then get it from view_data
        if(!isset($this->ctypeObj)){
            $this->ctypeObj = (new Ctype)->load($this->viewData->ctype_id);
        }

        $filter_query = " WHERE 1 = 1 ";

        $filter_query .= $this->basic();

        $filter_query .= $this->extraConditions();
        
        if(!isset($this->viewData) || !isset($this->postData)){
            return $filter_query;
        }
        
        //loop throw the filters
        foreach($this->viewData->filters as $filter){

            //get the field
            $field = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name);
            
            //if $ctype_id is not set for the filter
            if(!empty($filter->ctype_id) != true){
                $filter->ctype_id = $this->viewData->ctype_id;
            }
            
            //If we want to create where clause based on the default values only
            if($this->useDefaultFiltersOnly || $filter->is_hidden == true){
                
                //list of operators that does not need value
                
                //if the field has defautl value
                if(_strlen($filter->default_value) > 0 || in_array($filter->operator_id, $this->operatorsNotRequireValue)){
                
                    $filter_query .= $this->generateFromDefaultValues($filter, $field);
                }

            //If we want to create where clause based on post data from user
            } else {
                
                $filter_query .= $this->generateFromPostData($filter, $field);

            }
        
            
        }
        
        //return back the generated where cluase
        return $filter_query;
    }

    



    /**
     * generateFromPostData
     *
     * @param  object $filter
     * @param  ojbect $field
     * @return string
     *
     * This function will generate where clause from $_POST array from users
     */
    private function generateFromPostData($filter, $field) : ?string {

        $return_value = "";
    
        //get ctype for the filter
        $ctype_rel = (new Ctype)->load($filter->ctype_id);
        
        //generate some helper variables
        $field_full_name = $ctype_rel->id . "_" . $field->name;
        $field_full_name_sql = $ctype_rel->id . "." . $field->name;
        
        //loop throw the postData that we received
        foreach($this->postData as $key => $value){

            if($key == $field_full_name){
                
                $operator_id = (isset($this->postData[$field_full_name . "_operator_id"]) ? $this->postData[$field_full_name . "_operator_id"] : null);
                $filter_value = _str_replace("'","''", $this->postData[$field_full_name]);
                $filter_value_2nd_value = _str_replace("'","''", isset($this->postData[$field_full_name . "_2nd_value"]) ? $this->postData[$field_full_name . "_2nd_value"] : null);
                
                if(_strtolower($filter_value) == 'null'){
                    $filter_value = null;
                }

                if(_strtolower($filter_value_2nd_value) == 'null'){
                    $filter_value_2nd_value = null;
                }

                if(_strlen($filter_value) == 0 && !in_array($operator_id, $this->operatorsNotRequireValue))
                    return null;

                //1. Text
                if($field->field_type_id == "text"){
                
                    //send the parameters to generateText to generate where clause
                    $return_value .= $this->generateText($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value);
                    
                //9. Boolean
                } else if ($field->field_type_id == "boolean"){
                    
                    //send the parameters to generateBoolean to generate where clause
                    $return_value .= $this->generateBoolean($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value);

                //2. Relation
                } else if ($field->field_type_id == "relation"){

                    //send the parameters to generateRelation to generate where clause
                    $return_value .= $this->generateRelation($filter, $field, $field_full_name, $field_full_name_sql, $ctype_rel, $operator_id, $filter_value);

                //4. Date
                } else if ($field->field_type_id == "date"){
                    
                    //send the parameters to generateDate to generate where clause
                    $return_value .= $this->generateDate($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value, $filter_value_2nd_value);

                //6. Number
                //7. Decimal
                } else if ($field->field_type_id == "number" || $field->field_type_id == "decimal"){
                    
                    //send the parameters to generateNumberDecimal to generate where clause
                    $return_value .= $this->generateNumberDecimal($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value, $filter_value_2nd_value);

                //Else
                } else {

                    //send the parameters to generateOthers to generate where clause
                    $return_value .= $this->generateOthers($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value, $filter_value_2nd_value);

                }
                
            }
        }

        return $return_value;

    }

    



    /**
     * generateFromDefaultValues
     *
     * @param  object $filter
     * @param  ojbect $field
     * @return string
     *
     * This function will generate where clause from filter's default values only
     */
    private function generateFromDefaultValues($filter, $field) : ?string{

        $return_value = "";
        
        //get ctype for the filter
        $ctype_rel = (new Ctype)->load($filter->ctype_id);

        $field_full_name = $ctype_rel->id . "_" . $field->name;
        $field_full_name_sql = $ctype_rel->id . "." . $field->name;

        $operator_id = $filter->operator_id;
        $filter_value = _str_replace("'","''",$filter->default_value);
        
        //1 Text
        if($field->field_type_id == "text"){
            
            //send the parameters to generateText to generate where clause
            $return_value .= $this->generateText($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value);
            
        } else if ($field->field_type_id == "boolean"){
            
            if(!isset($filter_value) || _strlen($filter_value) == 0)
                return null;

            //send the parameters to generateBoolean to generate where clause
            $return_value .= $this->generateBoolean($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value);

        //2. Relation
        } else if ($field->field_type_id == "relation"){

            //send the parameters to generateRelation to generate where clause
            $return_value .= $this->generateRelation($filter, $field, $field_full_name, $field_full_name_sql, $ctype_rel, $operator_id, $filter_value);

        //4. Date
        } else if ($field->field_type_id == "date"){
            
            //send the parameters to generateDate to generate where clause
            $return_value .= $this->generateDate($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value, null);

        //6. Number
        //7. Decimal
        } else if ($field->field_type_id == "number" || $field->field_type_id == "decimal"){
            
            //get 2nd_value
            $filter_value_2nd_value = isset($this->postData[$field_full_name . "_2nd_value"]) ? $this->postData[$field_full_name . "_2nd_value"] : null;

            //send the parameters to generateNumberDecimal to generate where clause
            $return_value .= $this->generateNumberDecimal($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value, $filter_value_2nd_value);

        } else {

            //send the parameters to generateOthers to generate where clause
            $return_value .= $this->generateOthers($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value);

        }

        return $return_value;
    }



    
    /**
     * generateText
     *
     * @param  string $field_full_name
     * @param  string $field_full_name_sql
     * @param  int $operator_id
     * @param  string $filter_value
     * @return string
     *
     * This function will generate where clause for text
     */
    private static function generateText($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value) : ?string {
        

        $return_value = "";
        
        $ctype = (new Ctype)->load($field->parent_id);

        $filter_value = _trim($filter_value);

        //If the filter is inside a Field-Collection
        if($ctype->is_field_collection) {

            if($operator_id == 'text_equal'){ //Equals
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(sub.$field->name) = N'$filter_value') ";
            } else if($operator_id == 'text_not_equal'){ //Not Equals
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(sub.$field->name) != N'$filter_value') ";
            } else if($operator_id == 'text_contain'){ //Contains
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(sub.$field->name) like N'%$filter_value%') ";
            } else if($operator_id == 'text_not_contain'){ //Not Contains
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(sub.$field->name) not like N'%$filter_value%') ";
            } else if($operator_id == 'text_start_with'){ //Starts with
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(sub.$field->name) like N'$filter_value%') ";
            } else if($operator_id == 'text_end_with'){ //Ends with
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(sub.$field->name) like N'%$filter_value') ";
            } else if($operator_id == 'text_is_empty'){ //Is Null
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(isnull(sub.$field->name,'')) = '') ";
            } else if ($operator_id == 'text_is_not_empty'){ //Is Not Null
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(isnull(sub.$field->name,'')) != '') ";
            } else if ($operator_id == 'text_in' || $operator_id == 'text_not_in'){ //In
                
                $temp = "";
                $i = 0;
                foreach(_explode("\n",$filter_value) as $value){
                    if($i++ > 0){
                        $temp .= ",";
                    }

                    $value = _trim($value);
                    $temp .=  "N'$value'";

                }

                $isNotStr = ($operator_id == 'text_not_in' ? "not" : "");
                
                $return_value .=  " AND $isNotStr exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and trim(isnull(sub.$field->name,'')) in ($temp)) ";
            }

        //If the field is not inside a filter collection
        } else {

            
            if($operator_id == 'text_equal'){ //Equals
                $return_value .=  " and trim($field_full_name_sql) = N'$filter_value'";
            } else if($operator_id == 'text_not_equal'){ //Not Equals
                $return_value .=  " and trim($field_full_name_sql) != N'$filter_value'";
            } else if($operator_id == 'text_contain'){ //Contains
                $return_value .=  " and trim($field_full_name_sql) like N'%$filter_value%'";
            } else if($operator_id == 'text_not_contain'){ //Not Contains
                $return_value .=  " and trim($field_full_name_sql) not like N'%$filter_value%'";
            } else if($operator_id == 'text_start_with'){ //Starts with
                $return_value .=  " and trim($field_full_name_sql) like N'$filter_value%'";
            } else if($operator_id == 'text_end_with'){ //Ends with
                $return_value .=  " and trim($field_full_name_sql) like N'%$filter_value'";
            } else if($operator_id == 'text_is_empty'){ //Is Null
                $return_value .=  " and trim(isnull($field_full_name_sql,'')) = ''";
            } else if ($operator_id == 'text_is_not_empty'){ //Is Not Null
                $return_value .=  " and trim(isnull($field_full_name_sql,'')) != ''";
            } else if ($operator_id == 'text_in' || $operator_id == 'text_not_in'){ //In
                
                $temp = "";
                $i = 0;
                foreach(_explode("\n",$filter_value) as $value){
                    if($i++ > 0){
                        $temp .= ",";
                    }

                    $value = _trim($value);
                    $temp .=  "N'$value'";

                }

                $sqlOperator = ($operator_id == 'text_not_in' ? "not in" : "in");
                
                $return_value .=  " and trim(isnull($field_full_name_sql,'')) $sqlOperator ($temp) ";
            
            }

        }

        return $return_value;
    }


    private static function generateBoolean($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value) : ?string {

        $filter_value = intval($filter_value);

        return " AND isnull($field_full_name_sql,0) = $filter_value";

    }

    private static function generateOthers($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value) : ?string {

        return " AND $field_full_name_sql = '$filter_value'";

    }

    
                    
    
    /**
     * generateRelation
     *
     * @param  object $filter
     * @param  object $field
     * @param  int $operator_id
     * @param  string $filter_value
     * @return string
     *
     * This function will generate where clause for relation
     */
    private static function generateRelation($filter, $field, $field_full_name, $field_full_name_sql, $ctype_rel, $operator_id, $filter_value) : ?string {
        
        $return_value = "";
            
        $ctype = (new Ctype)->load($field->parent_id);

        $filter_value = _trim($filter_value);

        //If the filter is inside a Field-Collection
        if($ctype->is_field_collection) {

                
            if (isset($filter->field_type_id) && $filter->field_type_id == "text" && $field->is_multi != true){ // Single Relation filter by text
                
                if($operator_id == 'text_equal'){ //Equals
                    $return_value .=  " AND exists(select * from $ctype->id sub left join $field->data_source_table_name subx on subx.id = sub.$field->name where sub.parent_id = $ctype->parent_ctype_id.id and subx.$field->data_source_display_column = N'$filter_value') ";
                } else if($operator_id == 'text_not_equal'){ //Not Equals
                    $return_value .=  " AND exists(select * from $ctype->id sub left join $field->data_source_table_name subx on subx.id = sub.$field->name where sub.parent_id = $ctype->parent_ctype_id.id and subx.$field->data_source_display_column != N'$filter_value') ";
                } else if($operator_id == 'text_contain'){ //Contains
                    $return_value .=  " AND exists(select * from $ctype->id sub left join $field->data_source_table_name subx on subx.id = sub.$field->name where sub.parent_id = $ctype->parent_ctype_id.id and subx.$field->data_source_display_column like N'%$filter_value%') ";
                } else if($operator_id == 'text_not_contain'){ //Not Contains
                    $return_value .=  " AND exists(select * from $ctype->id sub left join $field->data_source_table_name subx on subx.id = sub.$field->name where sub.parent_id = $ctype->parent_ctype_id.id and subx.$field->data_source_display_column not like N'%$filter_value%') ";
                } else if($operator_id == 'text_start_with'){ //Starts with
                    $return_value .=  " AND exists(select * from $ctype->id sub left join $field->data_source_table_name subx on subx.id = sub.$field->name where sub.parent_id = $ctype->parent_ctype_id.id and subx.$field->data_source_display_column like N'$filter_value%') ";
                } else if($operator_id == 'text_end_with'){ //Ends with
                    $return_value .=  " AND exists(select * from $ctype->id sub left join $field->data_source_table_name subx on subx.id = sub.$field->name where sub.parent_id = $ctype->parent_ctype_id.id and subx.$field->data_source_display_column like '%$filter_value') ";
                } else if($operator_id == 'text_is_empty'){ //Is Null
                    $return_value .=  " AND (select count(*) from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name is null) = 0 ";
                } else if ($operator_id == 'text_is_not_empty'){ //Is Not Null
                    $return_value .=  " AND (select count(*) from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name is null) > 0 ";
                } else if ($operator_id == 'text_in' || $operator_id == 'text_not_in'){ //In
                    
                    $result = "";
                    $i = 0;
                    foreach(_explode("\n",$filter_value) as $value){
                        if($i++ > 0){
                            $result .= ", ";
                        }
                        $value = _trim($value);
                        $result .=  "N'$value'";
                    }

                    $isNotStr = ($operator_id == 'text_not_in' ? "not" : "");

                    $return_value .= " AND exists(select * from $ctype->id sub left join $field->data_source_table_name subx on subx.id = sub.$field->name where sub.parent_id = $ctype->parent_ctype_id.id and subx.$field->data_source_display_column $isNotStr in ($result)) ";
                    
                }


            
            } else if (isset($filter->field_type_id) && $filter->field_type_id == "text" && $field->is_multi == true){ //Multi Relation filter by text
                
                if($operator_id == 'text_equal'){ //Equals
                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id and suby.value_id = N'$filter_value') ";
                } else if($operator_id == 'text_not_equal'){ //Not Equals
                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id and suby.value_id != N'$filter_value') ";
                } else if($operator_id == 'text_contain'){ //Contains
                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id and suby.value_id like N'%$filter_value%') ";
                } else if($operator_id == 'text_not_contain'){ //Not Contains
                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id and suby.value_id not like N'%$filter_value%') ";
                } else if($operator_id == 'text_start_with'){ //Starts with
                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id and suby.value_id like N'$filter_value%') ";
                } else if($operator_id == 'text_end_with'){ //Ends with
                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id and suby.value_id like N'%$filter_value') ";
                } else if($operator_id == 'text_is_empty'){ //Is Null
                    $return_value .=  " AND (select count(*) from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id) = 0 ";
                } else if ($operator_id == 'text_is_not_empty'){ //Is Not Null
                    $return_value .=  " AND (select count(*) from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id) > 0";
                } else if ($operator_id == 'text_in' || $operator_id == 'text_not_in'){ //In - Not In
                    
                    $result = "";
                    $i = 0;
                    foreach(_explode("\n",$filter_value) as $value){
                        if($i++ > 0){
                            $result .= ", ";
                        }

                        $value = _trim($value);
                        $result .=  "N'$value'";
                    }

                    $isNotStr = ($operator_id == 'text_not_in' ? "not" : "");

                    $return_value .= " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " suby on sub.id = suby.parent_id left join $field->data_source_table_name subx on subx.id = suby.value_id where sub.parent_id = $ctype->parent_ctype_id.id and suby.value_id $isNotStr in ($result)) ";
                    
                }


            } else if ($field->is_multi == true){ //Multi Relation
                
                if($operator_id == 'relation_equal'){ //Equals
                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id .  "_" . $field->name . " subx on subx.parent_id = sub.id where sub.parent_id = $ctype->parent_ctype_id.id and subx.value_id = '$filter_value') ";
                } else if ($operator_id == 'relation_not_equal'){ //Not Equals
                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " subx on subx.parent_id = sub.id where sub.parent_id = $ctype->parent_ctype_id.id and subx.value_id != '$filter_value') ";
                } else if ($operator_id == 'relation_empty'){ // Is Empty
                    $return_value .=  " AND (select count(*) from $ctype->id sub left join " . $ctype->id .  "_" . $field->name . " subx on subx.parent_id = sub.id where sub.parent_id = $ctype->parent_ctype_id.id) = 0 ";
                } else if ($operator_id == 'relation_not_empty'){ // Is not empty
                    $return_value .=  " AND (select count(*) from $ctype->id sub left join " . $ctype->id .  "_" . $field->name . " subx on subx.parent_id = sub.id where sub.parent_id = $ctype->parent_ctype_id.id) > 0 ";
                } else if ($operator_id == 'relation_in'){ // In
                    
                    if (substr($filter_value, 0, 1) == ','){
                        $filter_value = substr($filter_value,1);
                    }

                    //filter out bad chars
                    $filter_value = preg_replace("/[^0-9a-zA-Z,_]/", "", $filter_value);
                    
                    $result = "";
                    foreach(_explode(",", $filter_value) as $item) {
                        if(_strlen($result) > 0)
                            $result .= ",";
                        $result .= "'" . $item . "'";
                    }

                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id . "_" . $field->name . " subx on subx.parent_id = sub.id where sub.parent_id = $ctype->parent_ctype_id.id and subx.value_id in ($filter_value)) ";
                    
                } else if ($operator_id == 'relation_not_in'){ //Not in
                    
                    if (substr($filter_value, 0, 1) == ','){
                        $filter_value = substr($filter_value,1);
                    }

                    //filter out bad chars
                    $filter_value = preg_replace("/[^0-9a-zA-Z,_]/", "", $filter_value);
                    
                    $result = "";
                    foreach(_explode(",", $filter_value) as $item) {
                        if(_strlen($result) > 0)
                            $result .= ",";
                        $result .= "'" . $item . "'";
                    }

                    $return_value .=  " AND exists(select * from $ctype->id sub left join " . $ctype->id  . "_" . $field->name . " subx on subx.parent_id = sub.id where sub.parent_id = $ctype->parent_ctype_id.id and subx.value_id not in ($filter_value)) ";
                }


            } else { //Single Relation

                if($operator_id == 'relation_equal'){ //Equals
                    $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name = '$filter_value') ";
                } else if ($operator_id == 'relation_not_equal'){ //Not Equals
                    $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name != '$filter_value') ";
                } else if ($operator_id == 'relation_empty'){ // Is Empty
                    $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name is null) ";
                } else if ($operator_id == 'relation_not_empty'){ // Is not empty
                    $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name is not null) ";
                } else if ($operator_id == 'relation_in'){ // In
                    
                    if (substr($filter_value, 0, 1) == ','){
                        $filter_value = substr($filter_value,1);
                    }

                    //filter out bad chars
                    $filter_value = preg_replace("/[^0-9a-zA-Z,_]/", "", $filter_value);
                    
                    $result = "";
                    foreach(_explode(",", $filter_value) as $item) {
                        if(_strlen($result) > 0)
                            $result .= ",";
                        $result .= "'" . $item . "'";
                    }

                    $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name in (" . $filter_value . ")) ";
                    
                } else if ($operator_id == 'relation_not_in'){ //Not in
                    
                    if (substr($filter_value, 0, 1) == ','){
                        $filter_value = substr($filter_value,1);
                    }

                    //filter out bad chars
                    $filter_value = preg_replace("/[^0-9a-zA-Z,_]/", "", $filter_value);
                    
                    $result = "";
                    foreach(_explode(",", $filter_value) as $item) {
                        if(_strlen($result) > 0)
                            $result .= ",";
                        $result .= "'" . $item . "'";
                    }

                    $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name not in (" . $filter_value . ")) ";
                    
                }
            }

        //If the field is not inside Field-Collection
        } else {
            
            if (isset($filter->field_type_id) && $filter->field_type_id == "text" && $field->is_multi != true){ // Single Relation filter by text
                
                if($operator_id == 'text_equal'){ //Equals
                    $return_value .=  " and trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) = N'$filter_value'";
                } else if($operator_id == 'text_not_equal'){ //Not Equals
                    $return_value .=  " and trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) != N'$filter_value'";
                } else if($operator_id == 'text_contain'){ //Contains
                    $return_value .=  " and trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) like N'%$filter_value%'";
                } else if($operator_id == 'text_not_contain'){ //Not Contains
                    $return_value .=  " and trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) not like N'%$filter_value%'";
                } else if($operator_id == 'text_start_with'){ //Starts with
                    $return_value .=  " and trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) like N'$filter_value%'";
                } else if($operator_id == 'text_end_with'){ //Ends with
                    $return_value .=  " and trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) like N'%$filter_value'";
                } else if($operator_id == 'text_is_empty'){ //Is Null
                    $return_value .=  " and trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) = ''";
                } else if ($operator_id == 'text_is_not_empty'){ //Is Not Null
                    $return_value .=  " and trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) != ''";
                } else if ($operator_id == 'text_in'){ //In
                    
                    $return_value .= " AND (";
                    $i = 0;
                    foreach(_explode("\n",$filter_value) as $value){
                        if($i++ > 0){
                            $return_value .= " OR ";
                        }
                        
                        $value = _trim($value);
                        $return_value .=  " trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) = N'$value'";
                    }
                    $return_value .= ") ";
                    
                } else if ($operator_id == 'text_not_in'){ //Not In
                    
                    $return_value 
                    .= " AND (";
                    $i = 0;
                    foreach(_explode("\n",$filter_value) as $value){
                        if($i++ > 0){
                            $return_value .= " AND ";
                        }
                        
                        $value = _trim($value);
                        $return_value .=  " trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) != N'$value'";
                    }
                    $return_value .= ") ";

                }


            } else if (isset($filter->field_type_id) && $filter->field_type_id == "text" && $field->is_multi == true){ //Multi Relation filter by text
                
                if($operator_id == 'text_equal'){ //Equals
                    $return_value .=  " AND (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id and trim(isnull($field->name.$field->data_source_display_column,'')) = N'$filter_value') > 0";
                } else if($operator_id == 'text_not_equal'){ //Not Equals
                    $return_value .=  " AND (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id and trim(isnull($field->name.$field->data_source_display_column,'')) = N'$filter_value') = 0";
                } else if($operator_id == 'text_contain'){ //Contains
                    $return_value .=  " AND (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id and trim(isnull($field->name.$field->data_source_display_column,'')) like N'%$filter_value%') > 0";
                } else if($operator_id == 'text_not_contain'){ //Not Contains
                    $return_value .=  " AND (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id and trim(isnull($field->name.$field->data_source_display_column,'')) like N'%$filter_value%') = 0";
                } else if($operator_id == 'text_start_with'){ //Starts with
                    $return_value .=  " AND (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id and trim(isnull($field->name.$field->data_source_display_column,'')) like N'$filter_value%') > 0";
                } else if($operator_id == 'text_end_with'){ //Ends with
                    $return_value .=  " AND (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id and trim(isnull($field->name.$field->data_source_display_column,'')) like N'%$filter_value') > 0";
                } else if($operator_id == 'text_is_empty'){ //Is Null
                    $return_value .=  " AND (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id) = 0";
                } else if ($operator_id == 'text_is_not_empty'){ //Is Not Null
                    $return_value .=  " AND (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id) > 0";
                } else if ($operator_id == 'text_in'){ //In
                    
                    $return_value .= " AND (";
                    $i = 0;
                    foreach(_explode("\n",$filter_value) as $value){
                        if($i++ > 0){
                            $return_value .= " OR ";
                        }
                        
                        $value = _trim($value);
                        $return_value .=  " (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id and trim(isnull($field->name.$field->data_source_display_column,'')) = N'$value') > 0";
                    }
                    $return_value .= ") ";
                    
                } else if ($operator_id == 'text_not_in'){ //Not In
                    
                    $return_value .= " AND (";
                    $i = 0;
                    foreach(_explode("\n",$filter_value) as $value){
                        if($i++ > 0){
                            $return_value .= " AND ";
                        }
                        
                        $value = _trim($value);
                        $return_value .=  " (select count(*) from $ctype_rel->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype_rel->id" . "_$field->name.value_id where $ctype_rel->id" . "_$field->name.parent_id = $ctype_rel->id.id and trim(isnull($field->name.$field->data_source_display_column,'')) = N'$value') = 0";
                    }
                    $return_value .= ") ";

                }


            } else if ($field->is_multi == true){ //Multi Relation
                
                if($operator_id == 'relation_equal'){ //Equals
                    $return_value .= " AND (SELECT count(*) FROM $ctype_rel->id" . "_$field->name sf WHERE sf.parent_id = $ctype_rel->id.id and sf.value_id = '$filter_value') > 0 ";
                } else if ($operator_id == 'relation_not_equal'){ //Not Equals
                    $return_value .= " AND (SELECT count(*) FROM $ctype_rel->id" . "_$field->name sf WHERE sf.parent_id = $ctype_rel->id.id and sf.value_id = '$filter_value') = 0 ";
                } else if ($operator_id == 'relation_empty'){ // Is Empty
                    $return_value .= " AND (SELECT count(*) FROM $ctype_rel->id" . "_$field->name sf WHERE sf.parent_id = $ctype_rel->id.id) = 0 ";
                } else if ($operator_id == 'relation_not_empty'){ // Is not empty
                    $return_value .= " AND (SELECT count(*) FROM $ctype_rel->id" . "_$field->name sf WHERE sf.parent_id = $ctype_rel->id.id) > 0 ";
                } else if ($operator_id == 'relation_in'){ // In
                    
                    if (substr($filter_value, 0, 1) == ','){
                        $filter_value = substr($filter_value,1);
                    }

                    //filter out bad chars
                    $filter_value = preg_replace("/[^0-9a-zA-Z,_]/", "", $filter_value);
                    
                    $result = "";
                    foreach(_explode(",", $filter_value) as $item) {
                        if(_strlen($result) > 0)
                            $result .= ",";
                        $result .= "'" . $item . "'";
                    }
                    
                    $return_value .= " AND (SELECT count(*) FROM $ctype_rel->id" . "_$field->name sf WHERE sf.parent_id = $ctype_rel->id.id and sf.value_id in ($result)) > 0 ";
                    
                } else if ($operator_id == 'relation_not_in'){ //Not in
                    
                    if (substr($filter_value, 0, 1) == ','){
                        $filter_value = substr($filter_value,1);
                    }

                    //filter out bad chars
                    $filter_value = preg_replace("/[^0-9a-zA-Z,_]/", "", $filter_value);
                    
                    $result = "";
                    foreach(_explode(",", $filter_value) as $item) {
                        if(_strlen($result) > 0)
                            $result .= ",";
                        $result .= "'" . $item . "'";
                    }

                    $return_value .= " AND (SELECT count(*) FROM $ctype_rel->id" . "_$field->name sf WHERE sf.parent_id = $ctype_rel->id.id and sf.value_id in ($filter_value)) = 0 ";
                }


                if (substr($filter_value, 0, 1) == ','){
                    $filter_value = substr($filter_value,1);
                }

            } else { //Single Relation

                if($operator_id == 'relation_equal'){ //Equals
                    $return_value .=  " AND $field_full_name_sql = '" . $filter_value . "'";
                } else if ($operator_id == 'relation_not_equal'){ //Not Equals
                    $return_value .=  " AND ($field_full_name_sql is null or $field_full_name_sql != '" . $filter_value . "')";
                } else if ($operator_id == 'relation_empty'){ // Is Empty
                    $return_value .=  " AND $field_full_name_sql is null ";
                } else if ($operator_id == 'relation_not_empty'){ // Is not empty
                    $return_value .=  " AND $field_full_name_sql is not null ";
                } else if ($operator_id == 'relation_in'){ // In
                    
                    if (substr($filter_value, 0, 1) == ','){
                        $filter_value = substr($filter_value,1);
                    }

                    $return_value .=  " AND $field_full_name_sql in (" . $filter_value . ")";
                    
                } else if ($operator_id == 'relation_not_in'){ //Not in
                    
                    if (substr($filter_value, 0, 1) == ','){
                        $filter_value = substr($filter_value,1);
                    }

                    $return_value .=  " AND ($field_full_name_sql is null or $field_full_name_sql not in (" . $filter_value . "))";
                    
                }
            }

        }

        return $return_value;

    }

    



    /**
     * generateDate
     *
     * @param  string $field_full_name
     * @param  string $field_full_name_sql
     * @param  int $operator_id
     * @param  string $filter_value
     * @param  string $filter_value_2nd_value
     * @return string
     *
     * This function will generate where clause for date
     */
    private static function generateDate($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value, $filter_value_2nd_value = null) : ?string {

        $return_value = "";
        if($filter_value == "null"){
            $filter_value = null;
        }

        $ctype = (new Ctype)->load($field->parent_id);

        $filter_value = _trim($filter_value);

        //If the filter is inside a Field-Collection
        if($ctype->is_field_collection) {

            $base = " select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and convert(date,sub.$field->name,103) ";

            $result = "";
            if($operator_id == 'date_equal'){ //Equals
                $result .= "$base = convert(date,'$filter_value',103) ";
            } else if($operator_id == 'date_not_equal'){ //Not Equals
                $result .= " $base != convert(date,'$filter_value',103) ";
            } else if($operator_id == 'date_greater_than'){ //greater than
                $result .=  " $base > convert(date,'$filter_value',103) ";
            } else if($operator_id == 'date_less_than'){ //less than
                $result .=  " $base < convert(date,'$filter_value',103) ";
            } else if($operator_id == 'date_between'){ //between
                $result .=  " $base between convert(date,'$filter_value',103) and convert(date,'$filter_value_2nd_value',103) ";
            } else if($operator_id == 'date_not_between'){ //not between
                $result .=  " $base not between convert(date,'$filter_value',103) and convert(date,'$filter_value_2nd_value',103) ";
            } else if($operator_id == 'date_empty'){ //Is Null
                $result .=  " $base is null";
            } else if ($operator_id == 'date_not_empty'){ //Is Not Null
                $result .=  " $base is not null";
            } else {

                if($operator_id == 'date_tomorrow'){ //Tomorrow
                    $result .=  "$base = convert(date,dateadd(d,1,getdate()),103) ";
                } else if($operator_id == 'date_today'){ //Today
                    $result .=  "$base = convert(date,getdate(),103) ";
                } else if($operator_id == 'date_yesterday'){ //Yesterday
                    $result .=  "$base = convert(date,dateadd(d,-1,getdate()),103) )";
                } else if($operator_id == 'date_next_week'){ //Next Week
                    $result .= "$base >= convert(date,dateadd(day, 8 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                    $result .="AND convert(date,sub.$field->name,103) < convert(date,dateadd(day, 14 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                } else if($operator_id == 'date_this_week'){ //This Week
                    $result .= "$base >= convert(date,dateadd(day, 1 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                    $result .="AND convert(date,sub.$field->name,103) < convert(date,dateadd(day, 7 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                } else if($operator_id == 'date_last_week'){ //Last Week
                    $result .= "$base >= convert(date,dateadd(day, -6 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                    $result .="AND convert(date,sub.$field->name,103) < convert(date,dateadd(day, 0 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                } else if($operator_id == 'date_next_month'){ //Next Month
                    $result .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()) + 1, 0),103) ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()) + 1, 0)),103) ";
                } else if($operator_id == 'date_this_month'){ //This Month
                    $result .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()), 0),103) ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()), 0)),103) ";
                } else if($operator_id == 'date_last_month'){ //Last Month
                    $result .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()) - 1, 0),103) ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()) - 1, 0)),103) ";
                } else if($operator_id == 'date_next_quarter'){ //Next Quarter
                    $result .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 1, 0),103) ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 2, 0)),103) ";
                } else if($operator_id == 'date_this_quarter'){ //This Quarter
                    $result .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()), 0),103) ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 1, 0)),103) ";
                } else if($operator_id == 'date_last_quarter'){ //Last Quarter
                    $result .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) - 1, 0),103)  ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()), 0)),103) ";
                } else if($operator_id == 'date_year_next_year'){ //Next Year
                    $result .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 1, 0),103) ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 2, 0)),103) ";
                } else if($operator_id == 'date_year_this_year'){ //This Year
                    $result .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()), 0),103) ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 1, 0)),103) ";
                } else if($operator_id == 'date_last_year'){ //Last Year
                    $result .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()) - 1, 0),103)  ";
                    $result .="AND convert(date,sub.$field->name,103) <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()), 0)),103) ";
                }


                $return_value = "AND exists( $result )";
            }

            //If the field is not inside Field-Collection
        } else {

            $base = " AND convert(date,$field_full_name_sql,103) ";

            if($operator_id == 'date_equal'){ //Equals
                $return_value .=  "$base = convert(date,'$filter_value',103) ";
            } else if($operator_id == 'date_not_equal'){ //Not Equals
                $return_value .=  "$base != convert(date,'$filter_value',103) ";
            } else if($operator_id == 'date_greater_than'){ //greater than
                $return_value .=  "$base > convert(date,'$filter_value',103) ";
            } else if($operator_id == 'date_less_than'){ //less than
                $return_value .=  "$base < convert(date,'$filter_value',103) ";
            } else if($operator_id == 'date_between'){ //between
                $return_value .=  "$base between convert(date,'$filter_value',103) and convert(date,'$filter_value_2nd_value',103) ";
            } else if($operator_id == 'date_not_between'){ //not between
                $return_value .=  "$base not between convert(date,'$filter_value',103) and convert(date,'$filter_value_2nd_value',103) ";
            } else if($operator_id == 'date_empty'){ //Is Null
                $return_value .=  "$base is null";
            } else if ($operator_id == 'date_not_empty'){ //Is Not Null
                $return_value .=  "$base is not null";
            } else {

                if($operator_id == 'date_tomorrow'){ //Tomorrow
                    $return_value .=  "$base = convert(date,dateadd(d,1,getdate()),103) ";
                } else if($operator_id == 'date_today'){ //Today
                    $return_value .=  "$base = convert(date,getdate(),103) ";
                } else if($operator_id == 'date_yesterday'){ //Yesterday
                    $return_value .=  "$base = convert(date,dateadd(d,-1,getdate()),103) ";
                } else if($operator_id == 'date_next_week'){ //Next Week
                    $return_value .= "$base >= convert(date,dateadd(day, 8 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                    $return_value .="$base < convert(date,dateadd(day, 14 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                } else if($operator_id == 'date_this_week'){ //This Week
                    $return_value .= "$base >= convert(date,dateadd(day, 1 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                    $return_value .="$base < convert(date,dateadd(day, 7 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                } else if($operator_id == 'date_last_week'){ //Last Week
                    $return_value .= "$base >= convert(date,dateadd(day, -6 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                    $return_value .="$base < convert(date,dateadd(day, 0 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                } else if($operator_id == 'date_next_month'){ //Next Month
                    $return_value .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()) + 1, 0),103) ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()) + 1, 0)),103) ";
                } else if($operator_id == 'date_this_month'){ //This Month
                    $return_value .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()), 0),103) ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()), 0)),103) ";
                } else if($operator_id == 'date_last_month'){ //Last Month
                    $return_value .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()) - 1, 0),103) ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()) - 1, 0)),103) ";
                } else if($operator_id == 'date_next_quarter'){ //Next Quarter
                    $return_value .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 1, 0),103) ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 2, 0)),103) ";
                } else if($operator_id == 'date_this_quarter'){ //This Quarter
                    $return_value .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()), 0),103) ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 1, 0)),103) ";
                } else if($operator_id == 'date_last_quarter'){ //Last Quarter
                    $return_value .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) - 1, 0),103)  ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()), 0)),103) ";
                } else if($operator_id == 'date_year_next_year'){ //Next Year
                    $return_value .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 1, 0),103) ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 2, 0)),103) ";
                } else if($operator_id == 'date_year_this_year'){ //This Year
                    $return_value .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()), 0),103) ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 1, 0)),103) ";
                } else if($operator_id == 'date_last_year'){ //Last Year
                    $return_value .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()) - 1, 0),103)  ";
                    $return_value .="$base <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()), 0)),103) ";
                }


            }
            
        }

        return $return_value;
    }
    
     
    



    /**
     * generateNumberDecimal
     *
     * @param  string $field_full_name
     * @param  string $field_full_name_sql
     * @param  int $operator_id
     * @param  string $filter_value
     * @param  string $filter_value_2nd_value
     * @return string
     */
    private static function generateNumberDecimal($filter, $field, $field_full_name, $field_full_name_sql, $operator_id, $filter_value, $filter_value_2nd_value = null) : ?string {

        $return_value = "";
        
        $ctype = (new Ctype)->load($field->parent_id);

        $filter_value = floatval($filter_value);
        $filter_value_2nd_value = floatval($filter_value_2nd_value);

        //If the field is inside Field-Collection
        if($ctype->is_field_collection) {

            if($operator_id == 'number_equal'){ //Equals
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name = $filter_value) ";
            } else if($operator_id == 'number_not_equal'){ //Not Equals
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name != $filter_value) ";
            } else if($operator_id == 'number_greater_than_or_equal'){ //greater than
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name > $filter_value) ";
            } else if($operator_id == 'number_less_than_or_equal'){ //less than 
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name < $filter_value) ";
            } else if($operator_id == 'number_between'){ //between
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name between $filter_value and $filter_value_2nd_value) ";
            } else if($operator_id == 'number_not_between'){ //not between
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name not between $filter_value and $filter_value_2nd_value) ";
            } else if($operator_id == 'number_empty'){ //Is Null
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name is null) ";
            } else if ($operator_id == 'number_not_empty'){ //Is Not Null
                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name is not null) ";
            } else if ($operator_id == 'number_in' || $operator_id == 'number_not_in'){ //In
                
                $result = "";
                $i = 0;
                foreach(_explode("\n",$filter_value) as $value){
                    if($i++ > 0){
                        $result .= ", ";
                    }
                    $value = floatval($value);
                    $result .=   $value;
                }

                $isNotStr = ($operator_id == 'number_not_in' ? "not" : "");

                $return_value .=  " AND exists(select * from $ctype->id sub where sub.parent_id = $ctype->parent_ctype_id.id and sub.$field->name $isNotStr in ($result)) ";
                
            }


        //If the field is not inside Field-Collection
        } else {

            if($operator_id == 'number_equal'){ //Equals
                $return_value .=  " AND $field_full_name_sql = " . floatval($filter_value) . " ";
            } else if($operator_id == 'number_not_equal'){ //Not Equals
                $return_value .=  " AND $field_full_name_sql != " . floatval($filter_value) . " ";
            } else if($operator_id == 'number_greater_than_or_equal'){ //greater than
                $return_value .=  " AND $field_full_name_sql > " . floatval($filter_value) . " ";
            } else if($operator_id == 'number_less_than_or_equal'){ //less than 
                $return_value .=  " AND $field_full_name_sql < " . floatval($filter_value) . " ";
            } else if($operator_id == 'number_between'){ //between
                $return_value .=  " AND $field_full_name_sql between " . floatval($filter_value) . " and " . floatval($filter_value_2nd_value) . " ";
            } else if($operator_id == 'number_not_between'){ //not between
                $return_value .=  " AND $field_full_name_sql not between " . floatval($filter_value) . " and " . floatval($filter_value_2nd_value) . " ";
            } else if($operator_id == 'number_empty'){ //Is Null
                $return_value .=  " AND $field_full_name_sql is null";
            } else if ($operator_id == 'number_not_empty'){ //Is Not Null
                $return_value .=  " AND $field_full_name_sql is not null";
            } else if ($operator_id == 'number_in' || $operator_id == 'number_not_in'){ //In
                
                $result = "";
                $i = 0;
                foreach(_explode("\n",$filter_value) as $value){
                    if($i++ > 0){
                        $result .= ", ";
                    }
                    $value = floatval($value);
                    $result .=   $value;
                }

                $isNotStr = ($operator_id == 'number_not_in' ? "not" : "");

                $return_value .= " AND $field_full_name $isNotStr in ($result) ";
                
            }

        }
        

        return $return_value;
    }

    



    /**
     * basic
     *
     * @param  object $this->ctypeObj
     * @param  object $this->postData
     * @return string
     *
     * This function will create where clause for basic filters which does not come from user side, example make sure this user will get result only from his governorate, unit, etc...
     */
    private function basic() : ?string {

        
        $filter_query = "";
        
        
        if(isset($this->postData['selected_ids']) && _strlen($this->postData['selected_ids']) > 0){
            
            //filter out bad chars
            $selectedIds = preg_replace("/[^0-9a-zA-Z,_]/", "", $this->postData['selected_ids']);
            
            $selectedIdsResult = "";
            foreach(_explode(",", $selectedIds) as $item) {
                if(_strlen($selectedIdsResult) > 0)
                    $selectedIdsResult .= ",";
                $selectedIdsResult .= "'" . $item . "'";
            }
            
            $filter_query .= " AND " . $this->ctypeObj->id . ".id in (" . $selectedIdsResult . ") ";
            
        }
        
        if(Application::getInstance()->user->isAuthenticated() != true || Application::getInstance()->request->isCli()){
            return $filter_query;
        }

        
        if(($this->ctypeObj->id == "ctypes" || $this->ctypeObj->id == "views" || $this->ctypeObj->id == "users" || $this->ctypeObj->id == "roles") && Application::getInstance()->user->isSuperAdmin() !== true){
            $filter_query .= sprintf(' AND isnull(%s.is_system_object,0) = 0 ', $this->ctypeObj->id);
        }
        
        if(isset($this->ctypeObj->governorate_field_name) && _strlen($this->ctypeObj->governorate_field_name) > 0 && \App\Core\Application::getInstance()->user->isAdmin() != true){
            $govs = implode(",", Application::getInstance()->user->getUserGovernorates($this->ctypeObj->id, "allow_read"));
            if(!isset($govs) || _strlen($govs) == 0){
                $govs = "NULL";
            }
            $filter_query .= sprintf(' AND %s.%s in (%s) ', $this->ctypeObj->id, $this->ctypeObj->governorate_field_name, $govs);
            
        }

        if(isset($this->ctypeObj->unit_field_name) && _strlen($this->ctypeObj->unit_field_name) > 0){
            
            $units = implode(",", Application::getInstance()->user->getUserUnits($this->ctypeObj->id, "allow_read"));
            if(!isset($units) || _strlen($units) == 0){
                $units = "NULL";
            }
            
            $filter_query .= sprintf(' AND %s.%s in (%s) ', $this->ctypeObj->id, $this->ctypeObj->unit_field_name, $units);
            
        }

        

        if(isset($this->ctypeObj->form_type_field_name) && _strlen($this->ctypeObj->form_type_field_name) > 0 && \App\Core\Application::getInstance()->user->isAdmin() != true){
            $form_types = implode(",", Application::getInstance()->user->getUserFormTypes($this->ctypeObj->id, "allow_read"));
            
            if(!isset($form_types) || _strlen($form_types) == 0){
                $form_types = "NULL";
            }

            $filter_query .= sprintf(' AND %s.%s in (%s) ', $this->ctypeObj->id, $this->ctypeObj->form_type_field_name, $form_types);
            
        }

        
        $permission_obj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);
        if($permission_obj->allow_read != true && $permission_obj->allow_read_only_your_own_records){
            $filter_query .= sprintf(' AND %s.created_user_id = %s', $this->ctypeObj->id, \App\Core\Application::getInstance()->user->getId());
        }
        
        return $filter_query;
    }


    private function extraConditions(): ?string {

        if(!isset($this->viewData)){
            return null;
        }

        //Check if we have filterApi for this
        $className = toPascalCase($this->viewData->id);
                                    
        $classToRun = sprintf('\App\Middlewares\%s', $className);
        if(!class_exists($classToRun)){
            $classToRun = sprintf('\Ext\Middlewares\%s', $className);
        }
        
        if(class_exists($classToRun)){
            
            $classObj = new $classToRun();
        
            if(method_exists($classObj, "addExtraConditionToGetData")){
                $value = $classObj->addExtraConditionToGetData();

                if(_strlen($value) > 0) {
                    $value = " AND " . $value;
                }

                return $value;
            }
        }
        

        return null;

    }
}