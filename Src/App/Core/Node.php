<?php

/**
 * This class can be used as node to fill then save it in database
 */

namespace App\Core;

use \StdClass;

class Node extends StdClass {

    public string $sett_ctype_id;
    public $id = null;

    public function __construct(string $sett_ctype_id = null) {
        if(!empty($sett_ctype_id))
            $this->sett_ctype_id = $sett_ctype_id;
    }

    public function save($settings = []){
        $this->id = Application::getInstance()->coreModel->node_save($this, $settings);
        return $this->id;
    }

}
