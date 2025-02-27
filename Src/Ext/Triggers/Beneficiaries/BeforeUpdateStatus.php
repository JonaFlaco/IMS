<?php

namespace Ext\Triggers\Beneficiaries;

use App\Core\BaseTrigger;
use App\Core\Application;
use App\Exceptions\IlegalUserActionException;
class BeforeUpdateStatus extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $from_status_id, &$to_status_id, $step = 0, $total_steps = 0, $path = null, &$justification = null, $confirmed = null)
    {
        $userId =  Application::getInstance()->user->getId();
        $userAdmin =  Application::getInstance()->user->isAdmin();
   
        $caso = $this->coreModel->nodeModel("beneficiaries")
            ->fields(['case_worker','created_user_id'])
            ->where("m.id = :id")
            ->bindValue("id", $id)
            ->load();

            // if($caso[0]->created_user_id && $caso[0]->created_user_id != 21468 ){
            //     if(!$caso[0]->case_worker){
            //         throw new IlegalUserActionException("Este caso aun no ha sido asignado a un gestor");
            //     }
            // }
        // if($userId != $caso[0]->case_worker && $userAdmin != 1){
        //     throw new IlegalUserActionException("Este caso fue asignado a otro gestor");
        // }


        if ($to_status_id == 2|| $to_status_id == 88) {

            $beneficiary = $this->coreModel->nodeModel("beneficiaries")->fields(['full_name', 'national_id_no','passport_no','birth_certificate_no', 'phone_number','other_id_no'])->id($id)->loadFirstOrFail();

            $bmModel = new \Ext\Models\BmModel;

            $duplication = $bmModel->getBeneficiaryDuplicates($beneficiary->id, $beneficiary->full_name, $beneficiary->national_id_no,$beneficiary->passport_no,$beneficiary->birth_certificate_no, $beneficiary->phone_number,$beneficiary->other_id_no);

            $beneficiariesFamilyInformation = $this->coreModel->nodeModel("beneficiaries_family_information")->fields(['full_name', 'family_national_id'])->where("parent_id = ".$id)->load();

            foreach($beneficiariesFamilyInformation as $fa){
                $familyDuplication = $bmModel->getBeneficiaryDuplicates($beneficiary->id, $fa->full_name, $fa->family_national_id, null, null, null,null);
                // Merge family duplication with existing duplication
                $duplication = array_merge($duplication, $familyDuplication);        
            }

            $error = "";
            $warning = "";
            foreach ($duplication as $row) {
                if ($row->is_warning != true) {
                    $error .= $row->error;
                } else {
                    $warning .= $row->error;
                }
            }

            if (!empty($error)) {
                $error = "<ul class=\\\"m-0\\\">$error</ul>";
                return array("status" => "error", "message" => $error);
            }

            if (!empty($warning)) {
                $warning = "<ul class=\\\"m-0\\\">$warning</ul>";
                return array("status" => "warning", "message" => $warning);
            }
        }
    }
}
