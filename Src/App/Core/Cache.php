<?php

/**
 * Base class for triggers
 */

namespace App\Core;

class Cache {

    private $data = [];
    
    public function get($key, $default = null) {
        return $this->data[_strtolower($key)] ?? $default;
    }

    public function set($key, $value) {
        $this->data[_strtolower($key)] = $value;
    }

    public function arrayPush($key, $value){
        if(!isset($this->data[_strtolower($key)])){
            $this->data[_strtolower($key)] = [];
        }
        array_push($this->data[_strtolower($key)], $value);
    }
    
    public function clear($key = null) {
        if($key == null) {
            $this->data = [];
        } 

        $this->data[$key] = [];
    }
}
