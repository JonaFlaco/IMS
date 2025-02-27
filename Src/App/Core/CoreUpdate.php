<?php

namespace App\Core;

use \App\Core\Application;

class CoreUpdate {

    private $workdir = ROOT_DIR . DS . "runtime" . DS . "system_update";
    private $workdir_full_Path;
    private $app;

    private $metadata;
    private $log_file;

    public function __construct() {
        $this->app = Application::getInstance();
    }


    public function initialize($file) {

        try {

            $this->filename = $file['name'];

            $file_temp_path = $file['tmp_name'];
            $filename = $file['name'];
            $file_path = $this->workdir . DS;
            $basename = basename($file_path . $filename, ".zip");
            $this->workdir_full_path = $this->workdir . DS . $basename . DS;
            $this->log_file = $this->workdir . DS . "log_update_" . $basename . ".txt";

            //check if system_update dir exist otherwise create it
            if(!file_exists($this->workdir)) {
                mkdir($this->workdir, 0777, true);
            }


            $this->generateLogHeader();

            //copy the uploaded file into system update dir
            copy($file_temp_path, $file_path . $filename );

            if(file_exists($file_path . $basename)) {
                del_dir($file_path . $basename);
            }

            $this->extractPackage($file_path, $filename, $basename);

            $this->readMetaData();

            if(!file_exists($this->workdir_full_path . "changelog.md")) {
                throw new \App\Exceptions\NotFoundException("changelog.md not found");
            }

            $changelog = $this->readChangelog();

            $this->checkVersionCompatability();
            
        } catch(\Exception $exc) {
            $this->addLog("x Error: " . $exc->getMessage());
            throw $exc;
        }

        $data = array(
            "title" => "Update System (" . $this->metadata->name . ")",
            "filename" => $filename,
            "changelog" => $changelog
        );

        $this->app->view->renderView("Admin/CoreUpdate/Wizard", $data);
    }

    private function checkVersionCompatability() {
        
        $metaData = $this->readMetaData();

        $current_version = Application::getInstance()->globalVar->get("version");
        // if($metaData->version <= Application::getInstance()->globalVar->get("version")) {
        //     $this->app->session->flash("flash_danger", "Unable to downgrade from " . $current_version . " to " . $metaData->version);
        //     $this->app->view->renderView("admin/coreupdate/index");
        // }
    }

    private function generateLogHeader() {
        $is_new = true;

        if(file_exists($this->log_file)) {
            $is_new = _strlen(file_get_contents($this->log_file)) == 0;
        }

        if(!$is_new) {
            $this->addLog("\n\n", true);
        }
        
        $this->addLog("----------------------------------------------------------------\n", true);
        $this->addLog("> Update Started");
        $this->addLog("----------------------------------------------------------------\n", true);
        
    }

    private function addLog($content, $empty = false) {

        $now = \DateTime::createFromFormat('U.u', microtime(true));
        
        if($empty) 
            file_put_contents($this->log_file, $content, FILE_APPEND);
        else 
            file_put_contents($this->log_file, $now->format("m-d-Y H:i:s.u") . "\t\t\t\t$content\n", FILE_APPEND);
    }

    private function readChangelog() {
        $this->addLog("+ Read changelog");

        $value = file_get_contents($this->workdir_full_path . "changelog.md");

        $value = \App\Core\MarkDown::parse($value);
        $this->addLog("- Read changelog finished");
        
        return $value;
    }

    
    private function extractPackage($file_path, $filename, $basename) {

        $this->addLog("+ Extarct update package");
        //extract the package
        $zip = new \ZipArchive();
        $x = $zip->open($file_path . $filename);

        if ($x === true)
        {
            $zip->extractTo($file_path . DS . $basename);
            $zip->close();
            
            //delete the package
            unlink($file_path . $filename);

        } else {
            throw new \App\Exceptions\CriticalException("Unable to open the update package");
        }

        $this->addLog("- Extarct update package finished");
    }

    private function readMetaData() {
        if(!file_exists($this->workdir_full_path . "metadata.json")) {
            throw new \App\Exceptions\NotFoundException("metadata.json not found");
        }

        
        $this->metadata = json_decode(file_get_contents($this->workdir_full_path . "metadata.json"));

        return $this->metadata;
    }


    public function run($POST) {
        
        $filename = $POST['filename'];
        $cmd = $POST['cmd'];

        $basename = basename($this->workdir . DS . $filename, ".zip");
        $this->workdir_full_path = $this->workdir . DS . $basename . DS;
        $this->log_file = $this->workdir . DS . "log_update_" . $basename . ".txt";

        $this->readMetaData();
        
        try {
                
            switch($cmd) {
                case 'update_files':
                    $this->updateFiles();
                    break;
                case 'update_db':
                    $this->updateDb();
                    break;
                case 'update_version_no':
                    $this->update_version_no();
                    break;
                case 'clean_up':
                    $this->clean_up();
                    break;
                default:
                    throw new \App\Exceptions\CriticalException("Undefined command");
                break;
            }
        } catch(\Exception $exc) {
            $this->addLog("x Error: " . $exc->getMessage());
            throw $exc;
        }
        Application::getInstance()->response->returnSuccess();
    }

