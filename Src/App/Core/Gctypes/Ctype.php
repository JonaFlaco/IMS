<?php

namespace App\Core\Gctypes;

use App\Core\Application;

#[\AllowDynamicProperties]
class Ctype {

    public function load($id) {
        return Application::getInstance()->coreModel->getCtypes($id);
    }


    public function getFields() {
        return Application::getInstance()->coreModel->getFields($this->id);
    }

    public function getParentCtypeFields() {
        if(isset($this->parent_ctype_id))
            return Application::getInstance()->coreModel->getFields($this->parent_ctype_id);
        else
            throw new \App\Exceptions\CriticalException("Parent Ctype Id is empty");
    }
    
}