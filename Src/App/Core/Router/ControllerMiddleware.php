<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Modules;
use App\Exceptions\NotFoundException;

class ControllerMiddleware extends RouterMiddleware
{

    public function resolve(array $url, array $params) : bool
    {

        
        $classToRun = "\\App\\Controllers\\" . $url[0];
        if(!class_exists($classToRun)){
            $classToRun = "\\Ext\\Controllers\\" . $url[0];
        }


        if($url[0] == "ctypes" || !class_exists($classToRun)){
            return parent::resolve($url, $params);
        }

        $currentController = new $classToRun();

        $currentMethod = "index";

        if(isset($url[1]) && _strlen($url[1]) > 0){

            //Check to see if method exists in controller
            if(method_exists($currentController, $url[1])){
                $currentMethod = $url[1];
            } else {
                throw new NotFoundException();
            }
        }

        if(method_exists($currentController, $currentMethod)) {

            call_user_func_array([$currentController, $currentMethod], array($url[2] ?? null, $params));

            return true;
        } else {

            throw new NotFoundException();
        }

    }
}