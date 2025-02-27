<?php

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use App\Exceptions\MissingDataFromRequesterException;

class CtypeOrphanColumnsGet extends BaseInternalApi
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

        $list = $this->coreModel->nodeModel("ctypes")->id($id)->fields(["id", "name"])->load();
        $result = [];

        foreach($list as $item) {
            $result = array_merge($result, $this->coreModel->getCtypeOrphanColumnsData($item->id));
        }

        $result = (object)[
            "status" => "success",
            "result" => $result
        ];

        return_json($result);
    }
}
