<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveHomeInfo extends Controller
{

    public function __construct()
    {
        parent::__construct();

        // Verifica si el usuario está autenticado o en local
        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $id = $_GET['id'];
        $home = $this->coreModel->nodeModel("rent_info")
            ->id($id)
            ->loadFirstOrFail();
        return_json($home);
    }
}
