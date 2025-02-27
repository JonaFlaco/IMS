<?php 

/** 
 * This controller handles download requests
 */

 
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;

class Filedownload extends Controller {

    public function __construct(){
        parent::__construct();
        // check if current user is logged in
        // $this->app->user->checkAuthentication();
        
    }

    
    /**
     * index
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * Main function, handles the download request
     */
    public function index($id, $params){
        
        //get the file name
        $fileName = null;
        if(isset($params['file_name'])){
            $fileName = $params['file_name'];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("File name is empty");
        }


        //if has temp name get it
        $file = null;
        $original_name = null;
        $is_temp = false;
        if(isset($params['temp'])){
            $is_temp = $params['temp'];
        }

        //if it is temp get it from temp folder
        if($is_temp == true){
            $file = TEMP_DIR . DS . $fileName;
        //If it is not in temp get it from the Content-Type
        } else {

            $fieldName = null;
            if(isset($params['field_name'])){
                $fieldName = $params['field_name'];
            } else {
                throw new \App\Exceptions\MissingDataFromRequesterException("Field name is empty");
            }

            $ctypeId = null;
            if(isset($params['ctype_id'])){
                $ctypeId = $params['ctype_id'];
            } else if(isset($params['ctype_name'])){
                $ctypeId = $params['ctype_name'];
            } else {
                throw new \App\Exceptions\MissingDataFromRequesterException("Content-Type name is empty");
            }

            //get size
            $size = null;
            if(isset($params['size'])){
                $size = $params['size'];
            }

            
            //get the field object
            $field = (new CtypeField)->loadByCtypeIdAndFieldName($ctypeId, $fieldName);
            
            //get Content-Type
            $ctype_obj = $field->getParentCtype();
            
            //get original file name
            $original_name = $this->coreModel->getFileOrginalName($ctype_obj->id,$field->name, $fileName, $field->is_multi);
            

            //get the file based on its size
            if($size == "small"){
                $file = UPLOAD_DIR_FULL . DS . $ctype_obj->parent_ctype_id . DS . 'thumbnails' . DS . $fileName;
            } else {
                $file = UPLOAD_DIR_FULL . DS . $ctype_obj->parent_ctype_id . DS . $fileName;
            }
        }

        //if the file doesn't exist show error
        if(empty($file) || !file_exists($file)){
            throw new \App\Exceptions\NotFoundException("File does not exist");
        }
        
        //download the found file
        if(empty($original_name)){ 
            $original_name = $fileName;
        }
        
        $info = getimagesize($file);
        
        if($info !== false && ($info['mime'] == "image/jpeg" || $info['mime'] == "image/jpg" || $info['mime'] == "image/png")){
            header('Content-type: ' . $info['mime']);
        } else {
            header('Content-type: application/force-download');
        }
        
        header('Content-Disposition: inline; filename="' . $original_name . '"');
        header('Content-Length: ' . filesize($file));
        @readfile($file);
        
    }


}