<?php 

/*
 * This is main database calss
 */

namespace App\Core\DAL;

use \PDO;
use \App\Core\Application;

class MainDatabase extends DAL {
    
    private $logError;
    
    public function __construct($logError = true) {

        $this->logError = $logError;

        $host = Application::getInstance()->env->get("DB_HOST");
        if(_strlen($host) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database host name is empty");
        }
        $port = Application::getInstance()->env->get("DB_PORT");
        $user = Application::getInstance()->env->get("DB_USER");
        if(_strlen($user) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database user name is empty");
        }
        $pass = Application::getInstance()->env->get("DB_PASS");
        if(_strlen($pass) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database password name is empty");
        }
        $dbname = Application::getInstance()->env->get("DB_NAME");
        if(_strlen($dbname) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Main database database name is empty");
        }
        $dbtype = "sqlsrv";

        //Set DSN
        $dsn = $dbtype . ':server=' . $host . (_strlen($port) > 0 ? ",$port" : "") . ';database=' . $dbname . ';TrustServerCertificate=yes';
        $options = array(
            // PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

        );

        // Create PDO instance
        $this->dbo = new PDO($dsn, $user, $pass, $options);
            
    }


}