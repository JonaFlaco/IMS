<?php

namespace Ext\Triggers\Evaluation;

use App\Core\BaseTrigger;
use App\Core\Application;
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

        $id = $data->tables[0]->data->beneficiary_id;
        $case = $this->coreModel->nodeModel("beneficiaries")
            ->id($id)
            ->loadFirstOrFail();
        if ($case->created_user_id && $case->created_user_id != 21468) {
            if (!$case->case_worker)
                throw new IlegalUserActionException("Este caso aún no ha sido asigando a un gestor");
        }


        // $haveDependentChildren = false; // Variable para verificar si hay dependientes menores
        // $haveAdult = false;
        // foreach ($case->family_information as $member) {
        //     if ($member->birthdate) {
        //         $birthdate = new DateTime($member->birthdate);
        //         $today = new DateTime();
        //         $age = $birthdate->diff($today)->y;

        //         if ($age < 18 && $member->relationship == 11) {
        //             $data->tables[0]->data->is_separated_chil = 1;
        //         }
        //         if ($age < 18) {
        //             $haveDependentChildren = true; // Al menos un menor encontrado
        //         }
        //         if ($age >= 18) {
        //             $haveAdult = true; // Al menos un adulto encontrado
        //         }
        //     }
        // }
        // if ($haveDependentChildren) {
        //     $data->tables[0]->data->have_dependent_children = 1;
        // }
        // if (!$haveAdult && $haveDependentChildren) {
        //     $data->tables[0]->data->alone_dependents = 1;
        // }
    }
}
