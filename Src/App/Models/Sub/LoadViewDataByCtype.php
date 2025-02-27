<?php 
/*
 * This class is responsible of retriving data from database based on ctype_id
 */


namespace App\Models\Sub;

use \App\Core\Application;
use App\Models\CoreModel;

class LoadViewDataByCtype {

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
     * This function will return data from database bas on a given ctype id
     */
    public static function main($ctype_obj, $page, $where) : array {

        $return_value = array();
        
        $ignorePagination = false;
        if(_strtolower($page) == "all"){
            $ignorePagination = true;
        } else {
            $page = intval($page);
        }
        
        self::$coreModel = CoreModel::getInstance();

        $fields = $ctype_obj->getFields();
        $ctype_id = $ctype_obj->id;

        if(!$ignorePagination){
        
            $rowsPerPage = Application::getInstance()->settings->get('views_pagination_records_per_page', 100);
            
            //How many buttons to show in pagination
            $paginationButtonsCount = Application::getInstance()->settings->get('views_pagination_buttons_count', 5);

            //get number of total records (for pagination)
            $qry = "SELECT count(*) as result from $ctype_id ";
            
            self::$coreModel->db->query($qry);

            $res = self::$coreModel->db->resultSingle();
            $noOfRows = $res->result;
    
            $noOfPages = ceil($noOfRows / $rowsPerPage);

            //page number
            $return_value['page'] = $page;

            //How many pages 
            $return_value['numberOfPages'] = $noOfPages;
            
            //How many records per page
            $return_value['rowsPerPage'] = $rowsPerPage;


            //Total records
            $return_value['noOfRows'] = $noOfRows;

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
                    if($j == $page)
                        array_push($paginationButtons, array("title" => self::$coreModel->getKeyword("Page") . " $j " . self::$coreModel->getKeyword("of") . " $noOfPages","pageNo" => $page, "is_current_page" => 1));
                    else 
                        array_push($paginationButtons, array("title" => $j, "pageNo" => $j));
                }
                
                //If number of pages is more than the number of buttons we return the put '...' in the middle
                if(($noOfPages - $page ) > $paginationButtonsCount)
                    array_push($paginationButtons, array("title" => "...", "pageNo" => "", "is_disabled" => 1));


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

            $return_value['paginationButtons'] = $paginationButtons;

            //generate pagination summary example: 101 - 150 of 303
            $footerNoOfRecords = (($rowsPerPage * ($page - 1)) + 1);
            $footerNoOfRecords .= " - " . (($page * $rowsPerPage) > $noOfRows ? $noOfRows : ($page * $rowsPerPage));
            $footerNoOfRecords .= " " . self::$coreModel->getKeyword("of") . " ";
            $footerNoOfRecords .= $noOfRows;
            
            $return_value['footerNoOfRecords'] = $footerNoOfRecords;

        }


        $paginationQry = $ignorePagination ? null : "OFFSET " . $rowsPerPage * ($page - 1) . " ROWS FETCH NEXT $rowsPerPage ROWS ONLY;";

        $fields_qry = "";
        foreach($fields as $field){

            if($field->field_type_id == "component" || $field->field_type_id == "field_collection" || $field->field_type_id == "button" || $field->field_type_id == "note" || ($field->is_multi == true) || $field->field_type_id == "media")
                continue;
    
            if($field->field_type_id == "decimal"){
                $fields_qry .= ",cast(cast(round(isnull($field->name,0),2) as decimal(18,2)) as varchar(250)) AS $field->name" ;
            } else if($field->field_type_id == "date"){
                $fields_qry .= ",format($field->name,'yyyy/MM/dd') as $field->name"; 
            } else {
                $fields_qry .= ",$field->name";
            }
        }

            $qry = "SELECT '' is_selected, ROW_NUMBER() OVER (ORDER BY id ASC) AS row_number, " . $ctype_id . ".id AS " . $ctype_id . "_id_main $fields_qry, $ctype_id.id as id_main FROM $ctype_id $where 
            ORDER BY id
            {$paginationQry}";
        
        self::$coreModel->db->query($qry);
        
        if(isset($id) && _strlen($id) > 0)
            self::$coreModel->db->bind(':id', $id);

        $results = self::$coreModel->db->resultSet();

        
        //put query result
        $return_value['records'] = $results;

        
        //return the array
        return $return_value;

    }

}