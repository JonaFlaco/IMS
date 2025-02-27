<?php

namespace App\Helpers;

class UploadHelper
{

    public static function getMimeType($filename)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mime;
    }

    public static function resizeImage($source_image, $destination, $is_thumb = false)
    {

        $file_extension = get_ext_from_file_name($source_image);

        if (startsWith($source_image, "http")) {

            $file_name = sprintf(
                "%s\\%s.%s",
                TEMP_DIR,
                time(),
                $file_extension
            );

            ecopy($source_image, $file_name);

            $source_image = $file_name;
            if ($is_thumb != true && (filesize($source_image) / 1024) <= UPLOAD_MAX_IMAGE_SIZE_KB) {
                return true;
            }
        } else {

            if ($is_thumb != true && (filesize($source_image) / 1024) <= UPLOAD_MAX_IMAGE_SIZE_KB) {
                return true;
            }
        }

        $dest_height = $is_thumb == true ? IMAGE_THUMBNAIL_HEIGHT : UPLOAD_MAX_IMAGE_HEIGHT;

        $info = getimagesize($source_image);
        $imgtype = image_type_to_mime_type($info[2]);

        switch ($imgtype) {
            case "image/jpeg":
                $source = imagecreatefromjpeg($source_image);
                break;
            case "image/gif":
                $source = imagecreatefromgif($source_image);
                break;
            case "image/png":
                $source = imagecreatefrompng($source_image);
                break;
            default:
                return true;
        }

        $src_w = imagesx($source);
        $src_h = imagesy($source);


        // if($is_thumb != true && $src_h <= $dest_height)
        //     return true;

        $scale = $dest_height / $src_h;

        $new_w = $src_w * $scale;
        $new_h = $src_h * $scale;

        $x_mid = $new_w / 2;
        $y_mid = $new_h / 2;

        if ($is_thumb) {
            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0777, true);
            }
        }

        // Now actually apply the crop and resize!
        $newpic = imagecreatetruecolor(round($new_w), round($new_h));
        imagecopyresampled($newpic, $source, 0, 0, 0, 0, round($new_w), round($new_h), round($src_w), round($src_h));
        $final = imagecreatetruecolor(round($new_w), round($new_h));
        imagecopyresampled($final, $newpic, 0, 0, round($x_mid - ($new_w / 2)), round($y_mid - ($new_h / 2)), round($new_w), round($new_h), round($new_w), round($new_h));

        if (Imagejpeg($final, $destination, 80)) {
            return true;
        }


        return false;
    }

    public static function uploadFile($content, $ctype_id, $orginal_file_name, $dont_upload = false, $is_file_object = false)
    {

        if ($content == null) {
            return null;
        }

        $file_extension = get_ext_from_file_name($orginal_file_name);
        $image_file_types = array("jpg", "jpeg", "png", "gif");

        $new_file_name = $orginal_file_name;
        if ($dont_upload != true) {
            $new_file_name = sprintf(
                "%s_%s.%s",
                $orginal_file_name,
                time(),
                $file_extension
            );
        }

        if (!file_exists(UPLOAD_DIR_FULL . DS . $ctype_id)) {
            mkdir(UPLOAD_DIR_FULL . DS . $ctype_id, 0777, true);
        }

        $dest_file_path = UPLOAD_DIR_FULL . DS . $ctype_id . DS . $new_file_name;
        $dest_thumb_path = UPLOAD_DIR_FULL . DS . $ctype_id . DS . 'thumbnails' . DS . $new_file_name;

        //If content is path to the actual image or is image binary
        if ($dont_upload != true) {
            if ($is_file_object != true) {
                ecopy($content, $dest_file_path);
            } else {

                file_put_contents($dest_file_path, $content);

                switch (_strtolower($file_extension)) {
                    case 'jpg':
                    case 'jpeg':
                        $image_value = imagecreatefromstring($content);
                        imagejpeg($image_value, $dest_file_path, 80);
                        imagedestroy($image_value);
                        break;
                    case 'png':
                        $image_value = imagecreatefromstring($content);
                        imagepng($image_value, $dest_file_path, 8);
                        imagedestroy($image_value);
                        break;
                    case 'gif':
                        $image_value = imagecreatefromstring($content);
                        imagegif($image_value, $dest_file_path, 80);
                        imagedestroy($image_value);
                        break;
                    default:
                        break;
                }
            }
        }

        if (in_array($file_extension, $image_file_types)) {
            \App\Helpers\UploadHelper::resizeImage($dest_file_path, $dest_file_path);
            \App\Helpers\UploadHelper::resizeImage($dest_file_path, $dest_thumb_path, true);
        }



        $data = new \stdClass();
        $data->name = $new_file_name;
        $data->original_name = $orginal_file_name;
        $data->extension = $file_extension;
        $data->size = filesize($dest_file_path);
        $data->type = self::getMimeType($dest_file_path);

        return $data;
    }


    public static function deleteAllFilesInsideDir($dir, $del_main_dir = false)
    {

        if (!file_exists($dir)) {
            return;
        }

        $files = scandir($dir);

        $disabled_files = array(EXT_ROOT_DIR . DS . '.htaccess', EXT_ROOT_DIR . DS . 'web.config', EXT_ROOT_DIR . DS . 'Bootstrap.php');

        foreach ($files as $key => $value) {

            $obj = new \stdClass();

            if (!is_dir($dir . DS . $value)) {

                unlink($dir . DS . $value);
            } else if (is_dir($dir . DS . $value)) {

                if ($value != "." && $value != "..") {
                    self::deleteAllFilesInsideDir($dir . DS . $value);
                    rmdir($dir . DS . $value);
                }
            }
        }

        if ($del_main_dir) {
            rmdir($dir);
        }
    }
}
