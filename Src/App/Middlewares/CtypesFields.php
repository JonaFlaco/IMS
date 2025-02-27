<?php 

namespace App\Middlewares;

use App\Core\Controller;
use App\Exceptions\CriticalException;
use App\Exceptions\ForbiddenException;

class CtypesFields extends Controller {
    
    public function __construct(){
        parent::__construct();
    }

    public function pl_data_source_id($filterValue) { //pl_{field_name}
        
        if(empty($filterValue))
            return;

        $value = $filterValue[0];

        if($value == "field_collection")
            return "isnull(m.is_field_collection,0) = 1 and (select count(*) from ctypes_fields f where f.data_source_id = m.id) = 0";
        elseif($value == "relation")
            return "isnull(m.is_field_collection,0) = 0";
    }

}
