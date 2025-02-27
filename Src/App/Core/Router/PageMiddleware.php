<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Modules;
use App\Exceptions\NotFoundException;

class PageMiddleware extends RouterMiddleware
{

    public function resolve(array $url, array $params) : bool
    {
        if($url[0] == "pages") {
            Application::getInstance()->setAuthRequired(false);
        }

        return parent::resolve($url, $params);
    }
}