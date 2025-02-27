<?php

/**
 * This class is is used to load data either based on view id or ctype id
 */


namespace App\Core\Gviews;

use App\Core\Application;
use App\Core\Gctypes\Ctype;

Class LoadDataBasedOnCtype {

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
     * main
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function loads data based on ctype_id
     */
    public function main(){

        $page = 1;
        if(isset($this->params) && isset($this->params["page"]))
            $page = $this->params["page"];

        //Get ctype_obj from the id
        $ctype_obj = (new Ctype)->load($this->id);
        
        //get where clause
        $filter_query = (new \App\Core\Gviews\GenerateFilterCriteria(array(), $ctype_obj, null ,false))->main();

        //load data from the database
        $return_value = \App\Models\Sub\LoadViewDataByCtype::main($ctype_obj, $page, $filter_query);

        //Mark it as success
        $return_value['status'] = 'success';

        //print the output as json
        echo json_encode($return_value);



    }

}