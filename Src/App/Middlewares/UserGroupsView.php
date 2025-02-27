<?php 

namespace App\Middlewares;

use App\Core\Controller;

class UserGroupsView extends Controller {
    
    public function __construct(){
        parent::__construct();

    }

    public function modifyGetDataResult($data, $postData = null){
        
        $diagramData = array();
        foreach($data["records"] as $item) {
            
            $obj = new \stdClass();
            $obj->id = $item->user_groups_id_main;
            $obj->name = $item->user_groups_name;
            $obj->organizer_name = $item->user_groups_organizer_id;
            $obj->description = $item->user_groups_description;
            $obj->parent = $item->user_groups_parent_group_id_id;
            $obj->color = $item->user_groups_color ?? "#00FFFF";

            $diagramData[] = $obj;
        }

        $data["records"] = $diagramData;
        
        return $data;
    }
}
