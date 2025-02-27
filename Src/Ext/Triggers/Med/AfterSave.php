<?php

namespace Ext\Triggers\Med;

use App\Core\BaseTrigger;
use App\Core\Application;
use App\Core\Communications\EmailService;
use App\Core\Node;
use DateTime;

class AfterSave extends BaseTrigger
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $is_update = false, $source = null)
    {
        $userId = Application::getInstance()->user->getId();
        $case = $this->coreModel->nodeModel("med")
            ->id($id)
            ->loadFirstOrFail();

        // Update the patients field
        if ($case->beneficiary) {
            $beneficiary = $this->coreModel->nodeModel("beneficiaries")
                ->where("m.id = :id")
                ->bindValue("id", $case->beneficiary)
                ->loadFirstOrFail();

            $current_date = new DateTime();
            $birth_date = new DateTime($beneficiary->birth_date);
            $age = $current_date->diff($birth_date)->y;

            $case->patients  = $beneficiary->full_name;
            $case->code_general = $beneficiary->code;
            $case->patients_age = $age;
            $case->patients_sex = $beneficiary->gender_id_display;
        }

        if ($case->family_code) {
            $family = $this->coreModel->nodeModel("beneficiaries_family_information")
                ->where("m.id = :id")
                ->bindValue("id", $case->family_code)
                ->loadFirstOrFail();

            $current_date = new DateTime();
            $birth_date = new DateTime($family->birthdate);
            $age = $current_date->diff($birth_date)->y;

            $case->patients = $family->full_name;
            $case->code_general = $family->code;
            $case->patients_age = $age;
            $case->patients_sex = $family->gender_id_display;
        }

        $this->coreModel->node_save($case, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => true, "justification" => "Update fields"]);

        // Create a new story_clinic node if the source is b_services
        if ($is_update && $source == 'b_services') {
            $node = new Node("story_clinic");
            if ($case->beneficiary) {
                $node->beneficiary  = $case->beneficiary;
            }
            if ($case->family_code) {
                $node->family_code =  $case->family_code;
            }
            $node->relation_med = $case->id;

            $createdDate = $case->created_date;
            $dateTime = new DateTime($createdDate);
            $formattedDate = $dateTime->format('d-m-Y h:i A');
            $node->vitalsigns_data = $formattedDate;


            // Check if the story_clinic node already exists
            $storyClinicQuery = $this->coreModel->nodeModel("story_clinic");

            if ($case->beneficiary) {
                $storyClinicQuery->where("m.beneficiary = :beneficiary")
                    ->bindValue("beneficiary", $case->beneficiary);
            }

            if ($case->family_code) {
                $storyClinicQuery->where("m.family_code = :family_code")
                    ->bindValue("family_code", $case->family_code);
            }

            $hasStoryClinic = $storyClinicQuery->load();
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
