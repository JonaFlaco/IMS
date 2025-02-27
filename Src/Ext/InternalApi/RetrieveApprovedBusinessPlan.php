<?php

namespace Ext\InternalApi;

use App\Core\BaseInternalApi;
use \App\core\Application;

class RetrieveApprovedBusinessPlan extends BaseInternalApi
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $params = [])
    {

        if (_strlen($id) == 0)
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");

        $id = $_GET['id'];

        $approvedBusinessPlan = \App\Core\Application::getInstance()->coreModel->nodeModel('approved_business_plan')
            ->where("m.business_id = :bsns_id")
            ->bindValue(":bsns_id", $id)
            ->load();

        return_json($approvedBusinessPlan);
    }
}
