<?php 

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Gctypes\Ctype;
use App\Core\Response;
use Exception;

class Fileserver extends Controller {

    public function __construct(){
        parent::__construct();
        $this->app->user->checkAuthentication();

    }


    public function get($id, $params){
        $folder = "";
        $file = "";

        if(isset($params['folder']))
            $folder = $params['folder'];
        
        if(isset($params['file']))
            $file = $params['file'];


        if(file_exists(UPLOAD_DIR_FULL . DS . $folder . DS . "thumbnails" . DS . $file)){
            $folder = $folder . "/thumbnails/";
        } 
        
        $path =  UPLOAD_DIR . DS . $folder . DS . $file;

        Application::getInstance()->response->redirect("/$path");

    }

    public function create_thumbnails($ctypeId, $params = null){
        
        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_TOO_LONG);

        if($ctypeId == array()){
            $ctypeId = null;
        }

        if(empty($ctypeName)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Ctype name is required");
        }

        echo "started...<br>";

        $path = UPLOAD_DIR_FULL;
        
        foreach(scandir($path) as $itm){

            if(is_dir(UPLOAD_DIR . DS . $itm)){
                //dir
                if(!empty($ctypeId) && $itm != $ctypeId){
                    continue;
                }

                foreach(scandir(UPLOAD_DIR . DS . $itm) as $sub_itm){

                    if(is_dir(UPLOAD_DIR . DS . $itm . DS . $sub_itm))
                        continue;
                    
                    if (!file_exists(UPLOAD_DIR . DS . $itm . "/thumbnails")) {
                        mkdir(UPLOAD_DIR . DS . $itm . "/thumbnails", 0777, true);
                    }

                    $file_extension = get_ext_from_file_name($sub_itm);
                    
                    if($file_extension == "jpeg" || $file_extension == "jpg" || $file_extension == "gif" || $file_extension == "png"){
                        
                        if(!file_exists(UPLOAD_DIR . DS . $itm . "/thumbnails/" . $sub_itm)){
                            echo "Not found for " . DS . $itm . "/thumbnails/" . $sub_itm;

                            if(\App\Helpers\UploadHelper::resizeImage(UPLOAD_DIR . DS . $itm . DS . $sub_itm, UPLOAD_DIR . DS . $itm . "/thumbnails/" . $sub_itm, true) == true){
                                echo " <color=\"green\">Success</color>";
                            } else {
                                echo " <color=\"red\">Failed</color>";
                            }

                            echo "<br>";
                        }

                    }

                    
                }


            } else {
                //file 
            }

        }

