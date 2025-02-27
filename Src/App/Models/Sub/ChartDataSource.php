<?php 

namespace App\Models\Sub;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;

class ChartDataSource {

    private object $chart;
    private $postData;

    private $query;
    private $filterQuery;
    private $app;

    public function __construct($chart, $postData, $useSecondaryQuery = false) {
        $this->chart = $chart;
        $this->postData = $postData;

        $this->query = $useSecondaryQuery ? $chart->secondary_query : $chart->query;

        $this->app = Application::getInstance();
    }
    
    public function main(){

        $this->filterQuery = "";

        $dashboardObj = $this->app->coreModel->nodeModel("dashboards")
            ->id($this->chart->dashboard_id)
            ->loadFirstOrFail();
        
        if(!empty($this->chart->filters)){

            foreach($this->chart->filters as $filter){

                //Find the filter inside dashboard's filters
                $currentFilter = null;
                foreach($dashboardObj->filters as $dashboardFilter){
                    if(_trim(_strtolower($dashboardFilter->name)) == _trim(_strtolower($filter->name))){
                        $currentFilter = $dashboardFilter;
                    }
                }

                
                if(!isset($currentFilter)){
                    
                    $filterCondition = "";

                    $filterValue = isset($this->postData[$filter->name]) ? $this->postData[$filter->name] : null;
                    if(_strlen($filterValue) > 0){
        
                        $filterCondition = "AND $filter->query = " . "'" . $filterValue . "'";
                        $this->filterQuery  .= $filterCondition;   
                        
                    }

                    $this->processFilterCondition($filter->name, $filterCondition);
                    
                } else {

                    $field = (new CtypeField)->loadByCtypeIdAndFieldName($currentFilter->ctype_id, $currentFilter->field_name); 
                    $ctype = (new Ctype)->load($currentFilter->ctype_id);
                    $fieldFullName = $ctype->id . "_" . $field->name;
                
                    foreach($this->postData as $key => $value){

                        
                        if($key == $fieldFullName){
                            
                            $operatorId = isset($this->postData[$fieldFullName . "_operator_id"]) ? $this->postData[$fieldFullName . "_operator_id"] : null;
                            $filterValue = isset($this->postData[$fieldFullName]) ? $this->postData[$fieldFullName] : null;
                            $filterValue2 = isset($this->postData[$fieldFullName . "_2nd_value"]) ? $this->postData[$fieldFullName . "_2nd_value"] : null;

                            switch($field->field_type_id) {
                                case "text": // Text
                                    $this->processText($filter, $operatorId, $filterValue);
                                    break;
                                case "relation": // Combobox
                                    $this->processCombobox($filter, $operatorId, $filterValue, $field, $ctype);
                                    break;
                                case "date": // Date
                                    $this->processDate($filter, $operatorId, $filterValue, $filterValue2);
                                    break;
                                case "number": // Number
                                case "decimal": // Decimal
                                    $this->processNumber($filter, $operatorId, $filterValue, $filterValue2, $field, $ctype);
                                    break;
                                case "boolean": // Boolean
                                    $this->processBoolean($filter, $operatorId, $filterValue, $field, $ctype);
                                    break;
                                default: //Other
                                    $this->processOtherFieldTypes($filter, $operatorId, $filterValue, $filterValue2, $field, $ctype);
                                    break;
                            }
                            
                        }
                    }
                    
                }
                
            }

        }
        
        $this->query = _str_replace("/*{where}*/", $this->filterQuery, $this->query);
        $this->query = _str_replace("/*{where_dyn}*/", _str_replace("'","''",$this->filterQuery), $this->query);
        
        $this->app->coreModel->db->query($this->query);
        
        $result = $this->app->coreModel->db->resultSet();
        
        return $result;
        
    }

