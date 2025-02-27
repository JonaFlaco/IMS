<?php

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use App\Exceptions\MissingDataFromRequesterException;

class CtypeOrphanColumnsDelete extends BaseInternalApi
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->app->user->isAdmin()) {
            throw new ForbiddenException();
        }
    }

    public function index($ctypeId, $params = [])
    {
        $columnName = isset($params["column_name"]) ? $params["column_name"] : null;

        if(empty($ctypeId)) {
            throw new MissingDataFromRequesterException("Ctype name is missing");
        }

        if(empty($columnName)) {
            throw new MissingDataFromRequesterException("Column name is missing");
        }
        

        $this->coreModel->deleteCtypeOrphanColumn($ctypeId, $columnName);

        return Application::getInstance()->response->returnSuccess();
    }
}
