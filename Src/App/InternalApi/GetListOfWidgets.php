<?php 

namespace App\InternalApi;

use App\Core\Response;
use App\Core\BaseInternalApi;

class GetListOfWidgets extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($favorites_only = true, $params = []){

        $data = [];

        $widgets = $this->coreModel->get_widgets_by_permission($favorites_only);


        foreach($widgets as $wid) {

            $data[] = (object)[
                "id" => $wid->id,
                "name" => $wid->name,
                "type" => $wid->type,
                "description" => $wid->description,
                "icon" => "/assets/app/images/icons/$wid->type-widget-32.png",
                "size" => intval($wid->size) > 0 ? intval($wid->size) : 12,
                "is_added" => false,
                "tags" => array_map('strtolower',array_map('trim',_explode(",", $wid->tags)))
            ];

        }

        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);
    }

}
