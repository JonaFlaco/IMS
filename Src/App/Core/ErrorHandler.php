<?php

/**
 * This class handles errors and exceptions
 */

namespace App\Core;

use \PDO;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\AuthenticationRequiredException;
use App\Helpers\MiscHelper;

class ErrorHandler {

    public static function handle($exc){
        
        $err_code = $exc->getCode();
        $err_msg = $exc->getMessage();
        $err_file = $exc->getFile();
        $err_line = $exc->getLine();
        $err_stack_trace = $exc->getTrace();
        $err_class = get_class($exc);
        $err_msg_to_show = $err_msg;


        //die($err_msg . ", " . $err_file . ":" . $err_line);exit;
        $extraDetail = method_exists($exc, "getExtraDetail") ? $exc->getExtraDetail() : null;
        $logError = method_exists($exc, "getLogError") ? $exc->getLogError() : true;
        $suppressError = method_exists($exc, "getSuppressError") ? $exc->getSuppressError() : false;
        $hideDetail = method_exists($exc, "getHideDetail") ? $exc->getHideDetail() : true;
        $isSimpleMode = method_exists($exc, "getIsSimpleMode") ? $exc->getIsSimpleMode() : false;
        $errLogLevel = method_exists($exc, "getLogLevel") ? $exc->getLogLevel() : 1;
        $responseCode = method_exists($exc, "getResponseCode") ? $exc->getResponseCode() : 500;
        
        $logError = $errLogLevel <= (isset(Application::getInstance()->settings) ? Application::getInstance()->settings->get("logger_level", 3) : 3);
        
        //Close all bufferes
        while (ob_get_level()){
            ob_get_clean();
        }

        $ref_code = MiscHelper::randomString(10);
        
        if($suppressError != true) {
            Application::getInstance()->response->statusCode($responseCode);
        }

        
        $errors_to_override = [
            '40001' => "Transaction was deadlocked on lock resources with another process and has been chosen as the deadlock victim",
        ];
        
        if(array_key_exists($err_code, $errors_to_override)) {
            $err_msg = $errors_to_override[$err_code];
        }

        $errors = [
            '23000' => "Error: Integrity constraint violation",
            '28000' => "Error: Unable to connect to database, invalid credential",
            '8001' => "Error: Unable to connect to database",
            '40001' => "Error: The system is busy at the moment, please retry",
            '22001' => "Error: String or binary data would be truncated",
            '42S02' => "Error: Database structural issue happened",
            'IMSSP' => "Error: Result has not output or binding is not set correctly",
            'IMSSP' => "Error: Out of range",
            '42000' => "Error: syntax is set incorrectly"
        ];

        $classes_to_hide_detail = [
            'ErrorException',
            'TypeError',
            'PDOException'
        ];

        if(array_key_exists($err_code, $errors)) {
            $err_msg_to_show = $errors[$err_code];
        } else if (Application::getInstance()->isNotInitialized() || (Application::getInstance()->user->isNotAdmin() && in_array($err_class, $classes_to_hide_detail))) {
            $err_msg_to_show = "Something went wrong";
        }


        if(Application::getInstance()->isNotInitialized() || ($hideDetail && Application::getInstance()->user->isNotAdmin())) {
            $err_msg_to_show = "Something went wrong";
        } 

        // if($logError && LOG_ERROR_TO_FILE){
        //     self::writeToFile($ref_code, $err_code,$err_msg, $err_file,$err_line, $err_stack_trace, $err_class, $extraDetail);
        // }

        
        if($logError){
            self::writeToDb($ref_code, $err_code,$err_msg, $err_file,$err_line, $err_stack_trace, $err_class, $extraDetail);
        }

        if($suppressError != true) {
            if(Application::getInstance()->response->getResponseFormat() == Response::$FORMAT_JSON) {
                $result = (object)[
                    "status" => "failed",
                    "message" => $err_msg_to_show,
                    "referenceCode" => $ref_code
                ];

                return_json($result);

            } else if ($err_class == get_class(new AuthenticationRequiredException)) {

                Application::getInstance()->response->returnNeedsLogin();
                
            } else if (Application::getInstance()->response->getResponseFormat() == Response::$FORMAT_SIMPLE || empty(Application::getInstance()->coreModel)) {
                echo sprintf('Error: %s, Reference ID: ERR-%s<br><a href="/">Click here to go back to homepage</a>', $err_msg_to_show, $ref_code);
            } else {

                if(Application::getInstance()->session->get("user_id") == null){ 
                    echo sprintf('Something went wrong!<br>Reference Code: %s<br><a href="/">Click here to go back to homepage</a>', $ref_code);
                } else {
                    
                    if($isSimpleMode){
                        
                        $data = [];
                        $data["title"] = "Error";
                        $data["message"] = $err_msg_to_show;
                        Application::getInstance()->view->renderView('/templates/ErrorTemplateSimple', $data);

                    } else {

                        $data = [];
                        $data["title"] = "Error";
                        $data["ref_code"] = $ref_code;
                        $data["err_code"] = $err_code;
                        $data["err_msg"] = $err_msg_to_show;
                        $data["err_file"] = $err_file;
                        $data["err_line"] = $err_line;
                        $data["err_stack_trace"] = $err_stack_trace;

                        Application::getInstance()->view->renderView('/templates/ErrorTemplateDetail', $data);
                        
                    }
                }

            }
            
            exit;
        
        }

    }

