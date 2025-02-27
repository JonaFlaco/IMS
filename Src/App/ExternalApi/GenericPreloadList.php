<?php 

/*
 * This class returns generic preloadlist when the use is not logged in
 */
namespace App\Externalapi;

use App\Core\Application;
use App\Core\Controller;
use App\Core\BaseExternalApi;
use App\Core\Gctypes\CtypeField;
use App\Exceptions\ForbiddenException;


class GenericPreloadList extends BaseExternalApi {
    
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * index
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * Main function
     */
    public function index($id, $params = []) : void {
        
        $field_id = null;
        if(isset($params['field_id'])){
            $field_id = $params['field_id'];
        } else {
            throw new \App\Exceptions\NotFoundException("field_id not found");
        }

        //check if the field is public or not
        if($this->coreModel->isPublicField($field_id) != true){
            throw new ForbiddenException();
        }

        //get the field obj
        $field = (new CtypeField)->loadById($field_id);
        
        $add_all = false;
        if(isset($params['add_all']) && $params['add_all'] == true){
            $add_all = true;
        }
            
        $data_source_fixed_where_condition = $field->data_source_fixed_where_condition;
        $filter_col = $field->data_source_filter_by_field_name_in_db;
        $ctype = $field->data_source_id;
        $value_col = $field->data_source_value_column;
        $display_col = $field->data_source_display_column;

        $filter_value = [];
        foreach(Application::getInstance()->request->POST() as $key => $value) {
            if(_strpos($key, "filters") !== false && $value != "null") {
                $filter_value[] = $value;
            }
        }
        
        //get the result
        $pl = $this->coreModel->getPreloadList($ctype,$value_col, $display_col, array("filter_by_column_name" => $filter_col, "filter_value" => $filter_value, "fixed_where_condition" => $data_source_fixed_where_condition, "add_all_option" => $add_all, "field_id" => $field_id));


        //return the result
        $result = (object)[
            "status" => "success",
            "result" => $pl
        ];

        return_json($result);

    }
}
