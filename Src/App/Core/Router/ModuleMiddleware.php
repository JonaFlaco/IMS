<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Modules;
use App\Exceptions\NotFoundException;

class ModuleMiddleware extends RouterMiddleware
{

    public function resolve(array $url, array $params) : bool
    {
        $id = $url[0];
        $request = Application::getInstance()->request;

        $item = Application::getInstance()->coreModel->nodeModel("modules")
            ->id($id)
            ->loadFirstOrDefault();

        if($item != null) {

            $currentController = new Modules($item);

            //Application::getInstance()->coreModel->addTrackRequest($request->getUrlStr(),json_encode($request->getParams()),null,null,null,null,0);

            call_user_func_array([$currentController, "index"], array($params));

            return true;
        }

        return parent::resolve($url, $params);
    }
}