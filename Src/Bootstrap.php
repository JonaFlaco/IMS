<?php

use \App\Core\Application;
use App\Core\Gctypes\CtypeField;

function get_machine_name($value, $keepHyphen = false)
{

    $value = _trim($value);

    $value = _str_replace(" ", "_", $value);
    $regExp = "/[^a-zA-Z0-9_]/";

    if ($keepHyphen) {
        $regExp = "/[^a-zA-Z0-9_-]/";
    } else {
        $value = _str_replace("-", "_", $value);
    }

    $value = preg_replace($regExp, "", $value);


    $value = _strtolower($value);

    return $value;
}

function to_snake_case($value)
{

    $value = _str_replace(" ", "-", $value);
    $value = _str_replace("_", "-", $value);
    $value = _strtolower($value);

    return $value;
}


function del_dir($dir)
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? del_dir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

function escape_query($string, $field_type_id = "text", $include_quotation = false)
{
    $field_type_id = "text";
    $include_quotation = true;
    if ($field_type_id == "text") {
        $string = _str_replace("'", "''", $string);
    }

    if ($field_type_id == "boolean") {
        return $string ? "1" : "0";
    }

    if (_strlen($string) == 0)
        return "null";

    if ($field_type_id == "text" || $field_type_id == "date" && $include_quotation) {
        return "'$string'";
    } else {
        return $string;
    }
}

function jsonDecode(string $value)
{
    $result = json_decode($value);

    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            //success
            break;
        case JSON_ERROR_DEPTH:
            throw new \App\Exceptions\CriticalException('Error decoding json: Maximum stack depth exceeded');
            break;
        case JSON_ERROR_STATE_MISMATCH:
            throw new \App\Exceptions\CriticalException('Error decoding json: Underflow or the modes mismatch');
            break;
        case JSON_ERROR_CTRL_CHAR:
            throw new \App\Exceptions\CriticalException('Error decoding json: Unexpected control character found');
            break;
        case JSON_ERROR_SYNTAX:
            throw new \App\Exceptions\CriticalException('Error decoding json: Syntax error, malformed JSON');
            break;
        case JSON_ERROR_UTF8:
            throw new \App\Exceptions\CriticalException('Error decoding json: Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
        default:
            throw new \App\Exceptions\CriticalException('Error decoding json: Unknown error');
            break;
    }

    return $result;
}

function get_is_mobile()
{
    $useragent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);

    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', _substr($useragent, 0, 4))) {
        return true;
    } else {
        return false;
    }
}

function get_ext_from_file_name($file_name)
{
    $temp = _explode(".", $file_name);
    return _strtolower(end($temp));
}


function object_set_property_to_lowercase($data, $upper_case = false)
{
    $temp = (array)$data;
    if ($upper_case) {
        $data = (object)array_combine(array_map('strtoupper', array_keys($temp)), $temp);
    } else {
        $data = (object)array_combine(array_map('strtolower', array_keys($temp)), $temp);
    }
    return $data;
}

function object_exist_in_array_of_objects(array $array, $field_name, $value, $case_sensitive = false)
{
    foreach ($array as $object) {
        if ($object->{$field_name} == $value) {
            return true;
        }
        if ($case_sensitive != true && _strtolower($object->{$field_name}) == _strtolower($value)) {
            return true;
        }
    }
    return false;
}

function get_object_in_array_of_objects(array $array, $field_name, $value, $case_sensitive = false)
{
    foreach ($array as $object) {
        if ($object->{$field_name} == $value) {
            return $object;
        }
        if ($case_sensitive != true && _strtolower($object->{$field_name}) == _strtolower($value)) {
            return $object;
        }
    }
    return null;
}

function url_encode($string)
{

    $string = rawurlencode($string);
    $string = _str_replace("%3A", ":", $string);
    $string = _str_replace("%2F", "/", $string);
    $string = _str_replace("%5C", "\\", $string);
    $string = _str_replace(" ", "%20", $string);

    return $string;
}

function ecopy($source_path, $dest_path)
{


    if (startsWith($source_path, "http")) {

        $source_path = url_encode($source_path);
    }

    return copy($source_path, $dest_path);
}


function get_button_method($method, $ctype_id, $record_id)
{

    if (isset($record_id)) {
        $method = _str_replace('[CTYPEID]', $ctype_id, $method);
        $method = _str_replace('[ID]', $record_id, $method);

        return "window.open('/$method', '_blank');";
    } else {
        return "alert('Id not found');";
    }
}

