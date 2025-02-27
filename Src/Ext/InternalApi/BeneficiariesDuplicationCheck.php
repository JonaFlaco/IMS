<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class BeneficiariesDuplicationCheck extends Controller
{

    public function __construct()
    {
        parent::__construct(false);
    }

    public function index($id, $params = [])
    {
        if (isset($_POST['id']))
            $id = $_POST['id'];
        else
            throw new \Exception("record id is empty");
    
        
        $beneficiary = $this->coreModel->nodeModel("beneficiaries")->fields(['full_name', 'national_id_no','passport_no','birth_certificate_no', 'phone_number', 'other_id_no'])->id($id)->loadFirstOrFail();

        $beneficiariesFamilyInformation = $this->coreModel->nodeModel("beneficiaries_family_information")->fields(['full_name', 'family_national_id'])->where("parent_id = ".$id)->load();

        $bmModel = new \Ext\Models\BmModel;

        $duplication = $bmModel->getBeneficiaryDuplicates($beneficiary->id, $beneficiary->full_name, $beneficiary->national_id_no,$beneficiary->passport_no,$beneficiary->birth_certificate_no, $beneficiary->phone_number, $beneficiary->other_id_no);
        
        foreach($beneficiariesFamilyInformation as $fa){
            $familyDuplication = $bmModel->getBeneficiaryDuplicates($beneficiary->id, $fa->full_name, $fa->family_national_id, null, null, null,null);
            // Merge family duplication with existing duplication
            $duplication = array_merge($duplication, $familyDuplication);        
        }

        $result = (object)[
            "status" => "success",
            "duplication" => $duplication
        ];

        return_json($result);

    }
}
