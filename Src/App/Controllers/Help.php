<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Gctypes\Ctype;
use App\Exceptions\ForbiddenException;

class Help extends Controller {

    public function __construct(){
        parent::__construct();

        $this->app->user->checkAuthentication();
    }
    
    /**
     * index
     *
     * @return void
     *
     * Home function
     */
    public function index(){

        $helpPostsCtype = (new Ctype)->load("help_posts");

        $data = [
            'title' => 'Help Center',
            'sett_load_rich_text_editor' => true,
            'helpPostsCtype' => $helpPostsCtype
        ];

        
        $this->app->view->renderView('help/index',$data);
    }
}

