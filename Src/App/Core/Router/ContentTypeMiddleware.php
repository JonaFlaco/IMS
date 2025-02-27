<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Exceptions\NotFoundException;

class ContentTypeMiddleware extends RouterMiddleware
{

    public function resolve(array $url, array $params) : bool
    {
        $ctypeObj = (new Ctype)->load($url[0]);

        if($ctypeObj == null) {
            return parent::resolve($url, $params);
        }

        $currentController = new Gctypes($ctypeObj, !Application::getInstance()->getAuthRequired());

        $currentMethod = "index";

        if(isset($url[1])){

            //Check to see if method exists in controller
            if(method_exists($currentController, $url[1])){
                $currentMethod = $url[1];

            } else {
                throw new NotFoundException();
            }
        }

        call_user_func_array([$currentController, $currentMethod], array($url[2] ?? null, $params));

        return true;

    }

}