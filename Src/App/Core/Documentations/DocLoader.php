<?php 

/*
 * This class handles rendering documentation 
 */

namespace App\Core\Documentations;

use App\Core\Application;
use App\Exceptions\NotFoundException;
    
class DocLoader {
    
    private $docObj;
    private $url;
    private $params;

    public function __construct($docObj, $url, $params) {
        $this->docObj = $docObj;
        $this->url = $url;
        $this->params = $params;

        Application::getInstance()->user->checkAuthentication();

    }


    public function main(){
        
        $root_dir = DOC_ROOT_DIR . "\\" . $this->docObj->name;

        $levels[0] = array("link" => "" . $this->docObj->name, "title" => $this->docObj->title);
        
        $folder = null;
        $file = null;
        $parentLink = null;
        
        if(isset($this->url) && isset($this->url[2])){
            $file = $this->url[2];
        }

        if(isset($this->url) && isset($this->url[1])){
            if(!empty($file)){
                $folder = $this->url[1];
                $parentLink = $this->docObj->name . "/" . $folder;
            } else {
                $file = $this->url[1];
            }
        }
        
        
        if(!empty($folder)) {
            $levels[1] = array("link" => $this->docObj->name . "/" . $folder , "title" => $this->decodeTitle($folder));
        }


        if(empty($file)){
            $file = "index";
        }

        if($file != "index" && empty($parentLink)) {
            $parentLink = $this->docObj->name;
        }
        if($file == "index" && !empty($parentLink)) {
            $parentLink = $this->docObj->name;
        }

        $full_path = $root_dir . "\\" . (!empty($folder) ? $folder . "\\" : "") . $file . ".md";

        if(!file_exists($full_path)){

            $full_path = $root_dir . "\\" . (!empty($folder) ? $folder . "\\" : "") . $file . "\\index.md";
            if(!file_exists($full_path)){
                throw new NotFoundException();
            } else {
                $levels[2] = array("link" => $this->docObj->name . "/" . (!empty($folder) ? $folder . "\\" : "") . $file, "title" => $this->decodeTitle($file));
            }
        } else {
            if(!empty($folder) || $file != "index"){
                $levels[2] = array("link" => $this->docObj->name . "/" . (!empty($folder) ? $folder . "\\" : "") . $file, "title" => $this->decodeTitle($file));
            }
        }

        $content = file_get_contents($full_path);

        $content = _str_replace("\${DOCNAME}",$this->docObj->name,$content);
        
        $data['content'] = \App\Core\MarkDown::parse($content);
        $data["docObj"] = $this->docObj;
        $data["title"] = $this->docObj->title;
        $data["menu_id"] = $this->docObj->menu_id;
        $data['parentLink'] = $parentLink;
        $data['levels'] = $levels;

        Application::getInstance()->view->renderView('system/docs', (array)$data);
    }


    private function decodeTitle($value){

        $value = _str_replace("_", " ", $value);

        return ucfirst($value);
    }

}