<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveHistoryClinic extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $storyClinicId = $_GET['id'];

        if ($storyClinicId) {
            $initialRecord = $this->coreModel->nodeModel("story_clinic")
                ->where("m.id = :id")
                ->bindValue("id", $storyClinicId)
                ->loadFirstOrDefault();

            if ($initialRecord) {
                $beneficiaryId = $initialRecord->beneficiary;
                $familyCode = $initialRecord->family_code;

                $storyClinicQuery = $this->coreModel->nodeModel("story_clinic");

                if ($beneficiaryId) {
                    $storyClinicQuery->where("m.beneficiary = :beneficiary")
                        ->bindValue("beneficiary", $beneficiaryId);
                }

                if ($familyCode) {
                    $storyClinicQuery->where("m.family_code = :family_code")
                        ->bindValue("family_code", $familyCode);
                }

                $retrieveRecords = $storyClinicQuery->load();

                return_json($retrieveRecords);
            } else {
                return_json([]);
            }
        } else {
            return_json([]);
        }
    }
}
