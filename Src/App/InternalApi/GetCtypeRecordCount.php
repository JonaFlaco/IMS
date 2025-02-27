<?php

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;

class getCtypeRecordCount extends BaseInternalApi
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->app->user->isAdmin()) {
            throw new ForbiddenException();
        }
    }

    public function index($ctype_id, $params = [])
    {

        $data = $this->coreModel->getCtypeRecordCount($ctype_id);

        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);
    }
}