    private function processText($filter, $operatorId, $filterValue) {
        
        $filterCondition = "";

        if($operatorId != "text_is_empty" && $operatorId != "text_is_not_empty" && _strlen($filterValue) == 0) {
            $this->processFilterCondition($filter->name, $filterCondition);
            return;
        }

        $base = " AND trim($filter->query) ";
        if($operatorId == "text_equal"){ //Equals
            $filterCondition .=  "$base = N'$filterValue'";
        } else if($operatorId == "text_not_equal"){ //Not Equals
            $filterCondition .=  " $base != N'$filterValue'";
        } else if($operatorId == "text_contain"){ //Contains
            $filterCondition .=  " $base like N'%$filterValue%'";
        } else if($operatorId == "text_not_contain"){ //Not Contains
            $filterCondition .=  " $base not like N'%$filterValue%'";
        } else if($operatorId == "text_start_with"){ //Starts with
            $filterCondition .=  " $base like N'$filterValue%'";
        } else if($operatorId == "text_end_with"){ //Ends with
            $filterCondition .=  " $base like N'%$filterValue'";
        } else if($operatorId == "text_is_empty"){ //Is Null
            $filterCondition .=  " AND trim(isnull($filter->query,'')) = ''";
        } else if ($operatorId == "text_is_not_empty"){ //Is Not Null
            $filterCondition .=  " AND trim(isnull($filter->query,'')) != ''";
        } else if ($operatorId == "text_in"){ //In
            
            $filterCondition .= " AND (";
            $i = 0;
            foreach(_explode("\n",$filterValue) as $value){
                if($i++ > 0){
                    $filterCondition .= " OR ";
                }
                $value = _str_replace("\n","",$value);
                $value = _str_replace("'","''",$value);
                $value = _trim($value);
                $filterCondition .=  "trim($filter->query) = N'$value'";
            }
            $filterCondition .= ") ";
            
        } else if ($operatorId == "text_not_in"){ //Not In
            
            $filterCondition .= " AND (";
            $i = 0;
            foreach(_explode("\n",$filterValue) as $value){
                if($i++ > 0){
                    $filterCondition .= " AND ";
                }
                $value = _str_replace("\n","",$value);
                $value = _str_replace("'","''",$value);
                $value = _trim($value);
                $filterCondition .=  "trim($filter->query) != N'$value'";
            }
            $filterCondition .= ") ";

        }

        $this->filterQuery .= $filterCondition;
        $this->processFilterCondition($filter->name, $filterCondition);
        
    }
    
