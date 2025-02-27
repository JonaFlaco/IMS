<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveCaseEspecialidades extends Controller
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
        $specialistData = $this->coreModel->nodeModel("story_clinic")
            ->where("m.beneficiary = :id")
            ->bindValue('id', $id)
            ->load();

        $specialist = array_map(function ($item) {
            return $item->specialist;
        }, $specialistData);

        $flatSpecialist = [];
        foreach ($specialist as $specialities) {
            $flatSpecialist = array_merge($flatSpecialist, $specialities);
        }

        return_json($flatSpecialist);
    }

}
