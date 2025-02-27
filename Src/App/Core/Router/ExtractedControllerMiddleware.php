<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Exceptions\NotFoundException;

class ExtractedControllerMiddleware extends RouterMiddleware
{

    public function resolve(array $url, array $params) : bool
    {

        $extractedControllers = ["actions", "internalapi", "externalapi"];


        if (!in_array($url[0], $extractedControllers) || sizeof($url) < 2) {
            return parent::resolve($url, $params);
        }
        
        
        $classToRun = "\\App\\" . $url[0] . "\\" . $url[1];
        
        if(!class_exists($classToRun)){
            
            $classToRun = "\\Ext\\" . $url[0] . "\\" . $url[1];
        }
        
        if (!class_exists($classToRun)) {
            return parent::resolve($url, $params);
        }

        $currentController = $classToRun;
        $currentController = new $currentController(Application::getInstance()->coreModel);
        $currentMethod = "index";

        if(isset($url[1])){

            //Check to see if method exists in controller
            if(method_exists($currentController, $url[1])){
                $currentMethod = $url[1];
            }

        }

        
        call_user_func_array([$currentController, $currentMethod], array($url[2] ?? null, $params));
        
        return true;

    }
}