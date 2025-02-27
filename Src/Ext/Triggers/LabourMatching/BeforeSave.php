<?php

namespace Ext\Triggers\LabourMatching;

use App\Core\BaseTrigger;
use App\Exceptions\IlegalUserActionException;
use DateTime;

class BeforeSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($data, $is_update = false)
    {
        //print_r($data->tables[0]->data->residence_country);exit;
        if ($data->tables[0]->data->residence_country != 1)
            throw new IlegalUserActionException("Lo sentimos este registro es solo para residentes de Ecuador");

        $birthdateString = $data->tables[0]->data->birthdate;
        $birthdate = DateTime::createFromFormat('d/m/Y H:i:s', $birthdateString);

        $today = new DateTime();
        $age = $birthdate->diff($today)->y;

        if ($age < 18)
            throw new IlegalUserActionException("Lo sentimos este registro es solo para mayores de 18");

        $currentYear = $today->format('Y');
        if ($data->tables[0]->data->estimated_graduation_year < $currentYear && $data->tables[0]->data->study_status == 1) 
            throw new IlegalUserActionException("El año estimado de graduacion no puede ser menor a la fecha actual");
        
    }
}
