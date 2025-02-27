<?php

namespace App\Core\Router;

use App\Core\Application;
use App\Core\Request;

class RouterMiddleware
{
    private ?RouterMiddleware $next = null;
    protected array $url;
    protected array $params;

    public function linkWith(RouterMiddleware $next) : RouterMiddleware
    {
        $this->next = $next;

        return $next;
    }

    public function resolve(array $url, array $params) : bool
    {
        if(!$this->next)
            return false;

        return $this->next->resolve($url, $params);
    }
}