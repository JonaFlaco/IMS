<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveCaseData extends Controller
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
            ->id($id)
            ->loadFirstOrFail();
        $uploadDir = UPLOAD_DIR_FULL; // Ruta completa de la carpeta de carga
        $case->uploadDir = $uploadDir; 
        return_json($case);
    }
}
