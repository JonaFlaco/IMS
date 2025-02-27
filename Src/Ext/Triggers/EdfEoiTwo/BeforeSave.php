<?php 

namespace Ext\Triggers\EdfEoiTwo;

use App\Core\BaseTrigger;
use App\Exceptions\CriticalException;
use App\Exceptions\IlegalUserActionException;
use App\Helpers\MiscHelper;

class BeforeSave extends BaseTrigger {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($data, $is_update = false){


        $data->tables[0]->data->oim_contribution= $data->tables[0]->data->total_oim_contribution;
        $data->tables[0]->data->business_contribution = $data->tables[0]->data->owner_contribution;
        $employees_number  = $data->tables[0]->data->employees_number;
        $male_employees_number = $data->tables[0]->data->male_employees;
        $female_employees_number = $data->tables[0]->data->female_employees;
        $non_binary_employees = $data->tables[0]->data->non_binary_employees;
        $priority_employees_number = $data->tables[0]->data->migrant_employees + $data->tables[0]->data->refugee_employees + $data->tables[0]->data->returned_employees + $data->tables[0]->data->disability_employees + $data->tables[0]->data->lgbt_employees;

        if(!$employees_number || $employees_number == 0) {
            throw new IlegalUserActionException("El total de empleados no puede ser 0");
        }
        if($data->tables[0]->data->oim_contribution  > 20000) {
            throw new IlegalUserActionException("La contribución maxima que aprobaria la OIM es de 20.000$ Dólares Americanos");
        }

        if($employees_number != ($male_employees_number + $female_employees_number + $non_binary_employees)) {
            throw new IlegalUserActionException("El número total de empleados no coincide con el número de empleados por género");
        }

        if($priority_employees_number > $employees_number) {
            throw new IlegalUserActionException("El número de empleados con prioridades es $priority_employees_number y no puede ser mayor que el número total de empleados $employees_number");
        }
        if($data->tables[0]->data->boss_female_no  > $data->tables[0]->data->female_employees ) {
            throw new IlegalUserActionException("El número de empleadas involucradas en las tomas de decisiones/posiciones gerenciales no puede ser mayor al numero de empleadas mujeres");
        }


        $duplication_result = $this->app->coreModel->nodeModel("edf_eoi_two")
            ->where("m.ruc = :ruc")
            ->orWhere("m.legal_name = :legal_name")
            ->orWhere("m.phone = :contact_number")
            ->bindValue("ruc", $data->tables[0]->data->ruc)
            ->bindValue("legal_name", $data->tables[0]->data->legal_name)
            ->bindValue("contact_number", $data->tables[0]->data->phone)
            ->load();

        if($is_update) {
            foreach($duplication_result as $itm) {
                if($itm->id != $data->tables[0]->data->id)
                    throw new IlegalUserActionException("Su solicitud está duplicada");
            }
        } else if(!$is_update && !empty($duplication_result))
            throw new IlegalUserActionException("Su solicitud está duplicada");

    }
}
