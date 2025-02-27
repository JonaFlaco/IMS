<?php 
/*
 * This class is responsible of retriving data from database based on a predefined schema in views
 */


namespace App\Models\Sub;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;
use App\Models\CoreModel;

class LoadViewData {

    private static $coreModel;
    private static $view_data;
    private static $ctype_obj;
    
    /**
     * main
     *
     * @param  object $view_data
     * @param  array $settings
     * @return array
     *
     * This function will return data from database bas on a given view id
     */
    public static function main($view_data, $settings = array()) : array{

        self::$coreModel = CoreModel::getInstance();
        self::$view_data = $view_data;
        //Where condition
        $where = null;
        if(isset($settings['where'])){
            $where = $settings['where'];
        }

        $postData = null;
        if(isset($settings['postData'])){
            $postData = $settings['postData'];
        }
        
        //Return all data or has pagination
        $returnAll = null;
        if(isset($settings['returnAll'])){
            $returnAll = $settings['returnAll'];
        }

        //Load specific page if has pagination
        $page = 1;
        if(isset($settings['page'])){
            $page = intval($settings['page']);
        }
        if($page == 0) {
            $page = 1;
        }

        //how many rows per page to return, if has pagination
        $rowsPerPage = 0;
        if(isset($settings['rowsPerPage'])){
            $rowsPerPage = intval($settings['rowsPerPage']);
        }
        
        //If rowsPerPage is not provided, then load default one
        if($rowsPerPage == 0){
            $rowsPerPage = Application::getInstance()->settings->get('views_pagination_records_per_page', 100);
        }

        self::$ctype_obj = (new Ctype)->load(self::$view_data->ctype_id);
        

        //How many buttons to show in pagination
        $paginationButtonsCount = Application::getInstance()->settings->get('views_pagination_buttons_count', 5);

        //If not return all, then find find total record for pagination
        if($returnAll != true){

            $qry = "SELECT count(*) as result " . self::getJoins();

            self::$coreModel->db->query($qry . $where);
            
            $res = self::$coreModel->db->resultSingle();

            $noOfRows = $res->result;

            $noOfPages = ceil($noOfRows / $rowsPerPage);

            if($noOfPages < $page) {
                $page = 1;
            }
        }

        $settings['page'] = $page;
        $settings['rowsPerpage'] = $rowsPerPage;
        $results = self::getData($settings);

        //create data array, append all data and return it back
        $data = array();

        //put query result
        $data['records'] = $results;

        //if not return all data then returnpagination button and information
        if($returnAll != true){

            //page number
            $data['page'] = $page;

            //How many pages 
            $data['numberOfPages'] = $noOfPages;

            //Total records
            $data['noOfRows'] = $noOfRows;

            //How many records per page
            $data['rowsPerPage'] = $rowsPerPage;


            if($page > $noOfPages)
                $page = $noOfPages;
            if($page <= 1)
                $page = 1;
                
            $paginationButtons = array();

            //If number of pages is more than 1 then return button for each page so user can navigate throw the pages
            if($noOfPages > 1) {

                //If user is not at page 1
                if($page > 1) {

                    //enable 'first','previous' button
                    array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("First"), "pageNo" => 1));
                    array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("Previous"), "pageNo" => intval($page) - 1));
                
