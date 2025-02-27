<?php

/**
 * Base class for triggers
 */

namespace App\Core\Cache;

class CacheDefault implements ICacheHandler {

    private $data = [];
    
    public function get($key, $default = null) {
        return $this->data[_strtolower($key)] ?? $default;
    }

    public function set($key, $value, $ttl = 3600) {
        $this->data[_strtolower($key)] = $value;
    }

    public function delete($key = null) {
        unset($this->data[$key]);
    }
}
