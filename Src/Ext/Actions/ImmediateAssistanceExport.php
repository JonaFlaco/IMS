<?php

namespace Ext\Actions;

use App\Core\Controller;
use App\Core\Application;

class ImmediateAssistanceExport extends Controller
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
            ->setName("ImmediateAssistanceExport")
            ->setActionName("ImmediateAssistanceExport")
            ->setPostData(Application::getInstance()->request->POST())
            ->addToQueue();
            Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;
}
}
