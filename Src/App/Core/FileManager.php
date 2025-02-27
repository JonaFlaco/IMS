<?php

/**
 * This class reads .env file
 */

namespace App\Core;

use App\Core\Common\CTypeLoader;
use App\Models\CoreModel;

class FileManager {
    
    public function loadDetail($directory)
    {
        $baseDir = UPLOAD_DIR_FULL . DS . $directory;
        
        $excludeList = Application::getInstance()->globalVar->get('fileCleanupExcludeList');

        $totalFilesInDb = 0;
        $totalFilesInDrive = 0;
        $orphans = 0;

        if(file_exists($baseDir)) {
                
            $ctype = CTypeLoader::load($directory);
            
            if(isset($ctype)){
                $filesInDb = CoreModel::getInstance()->getAttachmentFieldsByCtype($directory);
            } else {
                $filesInDb = [];
            }
            
            foreach(scandir($baseDir) as $item)
            {
                if(in_array($item, $excludeList))
                    continue;

                $existInDb = in_array($item, $filesInDb);
                $isDir = is_dir($baseDir . DS . $item);

                if(!$isDir)
                    $totalFilesInDrive++;

                if(!$existInDb && !$isDir)
                    $orphans++;
            }

            if(file_exists($baseDir . DS . "thumbnails")) {
                
                foreach(scandir($baseDir . DS . "thumbnails") as $item)
                {
                    if(in_array($item, $excludeList))
                        continue;

                    $existInDb = in_array($item, $filesInDb);
                    $isDir = is_dir($baseDir . DS . $item);

                    if(!$isDir)
                        $totalFilesInDrive++;

                    if(!$existInDb && !$isDir)
                        $orphans++;
                }
            }

            $totalFilesInDb = sizeof($filesInDb);
        }

        return (object)[
            "loading" => false,
            "total_files_in_drive" => $totalFilesInDrive,
            "total_files_in_db" => $totalFilesInDb,
            "orphans" => $orphans,
            "orphan_perc" => $totalFilesInDrive == 0 ? 0 : ($orphans / $totalFilesInDrive) * 100
        ];

    }

    public function cleanUp($directory)
    {
        
        $excludeList = Application::getInstance()->globalVar->get('fileCleanupExcludeList');

        $baseDir = UPLOAD_DIR_FULL . DS . $directory;
        $recycleDir = UPLOAD_DIR_FULL . DS . "recycle_bin" . DS . $directory;
        $ctype = CTypeLoader::load($directory);
        
        if(!file_exists($baseDir)) {
            return;
        }

        if(is_file($baseDir)) {

            if(!file_exists(UPLOAD_DIR_FULL . DS . "recycle_bin"))
                mkdir(UPLOAD_DIR_FULL . DS . "recycle_bin", 0777, true);

            rename($baseDir, $recycleDir);
            return;
        }

    
        if(isset($ctype)){
            $filesInDb = CoreModel::getInstance()->getAttachmentFieldsByCtype($directory);
        } else {
            $filesInDb = [];
        }
    
        if(!file_exists($recycleDir))
            mkdir($recycleDir, 0777, true);

        foreach(scandir($baseDir) as $item)
        {
            if(in_array($item, $filesInDb) || in_array($item, $excludeList) || is_dir($baseDir . DS . $item))
                continue;

            rename($baseDir . DS . $item, $recycleDir . DS . $item);

        }

        if(file_exists($baseDir . DS . "thumbnails")) {

            
            if(!file_exists($recycleDir . DS . "thumbnails"))
                mkdir($recycleDir . DS . "thumbnails", 0777, true);

            foreach(scandir($baseDir . DS . "thumbnails") as $item)
            {
                if(in_array($item, $filesInDb) || in_array($item, $excludeList) || is_dir($baseDir . DS . "thumbnails" . DS . $item))
                    continue;

                rename($baseDir . DS . "thumbnails" . DS . $item, $recycleDir . DS . "thumbnails" . DS . $item);

            }
        }

        return;

    }

}