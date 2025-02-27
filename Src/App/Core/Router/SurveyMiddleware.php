<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Controllers\SurveyManagement;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Modules;
use App\Exceptions\NotFoundException;

class SurveyMiddleware extends RouterMiddleware
{

    public function resolve(array $url, array $params) : bool
    {
        if($url[0] == "surveys") {

            if(sizeof($url) >= 3 && $url[1] == "show") {
                
                (new SurveyManagement())->fill($url[2], []);
                return true;
            }
            
            Application::getInstance()->setAuthRequired(false);

            if(isset($url[1]) && $url[1] == "postdata_add"){
                $url[1] = "add";
            }
            if(isset($url[1]) && $url[1] == "postdata_edit"){
                $url[1] = "edit";
            }

        }

        return parent::resolve($url, $params);
    }
}