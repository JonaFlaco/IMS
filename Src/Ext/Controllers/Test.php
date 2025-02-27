<?php

namespace Ext\Controllers;

use App\Core\Controller;
use App\Core\Application;
use DateTime;


class Test extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->app->user->checkAuthentication();
    }

    public function index()


    {
        $currentUser = 21588;

        $userProvinces = $this->coreModel->nodeModel("users")
            ->where("m.id = :id")
            ->bindValue("id", $currentUser)
            ->loadFirstOrDefault();

        // Verificar si existen provincias y tomar el primer valor
        if (!empty($userProvinces->provinces)) {
            $provinceValue = $userProvinces->provinces[0]->value; // Tomar el primer valor
        }

        // Usar el valor de la provincia en tu consulta
        $focalPointIp = $this->coreModel->nodeModel("users")
            ->fields(["email", "full_name"])
            ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'provider_id')")
           // ->where("m.province_id = :province_id")
          //  ->bindValue("province_id", $provinceValue) // Aquí se usa el valor extraído
            ->loadFirstOrFail();

        return_json($focalPointIp);


    }
}
