<?php

namespace Ext\Triggers\BServices;

use App\Core\BaseTrigger;
use App\Core\Application;
use App\Core\Communications\EmailService;
use App\Core\Node;

class AfterSave extends BaseTrigger
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id)
    {
        $userId = Application::getInstance()->user->getId();

        $bService = $this->coreModel->nodeModel("b_services")
            ->id($id)
            ->loadFirstOrFail();

        if ($bService->cbi_modality_regular == 2) {
            $bService->status_id = 2;
            //aprueba la asistencia si es un adelanto directo de cbi regularizacion
            $this->coreModel->node_save($bService, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => true, "justification" => "Update status"]);
        }

        if ($bService->unit_id == 2 && $bService->health_referral_new == 3) {

            if (!isset($bService->members_assisted) || !is_array($bService->members_assisted)) {
                return;
            }

            $patientData = [];

            foreach ($bService->members_assisted as $member) {
                $beneficiariesId = $member->beneficiaries_id;

                if ($beneficiariesId) {
                    $beneficiary = $this->coreModel->nodeModel("beneficiaries")
                        ->fields(["id", "code", "full_name"])
                        ->where("m.id = :id")
                        ->bindValue("id", $beneficiariesId)
                        ->loadFirstOrDefault();

                    if ($beneficiary) {
                        $patientData[] = [
                            'code' => $beneficiary->code,
                            'full_name' => $beneficiary->full_name,
                        ];
                    }
                }

                $familyId = $member->family_member;
                $familyMembers = $this->coreModel->nodeModel("beneficiaries_family_information")
                    ->fields(["code", "full_name"])
                    ->where("m.id = :id")
                    ->bindValue("id", $familyId)
                    ->load();

                foreach ($familyMembers as $familyMember) {
                    $patientData[] = [
                        'code' => $familyMember->code,
                        'full_name' => $familyMember->full_name,
                    ];
                }

                $node = new Node("med");
                if ($beneficiariesId) {
                    $node->beneficiary  = $beneficiariesId;
                }
                if ($familyId) {
                    $node->family_code = $familyId;
                }

                // Update the Med field
                $userProvinces = $this->coreModel->nodeModel("users")
                    ->where("m.id = :id")
                    ->bindValue("id", $userId)
                    ->loadFirstOrDefault();

                $provinceValue = $userProvinces->provinces[0]->value;
                $node->province = $provinceValue;
                $node->save();
            }
        }
    }
}
