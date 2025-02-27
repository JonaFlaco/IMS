<?php

namespace Ext\InternalApi;

use App\Core\Controller;
use App\Core\Application;

class RetrieveUserProvinces extends Controller
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

        $values = array_map(function ($province) {
            return $province->value; 
        }, $userProvinces->provinces);

        return_json($values);
    }
}
