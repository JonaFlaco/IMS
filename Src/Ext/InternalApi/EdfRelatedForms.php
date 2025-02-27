<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class EdfRelatedForms extends Controller
{

    public function __construct()
    {
        parent::__construct(false);
    }

    public function index($id, $params = [])
    {

        if (!isset($id)) {
            throw new \Exception("record id is empty");
        }
        
        $eoiData = $this->coreModel->nodeModel("edf_eoi")->fields(['id', 'code'])->where("m.id = :id")->bindValue('id', $id)->load(); 
        $edfEoiVer = $this->coreModel->nodeModel("edf_eoi_verification")->fields(['id', 'code'])->where("m.business_id = :id")->bindValue('id', $id)->load();
        $edfApp = $this->coreModel->nodeModel("edf_eoi_full_application")->fields(['id', 'code'])->where("m.eoi_id = :id")->bindValue('id', $id)->load();


        $result = (object)[
            "status" => "success",
            "edfEoiData" => $eoiData,
            "edfEoiVerData" => $edfEoiVer,
            "edfAppData" => $edfApp
        ];

        return_json($result);
    }
}
