<?php 

namespace App\Middlewares;

use App\Core\Controller;
use App\Exceptions\ForbiddenException;

class Ctypes extends Controller {
    
    public function __construct(){
        parent::__construct();
    }

    public function pl_governorate_field_name() { //pl_{field_name}
        return "m.data_source_id = 'governorates'";
    }

    public function pl_unit_field_name() { //pl_{field_name}
        return "m.data_source_id = 'units'";
    }

    public function pl_form_type_field_name() { //pl_{field_name}
        return "m.data_source_id = 'form_types'";
    }
}
