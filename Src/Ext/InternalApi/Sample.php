<?php 

namespace Ext\InternalApi;

use App\Core\BaseInternalApi;
use App\Exceptions\NotFoundException;

class Sample extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        throw new NotFoundException();
    }
}
