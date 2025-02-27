<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveEdfEoiData extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $id = $_GET['id'];
        $edf = $this->coreModel->nodeModel("edf_eoi")
            ->id($id)
            ->loadFirstOrFail();

        return_json($edf);
    }
}
