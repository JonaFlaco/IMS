<?php

use App\Core\Application;
use App\Core\FileManager;

function run_cleanup_ctype_files($args) {

    $directory = isset($args['arguments'][1]) ? $args['arguments'][1] : null;

    if(empty($directory)) {

        foreach(scandir(UPLOAD_DIR_FULL) as $item)
        {
            if(in_array($item, [".","..","recycle_bin"]))
                continue;

            (new FileManager)->cleanUp($item);

        }
    } else {
        
        (new FileManager)->cleanUp($directory);
    }

    Application::getInstance()->response->returnSuccess();
}
