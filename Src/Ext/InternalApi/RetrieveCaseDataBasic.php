<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveCaseDataBasic extends Controller
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
        $case = $this->coreModel->nodeModel("beneficiaries")
            ->fields([
                "id",
                "full_name",
                "code",
                "province",
                "national_id_no",
                "nationality_id"
            ])
            ->id($id)
            ->loadFirstOrDefault();

        return_json($case);
    }
}
