<?php

namespace Ext\Triggers\EdfEoiFullApplication;

use App\Core\BaseTrigger;
use App\Exceptions\IlegalUserActionException;
use App\Exceptions\MissingDataFromRequesterException;



class BeforeSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($data, $is_update = false)
    {
        //print_r($data);exit;
        $total_value = 0;


        foreach ($data->tables as $table) {
            if ($table->type == 'field_collection' && $table->id == 'edf_eoi_full_application_hitos') {
                foreach ($table->data->data->tables as $subTable) {
                    if (isset($subTable->data->quantity) && isset($subTable->data->unit_cost_fixed)) {
                        // para cada total_cost
                        $subTable->data->total_cost = $subTable->data->quantity * $subTable->data->unit_cost_fixed;
                        //para sumar el valor total de todos los hitos
                        $total_value += $subTable->data->quantity * $subTable->data->unit_cost_fixed;
                    } else {
                        // Si falta algún dato, lanzamos una excepción
                        throw new MissingDataFromRequesterException("Agregue cantidad y costo unitario de cada hito");
                    }
                }
            }
        }

        $data->tables[0]->data->total_value_hitos = $total_value;

        // if($total_value > $data->tables[0]->data->amount_requested)
        //         throw new IlegalUserActionException("El valor total de los hitos debe ser igual al total solicitado a OIM");

        
        $totalVacancies = 0;
        foreach ($data->tables[5]->data->data->tables as $vacancies) {
            $totalVacancies += $vacancies->data->number_of_positions;
        }

        //$count = countObjects($data);
        if ($data->tables[0]->data->number_new_jobs != $totalVacancies)
            throw new IlegalUserActionException("El número de empleos creados debe ser igual a la suma de las vacantes ingresadas.");



        //print_r($data); exit;
        if ($data->tables[0]->data->time_months_expansion_plan > 6) {
            throw new IlegalUserActionException("El plan de expansión EDF debe completarse en un plazo máximo de 6 meses");
        }
        if ($data->tables[0]->data->number_affiliated_iess > $data->tables[0]->data->current_number_employees) {
            throw new IlegalUserActionException("El número de personas afiliadas al IESS no debe superar el total de empleados");
        }
        if ($data->tables[0]->data->collaborators_service > $data->tables[0]->data->current_number_employees) {
            throw new IlegalUserActionException("El número de colaboradores bajo prestación de servicios no debe superar el total de empleados");
        }
        if ($data->tables[0]->data->employees_operations  > $data->tables[0]->data->current_number_employees) {
            throw new IlegalUserActionException("El número de personas que laboran en operaciones no debe superar el total de empleados");
        }
        if ($data->tables[0]->data->employees_administrative   > $data->tables[0]->data->current_number_employees) {
            throw new IlegalUserActionException("El número de personas que laboran en la parte administrativa no debe superar el total de empleados");
        }
        if ($data->tables[0]->data->employees_sales   > $data->tables[0]->data->current_number_employees) {
            throw new IlegalUserActionException("El número de personas que laboran en en el área comercial no debe superar el total de empleados");
        }

        $yearsArray = []; // Array to store unique years values

        foreach ($data->tables as $table) {
            if ($table->type == 'field_collection' && $table->id == 'edf_eoi_full_application_financial_analysis') {
                foreach ($table->data->data->tables as $subTable) {
                    if (isset($subTable->data->years)) {
                        $years = $subTable->data->years;

                        // Check if the years value is already in the array
                        if (in_array($years, $yearsArray)) {
                            // If it is, then it's repeated, so throw an exception
                            throw new IlegalUserActionException("No puede repetir el año en la sección Análisis financiero");
                        } else {
                            // If not, add it to the array
                            $yearsArray[] = $years;
                        }
                    }
                }
            }
        }

        //if there is missing years throw error
        if (count($yearsArray) !== 3) {
            throw new IlegalUserActionException("Complete el análisis financiero para los últimos 3 años");
        }

        $monthsArray = [];

        foreach ($data->tables as $table) {
            if ($table->type == 'field_collection' && $table->id == 'edf_eoi_full_application_sales_forecast') {
                foreach ($table->data->data->tables as $subTable) {
                    if (isset($subTable->data->months)) {
                        $monthsArray[] = $subTable->data->months;
                    }
                }
            }
        }

        // check for missing months
        for ($i = 1; $i <= 12; $i++) {
            if (!in_array($i, $monthsArray)) {
                throw new IlegalUserActionException("Ingrese los valores para los 12 meses en la Proyección mensual de ventas.");
            }
        }
    }
}

function countObjects($data)
{
    $count = 0;

    foreach ($data->tables as $table) {
        if ($table->type == 'field_collection' && $table->id == 'edf_eoi_full_application_vacancies') {
            $count += count($table->data->data->tables);
        }
    }

    return $count;
}
