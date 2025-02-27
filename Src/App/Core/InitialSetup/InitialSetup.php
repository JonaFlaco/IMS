<?php

/**
 * This class uses to
 */

namespace App\Core\InitialSetup;

use App\Core\Application;
use App\Core\DAL\MSSQLDatabase;
use Exception;

class InitialSetup {

    private string $host;
    private string $port;
    private string $user;
    private string $pass;
    private string $dbName;
    
    public function __construct() {

        die("Access Denied");
        $this->host = Application::getInstance()->env->get("DB_HOST");
        if(_strlen($this->host) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database host name is empty");
        }
        $this->port = Application::getInstance()->env->get("DB_PORT");
        $this->user = Application::getInstance()->env->get("DB_USER");
        if(_strlen($this->user) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database user name is empty");
        }
        $this->pass = Application::getInstance()->env->get("DB_PASS");
        if(_strlen($this->pass) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database password name is empty");
        }
        $this->dbName = Application::getInstance()->env->get("DB_NAME");
        if(_strlen($this->dbName) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database database name is empty");
        }
    }


    public function index($params) {
        
        
        if(Application::getInstance()->request->isPost() && isset($params["cmd"])) {
            if(_strtolower($params["cmd"]) == "setupdb") {
                $this->setUpDb();
            } else if(_strtolower($params["cmd"]) == "checkconnection") {
                $this->checkConnection();
            } else if(_strtolower($params["cmd"]) == "checkdbexist") {
                $this->checkDbExist();
            } else if(_strtolower($params["cmd"]) == "getlistofdb") {
                $this->getListOfDatabases();
            } else if(_strtolower($params["cmd"]) == "createdb") {
                $this->createDatabase();
            } else if(_strtolower($params["cmd"]) == "createadminuser") {
                $this->createAdminUser();
            } else if(_strtolower($params["cmd"]) == "saveconfigure") {
                $this->saveConfigure();
            }
            
            exit;
        }

        Application::getInstance()->view->renderView("Setup/index");
        exit;
        if($this->checkIfCanRun() != true) {
            die("Access denied");
        }

        // Default admin user: admin 
        // Default admin password: IMS@Default123
        
        
    }
    

    private function getScript($fileName) {
        
        $filePath = dirname(__FILE__) . DS . "SqlScripts" . DS . $fileName . ".sql";

        if(file_exists($filePath)) {
            return file_get_contents($filePath);
        } else {
            die("File $fileName not found");
        }
    }

    
    private function getDb($dbName = null) {
        if(empty($dbName)) {
            $dbName = $this->dbName;
        }

        return new MSSQLDatabase(null, $this->host, $dbName, $this->user, $this->pass, $this->port);
    }

    private function executeScript($scriptName, $dbName = null) {

        if(empty($dbName)) {
            $dbName = $this->dbName;
        }

        $script = $this->getScript($scriptName);
        $script = _str_replace("__DBNAME__", $this->dbName, $script);

        $db = $this->getDb($dbName);

        $db->query($script);
        $db->execute();
    }


    private function checkIfCanRun() {
        return false;
    }


    private function loadDbConfigFromPost($load_db_name = false) {
        $post = Application::getInstance()->request->POST();

        if(isset($post["db_host"]) && _strlen($post["db_host"]) > 0)
            $this->host = $post["db_host"];
        else
            throw new Exception("Server is missing");

        if(isset($post["db_port"]) && _strlen($post["db_port"]) > 0)
            $this->port = $post["db_port"];
        else
            throw new Exception("Server is missing");
            
        if(isset($post["db_username"]) && _strlen($post["db_username"]) > 0)
            $this->user = $post["db_username"];
        else throw new Exception("Username is missing");

        if(isset($post["db_password"]) && _strlen($post["db_password"]) > 0)
            $this->pass = $post["db_password"];
        else 
            throw new Exception("Password is missing");
        
        if($load_db_name) {
            if(isset($post["db_name"]) && _strlen($post["db_name"]) > 0)
                $this->dbName = $post["db_name"];
            else
                throw new Exception("Database name is missing");
        }

    }
    private function setUpDb() {

        $this->loadDbConfigFromPost();
        
        $filePath = ROOT_DIR . DS . ".env";
        $templateFilePath = ROOT_DIR . DS . ".env.template";

        $env = file_get_contents($templateFilePath);

        $env = _str_replace("__DB_HOST__", $this->host, $env);
        $env = _str_replace("__DB_PORT__", $this->port, $env);
        $env = _str_replace("__DB_USER__", $this->user, $env);
        $env = _str_replace("__DB_PASS__", $this->pass, $env);
        $env = _str_replace("__DB_NAME__", $this->dbName, $env);

        //file_put_contents($filePath, $env);

        $db = $this->getDb("master");

        if($this->checkIfDatabaseExist()) {
            throw new \App\Exceptions\CriticalException("Database is already exist");
        } 


        $this->createDatabase();

    }

    private function checkIfDatabaseExist() {
        $db = $this->getDb("master");

        $query = "IF DB_ID('" . $this->dbName . "') IS NOT NULL
        begin
            select 1 as result
        end else begin
            select 0 as result
        end";

        $db->query($query);
        $res = $db->resultSingle();

        return $res->result;

    }