function get_file_thumbnail($ctype_id, $field_name, $file_name)
{

    if (empty($file_name) || empty($ctype_id)) {
        return "/assets/app/images/icons/image.png";
    }

    $ext = get_ext_from_file_name($file_name);

    if (ext_is_image($ext)) {
        return "/filedownload?ctype_id=$ctype_id&field_name=$field_name&size=small&file_name=$file_name";
    } else if (ext_is_doc($ext)) {
        return "/assets/app/images/icons/doc.svg";
    } else if (ext_is_excel($ext)) {
        return "/assets/app/images/icons/xls.svg";
    } else if ($ext == 'pdf') {
        return "/assets/app/images/icons/pdf.svg";
    } else {
        return "/assets/app/images/icons/doc.svg";
    }
}

function ext_is_image($ext)
{
    $array = array("jpg", "png", "gif", "jpeg");

    return in_array(_strtolower($ext), $array);
}


function ext_is_doc($ext)
{
    $array = array("txt", "doc", "docx");

    return in_array(_strtolower($ext), $array);
}


function ext_is_excel($ext)
{
    $array = array("xls", "xlsx", "csv");

    return in_array(_strtolower($ext), $array);
}


function startsWith($string, $startString, $is_sensitive = false)
{
    if ($is_sensitive == true) {
        return _strpos($string, $startString) === 0;
    } else {
        return _strpos(_strtolower($string), _strtolower($startString)) === 0;
    }
}

function get_browser_name()
{
    $arr_browsers = ["Opera", "Edge", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];

    $agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);

    $user_browser = '';
    foreach ($arr_browsers as $browser) {
        if (_strpos($agent, $browser) !== false) {
            $user_browser = $browser;
            break;
        }
    }

    switch ($user_browser) {
        case 'MSIE':
            $user_browser = 'Internet Explorer';
            break;

        case 'Trident':
            $user_browser = 'Internet Explorer';
            break;

        case 'Edge':
            $user_browser = 'Microsoft Edge';
            break;
    }

    return $user_browser;
}

function get_os_name()
{

    $user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);

    $os_platform  = null;

    $os_array     = array(
        '/windows nt 10/i'      =>  'Windows 10',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'              =>  'Windows 98',
        '/win95/i'              =>  'Windows 95',
        '/win16/i'              =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'        =>  'Mac OS 9',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir")
                    rrmdir($dir . "/" . $object);
                else unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}


function t($keyword)
{
    return \App\Core\Application::getInstance()->coreModel->getKeyword($keyword);
}

function gv($name)
{
    return Application::getInstance()->globalVar->get($name);
}

function requireToVar($file, array $data = [])
{
    ob_start();
    require($file);
    return ob_get_clean();
}

function requireVarToVar($value, array $data = [])
{
    ob_start();
    eval("?> $value <?php ");
    return ob_get_clean();
}

function get_web_browser_logo($name)
{

    switch (_strtolower($name)) {
        case _strtolower("chrome"):
            $url = "google_chrome";
            break;
        case _strtolower("Internet Explorer"):
            $url = "internet_explorer";
            break;
        case _strtolower("Microsoft Edge"):
            $url = "microsoft_edge";
            break;
        case _strtolower("Firefox"):
            $url = "firefox";
            break;
        case _strtolower("Safari"):
            $url = "safari";
            break;
        default:
            $url = "unknown";
            break;
    }
    $url = "/assets/app/images/web_browsers/$url.png";
    return $url;
}


function get_operating_system_logo($name)
{

    switch (_strtolower($name)) {
        case _strtolower("Windows 10"):
            $url = "windows_10";
            break;
        case _strtolower("Windows 8.1"):
            $url = "windows_8";
            break;
        case _strtolower("Windows 8"):
            $url = "windows_8";
            break;
        case _strtolower("Windows 7"):
            $url = "windows_7";
            break;
        case _strtolower("Windows Vista"):
            $url = "windows_vista";
            break;
        case _strtolower("Windows XP"):
            $url = "windows_xp";
            break;
        case _strtolower("Windows Server 2003/XP x64"):
            $url = "windows_xp";
            break;
        case _strtolower("Mac OS X"):
            $url = "max_osx";
            break;
        case _strtolower("iPhone"):
            $url = "ios";
            break;
        case _strtolower("Linux"):
            $url = "linux";
            break;
        case _strtolower("Ubuntu"):
            $url = "ubuntu";
            break;
        default:
            $url = "unknown";
            break;
    }
    $url = "/assets/app/images/operat ing_systems/$url.png";
    return $url;
}


function save_file_to_browser($type, $file_name, $object)
{
    $type = _strtolower($type);

    if ($type == _strtolower("PhpSpreadSheet_Xlsx")) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        flush();
        ob_clean();
        $object->save('php://output');
    } else if ($type == _strtolower("PhpSpreadSheet_CSV")) {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' . $file_name . ".csv");
        header('Cache-Control: max-age=0');
        flush();
        ob_clean();
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $object->save('php://output');
    }
}

