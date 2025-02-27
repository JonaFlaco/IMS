<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Modules;
use App\Exceptions\NotFoundException;

class CustomUrlMiddleware extends RouterMiddleware
{

    public function resolve(array $url, array $params) : bool
    {
        $request = Application::getInstance()->request;

        $item = Application::getInstance()->coreModel->nodeModel("custom_url")
            ->where("m.new_url = :name")
            ->bindValue(":name", $url[0])
            ->loadFirstOrDefault();

        if($item != null) {

            //Application::getInstance()->coreModel->addTrackRequest($request->getUrlStr(),json_encode($request->getParams()),null,null,null,null,0);

            $_GET['url'] = _strtolower($item->old_url );

            Application::getInstance()->response->redirect(_strtolower($item->old_url));

            return true;
        }

        return parent::resolve($url, $params);
    }
}