<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrievePersonCodes extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {

        $familyMembers = $this->coreModel->nodeModel("beneficiaries_family_information")
            ->fields(['code'])
            ->load();

        $case = $this->coreModel->nodeModel("beneficiaries")
            ->fields(['code'])
            ->load();
        $codes=array_merge($familyMembers,$case);
        return_json($codes);
    }
}
