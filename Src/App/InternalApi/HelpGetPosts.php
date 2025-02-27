<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Helpers\DateHelper;
use App\Helpers\MiscHelper;

class HelpGetPosts extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
    }

    public function index($category_id = null, $params = []){
        
        $keyword = null;
        if(isset($params['keyword'])) {
            $keyword = $params['keyword'];
        }

        $queryObj = $this->coreModel->nodeModel("help_posts")
                ->where("isnull(m.is_published,0) = 1");

        $dynWhere = "";
        $dynWhereCount = 0;

        $tagList = _explode(" ", $keyword);

        if(_strlen($keyword) > 0) {
            
            foreach($tagList as $tag) {
                if(_strlen($dynWhere) > 0) {
                    $dynWhere .= " OR ";
                }
                $dynWhere .= " m.tags like :tag_$dynWhereCount ";
            
                $dynWhereCount++;
            }
        }

        if($dynWhereCount > 0) {
            $queryObj = $queryObj->where(" ( $dynWhere )");
            for($i = 0; $i < sizeof($tagList); $i++) {
                $queryObj = $queryObj->bindValue(":tag_$i", "%" . $tagList[$i] . "%");
            }
        }


        if(_strlen($category_id) > 0) {
            $queryObj->where("m.category_id = :category_id");
            $queryObj = $queryObj->bindValue(":category_id", $category_id);
        }

         $data = $queryObj->OrderBy("m.pin desc, m.id desc")
                        ->load();
        
        
        foreach($data as $item) {

            $item->body = \App\Core\MarkDown::parse($item->body);

            $item->created_date_humanify = DateHelper::humanify(strtotime($item->created_date));
            $item->tagList = _explode(" ", $item->tags);
            
            if(isset($item->last_update_date)){
                $item->last_update_date_humanify = DateHelper::humanify(strtotime($item->last_update_date));
            }

            $item->category_color = $this->coreModel->nodeModel("help_categories")->id($item->category_id)->loadFirstOrFail()->color;
        }

        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);
    }
}
