<?php

namespace Ext\Actions;

use App\Core\Controller;
use App\Core\Application;

class FavoritaExport extends Controller
{

    public function __construct()
    {
        parent::__construct();

        // Check if user is logged in or on local
        $this->app->user->checkAuthentication();
    }

    public function index($id, $params = [])
    {
        
        (new \App\Core\BgTask())
            ->setName("FavoritaExport")
            ->setActionName("FavoritaExport")
            ->setPostData(Application::getInstance()->request->POST())
            ->addToQueue();
            Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;
}
}