    private function getListOfDatabases() {
        $db = $this->getDb("master");

        $query = "select name = db_name(s_mf.database_id)
        from sys.master_files s_mf
        where
            s_mf.state = 0 -- ONLINE
            and has_dbaccess(db_name(s_mf.database_id)) = 1
            and db_name(s_mf.database_id) NOT IN ('master', 'tempdb', 'model', 'msdb')
            and db_name(s_mf.database_id) not like 'ReportServer%'
        group by s_mf.database_id
        order by 1;
        ";

        $db->query($query);
        $res = $db->resultSet();

        echo json_encode($res);
        exit;

    }

    private function createAdminUser() {

        $post = Application::getInstance()->request->POST();

        $admin_name = null;
        if(isset($post["admin_name"]) && _strlen($post["admin_name"]) > 0) {
            $admin_name = $post["admin_name"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Admin name is empty");
        }

        $admin_email = null;
        if(isset($post["admin_email"]) && _strlen($post["admin_email"]) > 0) {
            $admin_email = $post["admin_email"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Admin email is empty");
        }

        $admin_full_name = null;
        if(isset($post["admin_full_name"]) && _strlen($post["admin_full_name"]) > 0) {
            $admin_full_name = $post["admin_full_name"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Admin full name is empty");
        }

        $admin_password = null;
        if(isset($post["admin_password"]) && _strlen($post["admin_password"]) > 0) {
            $admin_password = $post["admin_password"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Admin password is empty");
        }

        
        if(_strlen($admin_password) < 8) {
            throw new \App\Exceptions\PasswordOperationException("Password must be at least 8 charecters long");
        }

        $this->loadDbConfigFromPost(true);

        $db = $this->getDb();

        $query = "update users set name = :name, email = :email, full_name = :full_name, password = :password where id = 1";
        
        $db->query($query);

        $db->bind(":name", $admin_name);
        $db->bind(":email", $admin_email);
        $db->bind(":full_name", $admin_full_name);
        $db->bind(":password", password_hash($admin_password, PASSWORD_DEFAULT));
        
        $db->execute();

        return Application::getInstance()->response->returnSuccess();
    }

    private function saveConfigure() {

        $post = Application::getInstance()->request->POST();

        $app_title = null;
        if(isset($post["app_title"]) && _strlen($post["app_title"]) > 0) {
            $app_title = $post["app_title"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Site Title is empty");
        }

        $app_email = null;
        if(isset($post["app_email"]) && _strlen($post["app_email"]) > 0) {
            $app_email = $post["app_email"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Site Email is empty");
        }

        $app_desc = null;
        if(isset($post["app_desc"]) && _strlen($post["app_desc"]) > 0) {
            $app_desc = $post["app_desc"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Site Description name is empty");
        }

        $app_url = null;
        if(isset($post["app_url"]) && _strlen($post["app_url"]) > 0) {
            $app_url = $post["app_url"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Site URL is empty");
        }

        $app_timezone = null;
        if(isset($post["app_timezone"]) && _strlen($post["app_timezone"]) > 0) {
            $app_timezone = $post["app_timezone"];
        } else {
            throw new \App\Exceptions\MissingDataFromRequesterException("Timezone is empty");
        }
        
        $this->saveSetting("app_title", $app_title);
        $this->saveSetting("app_url", $app_url);
        $this->saveSetting("app_description", $app_desc);
        $this->saveSetting("timezone", $app_timezone);
        $this->saveSetting("app_email", $app_email);

        
        return Application::getInstance()->response->returnSuccess();
    }

    private function saveSetting($name, $value) {

        $this->loadDbConfigFromPost(true);

        $db = $this->getDb();

        $query = "update settings set value = :value where name = :name";
        
        $db->query($query);

        $db->bind(":name", $name);
        $db->bind(":value", $value);
        
        $db->execute();

    }
    

    private function createDatabase() {

        $this->loadDbConfigFromPost(true);

        if($this->checkIfDatabaseExist()) {
            throw new Exception("Database already exist");
        }

        $this->executeScript("00_CREATE_DATABASE", "master");
        
        $this->executeScript("01_CREATE_TABLES");
        $this->executeScript("Functions" . DS . "core_Split");
        $this->executeScript("Functions" . DS . "core_FN_GetOicUsers");
        $this->executeScript("03_INSERT_INITIAL_DATA");
        $this->executeScript("04_CRETE_TABLE_RELATIONS_AND_MORE");
        $this->executeScript("StoredProcedure" . DS . "core_create_trigger");
        $this->executeScript("StoredProcedure" . DS . "core_get_user_id_by_gov_unit_role");
        $this->executeScript("StoredProcedure" . DS . "core_is_live_platform");

        return Application::getInstance()->response->returnSuccess();
    }

    
    private function checkConnection() {
        
        $this->loadDbConfigFromPost();

        $db = $this->getDb("master");

        Application::getInstance()->response->returnSuccess();
    }

    private function checkDbExist() {
        
        $this->loadDbConfigFromPost(true);

        if($this->checkIfDatabaseExist()) {
            die("1");
        } else {
            die("0");
        }
        
    }
}
