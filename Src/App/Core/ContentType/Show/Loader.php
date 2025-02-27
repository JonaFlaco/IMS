<?php

namespace App\Core\ContentType\Show;

use App\Core\Application;
use App\Core\Common\CTypeLoader;

class Loader
{
    // public static function get($ctypeId): string
    // {

    //     $cacheEnabled = (bool)Application::getInstance()->settings->get("sys_cache_tpl", false);

    //     $fileName = RUNTIME_CACHE_TPL . DS . $ctypeId . ".php";

    //     if(!file_exists(dirname($fileName)))
    //         mkdir(dirname($fileName), 0777, true);

    //     if (!$cacheEnabled) {
    //         file_put_contents($fileName, self::generate($ctypeId));
    //         return $fileName;
    //     }

    //     if (!file_exists(RUNTIME_CACHE_TPL)) mkdir(RUNTIME_CACHE_TPL, 0777, true);

    //     $cacheExpireAfter = (bool)Application::getInstance()->settings->get("sys_cache_tpl_expire_after_sec", 0);

    //     if (!file_exists($fileName) || self::getCreationDateDiffHours($fileName) > $cacheExpireAfter)
    //         file_put_contents($fileName, self::generate($ctypeId));


    //     return $fileName;
    // }

    public static function generate($ctypeId): string
    {
        $ctype = CTypeLoader::load($ctypeId)->loadFields();

        return (new Generator())->createTemplate($ctype);
    }

    // private static function getCreationDateDiffHours(string $fileName): int
    // {
    //     return ((time() - filemtime($fileName)) / 60) / 60;
    // }

    // public static function finish($ctypeId, string $fileName): void
    // {
    //     $cacheEnabled = (bool)Application::getInstance()->settings->get("sys_cache_tpl", false);

    //     if (!$cacheEnabled)
    //         unlink($fileName);
    // }
}
