<?php

namespace Ext\InternalApi;

use App\Core\Controller;
use App\Core\Application;

class RetrieveUserId extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->app->user->checkAuthentication();
    }

    public function index()
    {
        header('Content-Type: application/json');

        $userId = Application::getInstance()->user->getId();
        if ($userId) {
            $response = json_encode(['userId' => $userId]);
        } else {
            $response = json_encode(['userId' => null]);
        }

        echo $response;  
    }
}
