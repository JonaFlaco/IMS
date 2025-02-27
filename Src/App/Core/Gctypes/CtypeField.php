<?php

namespace App\Core\Gctypes;

use App\Core\Application;
use App\Core\FromArray;

#[\AllowDynamicProperties]
class CtypeField {
    use FromArray;

    public function loadById($id) {
        $result = Application::getInstance()->coreModel->getFields(null, $id);

        if(empty($result)) {
            throw new \App\Exceptions\NotFoundException("Field Not Found");
        }

        return $result[0];
    }

    public function loadByCtypeId($id) {
        return Application::getInstance()->coreModel->getFields($id);
    }

    public function loadByCtypeIdAndFieldName($ctypeId, string $fieldName) {
        
        $result = Application::getInstance()->coreModel->getFields($ctypeId, null, $fieldName);
        
        if(empty($result))
            throw new \App\Exceptions\NotFoundException(sprintf("Field (%s) not found in (%s)", $fieldName, $ctypeId));

        return $result[0];
    }

    public function loadByCtypeIdAndFieldNameOrDefault($ctypeId, string $fieldName) {
        
        $result = Application::getInstance()->coreModel->getFields($ctypeId, null, $fieldName);
        
        if(empty($result))
            return null;

        return $result[0];
    }


    public function getParentCtype() {
        return (new Ctype)->load($this->parent_id);
    }

    public function getFields() {
        return Application::getInstance()->coreModel->getFields($this->data_source_id);
    }
}