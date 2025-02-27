<?php

namespace Ext\Triggers\BServices;

use App\Core\BaseTrigger;
use App\Core\Application;
use App\Exceptions\CriticalException;
use App\Exceptions\IlegalUserActionException;
use App\Helpers\MiscHelper;
use DateTime;

class BeforeSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($data, $is_update = false)
    {

        $userId =  Application::getInstance()->user->getId();
        // if ($data->tables[0]->data->unit_id == 3 && $data->tables[0]->data->service_id == 1) {
        //     $this->coreModel->nodeModel("rental_assistance")
        //         ->fields(["beneficiary_id", "code"])
        //         ->where("m.beneficiary_id = :beneficiary_id")
        //         ->bindValue(":beneficiary_id", $data->tables[0]->data->bnf_id)
        //         ->loadFirstOrFail("Este beneficiario no ha realizado evaluacion de asistencia en rentas");
        // }

        //para guardar un servicio se debe tener una evaluacion general realizada al beneficiario
        $case = $this->coreModel->nodeModel("beneficiaries")
            ->fields(["code", "created_user_id"])
            ->where("m.id = :beneficiary_id")
            ->bindValue(":beneficiary_id", $data->tables[0]->data->bnf_id)
            ->loadFirst();


        $isNurse = $this->coreModel->nodeModel("users")
            ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'enfermeria' AND parent_id = :id)")
            ->bindValue("id", $userId)
            ->loadFirstOrDefault();

        //no se aplica la restriccion a casos importados
        if ($case->created_user_id && $case->created_user_id != 21468 && !$isNurse) {
            $evaluation = $this->coreModel->nodeModel("evaluation")
                ->fields(["beneficiary_id", "code", "protection_issues", "protection_confirm", "created_user"])
                ->where("m.beneficiary_id = :beneficiary_id")
                ->bindValue(":beneficiary_id", $data->tables[0]->data->bnf_id)
                ->loadFirstOrFail("Este beneficiario no ha realizado la evaluacion general");

            if ($evaluation->protection_issues == 1 && $evaluation->protection_confirm == 0) {
                throw new IlegalUserActionException("Este caso no se encuentra habilitado para agregar servicios, comuníquese con el punto focal de protección para que lo habilite");
            }
        }

        $membersAsistedNumber = count($data->tables[4]->data->data->tables);

        if ($data->tables[0]->data->sub_service == 8) {
            $amountPerPerson = $data->tables[0]->data->bnf_amount / $membersAsistedNumber;
            //restriccion para sub servicio de regularizacion CBI
            if ($amountPerPerson > 60 || $amountPerPerson <= 29 || $data->tables[0]->data->bnf_amount > 300)
                throw new IlegalUserActionException("El monto maximo por persona de CBI regularizacion es 60 USD y el maximo por carga es 300 (El monto para adultos mayores y personas con discapacidad es 30)");
        }
        if ($data->tables[0]->data->health_referral_new == 1) {
            //restriccion para sub servicio de salud CBI
            // if ($data->tables[0]->data->bnf_amount  > 150)
            //     throw new IlegalUserActionException("El monto maximo para CBI salud es 150 USD");

            if ($data->tables[0]->data->bnf_amount  < 10)
                throw new IlegalUserActionException("El monto minimo para CBI salud es 10 USD");
        }

        if ($data->tables[0]->data->sub_service == 25) {
            //restriccion para sub servicio de CBI vouchers la favorita
            $expectedAmounts = [
                1 => 45,
                2 => 90,
                3 => 135,
                4 => 180,
                5 => 225
            ];

            // Define el monto máximo para 5 o más miembros asistidos
            $maxMembers = 5;
            $maxAmount = 225;

            //monto
            $currentAmount = $data->tables[0]->data->bnf_amount;

            // Verifica el monto según el número de miembros asistidos
            if (isset($expectedAmounts[$membersAsistedNumber])) {
                if ($currentAmount != $expectedAmounts[$membersAsistedNumber]) {
                    throw new IlegalUserActionException("El monto para $membersAsistedNumber persona(s) de CBI vouchers la favorita es {$expectedAmounts[$membersAsistedNumber]}");
                }
            } elseif ($membersAsistedNumber >= $maxMembers && $currentAmount != $maxAmount) {
                throw new IlegalUserActionException("El monto para $maxMembers o más personas de CBI vouchers la favorita es $maxAmount");
            }
        }


        if ($is_update == false) {

            $beneficiary = $this->app->coreModel->nodeModel("beneficiaries")->fields(["province"])->id($data->tables[0]->data->bnf_id)->loadFirstOrFail();

            $data->tables[0]->data->province_id  = $beneficiary->province;
        }
    }
}
