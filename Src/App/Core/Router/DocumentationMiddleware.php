<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Core\Application;
use App\Core\Documentations\DocLoader;
use App\Core\Gctypes\Ctype;
use App\Core\Modules;
use App\Exceptions\NotFoundException;

class DocumentationMiddleware extends RouterMiddleware
{

    public function resolve(array $url, array $params) : bool
    {
        $request = Application::getInstance()->request;

        $item = Application::getInstance()->coreModel->nodeModel("dashboards")
            ->id($url[0])
            ->loadFirstOrDefault();

        if($item != null) {

            //Application::getInstance()->coreModel->addTrackRequest($request->getUrlStr(),json_encode($request->getParams()),null,null,null,null,0);

            (new \App\Core\Gdashboards\DashboardGenerator($item->id))->generate(); 

            return true;
        }
        
        return parent::resolve($url, $params);
    }
}