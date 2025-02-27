<?php

namespace App\Core\Cache;

interface ICacheHandler {

    public function get($key, $default = null);

    public function set($key, $value, $ttl = 600);

    public function delete($key);
    
}
