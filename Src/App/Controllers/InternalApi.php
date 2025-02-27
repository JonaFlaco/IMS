<?php 

/*
 * This controller handles internal api requests
 */

namespace App\Controllers;

use App\Core\Controller;

class InternalApi extends Controller {

    public function __construct(){
        parent::__construct();
        //check if current user is logged in
        $this->app->user->checkAuthentication();
    }

    
}