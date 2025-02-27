<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class CaseWorkerAssignment extends Controller
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
            ->fields(["code", "case_worker", "unit_id", "unit_id_display", "province", "province_display"])
            ->id($id)
            ->loadFirstOrFail();

        // Cargar los usuarios desde la base de datos
        $users = $this->coreModel->nodeModel("users")
            ->fields(["id", "full_name"])
            ->where("m.id IN (SELECT parent_id FROM users_roles WHERE value_id = 'bm_case_worker') AND id IN (SELECT parent_id FROM users_governorates WHERE value_id = :province) AND id IN (SELECT parent_id FROM users_units WHERE value_id = :unit)")
            ->bindValue("province", $case->province)
            ->bindValue("unit", $case->unit_id)
            ->load();

        // Inicializar un array para almacenar los usuarios en un formato adecuado para el dropdown
        $formattedUsers = [];

        // Iterar sobre los usuarios y agregarlos al array en el formato requerido
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->id,
                'full_name' => $user->full_name
            ];
        }

        // Devolver los usuarios en formato JSON
        return_json($formattedUsers);
    }
}
