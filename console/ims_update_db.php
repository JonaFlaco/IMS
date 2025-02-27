<?php

function run_update_db($args) {

    $g_before = microtime(true);

    $is_silent = in_array('s', $args['flags']);
    
    process($is_silent);


    $g_after = microtime(true);
    echo "\e[92m> Update database finished successfuly! (Elapsed time: " . sprintf('%0.2fs', $g_after - $g_before) . ")\e[39m\n";
    exit;

}


function process($is_silent) {

    readenv();
    
    $res = null;
    $valid_input = $is_silent;
    $attempt_no = 0;
    while(!$valid_input) {

        echo "> Are you sure you want update \e[93m" . getenv("DB_NAME") . "\e[39m on \e[93m" . getenv("DB_HOST") . "\e[39m? [Y/N] (default: Y):";
        $res = _trim(fgets(STDIN));

        if(_strlen($res) == 0 || _strtolower($res) == "y" || _strtolower($res) == "n") {
            $valid_input = true;
        } else {

            if($attempt_no > 5) {
                echo "\e[91m> Too many invalid attempt, cancelling the operation\e[39m\n";
                exit;
            }

            echo "\e[91m> Invalid input, please try again\e[39m\n";

            $attempt_no++;
        }
    }


    if(_strlen($res) == 0 || _strtolower($res) == "y") {
    } else {
        echo "\e[91m> Operation aborted by the user\e[39m\n";
        exit;
    }

    echo "> Update database (\e[93m" . getenv("DB_NAME") . "\e[39m) on (\e[93m" . getenv("DB_HOST") . ")\e[39m started\n";

    updatedb();

}

function updatedb() {
    $db_snapshot_path = dirname(dirname(__FILE__)) . DS . "src" . DS . "app" . DS . "Core" . DS . "DbSnapshot";
    
    if(file_exists($db_snapshot_path)) {
        
        
        $list = scandir($db_snapshot_path);
        
        usort($list, fn($a, $b) => strcmp($a, $b));
        
        $db = new Database();

        foreach($list as $script) {

            if($script == "." || $script == "..") continue;

            if(is_file($db_snapshot_path . DS . $script)) {
                
                $query = file_get_contents($db_snapshot_path . DS . $script);
                
                $db->query($query);
                echo "   > Executing " . $script . "... ";
                
                $before = microtime(true);
                $db->execute();
                $after = microtime(true);

                echo "\e[92mSuccess " . sprintf('(%0.2fs) ', $after - $before) . "\e[39m\n";

            } else {

                $list_sub = scandir($db_snapshot_path . DS . $script);
                
                usort($list_sub, fn($a, $b) => strcmp($a, $b));

                foreach($list_sub as $script2) {

                    if($script2 == "." || $script2 == "..") continue;

                    if(is_file($db_snapshot_path . DS . $script . DS . $script2)) {
                        
                        $query = file_get_contents($db_snapshot_path . DS . $script . DS . $script2);

                        $db->query($query);
                        echo "   > Executing " . $script . DS . $script2 . "... ";
                        
                        $before = microtime(true);
                        $db->execute();
                        $after = microtime(true);

                        echo "\e[92mSuccess " . sprintf('(%0.2fs) ', $after - $before) . "\e[39m\n";

                    }
                }

            }
        }

    }
}

function readenv() {
    echo "> Loading .env file... ";
    $file_path = dirname(dirname(__FILE__)) . DS . ".env";

    //check if .env file exist
    if(!file_exists($file_path)){
        throw new \App\Exceptions\FileNotFoundException($file_path . " file is missing");
    }

    (new \DotEnv($file_path))->load();

    echo "\e[92mSuccess\e[39m\n";
}


class DotEnv {
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;


    public function __construct(string $path)
    {
        if(!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }
        $this->path = $path;
    }

    public function load() :void
    {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (_strpos(_trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = _explode('=', $line, 2);
            $name = _strtolower(_trim($name));
            $value = _trim($value);

            $value = substr($value, 1, _strlen($value) - 2);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}


class Database  {
    
    protected $dbo;
    protected $stmt;
    protected $hasTransaction = false;


    public function __construct($logError = true) {

        $this->logError = $logError;

        $host = getenv("DB_HOST");
        if(_strlen($host) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database host name is empty");
        }
        $port = getenv("DB_PORT");
        $user = getenv("DB_USER");
        if(_strlen($user) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database user name is empty");
        }
        $pass = getenv("DB_PASS");
        if(_strlen($pass) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database password name is empty");
        }
        $dbname = getenv("DB_NAME");
        if(_strlen($dbname) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database database name is empty");
        }
        $dbtype = "sqlsrv";

        //Set DSN
        $dsn = $dbtype . ':server=' . $host . (_strlen($port) > 0 ? ",$port" : "") . ';database=' . $dbname;
        $options = array(
            // PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

        );

        // Create PDO instance
        $this->dbo = new PDO($dsn, $user, $pass, $options);
            
    }

    public function beginTransaction(){
        $this->dbo->beginTransaction();
        $this->hasTransaction = true;
    }

    
    public function commit(){
        $this->dbo->commit();
        $this->hasTransaction = false;
    }

    // Prepare statement with query
    public function query($sql){
        $this->stmt = $this->dbo->prepare($sql);
    }

    
    // Bind values
    public function bind($param, $value, $type = null){
        if(is_null($type)){
            switch(true){
                case is_int($value):    
                $type = PDO::PARAM_INT;
                break;
                case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
                case is_null($value):
                case !isset($value):
                case _strlen($value) == 0:
                $type = PDO::PARAM_NULL;
                break;
                default:
                $type = PDO::PARAM_STR;
            }
        }
        
        $this->stmt->bindValue($param, $value, $type);
    }
    
    // Execute the prepared statement
    public function execute(){
    
        try {
            $r = $this->stmt->execute();
        } catch (\PDOException $e){
        
            if($this->hasTransaction)
                $this->dbo->rollBack();
    
            throw $e;
        
        }
        
        return $r;
       
    }

    
    // Get result set as array of objects
    public function resultSet($type = null){

        $this->execute();
        
        if(empty($type)) {
            $this->stmt->setFetchMode(PDO::FETCH_OBJ);
        } else {

            $this->checkIfClassExsist($type);

            $this->stmt->setFetchMode(PDO::FETCH_CLASS, $type);
        }
        
        return $this->stmt->fetchAll();
    }

    private function checkIfClassExsist($class) {
        if(class_exists($class)) {
            return true;
        } else {
            
            throw new Exception(sprintf("Class (%s) not found", $class));
        }
    }

    public function resultSingle($type = null){

        $this->execute();

        if(empty($type)) {
            $this->stmt->setFetchMode(PDO::FETCH_OBJ);
        } else {

            $this->checkIfClassExsist($type);

            $this->stmt->setFetchMode(PDO::FETCH_CLASS, $type);
        }

        $return_value = $this->stmt->fetch();

        if(is_object($return_value) != true){
            $return_value = null;
        }
        return $return_value;
    }

    
    
    // Get row count
    public function rowCount(){
        return $this->stmt->rowCount();
    }

    public function lastInsertId(){
        return $this->dbo->lastInsertId();
    }



}
