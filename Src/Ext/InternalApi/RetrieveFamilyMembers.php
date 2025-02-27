<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveFamilyMembers extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $bnf_id = $_GET['id'];
        $familyMembers = $this->coreModel->nodeModel("beneficiaries_family_information")
            ->where ("parent_id = :bnf_id")
            ->bindValue('bnf_id',$bnf_id)
            ->load();

        return_json($familyMembers);
    }
}
