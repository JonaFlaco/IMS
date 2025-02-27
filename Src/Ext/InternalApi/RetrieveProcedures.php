<?php

namespace Ext\InternalApi;

use App\Core\Controller;
use App\Core\Application;

class RetrieveProcedures extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $userId =  Application::getInstance()->user->getId();
        $userProvinces = $this->coreModel->nodeModel("users")
            ->where("m.id = :id")
            ->bindValue("id", $userId)
            ->loadFirstOrDefault();

        $provinceId = isset($userProvinces->provinces[0]) ? (int)$userProvinces->provinces[0]->value : null;

        if ($provinceId === null) {
            return_json([]);
            return;
        }

        $procedimientosQuery = $this->coreModel->nodeModel("procedimientos")
            ->where("m.province_id = :province_id")
            ->bindValue("province_id", $provinceId);

        $procedimientos = $procedimientosQuery->load();
        return_json($procedimientos);
    }
}
