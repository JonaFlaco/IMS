<?php 

/**
 * This class allows you to reset the system (Content-Types, Views, Crons, Settings, etc..).
 */

namespace App\Actions;

use App\Core\Controller;
use App\Exceptions\ForbiddenException;

class Reset extends Controller {
    
    public function __construct(){
        parent::__construct();
    
        if($this->app->user->isAdmin() !== true){
            throw new \App\Exceptions\ForbiddenException();
        }
        
        //Reset works only in Maintenance mode or Demo Mode, it will not work on LIVE servers.
        if($this->app->settings->get('MAINTENANCE_MODE_IS_ACTIVE') != 1 && $this->app->settings->get('IS_LIVE_PLATFORM') == 1){
            throw new ForbiddenException("This page is accessible in maintenance and demo mode only!");
        }
        
    }

    /**
     * index 
     *
     * @param  string $id
     * @param  array $params
     * @return void
     * 
     * Render the interface
     */
    public function index(){

        $data['title'] = "Reset system";

        $this->app->view->renderView("admin/reset/index", $data);
    }

    private function getDirContents($dir){
        
        $files = scandir($dir);
        
        $results = array();

        $disabled_files = array(EXT_ROOT_DIR . DS . '.htaccess', EXT_ROOT_DIR . DS . 'web.config', EXT_ROOT_DIR . DS . 'Bootstrap.php');

        foreach($files as $key => $value){
            
            $obj = new \stdClass();    

            // echo $key . " " . $value . "";

            if(!is_dir($dir. DS . $value)){
                
                $file_extension = get_ext_from_file_name($value);
                
                $obj->file = $this->getExtension($file_extension);
                $obj->name = $value;
                $obj->path = $dir. DS . $value;
                
                if(in_array($obj->path, $disabled_files)){
                    $obj->disabled = true;
                }

                $results[] = $obj;

            } else if(is_dir($dir . DS . $value)) {
                
                if($value != "." && $value != ".."){
                
                    $obj->selected = true;
                    $obj->name = $value;
                    $obj->children = $this->getDirContents($dir . DS . $value);
                    $obj->path = $dir. DS . $value;
                    
                    $results[] = $obj;

                }

            }
        }
    
        return $results;
    
    }

    private function getExtension($ext){

        $list = array('html', 'js', 'json', 'md', 'pdf', 'png', 'txt', 'xls');

        if(in_array($ext, $list)){
            return $ext;
        } else {
            return "default";
        }

    }
    



}
