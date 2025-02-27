<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Core\Common\CTypeLoader;
use App\Core\FileManager;
use App\Core\Gctypes\CtypesHelper;
use App\Core\Response;
use App\Exceptions\ForbiddenException;
use App\Models\CoreModel;
use App\Models\CType;
use App\Models\CTypeLog;

use function PHPUnit\Framework\directoryExists;

class FileManagerLoadDetail extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();

        if(!$this->app->request->isCli() && !$this->app->user->isAdmin())
            throw new ForbiddenException();
            
        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_LONG);
    }

    public function index($directory, $params = []){
        
        
        $result = (object)[
            "status" => "success",
            "result" => (new FileManager)->loadDetail($directory),
        ];

        return_json($result);
    }
}
