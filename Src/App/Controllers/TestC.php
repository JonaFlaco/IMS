<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\DbStructureGenerator;
use App\Exceptions\ForbiddenException;
use App\Helpers\MiscHelper;

class TestC extends Controller {

    public function __construct(){
        parent::__construct();

        if(!$this->app->user->isAdmin()) {
            throw new ForbiddenException();
        }
    }

    public function index() {


    }
}
        
