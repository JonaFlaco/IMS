<?php

namespace App\Core\Gviews\Components;
class ExtensionsComponent {

    private $viewData;

    private $baseAppPath;
    private $baseExtPath;

    public function __construct($viewData) {
        $this->viewData = $viewData;

        $this->baseAppPath = APP_ROOT_DIR . DS . "Views" . DS . "GviewExtends" . DS . toPascalCase($this->viewData->id);
        $this->baseExtPath = EXT_ROOT_DIR . DS . "Views" . DS . "GviewExtends" . DS . toPascalCase($this->viewData->id);
    }

    
    public function loadExtendedHtml() : ?string {
        
        $file = $this->baseAppPath . ".html.php"; 

        if(!is_file($file)){
            $file = $this->baseExtPath . ".html.php"; 
        }

        if(is_file($file)){
            return requireToVar($file); 
        }

        return null;
        
    }

    
    public function loadExtendScript() : ?string {
        
        $file = $this->baseAppPath . ".js.php"; 

        if(!is_file($file)){
            $file = $this->baseExtPath . ".js.php";     
        }
        
        if(is_file($file)){
            return requireToVar($file);
        }
        
        return null;
    }

    function loadExtendStyle() {
        
        $file = $this->baseAppPath . ".css.php"; 

        if(!is_file($file)){
            $file = $this->baseExtPath . ".css.php";     
        }
        
        if(is_file($file)){
            return requireToVar($file);
        }

        return null;
        
    }


    public function loadExtended() {
         
        $file = $this->baseAppPath . "Extends.php"; 

        if(!is_file($file)){
            $file = $this->baseExtPath . "Extends.php";     
        }
        
        if(is_file($file)){
            return requireToVar($file);
        }

        return null;
    }
    

}