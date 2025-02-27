<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveSubServicesByType extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $serviceTypeId = $_GET['id'];
        $subServices = $this->coreModel->nodeModel("sub_services")
            ->where ("m.id in (select value_id from service_type_sub_service where parent_id = :serviceTypeId)")
            ->bindValue('serviceTypeId',$serviceTypeId)
            ->load();

        return_json($subServices);
    }
}
