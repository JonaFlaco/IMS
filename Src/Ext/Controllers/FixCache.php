<?php 

namespace Ext\Controllers;

use App\Core\Controller;
use App\Exceptions\NotFoundException;

class FixCache extends Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index(){
        
        echo "
        <script>
            localStorage.removeItem('_HYPER_CONFIG_');
            window.location.href = '" . $this->app->settings->get("APP_URL") . "';
        </script>
        ";
        
    }

}