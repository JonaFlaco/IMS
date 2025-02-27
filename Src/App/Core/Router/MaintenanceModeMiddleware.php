<?php

namespace App\Core\Router;

use App\Core\Application;
use App\Core\Response;

class MaintenanceModeMiddleware extends RouterMiddleware
{
    public function resolve(array $url, $params) : bool
    {

        if(
            Application::getInstance()->settings->get('MAINTENANCE_MODE_IS_ACTIVE') == 1 &&
            (
                !isset($url[0]) ||
                (isset($url[0]) && _strtolower($url[0]) != "user")
            )
        ){

            //If under maintenance and request came from external api show the error as json format or simple string
            if(
                Application::getInstance()->user->isAdmin() != true &&
                isset($url) && (_strtolower($url[0]) == "externalapi" || Application::getInstance()->response->getResponseFormat() == Response::$FORMAT_SIMPLE || Application::getInstance()->response->getResponseFormat() == Response::$FORMAT_JSON)
            ){

                Application::getInstance()->response->setResponseFormat(Response::$FORMAT_JSON);

                throw new \App\Exceptions\MaintenanceModeException(Application::getInstance()->settings->get("maintenance_mode_message"));
            } else {

                if(Application::getInstance()->user->isAdmin() !== true){

                    $data['title'] = "Maintenance Mode";

                    //Request Tracker
                    //Application::getInstance()->coreModel->addTrackRequest(Application::getInstance()->request->getUrlStr(),json_encode(Application::getInstance()->request->getParams()));

                    Application::getInstance()->view->renderView('/templates/MaintenanceMode', $data);
                    exit;
                }

            }
        }

        return parent::resolve($url, $params);
    }
}