        echo "finished...<br>";
    }

    public function resize($folder_name, $params = null){
        
        if($folder_name == array()){
            $folder_name = null;
        }

        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_TOO_LONG);

        echo "started...<br>";

        $path    = UPLOAD_DIR_FULL;
        
        foreach(scandir($path) as $itm){

            if(is_dir(UPLOAD_DIR . DS . $itm)){
                //dir
                if(!empty($folder_name) && $itm != $folder_name){
                    continue;
                }

                foreach(scandir(UPLOAD_DIR . DS . $itm) as $sub_itm){

                    $fileName = UPLOAD_DIR . DS . $itm . DS . $sub_itm;
                    if(is_dir($fileName))
                        continue;

                    $file_extension = get_ext_from_file_name($sub_itm);
                    
                    if((filesize($fileName) / 1024) > UPLOAD_MAX_IMAGE_SIZE_KB && ($file_extension == "jpeg" || $file_extension == "jpg" || $file_extension == "gif" || $file_extension == "png")){
                        echo "Resizing " . $fileName . "<br>";
                        \App\Helpers\UploadHelper::resizeImage($fileName, $fileName);
                    }
                    
                }

            } else {
                //file 
            }

        }

        echo "finished...<br>";
    }

    
	public function resize2($ctypeId, $params = null){
        
        if($ctypeId == array()){
            $ctypeId = null;
        }

        $field_name = null;
        if(isset($params["field_name"])) {
            $field_name = $params["field_name"];
        }

        if(empty($ctypeId)) {
            throw new Exception("Content-Type name is missing");
        }

        if(empty($field_name)) {
            throw new Exception("Field name is missing");
        }

        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_TOO_LONG);

        $select = $this->coreModel->newSelect()
            ->cols([
            "id",
            "{$field_name}_name as name", 
            "{$field_name}_size as size" //KB
            ])
            ->from($ctypeId)
            ->where("{$field_name}_size is not null")
            ->where("{$field_name}_size / 1024 > " . UPLOAD_MAX_IMAGE_SIZE_KB)
			->orderBy(["{$field_name}_size desc"])
            ->limit(10000);
        $results = $this->coreModel->db->querySelect($select);

        foreach($results as $item) {

            $newSize = $item->size;

            $filePath = UPLOAD_DIR_FULL . DS . $ctypeId . DS . $item->name;
            
            $file_extension = get_ext_from_file_name($filePath);
                
            $file_size_on_disk = filesize($filePath) / 1024;
            if(($file_size_on_disk > UPLOAD_MAX_IMAGE_SIZE_KB || ($item->size / 1024) > UPLOAD_MAX_IMAGE_SIZE_KB) && ($file_extension == "jpeg" || $file_extension == "jpg" || $file_extension == "gif" || $file_extension == "png")){
                
                if($file_size_on_disk > UPLOAD_MAX_IMAGE_SIZE_KB) {
                    \App\Helpers\UploadHelper::resizeImage($filePath, $filePath);
                }

                $newSize = filesize($filePath);
                echo "Resized " . $item->name . " from " . round($item->size / 1024,1) . "KB to " . round($newSize / 1024,1) . "KB saved (" . round(($newSize - $item->size) / 1024, 1) . "KB)<br>";

                $update = $this->coreModel->newUpdate()
                    ->table($ctypeId)
                    ->set("{$field_name}_size", $newSize)
                    ->where("id = :id")
                    ->bindValue("id", $item->id);
                $this->coreModel->db->queryUpdate($update);
            }
        }
    }
	
    
    public function file_cleanup($id, $params){
        die("commented for now");

        if($id == array()){
            $id = null;
        }

        if(empty($id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Ctype ID is required, but not provided");
        }

        $ctypeObj = $this->coreModel->nodeModel("ctypes")
            ->id($id)
            ->loadFirstOrFail();

        echo "started $id<br>";

        $path = UPLOAD_DIR_FULL;

        $ignore_files = array("default_profile_pic_anonymous.jpg","default_profile_pic_female.png","default_profile_pic_male.png");
        
        $db = new \App\Core\DAL\MainDatabase;
        $query = "
        declare @ctype_id bigint = :id
        select
            c.id,
            c.name,
            f.name as field_name,
            isnull(f.is_multi,0) as is_multi
        from ctypes_fields f
        left join ctypes c on c.id = f.parent_id
        where
            (f.field_type_id = 'attachment' and c.id = @ctype_id)
        
        UNION ALL

        select
            c.id,
            c.name,
            f2.name as field_name,
            isnull(f2.is_multi,0) as is_multi
        from ctypes_fields f
        left join ctypes_fields f2 on f2.parent_id = f.data_source_id
        left join ctypes c on f2.parent_id = c.id
        left join ctypes cp on f.parent_id = cp.id
        where
            (f.field_type_id = 'field_collection' and f.parent_id = @ctype_id) and f2.field_type_id = 'attachment'
        ";

        $db->query($query);
        $db->bind(':id', $id);
        $fieldsToCheck = $db->resultSet();
        
        $dyn_query = "";

        foreach($fieldsToCheck as $field){

            if(_strlen($dyn_query) > 0)
                $dyn_query .= " UNION ALL ";

            if($field->is_multi != true) {
                $dyn_query .= "
                select
                    " . $field->field_name . "_name as file_name
                from $field->name
                where
                    " . $field->field_name . "_name is not null
                ";
            } else {
                $dyn_query .= "
                select
                    name as file_name
                from " . $field->name . "_" . $field->field_name . "
                where
                    name is not null
                ";
            }
            
        }

        $path = UPLOAD_DIR_FULL . DS . $ctypeObj->id;
        
        if(!file_exists($path)){
            throw new \App\Exceptions\NotFoundException(sprintf("%s does not exist",$path));
        }

        $files_on_drive = array();
        $thumbnails = array();
        $files_in_db = array();

        foreach(scandir($path) as $itm){
            if(!is_dir($path . DS . $itm)){
                $files_on_drive[] = $itm;
            }
        }
        
        foreach(scandir($path . DS . "thumbnails") as $itm){
            if(!is_dir($path . DS . $itm)){
                $thumbnails[] = $itm;
            }
        }

        $db->query($dyn_query);

        $result2 = $db->resultSet();
        foreach($result2 as $res2){
            $files_in_db[] = $res2->file_name;
        }
        
        foreach($files_on_drive as $file_on_drive){

            $found = false;
            foreach($files_in_db as $file_in_db){
                if($file_on_drive == $file_in_db){
                    $found = true;
                    break;
                }
            }
            
            
            if(!$found && !in_array($file_on_drive, $ignore_files)){
                if (!file_exists( UPLOAD_DIR_FULL . DS . "recycle_bin" . DS . $ctypeObj->id)) {
                    mkdir(UPLOAD_DIR_FULL . DS . "recycle_bin" . DS . $ctypeObj->id, 0777, true);
                }
                rename($path . DS . $file_on_drive, UPLOAD_DIR_FULL . DS . "recycle_bin" . DS . $ctypeObj->id . DS .  $file_on_drive);
                echo "Moved " . $ctypeObj->id . DS .  $file_on_drive . "<br>";
            }
        }



        foreach($thumbnails as $thumbnail){
            
            $found = false;
            foreach($files_in_db as $file_in_db){
                if($thumbnail == $file_in_db){
                    $found = true;
                    break;
                }
            }
            
            
            if(!$found){
                if (!file_exists( UPLOAD_DIR_FULL . DS . "recycle_bin" . DS . $ctypeObj->id . DS . "thumbnails")) {
                    mkdir(UPLOAD_DIR_FULL . DS . "recycle_bin" . DS . $ctypeObj->id . DS . "thumbnails", 0777, true);
                }
                
                rename($path . DS . "thumbnails" . DS . $thumbnail, UPLOAD_DIR_FULL . DS . "recycle_bin" . DS . $ctypeObj->id . DS . "thumbnails" . DS .  $thumbnail);
                echo "Moved " . $ctypeObj->id . DS . "thumbnails" . DS .  $thumbnail . "<br>";
            }
        }


        echo "Finished.<br>";
    }

    public function get_missing($ctype_id, $params = null){

        if(empty($ctype_id)){
            throw new \App\Exceptions\MissingDataFromRequesterException("Ctype is not provided");
        }
        $db = new \App\Core\DAL\MainDatabase;
        $ctypeObj = (new Ctype)->load($ctype_id);
        
        $path = UPLOAD_DIR_FULL;

        
        $dyn_query = "";
        $query = "
        select distinct
            f.id as id,
            f.name as name,
            isnull(f.is_multi,0) as is_multi
        from ctypes_fields f
        left join ctypes c on c.id = f.parent_id
        where
            f.field_type_id = 'attachment' and c.name = :name
        order by f.name
        ";
            
        $db->query($query);
        $db->bind("name", $ctypeObj->name);
        $fields = $db->resultSet();
        
        $dyn_query .= "with cte as (";

        $i = 0;
        foreach($fields as $field){

            if($i++ > 0)
                $dyn_query .= " UNION ALL ";

            if($field->is_multi != true) {
                $dyn_query .= "
                select
                    " . $field->name . "_name as file_name
                from $ctypeObj->id
                where
                    " . $field->name . "_name is not null
                ";
            } else {
                $dyn_query .= "
                select
                    name as file_name
                from " . $ctypeObj->id . "_" . $field->name . "
                where
                    name is not null
                ";
            }
            
        }

        $dyn_query .= ") select distinct * from cte";
        $path = UPLOAD_DIR_FULL . DS . $ctypeObj->id;
        
        $files_on_drive = array();

        $files_in_db = array();

        foreach(scandir($path) as $itm){
            if(!is_dir($path . DS . $itm)){
                $files_on_drive[] = $itm;
            }
        }
        
        $db->query($dyn_query);

        echo "<h3>Not found</h3>";
        echo "<ol>";
        $result2 = $db->resultSet();

        $i = 1;
        foreach($result2 as $res2){
            $files_in_db[] = $res2->file_name;

            $found = false;
            foreach($files_on_drive as $file_on_drive){
                if($file_on_drive == $res2->file_name){
                    $found = true;
                    break;
                }
            }

            if($found != true){
                echo sprintf("<li>%s</li>", e($res2->file_name));
            }
        }
        
        echo "</ol>
        ";
    }

    public function rotateImage($image, $direction) {
        $direction = _strtolower($direction);
        $degrees = $direction == 'cw' ? 270 : ($direction == 'ccw' ? 90 : NULL);
        if(!$degrees)
            return $image;
        $width = imagesx($image);
        $height = imagesy($image);
        $side = $width > $height ? $width : $height;
        $imageSquare = imagecreatetruecolor($side, $side);
        imagecopy($imageSquare, $image, 0, 0, 0, 0, $width, $height);
        imagedestroy($image);
        $imageSquare = imagerotate($imageSquare, $degrees, 0, -1);
        $image = imagecreatetruecolor($height, $width);
        $x = $degrees == 90 ? 0 : ($height > $width ? 0 : ($side - $height));
        $y = $degrees == 270 ? 0 : ($height < $width ? 0 : ($side - $width));
        imagecopy($image, $imageSquare, 0, 0, $x, $y, $height, $width);
        imagedestroy($imageSquare);
        return $image;
    }


    private function rotateAction($fileFullName, $direction) {

        $type = \App\Helpers\UploadHelper::getMimeType($fileFullName);
        
        $source = null;

        switch(_strtolower($type)) {
            case "image/jpeg":
                // Load
                $source = imagecreatefromjpeg($fileFullName);
                // Rotate
                $rotate = $this->rotateImage($source, $direction);
                // Output
                imagejpeg($rotate,$fileFullName);
                break;
            case "image/gif":
                // Load
                $source = imagecreatefromgif($fileFullName);
                // Rotate
                $rotate = $this->rotateImage($source, $direction);
                // Output
                imagegif($rotate,$fileFullName);
                break;
            case "image/png":
                // Load
                $source = imagecreatefrompng($fileFullName);
                // Rotate
                $rotate = $this->rotateImage($source, $direction);
                // Output
                imagepng($rotate,$fileFullName);
                break;
            default:
                throw new Exception("Rotate function works only with image files");
            break;
        }

    }

    

    public function rotate($fileName, $params) {
        
        $ctypeId = $this->app->request->getParam("ctype_id");
        $ctypeObj = (new Ctype)->load($ctypeId);
        $direction = ($this->app->request->getParam("direction") == true ? 'ccw' : 'cw');

        $fileFullName = UPLOAD_DIR_FULL . "\\" . $ctypeObj->id . "\\" . $fileName;
        $fileFullNameThumb = UPLOAD_DIR_FULL . "\\" . $ctypeObj->id . "\\thumbnails\\" . $fileName;

        $this->rotateAction($fileFullName, $direction);
        $this->rotateAction($fileFullNameThumb, $direction);

        $this->app->response->returnSuccess();
    }
}

