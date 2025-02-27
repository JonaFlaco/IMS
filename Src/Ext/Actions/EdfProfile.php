<?php

namespace Ext\Actions;

use App\Core\Controller;
use App\Core\Application;

class EdfProfile extends Controller
{

    public function __construct()
    {
        parent::__construct();

        // Check if user is logged in or on local
        $this->app->user->checkAuthentication();
    }

    public function index($id, $params = [])
    {
        
        if(empty($_POST['id']))                     
            throw new \App\Exceptions\MissingDataFromRequesterException("ID is required , but not provided");

        (new \App\Core\BgTask())
            ->setName("Business Profile")
            ->setActionName("EdfProfile")
            ->setPostData(Application::getInstance()->request->POST())
            ->addToQueue();
            Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;
}
}
