<?php

/**
 * This class uses to get/set global variables
 */

namespace App\Core;

class GlobalVar {

    
    public function get($key, $default = null) {
        return $GLOBALS[_strtolower($key)] ?? $default;
    }

    public function set($key, $value) {
        $GLOBALS[_strtolower($key)] = $value;
    }

    public function arrayPush($key, $value){
        array_push($GLOBALS[_strtolower($key)], $value);
    }

}