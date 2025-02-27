<?php 

namespace Ext\Triggers\EdfEoi;

use App\Core\BaseTrigger;
use App\Exceptions\CriticalException;
use App\Exceptions\IlegalUserActionException;
use App\Helpers\MiscHelper;

class BeforeSave extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($data, $is_update = false){


        //print_r($data); exit;
        $employees_number  = $data->tables[0]->data->employees_number;
        $male_employees_number = $data->tables[0]->data->male_employees_number;
        $female_employees_number = $data->tables[0]->data->female_employees_number;
        $non_binary_employees = $data->tables[0]->data->non_binary_employees;
        $priority_employees_number = $data->tables[0]->data->priority_employees_number;

        if($employees_number != ($male_employees_number + $female_employees_number + $non_binary_employees)) {
            throw new IlegalUserActionException("El número total de empleados no coincide con el número de empleados por género");
        }

        $priority_employees_number = $data->tables[0]->data->priority_employees_number;

        if($priority_employees_number > $employees_number) {
            throw new IlegalUserActionException("El número de empleados con prioridades no puede ser mayor que el número total de empleados");
        }


        $duplication_result = $this->app->coreModel->nodeModel("edf_eoi")
            ->where("m.single_taxpayer_registry_number = :ruc")
            ->orWhere("m.legal_name = :legal_name")
            ->orWhere("m.contact_number = :contact_number")
            ->bindValue("ruc", $data->tables[0]->data->single_taxpayer_registry_number)
            ->bindValue("legal_name", $data->tables[0]->data->legal_name)
            ->bindValue("contact_number", $data->tables[0]->data->contact_number)
            ->load();

        // if($is_update) {
        //     foreach($duplication_result as $itm) {
        //         if($itm->id != $data->tables[0]->data->id)
        //             throw new IlegalUserActionException("Su solicitud está duplicada");
        //     }
        // } else if(!$is_update && !empty($duplication_result))
        //     throw new IlegalUserActionException("Su solicitud está duplicada");

        if(!$is_update)
            $data->tables[0]->data->password = MiscHelper::randomString(10);

    }
}
