<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Exceptions\ForbiddenException;
use App\Models\CoreModel;
use App\Models\CTypeLog;

class FileManagerLoadDirectories extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();

        if(!$this->app->user->isAdmin())
            throw new ForbiddenException();
    }

    public function index($id, $params = []){
        
        $ctypeList = CoreModel::getInstance()->nodeModel("ctypes")
            ->fields(["id", "name"])
            ->Load();

        $baseDir = UPLOAD_DIR_FULL;

        $list = [];
        
        foreach(scandir($baseDir) as $item)
        {
            if(in_array($item, [".","..","recycle_bin"]))
                continue;

            $list[] = (object)[
                "name" => $item,
                "fullPath" => $baseDir . DS . $item,
                "isDir" => is_dir($baseDir . DS . $item),
                "ctypeFound" => object_exist_in_array_of_objects($ctypeList, "id", $item),
                "stats" => (object)[
                    "loading" => false,
                    "total_files_in_drive" => null,
                    "total_files_in_db" => null,
                    "linked_files" => null
                ]
            ];
        }
         
    
        $result = (object)[
            "status" => "success",
            "result" => $list
        ];

        return_json($result);
    }
}