    private function processCombobox($filter, $operatorId, $filterValue, $field, $ctype) {

        $filterCondition = "";

        if(_strlen($filterValue) == 0) {
            $this->processFilterCondition($filter->name, $filterCondition);
            return;
        }
        
        if (isset($filter->field_type_id) && $filter->field_type_id == "text" && $field->is_multi != true){ // Single Combobox filter by text
            
            if($operatorId != "text_is_empty" && $operatorId != "text_is_not_empty" && _strlen($filterValue) == 0) {
                $this->processFilterCondition($filter->name, $filterCondition);
                return;
            }

            $base = " trim(isnull(f_$field->data_source_table_name.$field->data_source_display_column,'')) ";
            if($operatorId == "text_equal"){ //Equals
                $filterCondition .=  " AND $base = N'$filterValue'";
            } else if($operatorId == "text_not_equal"){ //Not Equals
                $filterCondition .=  " AND $base != N'$filterValue'";
            } else if($operatorId == "text_contain"){ //Contains
                $filterCondition .=  " AND $base like N'%$filterValue%'";
            } else if($operatorId == "text_not_contain"){ //Not Contains
                $filterCondition .=  " AND $base not like N'%$filterValue%'";
            } else if($operatorId == "text_start_with"){ //Starts with
                $filterCondition .=  " AND $base like N'$filterValue%'";
            } else if($operatorId == "text_end_with"){ //Ends with
                $filterCondition .=  " AND $base like N'%$filterValue'";
            } else if($operatorId == "text_is_empty"){ //Is Null
                $filterCondition .=  " AND $base = ''";
            } else if ($operatorId == "text_is_not_empty"){ //Is Not Null
                $filterCondition .=  " AND $base != ''";
            } else if ($operatorId == "text_in"){ //In
                
                $filterCondition .= " AND (";
                $i = 0;
                foreach(_explode("\n",$filterValue) as $value){
                    if($i++ > 0){
                        $filterCondition .= " OR ";
                    }
                    $value = _str_replace("\n","",$value);
                    $value = _str_replace("'","''",$value);
                    $value = _trim($value);
                    $filterCondition .=  " $base = N'$value'";
                }
                $filterCondition .= ") ";
                
            } else if ($operatorId == "text_not_in"){ //Not In
                
                $filterCondition .= " AND (";
                $i = 0;
                foreach(_explode("\n",$filterValue) as $value){
                    if($i++ > 0){
                        $filterCondition .= " AND ";
                    }
                    $value = _str_replace("\n","",$value);
                    $value = _str_replace("'","''",$value);
                    $value = _trim($value);
                    $filterCondition .=  " $base != N'$value'";
                }
                $filterCondition .= ") ";

            }


        
        } else if (isset($filter->field_type_id) && $filter->field_type_id == "text" && $field->is_multi == true){ //Multi Combobox filter by text
            
            
            if($operatorId != "text_is_empty" && $operatorId != "text_is_not_empty" && _strlen($filterValue) == 0) {
                $this->processFilterCondition($filter->name, $filterCondition);
                return;
            }
            
            $base = " (select count(*) from $ctype->id" . "_$field->name left join $field->data_source_table_name on $field->data_source_table_name.id = $ctype->id" . "_$field->name.value_id where $ctype->id" . "_$field->name.parent_id";
            if($operatorId == "text_equal"){ //Equals
                $filterCondition .=  " AND $base = $ctype->id.id AND trim(isnull($field->name.$field->data_source_display_column,'')) = N'$filterValue') > 0";
            } else if($operatorId == "text_not_equal"){ //Not Equals
                $filterCondition .=  " AND $base = $ctype->id.id AND trim(isnull($field->name.$field->data_source_display_column,'')) = N'$filterValue') = 0";
            } else if($operatorId == "text_contain"){ //Contains
                $filterCondition .=  " AND $base = $ctype->id.id AND trim(isnull($field->name.$field->data_source_display_column,'')) like N'%$filterValue%') > 0";
            } else if($operatorId == "text_not_contain"){ //Not Contains
                $filterCondition .=  " AND $base = $ctype->id.id AND trim(isnull($field->name.$field->data_source_display_column,'')) like N'%$filterValue%') = 0";
            } else if($operatorId == "text_start_with"){ //Starts with
                $filterCondition .=  " AND $base = $ctype->id.id AND trim(isnull($field->name.$field->data_source_display_column,'')) like N'$filterValue%') > 0";
            } else if($operatorId == "text_end_with"){ //Ends with
                $filterCondition .=  " AND $base = $ctype->id.id AND trim(isnull($field->name.$field->data_source_display_column,'')) like N'%$filterValue') > 0";
            } else if($operatorId == "text_is_empty"){ //Is Null
                $filterCondition .=  " AND $base = $ctype->id.id) = 0";
            } else if ($operatorId == "text_is_not_empty"){ //Is Not Null
                $filterCondition .=  " AND $base = $ctype->id.id) > 0";
            } else if ($operatorId == "text_in"){ //In
                
                $filterCondition .= " AND (";
                $i = 0;
                foreach(_explode("\n",$filterValue) as $value){
                    if($i++ > 0){
                        $filterCondition .= " OR ";
                    }
                    $value = _str_replace("\n","",$value);
                    $value = _str_replace("'","''",$value);
                    $value = _trim($value);
                    $filterCondition .=  " $base = $ctype->id.id AND trim(isnull($field->name.$field->data_source_display_column,'')) = N'$value') > 0";
                }
                $filterCondition .= ") ";
                
            } else if ($operatorId == "text_not_in"){ //Not In
                
                $filterCondition .= " AND (";
                $i = 0;
                foreach(_explode("\n",$filterValue) as $value){
                    if($i++ > 0){
                        $filterCondition .= " AND ";
                    }
                    $value = _str_replace("\n","",$value);
                    $value = _str_replace("'","''",$value);
                    $value = _trim($value);
                    $filterCondition .=  " $base = $ctype->id.id AND trim(isnull($field->name.$field->data_source_display_column,'')) = N'$value') = 0";
                }
                $filterCondition .= ") ";

            }


        } else if ($field->is_multi == true){ //Multi Combobox
            
            $base = "SELECT count(*) FROM $ctype->id" . "_$field->name sf WHERE sf.parent_id = $ctype->id.id";
            if($operatorId == "relation_equal"){ //Equals
                $filterCondition .= " AND ($base and sf.value_id = '" . $filterValue . "') > 0 ";
            } else if ($operatorId == "relation_not_equal"){ //Not Equals
                $filterCondition .= " AND ($base and sf.value_id = '" . $filterValue . "') = 0 ";
            } else if ($operatorId == "relation_empty"){ // Is Empty
                $filterCondition .= " AND ($base) = 0 ";
            } else if ($operatorId == "relation_not_empty"){ // Is not empty
                $filterCondition .= " AND ($base) > 0 ";
            } else if ($operatorId == "relation_in"){ // In
                
                if (substr($filterValue, 0, 1) == ','){
                    $filterValue = substr($filterValue, 1);
                }

                $filterCondition .= " AND ($base and sf.value_id in (" . $filterValue . ")) > 0 ";
                
            } else if ($operatorId == "relation_not_in"){ //Not in
                
                if (substr($filterValue, 0, 1) == ','){
                    $filterValue = substr($filterValue, 1);
                }

                $filterCondition .= " AND ($base and sf.value_id in (" . $filterValue . ")) = 0 ";
            }


            if (substr($filterValue, 0, 1) == ','){
                $filterValue = substr($filterValue,1);
            }

        } else { //Single combobox

            if($operatorId == "relation_equal"){ //Equals
                $filterCondition .=  " AND $filter->query = '" . $filterValue . "'";
            } else if ($operatorId == "relation_not_equal"){ //Not Equals
                $filterCondition .=  " AND ($filter->query is null or $filter->query != '" . $filterValue . "')";
            } else if ($operatorId == "relation_empty"){ // Is Empty
                $filterCondition .=  " AND $filter->query is null ";
            } else if ($operatorId == "relation_not_empty"){ // Is not empty
                $filterCondition .=  " AND $filter->query is not null ";
            } else if ($operatorId == "relation_in"){ // In
                
                if (substr($filterValue, 0, 1) == ','){
                    $filterValue = substr($filterValue,1);
                }

                $filterCondition .=  " AND $filter->query in (" . $filterValue . ")";
                
            } else if ($operatorId == "relation_not_in"){ //Not in
                
                if (substr($filterValue, 0, 1) == ','){
                    $filterValue = substr($filterValue,1);
                }

                $filterCondition .=  " AND ($filter->query is null or $filter->query not in (" . $filterValue . "))";
                
            }
        }

        $this->filterQuery .= $filterCondition;
        $this->processFilterCondition($filter->name, $filterCondition);

    }

