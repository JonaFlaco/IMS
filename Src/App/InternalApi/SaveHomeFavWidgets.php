<?php 

namespace App\InternalApi;

use App\Core\Response;
use App\Core\BaseInternalApi;

class SaveHomeFavWidgets extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($favorite_only = true, $params = []){

        $data = $this->app->request->POST();
        
        $list = json_decode(isset($data['widgets']) ? $data['widgets'] : null);
        
        $this->coreModel->save_home_fav_widgets($list);

        $this->app->response->returnSuccess();
    }

}
