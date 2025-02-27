<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveRentInfo extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $id = $_GET['id'];
        $case = $this->coreModel->nodeModel("rent_info_user_form_property")
            ->where("m.code = :code") 
            ->bindValue("code", $id)
            ->load();
        return_json($case);
    }
}
