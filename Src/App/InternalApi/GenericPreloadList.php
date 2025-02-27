<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Core\Gctypes\CtypeField;

class GenericPreloadList extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $field_id = null;
        if(isset($params['field_id'])){
            $field_id = $params['field_id'];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("field_id not found");
        }

        $keyword = null;
        if(isset($params['keyword'])){
            $keyword = $params['keyword'];
        }
        
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
        
        $pl = $this->coreModel->getPreloadList($ctype,$value_col, $display_col, array("filter_by_column_name" => $filter_col, "filter_value" => $filter_value, "fixed_where_condition" => $data_source_fixed_where_condition, "add_all_option" => $add_all, "field_id" => $field_id, "keyword" => $keyword));
        
        if(isset($params['return_object']) && $params['return_object'] == 1) {
            return $pl;
        } else {
            
            $result = (object)[
                "status" => "success",
                "result" => $pl
            ];

            return_json($result);
        }
    }
}