function setHeaderRowFormat($sheet, $column, $row)
{
    //Set bold, and font color
    $sheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true)->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

    //Set background color
    $sheet->getStyleByColumnAndRow($column, $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE);

    //Set alignment Vertical Alignment
    $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);

    //Set alignment Horizontal Alignment
    $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setWrapText(true);

    //Set border style
    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()
        ->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()
        ->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()
        ->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
    $sheet->getStyleByColumnAndRow($column, $row)->getBorders()
        ->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));

    return $sheet;
}

function filter_data_before_save($data)
{

    foreach ($data->tables as $itm) {

        $fields_fields = (new CtypeField)->loadByCtypeId($itm->id);

        $dataTobeChecked = array();
        if ($itm->type == "main_table")
            $dataTobeChecked[] = $itm;
        else if ($itm->type == "fieldcollection")
            $dataTobeChecked = $itm->data->data->tables;


        foreach ($dataTobeChecked as $records) {

            foreach ($fields_fields as $field_def) {

                foreach ($records->data as $key => &$value) {

                    if ($field_def->name == $key) {

                        if ($field_def->allow_basic_html_tags == true) {
                            _strip_tags($value, $field_def->allow_basic_html_tags);
                        } else {
                            $value = filter_var($value, FILTER_SANITIZE_STRING);
                        }
                    }
                }
            }
        }
    }

    return $data;
}

function return_json($value)
{

    Application::getInstance()->response->setResponseContentTypeAsJson();

    echo json_encode($value);
    exit;
}


function e($value)
{
    return isset($value) ? htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
}

function x($value)
{
    return isset($value) ? filter_var(_strip_tags($value), FILTER_SANITIZE_ADD_SLASHES) : null;
}

function json_prepare($string)
{

    $string = e($string);
    $string = _str_replace("/", "\/", $string);
    $string = _str_replace("\\", "\\\\", $string);
    $string = _str_replace("\"", "\\\"", $string);
    $string = _str_replace("\n", "\\n", $string);
    $string = _str_replace("'", "\'", $string);


    return $string;
}


function _strlen($value = null)
{
    return isset($value) ? strlen($value) : 0;
}

function _strtolower($value = null)
{
    return isset($value) ? strtolower($value) : null;
}

function _strip_tags($value = null, array|string|null $allowed_tags = null)
{
    return isset($value) ? strip_tags($value, $allowed_tags) : null;
}

function _trim($value = null, $delimiter = null)
{
    if (!isset($value)) {
        return null;
    }
    
    if (isset($delimiter)) {
        return trim($value, $delimiter);
    }
    
    return trim($value);
}

function _rtrim($value = null, $delimiter = null)
{
    if (!isset($value)) {
        return null;
    }
    
    if (isset($delimiter)) {
        return rtrim($value, $delimiter);
    }
    
    return rtrim($value);
}

function _str_replace($search = null, $replace = null, string $subject = null, int &$count = null)
{
    return str_replace($search ?? "", $replace ?? "", $subject ?? "", $count);
}

function _explode(string $separator, string $string = null, string $limit  = PHP_INT_MAX)
{
    return explode($separator, $string ?? "", $limit);
}

function _strpos(string $hystack = null, string $needle = null, int $offset = 0)
{
    return strpos($hystack ?? "", $needle ?? "", $offset);
}

function _substr(string $hystack = null, string $needle = null, int $offset = 0)
{
    return substr($hystack ?? "", $needle ?? "", $offset);
}

function _preg_split(string $separator, string $string = null, string $limit  = PHP_INT_MAX)
{
    return preg_split($separator, $string ?? "", $limit);
}

function toPascalCase($string)
{
    // Check if the string is already in PascalCase
    if (ctype_upper(_substr($string, 0, 1)) && !_strpos($string, '_') && !_strpos($string, '-')) {
        // If it's already in PascalCase, return it as is
        return $string;
    } else {
        // Remove underscores and hyphens, and split the string into an array of words
        $words = _preg_split('/[_-]/', $string);
        // Capitalize the first letter of each word
        $pascalCaseWords = array_map('ucfirst', $words);
        // Join the words back together
        $pascalCaseString = implode('', $pascalCaseWords);
        return $pascalCaseString;
    }
}


define('ROOT_DIR', dirname(__FILE__, 2));
define('PHP_UPLOAD_TMP_FOLDER', ini_get('upload_tmp_dir'));

const APP_ROOT_DIR = ROOT_DIR . DS . "Src" . DS . "App";
const EXT_ROOT_DIR = ROOT_DIR . DS . "Src" . DS . "Ext";
const DOC_ROOT_DIR = ROOT_DIR . DS . "Src" . DS . "Docs";

