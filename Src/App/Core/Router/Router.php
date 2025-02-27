<?php

/**
 * This class handles all the routing
 */
namespace App\Core\Router;

use App\Controllers\SurveyManagement;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\NotFoundException;
use App\Models\NodeModel;

class Router {

    private Request $request;
    private Response $response;
    private NodeModel $nodeModel;

    private array $url;
    private array $params;

    public function __construct(NodeModel $nodeModel, Request $request, Response $response) {
        
        $this->request = $request;
        $this->response = $response;
        $this->nodeModel = $nodeModel;

        $this->url = $this->request->getUrlAsLowerCase();
        $this->params = $this->request->getParams();

    }


    //This method processes the request
    public function resolve(){

        $middleware = new RouterMiddleware();

        
        $middleware
            //->linkwith(new MaintenanceModeMiddleware())
            ->linkWith(new ExtractedControllerMiddleware())
            ->linkWith(new ControllerMiddleware())
            ->linkWith(new SurveyMiddleware())
            ->linkWith(new PageMiddleware())
            ->linkWith(new ContentTypeMiddleware())
            ->linkWith(new ModuleMiddleware())
            ->linkWith(new CustomUrlMiddleware())
            ->linkWith(new DocumentationMiddleware());
            
        if(!$middleware->resolve($this->url, $this->params)) {
            throw new NotFoundException();
        }

        exit;
    }


}