    private function update_version_no() {
        $path = ROOT_DIR . DS . "release.json";
        $data = json_decode(file_get_contents($path));
        $data->build_no = $this->metadata->build_no;
        $data->date = $this->metadata->date;
        file_put_contents($path, json_encode($data));
    }

    private function regenerate_ctype_db_str() {
        $this->addLog("+ Regenerate ctype db structure started");

        if(file_exists($this->workdir_full_path . "regenerate_ctype_db_str.txt")){
            

            $items = file_get_contents($this->workdir_full_path . "regenerate_ctype_db_str.txt");

            foreach(_explode("\n", $items) as $ctype_name) {
                
                $ctype_name = _trim($ctype_name);

                if(_strlen($ctype_name) == 0) return;

                $this->addLog("\n+ Regenerate ctype db structure ($ctype_name) started");
                Application::getInstance()->coreModel->GenerateDbSchemaForCtype($ctype_name);
                $this->addLog("\n+ Regenerate ctype db structure ($ctype_name) finished");
            }
        }

        $this->addLog("- Regenerate ctype db structure finished");
    }
    
    private function clean_up() {

        $this->addLog("+ Clean up update files (" . $this->workdir_full_Path . ")");
        
        del_dir($this->workdir_full_path);

        $this->addLog("- Cleaning up finished");
    }



    private function updateDb() {

        $this->addLog("+ Date Database started");

        if(file_exists($this->workdir_full_path . "db-migrations")) {
            
            
            $db_migrations = scandir($this->workdir_full_path . "db-migrations");
            
            usort($db_migrations, fn($a, $b) => strcmp($b, $a));

            $db = $this->app->coreModel->db;

            foreach($db_migrations as $script) {
                if(is_file($this->workdir_full_path . "db-migrations" . DS . $script)) {
                    
                    $this->addLog("\t+ Execute $script started");
                    $query = file_get_contents($this->workdir_full_path . "db-migrations" . DS . $script);
                    
                    $db->query($query);
                    $db->execute();
                    $this->addLog("\t- Execute $script finished");
                }
            }
        }

        if(file_exists($this->workdir_full_path . "db-snapshot")) {
            
            
            $list = scandir($this->workdir_full_path . "db-snapshot");
            
            usort($list, fn($a, $b) => strcmp($a, $b));

            $db = $this->app->coreModel->db;

            foreach($list as $script) {
                if(is_file($this->workdir_full_path . "db-snapshot" . DS . $script)) {
                    
                    $this->addLog("\t+ Execute $script started");
                    // $query = file_get_contents($this->workdir_full_path . "db-snapshot" . DS . $script);
                    
                    // $db->query($query);
                    // $db->execute();
                    $this->addLog("\t- Execute $script finished");
                } else {

                    $list_sub = scandir($this->workdir_full_path . "db-snapshot" . DS . $script);
                    
                    usort($list_sub, fn($a, $b) => strcmp($a, $b));

                    foreach($list_sub as $script2) {
                        if(is_file($this->workdir_full_path . "db-snapshot" . DS . $script . DS . $script2)) {
                            
                            $this->addLog("\t+ Execute $script started");
                            // $query = file_get_contents($this->workdir_full_path . "db-snapshot" . DS . $script . DS . $script2);
                            
                            // $db->query($query);
                            // $db->execute();
                            $this->addLog("\t- Execute $script finished");
                        }
                    }


                }
            }
        }
        
        $this->addLog("- Update database finished");
    }

    private function updateFiles() {
        $this->addLog("+ Update files started");

        $this->recurseCopy($this->workdir_full_path . "files", ROOT_DIR);
        $this->deleteFiles();
        
        $this->addLog("- Update files finished");

    }

    private function deleteFiles() {
        
        if(!file_exists($this->workdir_full_path . "files_to_delete.txt")){
            return;
        }

        $items = file_get_contents($this->workdir_full_path . "files_to_delete.txt");

        foreach(_explode("\n", $items) as $file) {
            
            $file = _trim($file);

            if(_strlen($file) == 0 || $file == "." || $file == "..") {
                continue;
            }
            
            $file_path = ROOT_DIR . DS . $file;
            if(file_exists($file_path)) {
                if(is_dir($file_path)) {
                    $this->addLog("\t+ Delete dir " . $file_path . " started");
                    del_dir($file_path);
                    $this->addLog("\t- Delete dir " . $file_path . " finished");
                } else {
                    $this->addLog("\t+ Delete file " . $file_path . " started");
                    unlink($file_path);
                    $this->addLog("\t- Delete file " . $file_path . " finished");
                }
                
            }
        }

        
    }
    

    function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        
        if(!file_exists($dst)) {
            $this->addLog("+ Create dir $dst started");
            mkdir($dst, 0777, true);
            $this->addLog("- Create dir $dst finished");
        }

        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file) ) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    $this->addLog("+ Copy $src/$file to $dst/$file started");
                    copy($src . '/' . $file,$dst . '/' . $file);
                    $this->addLog("- Copy $src/$file to $dst/$file finished");
                }
            }
        }
        closedir($dir);
    }


}