                //If user is at page 1
                } else {
                    
                    //disabled 'first','previous' button
                    array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("First"), "pageNo" => 1, "is_disabled" => 1));
                    array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("Previous"), "pageNo" => intval($page) - 1, "is_disabled" => 1));
                }

                //If number of pages is more than the number of buttons we return the put '...' in the middle
                if($page > ($paginationButtonsCount + 1)){
                    array_push($paginationButtons, array("title" => "...", "pageNo" => "", "is_disabled" => 1));
                }

                
                //generate buttons
                $j = 1;
                for($j = 1; $j <= $noOfPages ; $j++){
                    if(abs($j - $page) > $paginationButtonsCount)
                        continue;

                    //if page is the current page
                    if($j == $page){
                        array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("Page") . " $j " . self::$coreModel->getKeyword("of") . " $noOfPages","pageNo" => $page, "is_current_page" => 1));

                    } else  {
                        array_push($paginationButtons, array("title" => $j, "pageNo" => $j));
                    }
                }

                //If number of pages is more than the number of buttons we return the put '...' in the middle
                if(($noOfPages - $page ) > $paginationButtonsCount){
                    array_push($paginationButtons, array("title" => "...", "pageNo" => "", "is_disabled" => 1));
                }


                //If user is not at last page, enable 'next','last'
                if($page < $noOfPages){
                    array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("Next"), "pageNo" => intval($page) + 1, "is_current_page" => 0));
                    array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("Last"), "pageNo" => $noOfPages, "is_current_page" => 0));

                //If use is at last page disable 'next','last'
                } else {
                    array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("Next"), "pageNo" => intval($page) + 1, "is_current_page" => 0, "is_disabled" => 1));
                    array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("Last"), "pageNo" => $noOfPages, "is_current_page" => 0, "is_disabled" => 1));
                }
            }

            $data['paginationButtons'] = $paginationButtons;
            
            //generate pagination summary example: 101 - 150 of 303
            $footerNoOfRecords = (($rowsPerPage * ($page - 1)) + 1);
            $footerNoOfRecords .= " - " . (($page * $rowsPerPage) > $noOfRows ? $noOfRows : ($page * $rowsPerPage));
            $footerNoOfRecords .= " " . self::$coreModel->getKeyword("of") . " ";
            $footerNoOfRecords .= $noOfRows;
            
            $data['footerNoOfRecords'] = $footerNoOfRecords;
            
        }


        //Check if we have filterApi for this
        $className = toPascalCase(self::$view_data->id);
                                    
        $classToRun = sprintf('\App\Middlewares\%s', $className);
        if(!class_exists($classToRun)){
            $classToRun = sprintf('\Ext\Middlewares\%s', $className);
        }
        
        if(class_exists($classToRun)){
            
            $classObj = new $classToRun();
        
            if(method_exists($classObj, "modifyGetDataResult")){
                $data = $classObj->modifyGetDataResult($data, $postData);
            }
        }


        return $data;
        

    }

    


    
    /**
     * getData
     *
     * @param  array $settings
     * @return array
     *
     * This function will retrive data from from database
     */
    private static function getData($settings) : array{

        $where = null;
        if(isset($settings['where'])){
            $where = $settings['where'];
        }
        
        //Return all data or has pagination
        $returnAll = null;
        if(isset($settings['returnAll'])){
            $returnAll = $settings['returnAll'];
        }

        //Load specific page if has pagination
        $page = 1;
        if(isset($settings['page'])){
            $page = intval($settings['page']);
        }
        if($page == 0) {
            $page = 1;
        }
        
        //how many rows per page to return, if has pagination
        $rowsPerPage = 0;
        if(isset($settings['rowsPerPage'])){
            $rowsPerPage = intval($settings['rowsPerPage']);
        }
        
        //If rowsPerPage is not provided, then load default one
        if($rowsPerPage == 0){
            $rowsPerPage = Application::getInstance()->settings->get('views_pagination_records_per_page', 100);
        }

        
        //Truncate long text or not
        $truncate_long_text = null;
        if(isset($settings['truncate_long_text'])){
            $truncate_long_text = $settings['truncate_long_text'];
        }

        //Format numbers or not
        $format_number = null;
        if(isset($settings['format_number'])){
            $format_number = $settings['format_number'];
        }

        //Sepertate Field-Collection values by what seperator
        $fc_value_seperator = "'\n'";
        if(isset($settings['fc_value_seperator'])){
            $fc_value_seperator = $settings['fc_value_seperator'];
        }

        $ctype_id = self::$ctype_obj->id;

        //select_fields is an array which store all columns and generate select statement later
        $select_fields = array();
        $order_by_array = array();

        //is_selected flag for later in view we need it
        $select_fields[] = "null AS is_selected";
        //row_number
        $select_fields[] = "ROW_NUMBER() OVER (ORDER BY {$ctype_id}.id DESC) AS row_number";
        //primary id
        $select_fields[] = $ctype_id . ".id AS {$ctype_id}_id_main";
        
        
        $field_collection_count_array = array();

        //loop throw the view fields one by one and insert it into select_array
        foreach(self::$view_data->fields as $vfield){
  
            //check if we need to limit this field (if string)
            $max_chars = 0;
            if($truncate_long_text == true){
                
                //get default from settings
                $max_chars = Application::getInstance()->settings->get('views_max_chars_to_show');
                //check if the field has its own value use it
                if(intval($vfield->max_chars_to_show) > 0){
                    $max_chars = $vfield->max_chars_to_show;
                }
            }

            //we store full name of fields and use it later for sorting purpose
            $field_full_name = "";

            //declare an empty variable for field which later we assign field object
            $field = null;
            
            //check if we defined Content-Type id for the view field or not
            if(!empty($vfield->ctype_id)){

                if(isset($vfield->field_name)){
                    $field = (new CtypeField)->loadByCtypeIdAndFieldNameOrDefault($vfield->ctype_id, $vfield->field_name);
                    if(isset($field) != true){
                        continue;
                    }
                } else {
                    $field = null;
                }

                //get ctype object for the field
                $ctypeRel = (new Ctype)->load($vfield->ctype_id);

                //prepare the base neame of the field
                $field_base_name = "{$ctypeRel->id}_" . (isset($field) ? $field->name : get_machine_name($vfield->custom_title)) ;

                //ignore below field types
                if(isset($vfield->field_name) && ($field->field_type_id == "field_collection" || $field->field_type_id == "button" || $field->field_type_id == "note"))
                    continue;

                $field_full_name = $ctypeRel->id . "_" . (isset($field) ? $field->name : get_machine_name($vfield->custom_title));


                //If field is from Field-Collection, then do concatename all data and seperate them by a variable then in view or export we will seperate them again
                if(self::$view_data->export_type_id != EXPORT_CSV_ID && self::$ctype_obj->is_field_collection != true && $ctypeRel->is_field_collection){

                    //get how many records this Field-Collection have for earch main row
                    if(in_array($ctypeRel->id, $field_collection_count_array) != true){
                        $select_fields[] = "(select count(*) from dbo.{$ctypeRel->id} sub where sub.parent_id = {$ctypeRel->parent_ctype_id}.id) as {$ctypeRel->id}_count";
                        $field_collection_count_array[] = $ctypeRel->id;
                    }
                    
                    //2. Combobox - Single - Don't show row data
                     //Is Custom field
                     if(isset($vfield->custom_field) && _strlen($vfield->custom_field) > 0){
                        
                        if(isset($field)){
                            $select_fields[] = "STUFF((SELECT $fc_value_seperator + ISNULL(CONVERT(NVARCHAR(MAX)," . _str_replace("[FIELD]", "$ctypeRel->id.$field->name", $vfield->custom_field) . "),'') FROM dbo.{$ctypeRel->id} sub WHERE sub.parent_id = {$ctypeRel->parent_ctype_id}.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'),1," . (_strlen($fc_value_seperator) - 2) . ",'') as $field_base_name";
                        } else {
                            $select_fields[] = "STUFF((SELECT $fc_value_seperator + ISNULL(CONVERT(NVARCHAR(MAX),$vfield->custom_field),'') FROM dbo.{$ctypeRel->id} sub WHERE sub.parent_id = {$ctypeRel->parent_ctype_id}.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'),1," . (_strlen($fc_value_seperator) - 2) . ",'') as {$ctypeRel->id}_" . get_machine_name($vfield->custom_title);
                        }
                    } else if($field->field_type_id == "relation" && $field->is_multi != true && $vfield->show_row_data != true){
                        $select_fields[] = "STUFF((SELECT $fc_value_seperator + ISNULL(CONVERT(NVARCHAR(MAX),d.$field->data_source_display_column),'') FROM dbo.{$ctypeRel->id} sub LEFT JOIN $field->data_source_table_name d on d.id = sub.$vfield->field_name WHERE sub.parent_id = {$ctypeRel->parent_ctype_id}.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'),1," . (_strlen($fc_value_seperator) - 2) . ",'') as $field_base_name";
                    // Else
                    } else {
                        $select_fields[] = "STUFF((SELECT $fc_value_seperator + ISNULL(CONVERT(NVARCHAR(MAX),sub.{$vfield->field_name}),'') FROM dbo.{$ctypeRel->id} sub WHERE sub.parent_id = {$ctypeRel->parent_ctype_id}.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'),1," . (_strlen($fc_value_seperator) - 2) . ",'') as $field_base_name";
                    }

                    //If the field is not from Field-Collection
                } else {
                    
                    //Is Custom field
                    if(!empty($vfield->custom_field)){
                           
                        if(isset($field)){
                            $select_fields[] = _str_replace("[FIELD]", "$ctypeRel->id.$field->name", $vfield->custom_field) . " AS {$field_base_name}";
                        } else {
                            $select_fields[] = $vfield->custom_field . " AS {$ctypeRel->id}_" . get_machine_name($vfield->custom_title);
                        }

                    //5. Attachemnt - Single
                    } else if($field->field_type_id == "media" && $field->is_multi != true){
                        
                        //get attachment name
                        $select_fields[] = "'/filedownload?ctype_id=$field->ctype_id&field_name=$field->name&size=small&file_name=' + {$ctypeRel->id}.{$field->name}_name AS {$field_base_name}_thumb";
                        
                        //get attachment thumbnail
                        $select_fields[] = "'/filedownload?ctype_id=$field->ctype_id&field_name=$field->name&size=orginal&file_name=' + {$ctypeRel->id}.{$field->name}_name AS {$field_base_name}_name";
                        
                        $field_full_name = "{$field_base_name}_name";
                    
                    //2. Combobox - Multi
                    } else if($field->field_type_id == "relation" && $field->is_multi == true){
                        
                        $select_fields[] = "STUFF((SELECT char(10) + s." . $field->data_source_display_column . " FROM {$field_base_name} sf left join $field->data_source_table_name s on s.id = sf.value_id WHERE sf.parent_id = $ctypeRel->id.id FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as $field_base_name";

                    //2. Combobox - Single - Don't show row data
                    } else if($field->field_type_id == "relation" && $field->is_multi != true && $vfield->show_row_data != true){

                    
                        $dsCtype = (new Ctype)->load($field->data_source_id);

                        $prefix = "{$field_base_name}_{$dsCtype->id}";
                        
                        //If it is status_id and add special effects is true, then get required information about status of each record
                        if($field->name == "status_id" && $vfield->add_special_effects){

                            $select_fields[] = "{$ctype_id}_status_id_{$dsCtype->id}.style AS {$ctype_id}_status_id_style";
                        
                        // If the datasource is users and add speical effects is true then load profile picture of the user
                        } else if ($field->data_source_table_name == "users" && $vfield->add_special_effects){

                            $select_fields[]  .= "CASE WHEN $prefix.$field->data_source_display_column IS NULL THEN null WHEN $prefix.profile_picture_name is null then CASE WHEN $prefix.gender_id = 2 then '" . DEFAULT_PROFILE_PICTURE_FEMALE . "' else '" . DEFAULT_PROFILE_PICTURE_MALE . "' end else $prefix.profile_picture_name end AS {$field_base_name}_profile_picture_name";

                        }
                        
                        //load id of the choice
                        $select_fields[] = "$prefix.$field->data_source_value_column AS {$field_base_name}_id";
                        //load display value
                        $select_fields[] = "$prefix.$field->data_source_display_column" ." AS {$field_base_name}";
                
                    //4. Date
                    } else if($field->field_type_id == "date"){
                        
                        //format the value
                        $select_fields[] = "format($ctypeRel->id.$field->name,'yyyy/MM/dd') AS {$field_base_name}";
                    
                    //6. Number
                    } else if($field->field_type_id == "number" && $field->is_system_field != true){

                        //Check if we need to format the value or not
                        if($format_number == true){
                            $select_fields[] = "format($ctypeRel->id.$field->name,'#,0') AS {$field_base_name}";
                        } else {
                            $select_fields[] = "$ctypeRel->id.$field->name AS {$field_base_name}";
                        }

                    //7. Decimal
                    } else if($field->field_type_id == "decimal"){
                        
                        //check no_of_decimal_points
                        $no_of_decimal_points = 2;
                        if(intval($vfield->no_of_decimal_points) > 0){
                            $no_of_decimal_points = $vfield->no_of_decimal_points;
                        }
                    
                        //Check if we need to format the value or not
                        if($format_number == true){
                            $select_fields[] = "cast(format(cast(round(isnull($ctypeRel->id.$field->name,0), $no_of_decimal_points) as decimal(18,$no_of_decimal_points)) ,'#,0." . (str_repeat("0", $no_of_decimal_points)) . "') as varchar(250)) AS {$field_base_name}";
                        } else {
                            $select_fields[] = "cast(cast(round(isnull($ctypeRel->id.$field->name,0), $no_of_decimal_points) as decimal(18,$no_of_decimal_points)) AS varchar(250)) AS {$field_base_name}";
                        }

                    // Else, Text, Combobx - show row data
                    } else {
                     
                        //check if we need to limit number of chars or not
                        if(intval($max_chars) > 0){
                            $select_fields[] = "case when len($ctypeRel->id.$field->name) > $max_chars then left($ctypeRel->id.$field->name,$max_chars) + '...' else $ctypeRel->id.$field->name end AS {$field_base_name}";
                        } else {
                            $select_fields[] = "$ctypeRel->id.$field->name AS {$field_base_name}";
                        }

                    }

                }
                

            //If ctype_id is not specified for the field
            } else {

                //check if it is custom field
                if(isset($vfield->custom_field) && isset($vfield->custom_title)){
                    
                    $select_fields[] = "$vfield->custom_field AS $vfield->custom_title";

                    $field_full_name = $vfield->custom_title;
                }
            }

            //If the field has order then push it to orders array
            if(isset($vfield->order_by_index) && _strlen($vfield->order_by_index) > 0){
                $order_by_array[intval($vfield->order_by_index)] = array("alias" => $field_full_name, "index" => $vfield->order_by_index, "is_desc" => $vfield->order_by_desc);
            }
            
        }

        $joins = self::getJoins();
        
        
        //general select
        $select = "SELECT ";
        $t = 0;
        foreach($select_fields as $item){
            if($t++ > 0){
                $select .= ",";
            }

            $select .= $item;
        }


        //generate order_by clause based on the order_by_array
        $order_by_qry = "";
        foreach($order_by_array as $itm){
            
            if(!empty($order_by_qry)){
                $order_by_qry .= ",";
            }
            $order_by_qry .= $itm['alias']  . ($itm['is_desc'] == true ? " desc" : "") ;
        }

        if(!empty($order_by_qry) != true){
            $order_by_qry = "order by {$ctype_id}_id_main desc";
        } else {
            $order_by_qry = "order by " . $order_by_qry;
        }

        //generate pagination qry
        $pagination_qry = null;
        if($returnAll != true){
            $pagination_qry .= "OFFSET " . ($rowsPerPage * ($page - 1) ) . " ROWS FETCH NEXT $rowsPerPage ROWS ONLY;";
        }

        


        self::$coreModel->db->query("$select $joins $where $order_by_qry $pagination_qry");

        return self::$coreModel->db->resultSet();
    }




    
    /**
     * getJoins
     *
     * @return string
     *
     * This function will generate joins for the queries
     */
    private static function getJoins() : string {

        $joins_array = array();

        $ctype_id = self::$ctype_obj->id;

        //loop throw the relations and generate query joins
        foreach (self::$view_data->relations as $key => $rel) {
            
            $leftCtypeObj = self::$ctype_obj;
            
            //if left ctype is specific then use it other wise we use main ctype
            if(isset($rel->left_ctype_id)){
                $leftCtypeObj = (new Ctype)->load($rel->left_ctype_id);
            }
            
            $rightCtypeObj = (new Ctype)->load($rel->ctype_id);
            
            //if the joined table is Field-Collection then ignore, since we did group_contact for the Field-Collections at the top
            if(self::$view_data->export_type_id != EXPORT_CSV_ID && self::$ctype_obj->is_field_collection != true && $rightCtypeObj->is_field_collection){
                continue;
            }

            // $joins_array[] = " LEFT JOIN $rightCtypeObj->id $rel->name ON $leftCtypeObj->id.$rel->table_1_field_name = $rel->name.$rel->table_2_field_name ";
            
            $joins_array[] = " LEFT JOIN $rightCtypeObj->id ON $leftCtypeObj->id.$rel->table_1_field_name = $rightCtypeObj->id.$rel->table_2_field_name ";
        }
        

        //loop throw the fields and make join for necessory fields
        foreach(self::$view_data->fields as $vfield){

            if(empty($vfield->field_name) || $vfield->show_row_data){
                continue;
            }

            $field = (new CtypeField)->loadByCtypeIdAndFieldNameOrDefault($vfield->ctype_id, $vfield->field_name);
            
            if(empty($field))
                continue;
            
            if($vfield->custom_field == "\${row_number}"){
            
            //2. Combobox - Single
            } else if($field->field_type_id == "relation" && $field->is_multi == false){
                
                $dsCtype = (new Ctype)->load($field->data_source_id);
                
                //If ctype_id is set
                if(!empty($vfield->ctype_id) > 0){

                    $ctypeRel = (new Ctype)->load($vfield->ctype_id);

                    if(self::$view_data->export_type_id != EXPORT_CSV_ID && self::$ctype_obj->is_field_collection != true && $ctypeRel->is_field_collection){
                        continue;
                    }

                    $alias = "{$ctypeRel->id}_{$field->name}_{$dsCtype->id}";
                    $joins_array[] = " LEFT JOIN $dsCtype->id as {$alias} ON $ctypeRel->id.$field->name = {$alias}.$field->data_source_value_column ";

                //If ctype_id is not set
                } else {
                    
                    $alias = "{$ctype_id}_{$field->name}_{$dsCtype->id}";
                    $joins_array[] = " LEFT JOIN $dsCtype->id as {$alias} ON $ctype_id.$field->name = {$alias}.$field->data_source_value_column ";

                }

                
            }

        }


        //Loop throw the filters and make join for necessory filters
        foreach(self::$view_data->filters as $filter){
            $field = (new CtypeField)->loadByCtypeIdAndFieldNameOrDefault($filter->ctype_id, $filter->field_name);
            
            if(empty($field))
                continue;
            //If the field is cbx and is from Field-Collection then don't create relation
            if($field->field_type_id == "relation" && _strlen($field->parent_id) > 0) {
                $ctype = (new Ctype)->load($field->parent_id);
                
                if($ctype->is_field_collection) {
                    continue;
                } else {
                    if($field->field_type_id == "relation" && isset($filter->field_type_id) && $filter->field_type_id == "text" && $field->is_multi != true){
                        $joins_array[] =  "LEFT JOIN $field->data_source_table_name as f_$field->data_source_table_name on f_$field->data_source_table_name.id = $field->ctype_id.$field->name ";
                    }
                }
            }

        }

        //general select
        $joins = " FROM $ctype_id ";
        $t = 0;
        foreach($joins_array as $item){
            
            $joins .= $item;
        }

        return $joins;

    }



    
    
        
    /**
     * getIds
     *
     * @param  object $view_data
     * @param  string $where
     * @return string
     *
     * This is standalone function which will return comma separated ids from database based on the where clause provided
     */
    public static function getIds($view_data, $whereData) : ?string {

        self::$coreModel = new \App\Models\CoreModel();
        self::$view_data = $view_data;
        
        self::$ctype_obj = (new Ctype)->load($view_data->ctype_id);

        $ctype_id = self::$ctype_obj->id;

        // query, comma separated ids
        $qry = " SELECT STRING_AGG(CAST($ctype_id.id as nvarchar(max)),',') as result " . self::getJoins();

        $where = (new \App\Core\Gviews\GenerateFilterCriteria($whereData, null, $view_data,false))->main();

        self::$coreModel->db->query($qry . $where);
        
        $res = self::$coreModel->db->resultSingle();

        //return the result as string
        return $res->result;
    }
}