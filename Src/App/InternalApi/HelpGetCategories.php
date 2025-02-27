<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Helpers\DateHelper;
use App\Helpers\MiscHelper;

class HelpGetCategories extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
    }

    public function index($category_id, $params = []){
        
        $keyword = null;
        if(isset($params['keyword'])) {
            $keyword = $params['keyword'];
        }

        $data = $this->coreModel->getHelpCategories($keyword);

        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);
    }
}
