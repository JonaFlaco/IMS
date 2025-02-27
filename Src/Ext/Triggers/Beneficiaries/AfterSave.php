<?php

namespace Ext\Triggers\Beneficiaries;

use App\Core\BaseTrigger;
use App\Core\Application;
use App\Exceptions\IlegalUserActionException;
use App\Core\Node;

class AfterSave extends BaseTrigger
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id)
    {
        $userId =  Application::getInstance()->user->getId();
        $case = $this->coreModel->nodeModel("beneficiaries")
            ->id($id)
            ->loadFirstOrFail();

        foreach ($case->family_information as $familyMember) {
            $formattedCode = "IOM-FAM-" . str_pad($familyMember->id, 6, "0", STR_PAD_LEFT);
            $familyMember->code = $formattedCode;
        }

        if (in_array($case->province, [24, 8, 7, 18, 2])) {
            $case->sub_office = 1;
        } else if ($case->province == 4) {
            $case->sub_office = 2;
        } else if (in_array($case->province, [5, 6, 13])) {
            $case->sub_office = 3;
        } else if (in_array($case->province, [16, 22, 11, 20])) {
            $case->sub_office = 4;
        } else if (in_array($case->province, [10, 17, 9])) {
            $case->sub_office = 5;
        } else if (in_array($case->province, [15, 19])) {
            $case->sub_office = 6;
        } else {
            $case->sub_office = null;
        }


        $isNurse = $this->coreModel->nodeModel("users")
            ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'enfermeria' AND parent_id = :id)")
            ->bindValue("id", $userId)
            ->loadFirstOrDefault();

        if ($isNurse) {
            $case->status_id = 2;
        }

        $this->coreModel->node_save($case, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => true, "justification" => "Auto-generate family member code"]);

        // $isNurse = $this->coreModel->nodeModel("users")
        //     ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'enfermeria' AND parent_id = :id)")
        //     ->bindValue("id", $userId)
        //     ->loadFirstOrDefault();

        // if ($isNurse) {
        //     // Guardar el beneficiario como un nodo independiente
        //     if ($case->id) {
        //         $beneficiaryNode = new Node("med");
        //         $beneficiaryNode->beneficiary = $case->id;
        //         $beneficiaryNode->save();
        //     }

        //     // Guardar cada miembro de la familia con su propio nodo
        //     foreach ($case->family_information as $familyMember) {
        //         $node = new Node("med");
        //         $node->family_code = $familyMember->id;
        //         $node->code_general = $familyMember->code;
        //         $node->save();
        //     }
        // }
    }
}