const RUNTIME = ROOT_DIR . DS . "runtime";
const TEMP_DIR = RUNTIME . DS . "temp";
const RUNTIME_CACHE = RUNTIME . DS . "cache";
const RUNTIME_CACHE_TPL = RUNTIME_CACHE . DS . "tpl";

const PUBLIC_DIR = "public";
const PUBLIC_DIR_FULL = ROOT_DIR . DS . PUBLIC_DIR;
const SRC_ROOT_DIR = ROOT_DIR . DS . "Src";



const EXT_DIR_TEMPLATE = APP_ROOT_DIR . DS . "Resources" . DS . "ExtDirTemplate.zip";
const DOC_TEMPLATE_FOLDER = EXT_ROOT_DIR . DS . "Resources" . DS . "docs";
const APP_EMAIL_TEMPLATE_FOLDER = APP_ROOT_DIR . DS . "Resources" . DS . "EmailTemplates";
const EXT_EMAIL_TEMPLATE_FOLDER = EXT_ROOT_DIR . DS . "Resources" . DS . "EmailTemplates";

const IS_DEBUG = 1;
const LOG_ERROR_TO_FILE = 0;

const TPL_DEFAULT_TAB_NAME = 'General';
const TPL_DEFAULT_GROUP_NAME = 'General';


const UPLOAD_MAX_IMAGE_HEIGHT = 1200;
const IMAGE_THUMBNAIL_HEIGHT = 100;
const UPLOAD_MAX_IMAGE_SIZE_KB = 750;

const DEFAULT_PROFILE_PICTURE_MALE = "default_profile_pic_male.png";
const DEFAULT_PROFILE_PICTURE_FEMALE = "default_profile_pic_female.png";
const DEFAULT_PROFILE_PICTURE_ANONYMOUS = "default_profile_pic_anonymous.png";


const DEFAULT_PROFILE_PICTURE_MALE_FULL = "/assets/app/images/icons/" . DEFAULT_PROFILE_PICTURE_MALE;
const DEFAULT_PROFILE_PICTURE_FEMALE_FULL = "/assets/app/images/icons/" . DEFAULT_PROFILE_PICTURE_FEMALE;
const DEFAULT_PROFILE_PICTURE_ANONYMOUS_FULL = "/assets/app/images/icons/" . DEFAULT_PROFILE_PICTURE_ANONYMOUS;

const MENU_ICON_SIZE = 24;

const AUTHENTICATED_USER_ROLE_ID = "authenticated";

const EXPORT_EXCEL_ID = "excel";
const EXPORT_CSV_ID = "csv";

const WIDGET_COLORS = "'#727cf5','#fa5c7c','#F8C471','#0acf97','#6c757d','#2c8ef8','#A9CCE3','#E5E8E8','#A3E4D7','#D7BDE2','#808B96','#CD6155','#F1C40F','#1E8449','#21618C','#FAD7A0','#7D6608','#0E6251','#283747','#717D7E'";


const CSRF_TOKENS_FOLDER_NAME = "csrf_tokens";
const CSRF_TOKENS_FOLDER_PATH = ROOT_DIR . DS . "runtime" . DS . CSRF_TOKENS_FOLDER_NAME;
const CSRF_TOKEN_INVALID_ERROR_MESSAGE = "CSRF Token is invalid, please retry";

const BASIC_HTML_TAGS = "<img><p><ul><li><strong><color><b><span>";


const SYSTEM_USER_NAME = 'ims';
const SYSTEM_USER_PWD = '4&P5DDfb5Ua#r59Y';
const SYSTEM_USER_PROFILE_PICTURE = "system_user_profile_picture.png";


const TEXT_DEFAULT_LENGTH = 255;


const CRONS_IGNORE_VALUE = "_";
const CRONS_VALUE_DELIMITER = "[/.-]";
const GVIEW_VALUE_DELIMITER = "[/.-]";

const SYSTEM_UPDATE_OUTPUT_DIR = APP_ROOT_DIR . DS . "Core" . DS . "SystemUpdate" . DS . "Output";

const LOGO_FULL_PATH = "[PUBLIC_DIR]" . DS . "assets" . DS . "ext" . DS . "images" . DS . "logo.png";
const LOGO_URL = "/assets/ext/images/logo.png";


require_once APP_ROOT_DIR . DS . 'Helpers' . DS . 'CtypesGenerateDeleteTsqlCode.php';

require_once APP_ROOT_DIR . DS . 'Helpers' . DS . 'MenuHelper.php';

require_once APP_ROOT_DIR . DS . 'Helpers' . DS . 'GimportValidationHelper.php';
