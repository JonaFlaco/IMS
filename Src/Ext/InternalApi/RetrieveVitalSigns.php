<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveVitalSigns extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $id = $_GET['id'];
        $retrieveRecord = $this->coreModel->nodeModel("med")
            ->fields(["vital_signs", "weight", "height", "pulse", "oxygen_saturation", "blood_pressure", "beneficiary", "temperature"])
            ->where("m.beneficiary = :id")
            ->bindValue("id", $id)
            ->loadFirstOrDefault();

        if (!$retrieveRecord || !$retrieveRecord->beneficiary) {
            $retrieveRecord = $this->coreModel->nodeModel("med")
                ->fields(["vital_signs", "weight", "height", "pulse", "oxygen_saturation", "blood_pressure", "family_code", "temperature"])
                ->where("m.family_code = :id")
                ->bindValue("id", $id)
                ->loadFirstOrFail();
        }

        return_json($retrieveRecord);
    }
}
