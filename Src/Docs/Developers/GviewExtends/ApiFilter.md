# apiFilter

Example of how to use ApiFilter

```
<?php 

namespace Ext\ApiFilter;

use App\Core\Controller;

class user_groups_view extends Controller {
    
    public function __construct(){
        parent::__construct();

    }

    public function index($data){
        
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
```

Note:
    - user_groups_view: is view name