    private static function writeToFile(string $ref_code, string $err_code = null,string $err_msg,string $err_file,string $err_line, array $err_stack_trace = [], string $err_class = null, string $extraDetail = null){

        try {
            $path = ROOT_DIR . DS. "runtime" . DS . "error_log" . DS . date("Y_m_d");

            if(!file_exists($path)){
                mkdir($path, 0777, true);
            }

            $fp = fopen($path . DS . (Application::getInstance()->session->get("user_name") ?? "unknown") . ".txt", 'a');//opens file in append mode  
            
            fwrite($fp, sprintf("Reference Code: %s\n", $ref_code));
            fwrite($fp, sprintf("IP Address: %s\n", Application::getInstance()->request->getClientIPAddress()));
            fwrite($fp, sprintf("Code: %s\n", $err_code));
            fwrite($fp, sprintf("Message: %s\n", $err_msg));
            fwrite($fp, sprintf("Date: %s\n", date("Y-m-d H:i:s")));
            fwrite($fp, sprintf("File: %s\n", $err_file));
            fwrite($fp, sprintf("Line: %s\n", $err_line));
            fwrite($fp, sprintf("Stack trace: %s\n", json_encode($err_stack_trace)));
            fwrite($fp, sprintf("Class: %s\n", $err_class));
            fwrite($fp, sprintf("Extra Detail: %s\n", $extraDetail));
            fwrite($fp, "--------------------------------------------------------------\n\n\n");  
            fclose($fp);
        } catch (\Exception $exc) {
            return;
        }

    }

    private static function writeToDb(string $ref_code, string $err_code = null,string $err_msg,string $err_file,string $err_line, array $err_stack_trace = [], string $err_class = null, string $extraDetail = null){
        
        try {
            $db = new \App\Core\DAL\MainDatabase(false);
            $query = "INSERT INTO error_log (ref_code, code, title, location, line, trace, created_user_id, class, extra_detail) VALUES 
                                            (:ref_code, :code, :title, :location, :line, :trace, :created_user_id, :class, :extra_detail) ";
                
            $db->query($query);
            $db->bind(':ref_code', $ref_code, PDO::PARAM_STR);
            $db->bind(':code', $err_code, PDO::PARAM_STR);
            $db->bind(':title', $err_msg, PDO::PARAM_STR);
            $db->bind(':location', $err_file, PDO::PARAM_STR);
            $db->bind(':line', $err_line, PDO::PARAM_STR);
            $db->bind(':trace', json_encode($err_stack_trace), PDO::PARAM_STR);
            $db->bind(':created_user_id', Application::getInstance()->session->get("user_id"));
            $db->bind(':class', $err_class);
            $db->bind(':extra_detail', $extraDetail);

            $db->execute();
        } catch (\Exception $exc) {
            return;
        }
    }

}