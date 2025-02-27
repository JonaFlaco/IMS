<?php

namespace App\Core\Router;

use App\Controllers\Gctypes;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Modules;
use App\Core\Request;
use App\Exceptions\NotFoundException;
use App\Models\NodeModel;

class CheckBlacklistMiddleware extends RouterMiddleware
{

    private Request $request;
    private NodeModel $nodeModel;
    private string $ipAddress;


    public function __construct(NodeModel $nodeModel, Request $request) {
        $this->request = $request;
        $this->nodeModel = $nodeModel;

        $this->ipAddress = $this->request->getClientIPAddress();
    }

    public function resolve(array $url, array $params) : bool
    {

        if($this->isIpAddressBanned()) {
            return $this->handleBannedIPAddress();
        }

        return parent::resolve($url, $params);
    }

    protected function isIpAddressBanned() : bool
    {

        $result = $this->nodeModel::new("sec_ip_address")
            ->fields(["id"])
            ->where("m.is_blocked = 1")
            ->where("m.ip_address = :ip_address")
            ->bindValue(":ip_address", $this->ipAddress)
            ->loadFirstOrDefault();

        return !is_null($result);
    }


    protected function handleBannedIPAddress(): bool
    {

        Application::getInstance()->pushNotification->add("Blocked IP Address tried to access IMS ($this->ipAddress)", Application::getInstance()->user->getSystemUserId(), null, array('admin'), null, null, "warning", true);

        Application::getInstance()->coreModel->addTrackRequest($this->request->getUrlStr(), json_encode($this->request->getParams()), null, null, true);

        Application::getInstance()->view->renderView("templates/BlockedIP", ["ip_address" => $this->ipAddress]);

        return true;
    }
}