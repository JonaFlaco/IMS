<?php 

/*
 * Home controller
 */
namespace App\Core;

abstract class BgTaskHandlers extends Controller {

    abstract public function run();

    abstract public function afterCompletion();

}

