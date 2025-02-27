<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;

class Core extends Controller {

    public function __construct(){
        parent::__construct();
        
        if($this->app->user->isAdmin() != true) {
            throw new \App\Exceptions\ForbiddenException();
        }
    }
    


    public function prepare($release_name) {
        
        $overwrite = $this->app->request->getParam("overwrite");
        (new \App\Core\ReleaseNewVersion($release_name, $overwrite))->release();

        $this->app->response->returnSuccess();
    }

    public function update() {

        if($this->app->request->isPost()) {

            if(empty($_FILES)) {
                (new \App\Core\CoreUpdate())->run($this->app->request->POST());
            } else {
                (new \App\Core\CoreUpdate())->initialize($_FILES['file']);
            }

        } else {
            $this->app->view->renderView("admin/coreupdate/index");
        }
 

    }
    

    public function releases($release_name) {

        $base = APP_ROOT_DIR . DS . "Core" . DS . DS .  "Releases";

        if(empty($release_name)) {
            
            $items = array();

            $i = 0;
            $dirs = scandir($base);

            usort($dirs, fn($a, $b) => strcmp($b, $a));
            
            foreach($dirs as $dir) {
                if($dir == "." || $dir == ".." || is_file($base . DS . $dir)) {
                    continue;
                }

                $metaData = json_decode(file_get_contents($base . DS . $dir . DS . "metadata.json"));
                
                $changelog = file_get_contents($base . DS . $dir . DS . "changelog.md");
                
                 $changelog = \App\Core\MarkDown::parse($changelog);

                $items[] = (object)[
                    "id" => $i,
                    "version" => $metaData->version,
                    "date" => $metaData->date,
                    "file_name" => $dir,
                    "changelog" => $changelog,
                    "ready" => file_exists($base . DS . $dir . ".zip"),
                    "preparing" => false,
                ];

                $i++;
            }

            $data = array(
                "title" => "Releases",
                "items" => $items
            );

            $this->app->view->renderView("admin/coreupdate/releases", $data);
        }
    }

    public function download($release_name) {

        $base = APP_ROOT_DIR . DS . "Core" . DS . DS .  "Releases";

        $file_full_path = $base . DS . $release_name . ".zip";

        if(!file_exists($file_full_path)) {
            throw new \App\Exceptions\NotFoundException("file $release_name.zip not found");
        }
        
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".filesize($file_full_path));
        header("Content-Disposition: attachment; filename=" . $release_name . ".zip");
        readfile($file_full_path);

        echo "<script>window.close();</script>";
        exit;

    }
       
    function zipFile($source, $destination, $flag = '')
    {
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
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString($flag.basename($source), file_get_contents($source));
        }

        return $zip->close();
    }

}