    private function processDate($filter, $operatorId, $filterValue, $filterValue2) {

        $filterCondition = "";

        if($filterValue == "null") {
            $filterValue = null;
        }

        if($filterValue2 == "null") {
            $filterValue2 = null;
        }

        if(($operatorId == "date_equal" || $operatorId == "date_not_equal" || $operatorId == "date_greater_than" || $operatorId == "date_less_than" || $operatorId == "date_between" || $operatorId == "date_not_between") && _strlen($filterValue) == 0) {
            $this->processFilterCondition($filter->name, $filterCondition);
            return;
        }
        
        $base = " AND convert(date,$filter->query,103) ";

        if($operatorId == "date_equal"){ //Equals
            $filterCondition .=  "$base = convert(date,'$filterValue',103) ";
        } else if($operatorId == "date_not_equal"){ //Not Equals
            $filterCondition .=  "$base != convert(date,'$filterValue',103) ";
        } else if($operatorId == "date_greater_than"){ //greater than
            $filterCondition .=  "$base > convert(date,'$filterValue',103) ";
        } else if($operatorId == "date_less_than"){ //less than
            $filterCondition .=  "$base < convert(date,'$filterValue',103) ";
        } else if($operatorId == "date_between"){ //between
            $filterCondition .=  "$base between convert(date,'$filterValue',103) and convert(date,'$filterValue2',103) ";
        } else if($operatorId == "date_not_between"){ //not between
            $filterCondition .=  "$base not between convert(date,'$filterValue',103) and convert(date,'$filterValue2',103) ";
        } else if($operatorId == "date_empty"){ //Is Null
            $filterCondition .=  "$base is null";
        } else if ($operatorId == "date_not_empty"){ //Is Not Null
            $filterCondition .=  "$base is not null";
        } else {

            if($operatorId == "date_tomorrow"){ //Tomorrow
                $filterCondition .=  "$base = convert(date,dateadd(d,1,getdate()),103) ";
            } else if($operatorId == "date_today"){ //Today
                $filterCondition .=  "$base = convert(date,getdate(),103) ";
            } else if($operatorId == "date_yesterday"){ //Yesterday
                $filterCondition .=  "$base = convert(date,dateadd(d,-1,getdate()),103) ";
            } else if($operatorId == "date_next_week"){ //Next Week
                $filterCondition .= "$base >= convert(date,dateadd(day, 8 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                $filterCondition .="$base < convert(date,dateadd(day, 14 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
            } else if($operatorId == "date_this_week"){ //This Week
                $filterCondition .= "$base >= convert(date,dateadd(day, 1 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                $filterCondition .="$base < convert(date,dateadd(day, 7 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
            } else if($operatorId == "date_last_week"){ //Last Week
                $filterCondition .= "$base >= convert(date,dateadd(day, -6 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
                $filterCondition .="$base < convert(date,dateadd(day, 0 - datepart(dw, getdate()), CONVERT(date,getdate())),103) ";
            } else if($operatorId == "date_next_month"){ //Next Month
                $filterCondition .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()) + 1, 0),103) ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()) + 1, 0)),103) ";
            } else if($operatorId == "date_this_month"){ //This Month
                $filterCondition .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()), 0),103) ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()), 0)),103) ";
            } else if($operatorId == "date_last_month"){ //Last Month
                $filterCondition .= "$base >= convert(date,DATEADD(month, DATEDIFF(month, 0, getdate()) - 1, 0),103) ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(month, DATEDIFF(month, -1, getdate()) - 1, 0)),103) ";
            } else if($operatorId == "date_next_quarter"){ //Next Quarter
                $filterCondition .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 1, 0),103) ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 2, 0)),103) ";
            } else if($operatorId == "date_this_quarter"){ //This Quarter
                $filterCondition .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()), 0),103) ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) + 1, 0)),103) ";
            } else if($operatorId == "date_last_quarter"){ //Last Quarter
                $filterCondition .= "$base >= convert(date,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()) - 1, 0),103)  ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(qq, DATEDIFF(qq, 0, GETDATE()), 0)),103) ";
            } else if($operatorId == "date_year_next_year"){ //Next Year
                $filterCondition .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 1, 0),103) ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 2, 0)),103) ";
            } else if($operatorId == "date_year_this_year"){ //This Year
                $filterCondition .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()), 0),103) ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()) + 1, 0)),103) ";
            } else if($operatorId == "date_last_year"){ //Last Year
                $filterCondition .= "$base >= convert(date,DATEADD(year, DATEDIFF(year, 0, GETDATE()) - 1, 0),103)  ";
                $filterCondition .="$base <= convert(date,dateadd(day,-1,DATEADD(year, DATEDIFF(year, 0, GETDATE()), 0)),103) ";
            }


        }

        $this->filterQuery .= $filterCondition;   
        $this->processFilterCondition($filter->name, $filterCondition);

    }

    private function ProcessNumber($filter, $operatorId, $filterValue, $filterValue2, $field, $ctype) {
        
        $filterCondition = "";

        if($operatorId != "text_is_empty" && $operatorId != "text_is_not_empty" && _strlen($filterValue) == 0) {
            $this->processFilterCondition($filter->name, $filterCondition);
            return;
        }
        
        if($operatorId == "number_equal"){ //Equals
            $filterCondition .=  " AND $filter->query = " . floatval($filterValue) . " ";
        } else if($operatorId == "number_not_equal"){ //Not Equals
            $filterCondition .=  " AND $filter->query != " . floatval($filterValue) . " ";
        } else if($operatorId == "number_greater_than_or_equal"){ //greater than
            $filterCondition .=  " AND $filter->query > " . floatval($filterValue) . " ";
        } else if($operatorId == "number_less_than_or_equal"){ //less than 
            $filterCondition .=  " AND $filter->query < " . floatval($filterValue) . " ";
        } else if($operatorId == "number_between"){ //between
            $filterCondition .=  " AND $filter->query between " . floatval($filterValue) . " and " . floatval($filterValue2) . " ";
        } else if($operatorId == "number_not_between"){ //not between
            $filterCondition .=  " AND $filter->query not between " . floatval($filterValue) . " and " . floatval($filterValue2) . " ";
        } else if($operatorId == "number_empty"){ //Is Null
            $filterCondition .=  " AND $filter->query is null";
        } else if ($operatorId == "number_not_empty"){ //Is Not Null
            $filterCondition .=  " AND $filter->query is not null";
        } else if ($operatorId == "number_in"){ //In
            
            $filterCondition .= " AND (";
            $i = 0;
            foreach(_explode("\n",$filterValue) as $value){
                if($i++ > 0){
                    $filterCondition .= " OR ";
                }
                $value = floatval($value);
                $filterCondition .=  $ctype->id . "." . $field->name . " = $value";
            }
            $filterCondition .= ") ";
            
        } else if ($operatorId == "number_not_in"){ //Not In
            
            $filterCondition .= " AND (";
            $i = 0;
            foreach(_explode("\n",$filterValue) as $value){
                if($i++ > 0){
                    $filterCondition .= " AND ";
                }
                $value = floatval($value);
                $filterCondition .=  $ctype->id . "." . $field->name . " != $value";
            }
            $filterCondition .= ") ";

        }
        
        $this->filterQuery .= $filterCondition;
        $this->processFilterCondition($filter->name, $filterCondition);

    }

     private function processBoolean($filter, $operatorId, $filterValue) {

        $filterCondition = "";

        if(_strlen($filterValue) == 0 || $filterValue == "null") {
            $this->processFilterCondition($filter->name, $filterCondition);
            return;
        }

        $filterCondition .=  " AND isnull($filter->query,0) = N'" . $filterValue . "'";

        $this->processFilterCondition($filter->name, $filterCondition);
        $this->filterQuery .=  $filterCondition;

    }

    private function processOtherFieldTypes($filter, $operatorId, $filterValue) {

        $filterCondition = "";

        if(_strlen($filterValue) == 0) {
            $this->processFilterCondition($filter->name, $filterCondition);
            return;
        }

        $filterCondition = " AND $filter->query = '" . $filterValue . "'";

        $this->processFilterCondition($filter->name, $filterCondition);
        $this->filterQuery .=  $filterCondition;

    }

    private function processFilterCondition($filter_name, $filterCondition = "") {
        $this->query = _str_replace("/*{" . $filter_name . "}*/", $filterCondition, $this->query);
    }
    
}