<?php

namespace Ext\InternalApi;

use App\Core\Controller;
use App\Core\Application;

class RetrieveUserRoles extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        // Obtiene los roles del usuario como una cadena separada por comas
        $userRolesString = Application::getInstance()->user->getRoles();

        // Divide la cadena en un array de roles
        $userRoles = explode(',', $userRolesString);

        // Verifica si "protection_focal_point" está en el array de roles
        $hasProtectionFocalPointRole = in_array('protection_focal_point', $userRoles);

        // Devuelve true o false según el resultado de la verificación
        echo json_encode(['hasProtectionFocalPointRole' => $hasProtectionFocalPointRole]);
    }
}
