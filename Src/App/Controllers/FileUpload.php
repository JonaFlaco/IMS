<?php

/*
 * This controller handles file upload requests
 */


namespace App\Controllers;

use App\Core\Controller;
use \App\Core\Application;
use App\Helpers\MiscHelper;

class Fileupload extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * upload
     *
     * @param  string $folder
     * @param  array $params
     * @return void
     *
     * Upload function
     */
    public function upload($folder, $params)
    {

        //get file type
        $file_type_id = "";
        if (isset($params['file_type']))
            $file_type_id = $params['file_type'];

        //get fiel name
        $field_name = "";
        if (isset($params['field_name']))
            $field_name = $params['field_name'];

        //get file type object
        $file_type_obj = $this->coreModel->getFileTypes($file_type_id);

        $file_types_array = array();

        if (isset($file_type_obj)) {
            $file_types_array = (_explode(",", $file_type_obj->extension));
        }

        $imagesArray = array();

        //loop throw the files
        foreach ($_FILES as $key => $file) {

            for ($i = 0; $i < sizeof($file['name']); $i++) {

                $error = $file['error'][$i];
                if (!empty($error)) {
                    throw new \App\Exceptions\FileOperationFailedException("Error in uploading file: " . $error);
                }

                // get details of the uploaded file
                $file_tmp_path = $file['tmp_name'][$i];
                $file_size = $file['size'][$i];
                $file_type = $file['type'][$i];

                if ($file_type == "image/jpeg" || $file_type == "image/gif" || $file_type == "image/png") {
                    \App\Helpers\UploadHelper::resizeImage($file['tmp_name'][$i], $file['tmp_name'][$i]);
                }

                $file_name = $file['name'][$i];

                $file_type = $file['type'][$i];
                $file_extension = get_ext_from_file_name($file_name);

                //generate a new name
                $new_file_name = sprintf("%s_%s_%s.%s", $field_name, time(), MiscHelper::randomString(5), $file_extension);

                //get allowed_file_extension, if the field does not have its own extensions list then get the allowed list from global variable ALLOW_FILE_TYPES
                $allowed_file_extensions = $file_types_array == array() ? Application::getInstance()->globalVar->get('ALLOW_FILE_TYPES') : $file_types_array;

                //check if the file extension is allowed
                if (in_array($file_extension, $allowed_file_extensions)) {



                    //check if required dir exist, otherwise create
                    if (isset($folder) && _strlen($folder) > 0 && !file_exists(UPLOAD_DIR_FULL . DS . $folder)) {
                        mkdir(UPLOAD_DIR_FULL . DS . $folder, 0777, true);
                    }

                    $dest_path = UPLOAD_DIR_FULL . (isset($folder) && _strlen($folder) > 0 ? DS . "$folder" : "") . DS . $new_file_name;
                    $thum_dest_path = UPLOAD_DIR_FULL . (isset($folder) && _strlen($folder) > 0 ? DS . "$folder" : "") . DS . "thumbnails" . DS . $new_file_name;

                    if (empty($file_tmp_path)) {
                        throw new \App\Exceptions\FileOperationFailedException("Unable to upload file, temp dir is empty");
                    }

                    //move the file form temp to the required dir
                    if (!move_uploaded_file($file_tmp_path, $dest_path)) {
                        throw new \App\Exceptions\FileOperationFailedException("Unable to upload one of the files");
                    } else {

                        if ($file_type == "image/jpeg" || $file_type == "image/gif" || $file_type == "image/png") {

                            $file_size = filesize($dest_path);

                            \App\Helpers\UploadHelper::resizeImage($dest_path, $thum_dest_path, true);
                        }
                    }
                    //If the file extension is not allowed then show error
                } else {

                    throw new \App\Exceptions\InvalidFileExtentionException(e($file_extension) . " extension is not allowed");
                }

                //create an object for the uploaded file
                $item = new \StdClass();
                $item->tmp_name = $file_tmp_path;
                $item->original_name = $file_name;
                $item->size = $file_size;
                $item->type = $file_type;
                $item->extension = $file_extension;
                $item->name = $new_file_name;

                //add the object to array of images
                $imagesArray[] = $item;
            }
        }

        //create result object
        $result = new \StdClass();
        $result->status = "success";
        $result->images = $imagesArray;

        //return result object as json
        return_json($result);
    }
}
