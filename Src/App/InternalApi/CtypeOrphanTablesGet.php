<?php

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use App\Exceptions\MissingDataFromRequesterException;

class CtypeOrphanTablesGet extends BaseInternalApi
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->app->user->isAdmin()) {
            throw new ForbiddenException();
        }
    }

    public function index($id = null, $params = [])
    {

        $result = (object)[
            "status" => "success",
            "result" => $this->coreModel->get_stuck_tables()
        ];

        return_json($result);
    }
}
