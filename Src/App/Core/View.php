<?php

/**
 * This class contains methods and helpers to rendew view
 */
namespace App\Core;

use \App\Exceptions\NotFoundException;

class View {


    //This class renders view
    public function renderView(string $view, array $data = array(), $useCache = false){

        
        $view = _str_replace("\\",DS, $view);
        $view = _str_replace("/",DS, $view);
        $view = _str_replace(".php","", $view);

        $viewFullPath = DS . "Views" . DS . toPascalCase($view) . ".php";
        
        if($useCache) {
            $dataFromCache = null;//Application::getInstance()->cache->get("render_view.$viewFullPath");
            if(isset($dataFromCache)) {
                echo $dataFromCache;
            } else {
                if(file_exists(APP_ROOT_DIR . $viewFullPath)){
                    $x = requireToVar(APP_ROOT_DIR . $viewFullPath, $data);
                    Application::getInstance()->cache->set("render_view.$viewFullPath", $x, 600);
                    echo $x;
                } else if (file_exists(EXT_ROOT_DIR . $viewFullPath)){
                    $x = requireToVar(EXT_ROOT_DIR . $viewFullPath, $data);
                    Application::getInstance()->cache->set("render_view.$viewFullPath", $x, 600);
                    echo $x;
                } else {
                    throw new NotFoundException("View not found: " . $viewFullPath);
                }
            }
        } else {
            if(file_exists(APP_ROOT_DIR . $viewFullPath)){
                require_once APP_ROOT_DIR . $viewFullPath;
            } else if (file_exists(EXT_ROOT_DIR . $viewFullPath)){
                require_once EXT_ROOT_DIR . $viewFullPath;
            } else {
                throw new NotFoundException("View not found: " . $viewFullPath);
            }
        }
    }

}