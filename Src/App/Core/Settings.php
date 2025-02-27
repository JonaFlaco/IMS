<?php

/**
 * This class get/set Settings
 */

namespace App\Core;

class Settings {

    public function set($key, $value) {
        Application::getInstance()->coreModel->saveSetting($key, $value);
    }

    public function get($key, $defaultValue = null){
        return Application::getInstance()->coreModel->getSetting($key, $defaultValue);
    }


}