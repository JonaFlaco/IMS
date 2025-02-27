<?php

namespace App\Core;

use App\Core\Gctypes\DbStructureGenerator;
use App\Models\NodeModel;

class ReleaseNewVersion {

    private $app;
    private $base;
    private $release_name;



    public function __construct($release_name, $overwrite) {
        $this->app = Application::getInstance();

        if(empty($release_name)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Release name empty");
        }
        
        $this->release_name = $release_name;
        
        $this->base = ROOT_DIR . DS . "runtime" . DS .  "Releases" . DS . $this->release_name . DS;

        if(file_exists(APP_ROOT_DIR . DS . "Core" . DS . "Releases" . DS . $this->release_name . ".zip")){
            if($overwrite) {
                unlink(APP_ROOT_DIR . DS . "Core" . DS . "Releases" . DS . $this->release_name . ".zip");
            } else {
                throw new \App\Exceptions\IlegalUserActionException("This release is already prepared");
            }
        }

    }


    public function release() {
        
        $this->initializeDir();

        $this->take_dbsnapshot();

        $this->copy_files();

        $this->copy_other_files();

        $this->zipIt();

        $this->cleanup();
    }

    private function initializeDir() {
        
        //parent dir
        if(file_exists($this->base)) {
            del_dir($this->base);    
        }
        mkdir($this->base, 0777, true);

    }

    private function take_dbsnapshot() {
        (new \App\Core\DbSnapshot(false, ROOT_DIR . DS . "runtime" . DS .  "Releases" . DS . $this->release_name . DS . "db-snapshot" . DS))->take();
    }
    

    private function copy_files() {

        $include = [
            "",
            // "public" . DS . "uploaded_files" . DS . "web.config",
            // "public" . DS . "uploaded_files" . DS . ".htaccess",

            // "public" . DS . "uploaded_files" . DS . "users" . DS . "default_profile_pic_anonymous.png",
            // "public" . DS . "uploaded_files" . DS . "users" . DS . "default_profile_pic_female.png",
            // "public" . DS . "uploaded_files" . DS . "users" . DS . "default_profile_pic_male.png",
            // "public" . DS . "uploaded_files" . DS . "users" . DS . "system_user_profile_picture.png",
            // "public" . DS . "uploaded_files" . DS . "users" . DS . "thumbnails" . DS . "default_profile_pic_anonymous.png",
            // "public" . DS . "uploaded_files" . DS . "users" . DS . "thumbnails" . DS . "default_profile_pic_female.png",
            // "public" . DS . "uploaded_files" . DS . "users" . DS . "thumbnails" . DS . "default_profile_pic_male.png",
            // "public" . DS . "uploaded_files" . DS . "users" . DS . "thumbnails" . DS . "system_user_profile_picture.png",
            
            "Src" . DS . "Bootstrap.php",

            "Src" . DS . "Ext" . DS . "web.config",
            "Src" . DS . "Ext" . DS . ".htaccess",

        ];
        
        foreach($include as $x) {
            $this->recusiveCopy(realpath(ROOT_DIR . DS . $x), $this->base . "files" . DS . $x);
        }

    }

    
    private function recusiveCopy($src, $dst) {

        if(_strlen($dst) == 0 || _strlen($src) == 0){
            return;
        }

        $exclude = [
            realpath(ROOT_DIR . DS . "public" . DS . "theme"),
            realpath(ROOT_DIR . DS . "public" . DS . "uploaded_files"),
            realpath(ROOT_DIR . DS . "vendor"),
            realpath(ROOT_DIR . DS . "runtime"),
            realpath(ROOT_DIR . DS . ".git"),
            realpath(ROOT_DIR . DS . ".env"),
            realpath(APP_ROOT_DIR . DS . "Core" . DS . "Releases"),
            realpath(DOC_ROOT_DIR),
            realpath(EXT_ROOT_DIR),

        ];




        if(in_array(realpath($src), $exclude)) {
            return;
        }

        if(is_file($src)){
            
            if(!file_exists(dirname($dst))) {
                mkdir(dirname($dst), 0777, true);
            }
            
            copy($src, $dst);
            return;
        }

        $dir = opendir($src);
        
        if(!file_exists($dst)) {
            mkdir($dst, 0777, true);
        }

        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file) ) {
                    $this->recusiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {

                    if(in_array(realpath($src . '/' . $file), $exclude)) {
                        continue;
                    }
            
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);

    }

    private function copy_other_files() {
        $this->recusiveCopy(APP_ROOT_DIR . DS . "Core" . DS . "Releases" . DS . $this->release_name, ROOT_DIR . DS . "runtime" . DS . "Releases" . DS . $this->release_name);
    }


    private function zipIt() {
        
        $this->zipFile(ROOT_DIR . DS . "runtime" . DS . "Releases" . DS . $this->release_name, APP_ROOT_DIR . DS . "Core" . DS . "Releases" . DS . $this->release_name . ".zip");

    }

    function zipFile($source, $destination, $flag = '') {
    
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = _str_replace('\\', '/', realpath($source));
        if($flag)
        {
            $flag = basename($source) . '/';
            echo $flag;exit;
            //$zip->addEmptyDir(basename($source) . '/');
        }

        if (is_dir($source) === true)
        {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file)
            {
                
                if(basename($file) == '.' || basename($file) == '..'){
                    continue;
                }
                
                $file = _str_replace('\\', '/', realpath($file));

                
                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(_str_replace($source . '/', '', $flag.$file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(_str_replace($source . '/', '', $flag.$file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true)
        {
            $zip->addFromString($flag.basename($source), file_get_contents($source));
        }

        return $zip->close();
    }

    private function cleanup() {
        del_dir(ROOT_DIR . DS . "runtime" . DS . "Releases" . DS . $this->release_name);      
    }

}