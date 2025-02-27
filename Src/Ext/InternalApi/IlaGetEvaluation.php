<?php

namespace Ext\InternalApi;

use App\Core\BaseInternalApi;
use \App\core\Application;

class IlaGetEvaluation extends BaseInternalApi
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $params = [])
    {

        if (_strlen($id) == 0)
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");

        $evaluation = \App\Core\Application::getInstance()->coreModel->nodeModel('evaluation')
            ->where("m.beneficiary_id = :beneficiary_id")
            ->bindValue(":beneficiary_id", $id)
            ->load();


        $result = (object)[
            "status" => "success",
            "result" => (object)[
                "evaluation" => sizeof($evaluation) > 0 ? $evaluation : null
            ]
        ];
        return_json($result);
    }
}
