<?php

/**
 * This class is is used to load data either based on view id or ctype id
 */


namespace App\Core\Gviews;

use App\Core\Application;

Class LoadDataBasedOnView {

    private $id;
    private $params;

    private $coreModel;
    
    public function __construct($id, array $params) {
        $this->id = $id;
        $this->params = $params;

        
        $this->coreModel = Application::getInstance()->coreModel;

        
        //check if $id is not empty
        if(empty($id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Id not provided");
        }
        
    }


    
    /**
     * basedOnView
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function will load data based on view id provided
     */
    public function main(){

        $postData = Application::getInstance()->request->POST();
        $pagination_records_per_page = "";
        if(isset($postData['pagination_records_per_page'])){
            $pagination_records_per_page = $postData['pagination_records_per_page'];
        }

        $page = 1;
        if(isset($this->params) && isset($this->params["page"]) && intval($this->params["page"]) > 0)
            $page = $this->params["page"];
        
        //load the view
        $view_data = $this->coreModel->nodeModel("views")
            ->id($this->id)
            ->loadFirstOrFail();
        
        //generate where clause based on the $postData
        $filter_query = (new \App\Core\Gviews\GenerateFilterCriteria($postData, null, $view_data, false))->main();
        
        //Retrive data from database
        $return_value = \App\Models\Sub\LoadViewData::main($view_data, array("where" => $filter_query, "page" => $page, "rowsPerPage" => $pagination_records_per_page, "truncate_long_text" => true, "format_number" => true, "postData" => $postData));

        //Mark as success
        $return_value['status'] = 'success';
        
        //print the output as json
        echo json_encode($return_value);
        
    }

}