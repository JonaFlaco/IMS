<?php

namespace Ext\InternalApi;

use App\Core\Controller;

class RetrieveSubServices extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        $serviceId = $_GET['id'];
        $subServices = $this->coreModel->nodeModel("sub_services")
            ->where ("m.id in (select value_id from service_type_sub_service where parent_id = (select service_type from services where id = :serviceId ))")
            ->bindValue('serviceId',$serviceId)
            ->load();

        return_json($subServices);
    }
}
