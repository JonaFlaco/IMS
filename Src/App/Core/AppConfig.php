<?php

namespace App\Core;

class AppConfig
{

    public function load()
    {


        $this->defineConstants();
        
        session_start();

        session_write_close();
    }

    private function defineConstants()
    {


        require_once APP_ROOT_DIR . DS . 'Bootstrap.php';

        //require bootstrap inside ext dir (if exist)
        require_once EXT_ROOT_DIR . DS . 'Bootstrap.php';

    }




}
