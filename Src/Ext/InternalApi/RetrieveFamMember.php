<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveFamMember extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $bnf_id = $_GET['id'];
        $familyMember = $this->coreModel->nodeModel("beneficiaries_family_information")
            ->where ("m.id = :bnf_id")
            ->bindValue('bnf_id',$bnf_id)
            ->loadFirstOrDefault();

        return_json($familyMember);
    }
}
