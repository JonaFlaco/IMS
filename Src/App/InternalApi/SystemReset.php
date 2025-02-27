<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Core\Gctypes\Ctype;
use App\Core\Response;
use App\Models\CTypeLog;

class SystemReset extends BaseInternalApi {
    
    private $options = array();

    public function __construct(){
        parent::__construct();

    }

    public function index($id, $params = []){

        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_LONG);

        $cmd = ""; 
        if(isset($params['cmd'])){
            $cmd = _strtolower($params['cmd']);
        }

        if(_strtolower(substr($cmd,0, 6)) == "reset_") {
            $this->app->csrfProtection->check();
        }

        $ids = "";
        if(isset($_POST['ids'])){
            $ids = $_POST['ids'];
        }

        if(isset($_POST['options'])){
            $this->options = _explode(",", $_POST['options']);
        }

        $select_fields = array("id", "name");

        if($cmd == "get_ctypes"){

            $data = $this->coreModel->nodeModel("ctypes")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            
            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);

        } else if ($cmd == "get_views"){

            $data = $this->coreModel->nodeModel("views")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

                $result = (object)[
                    "status" => "success",
                    "result" => $data
                ];
        
                return_json($result);

        } else if ($cmd == "get_status_list"){

            $data = $this->coreModel->nodeModel("status_list")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);

        } else if ($cmd == "get_status_workflow_templates"){

            $data = $this->coreModel->nodeModel("status_workflow_templates")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_form_types"){

            $data = $this->coreModel->nodeModel("form_types")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_crons"){

            $data = $this->coreModel->nodeModel("crons")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_roles"){

            $data = $this->coreModel->nodeModel("roles")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_units"){

            $data = $this->coreModel->nodeModel("units")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_positions"){

            $data = $this->coreModel->nodeModel("positions")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_users"){

            $data = $this->coreModel->nodeModel("users")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_user_groups"){

            $data = $this->coreModel->nodeModel("user_groups")
                ->fields($select_fields)
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_governorates"){

            $data = $this->coreModel->nodeModel("governorates")
                ->fields($select_fields)
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_menu"){

            $data = $this->coreModel->nodeModel("menu")
                ->fields($select_fields)
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_settings"){

            $data = $this->coreModel->nodeModel("settings")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_dashboards"){

            $data = $this->coreModel->nodeModel("dashboards")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_widgets"){

            $data = $this->coreModel->nodeModel("widgets")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_custom_url"){
            $select_fields = ["old_url","new_url"];
            $data = $this->coreModel->nodeModel("custom_url")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_documents"){

            $data = $this->coreModel->nodeModel("documents")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_surveys"){

            $data = $this->coreModel->nodeModel("surveys")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_modules"){

            $data = $this->coreModel->nodeModel("modules")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_db_connection_strings"){

            $data = $this->coreModel->nodeModel("db_connection_strings")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_documentations"){

            $data = $this->coreModel->nodeModel("documentations")
                ->fields($select_fields)
                ->where("isnull(m.is_system_object,0) = 0")
                ->OrderBy("m.name")
                ->loadFc(false)
                ->load();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_stuck_tables"){
            $data = $this->coreModel->get_stuck_tables();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_misc"){


            $query = "
            SET NOCOUNT ON;
            declare @temp table (id varchar(50), name nvarchar(250), value bigint, is_custom_actions bit)

            insert into @temp (id, name, value, is_custom_actions) values ('password_reset_requests', 'Reset Password Reset Requests', (select count(*) from password_reset_requests), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('notifications', 'Reset Notifications', (select count(*) from notifications), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('error_log', 'Reset Error Log', (select count(*) from error_log), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('emails', 'Reset Email Records', (select count(*) from emails), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('sms', 'Reset SMS Records', (select count(*) from sms), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('bg_tasks', 'Reset Email Records', (select count(*) from bg_tasks), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('users_login_logs', 'Reset User login log', (select count(*) from users_login_logs), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('request_tracker', 'Reset Request Tracker', (select count(*) from request_tracker), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('sec_ip_address', 'Reset IP Address Records', (select count(*) from sec_ip_address), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('reset_non_core_sp_fn_in_db', 'Reset ext stored procedure and functions in db', null, 1)
            insert into @temp (id, name, value, is_custom_actions) values ('crons_logs', 'Reset Cron Log', (select count(*) from crons_logs), 0)
            insert into @temp (id, name, value, is_custom_actions) values ('ctypes_logs', 'Reset Content-Type Log', (select count(*) from ctypes_logs), 0)
            
            select * from @temp --where value > 0 or is_custom_actions = 1
            ";

            $this->coreModel->db->query($query);
            $data = $this->coreModel->db->resultSet();

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_ext_dir"){

            $total_files = 0;
            $data = $this->getDirContents(EXT_ROOT_DIR, $total_files);

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);
        } else if ($cmd == "get_options"){

            $data = array();
            
            $data[] = array("id" => "update_all_user_references_to_system_user", "status" => 0, "name" => "Update all user references to system user", "value" => true);                
            $data[] = array("id" => "insert_initial_log_for_each_record_after_reset_log", "status" => 0, "name" => "Insert initial log for each record after reset log", "value" => true);

            $result = (object)[
                "status" => "success",
                "result" => $data
            ];
    
            return_json($result);

        } else if ($cmd == "reset_ctypes"){
            $this->reset_table("ctypes", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_views"){
            $this->reset_table("views", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_crons"){
            $this->reset_table("crons", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_status_list"){
            $this->reset_table("status_list", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_status_workflow_templates"){
            $this->reset_table("status_workflow_templates", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_form_types"){
            $this->reset_table("form_types", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_roles"){
            $this->reset_table("roles", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_units"){
            $this->reset_table("units", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_governorates"){
            $this->reset_table("governorates", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_menu"){
            
            foreach(_explode(",", $ids) as $id){
            
                $this->coreModel->delete_menu($id);
            }

            $this->coreModel->reset_table_numbering("menu");

            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_settings"){
            $this->reset_table("settings", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_emails"){
            $this->reset_table("emails", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_sms"){
            $this->reset_table("sms", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_dashboards"){
            $this->reset_table("dashboards", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_widgets"){
            $this->reset_table("widgets", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_custom_url"){
            $this->reset_table("custom_url", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_documents"){
            $this->reset_table("documents", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_surveys"){
            $this->reset_table("surveys", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_modules"){
            $this->reset_table("modules", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_db_connection_strings"){
            $this->reset_table("db_connection_strings", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_documentations"){
            $this->reset_table("documentations", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_positions"){
            $this->reset_table("positions", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_users"){
            
            if(in_array('update_all_user_references_to_system_user',$this->options)){
                $this->coreModel->update_user_ref_columns_to_system_user();
            }
            
            $this->reset_table("users", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_user_groups"){
            $this->reset_table("user_groups", $ids);
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_ext_dir"){
            \App\Helpers\UploadHelper::deleteAllFilesInsideDir(EXT_ROOT_DIR, true);
            $this->restoreDefaultExtDir();
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_misc"){
            
            foreach(_explode(",", $ids) as $id){

                if($id == "reset_non_core_sp_fn_in_db"){
                    $this->coreModel->reset_sp_fn_in_db();
                } else {

                    $this->coreModel->reset_table($id);

                    if($id == (new Ctype)->load("ctypes_logs")->id && in_array("insert_initial_log_for_each_record_after_reset_log", $this->options)){
                        
                        $ctypes = $this->coreModel->nodeModel("ctypes")
                            ->loadFc(false)
                            ->load();

                        foreach($ctypes as $ctype){
                            if($ctype->is_field_collection){
                                continue;
                            }

                            $data = $this->coreModel->nodeModel($ctype->name)
                                ->loadFc(false)
                                ->load();

                            foreach($data as $item){
                                
                                (new CTypeLog($ctype->id))
                                    ->setContentId($item->id)
                                    ->setUserId(Application::getInstance()->user->getSystemUserId())
                                    ->setJustification("Log Reset")
                                    ->setTitle("Log Reset")
                                    ->setGroupNam("reset")
                                    ->save();
                            }
                        }
                    }
                    
                }

            }
            $this->app->response->returnSuccess();
        } else if ($cmd == "reset_stuck_tables"){
            
            $tables = _explode(",", $ids);

            $this->coreModel->delete_stuck_tables($tables);
            $this->app->response->returnSuccess();
        } else {
            throw new \App\Exceptions\PasswordOperationException("Command not found");
        }

    }

    private function reset_table($ctype_id, $ids){
        
        foreach(_explode(",", $ids) as $id){
            
            $this->coreModel->delete($ctype_id, $id, true);
        }

        $this->coreModel->reset_table_numbering($ctype_id);

    }

    private function getDirContents($dir, &$counter = 0){
        
        $files = scandir($dir);
        
        $results = array();

        //$disabled_files = array(EXT_ROOT_DIR . DS . '.htaccess', EXT_ROOT_DIR . DS . 'web.config', EXT_ROOT_DIR . DS . 'bootstrap.php');
        $disabled_files = array();

        foreach($files as $key => $value){
            
            $obj = new \stdClass();    

            if(!is_dir($dir. DS . $value)){
                
                $file_nameCmps = _explode(".", $value);
                $file_extension = _strtolower(end($file_nameCmps));
                
                $obj->file = $this->getExtension($file_extension);
                $obj->name = $value;
                $obj->path = $dir. DS . $value;
                
                if(in_array($obj->path, $disabled_files)){
                    $obj->disabled = true;
                }

                $counter++;
                $results[] = $obj;

            } else if(is_dir($dir . DS . $value)) {
                
                if($value != "." && $value != ".."){
                
                    $obj->selected = true;
                    $obj->name = $value;
                    $obj->children = $this->getDirContents($dir . DS . $value, $counter);
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

    private function restoreDefaultExtDir(){

        $zip = new \ZipArchive();
        $x = $zip->open(EXT_DIR_TEMPLATE);
        if ($x === true)
        {
            $zip->extractTo(EXT_ROOT_DIR);
            $zip->close();
            
        } else {
            throw new \App\Exceptions\NotFoundException(EXT_DIR_TEMPLATE . " not found");
        }
        

    }



}
