<?php

namespace App\Core\Gdashboards\Components;

use App\Core\Application;

class PageContentComponent {

    private $dashboardObj;

    private $coreModel;

    public function __construct($dashboardObj) {
        
        $this->dashboardObj = $dashboardObj;

        $this->coreModel = Application::getInstance()->coreModel;
    }

    public function generate(){
        
        ob_start(); ?>

        <div class="row">
            
            <?php foreach($this->dashboardObj->widgets as $widget):
    
                if($widget->is_hidden == true)
                    continue;
    
                if(intval($widget->size) < 1 || intval($widget->size) > 12 ) //|| !is_null($prefix)
                    $widget->size = 1;
    
                $is_hidden_cookie = false;
                if(isset($_COOKIE[$this->dashboardObj->id . "_widget_" . $widget->id . ""]) && $_COOKIE[$this->dashboardObj->id . "_widget_" . $widget->id . ""] == "0"){
                    $is_hidden_cookie = true;
                }
                ?>

                <div id="chart_parent_<?= $widget->id ?>" style="display: <?= $is_hidden_cookie == true ? "none" : "block" ?>" class="col-xl-<?= $widget->size ?>">
                    
                </div>
                
            <?php endforeach; ?>
    
            </div>

        <?php

        return ob_get_clean();
    }

}