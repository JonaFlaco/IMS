<?php

namespace Tests\Unit;

use App\Core\Request;
use App\Core\Response;
use App\Core\Router\CheckBlacklistMiddleware;
use App\Core\Router\Router;
use App\Core\Router\RouterMiddleware;
use App\Models\NodeModel;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class RouterTest extends MockeryTestCase
{
    protected $preserveGlobalState = FALSE; // Only use local info
    protected $runTestInSeparateProcess = TRUE; // Run isolated

    private $url;
    private $request;
    private $nodeModel;
    private $checkBlacklistModdleware;

    public function setUp(): void
    {
        $this->request = $this->createMock(Request::class);

        $this->url = ["ctypes", "edit", 1];
        $this->request->method("getUrlAsLowerCase")->willReturn($this->url);

        $this->request->method("getClientIPAddress")->willReturn("127.0.0.1");


        $this->nodeModel = Mockery::mock('overload:' . NodeModel::class);
        $this->nodeModel->shouldReceive("new")->withAnyArgs()->andReturnSelf();
        $this->nodeModel->shouldReceive("fields")->withAnyArgs()->andReturnSelf();
        $this->nodeModel->shouldReceive("where")->withAnyArgs()->andReturnSelf();
        $this->nodeModel->shouldReceive("bindValue")->withAnyArgs()->andReturnSelf();

        $this->checkBlacklistModdleware = Mockery::mock('App\Core\Router\CheckBlacklistMiddleware[handleBannedIPAddress]', [$this->nodeModel, $this->request])
            ->shouldAllowMockingProtectedMethods();

        $this->checkBlacklistModdleware->shouldReceive("handleBannedIPAddress")->withAnyArgs()->andReturnTrue();
    }

    public function test_CheckBlacklistMiddleware_isNotBanned_ReturnFalse(): void
    {

        $this->nodeModel->shouldReceive("loadFirstOrDefault")->withAnyArgs()->andReturnNull();

        $result = $this->checkBlacklistModdleware->resolve($this->url, []);

        $this->assertFalse($result);

    }

    public function test_CheckBlacklistMiddleware_isBanned_ReturnTrue(): void
    {

        $this->nodeModel->shouldReceive("loadFirstOrDefault")->withAnyArgs()->andReturn(["1"]);

        $result = $this->checkBlacklistModdleware->resolve($this->url, []);

        $this->assertTrue($result);

    }

}