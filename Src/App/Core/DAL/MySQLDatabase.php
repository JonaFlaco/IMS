<?php 

/*
 * This is MYSQL class, can be used to connect to 3rd party databases
 */

namespace App\Core\DAL;

use App\Core\Application;
    
use \PDO;

class MySQLDatabase extends DAL {
    
    
    public function __construct($name,$host,$dbname,$user,$pass,$port) {

        if(_strlen($host) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("MySQL database host name is empty");
        }

        if(_strlen($user) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("MySQL database user name is empty");
        }

        if(_strlen($pass) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("MySQL database password name is empty");
        }
        
        if(_strlen($dbname) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("MySQL database database name is empty");
        }

        //Set DSN
        $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
        $options = array(
            // PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );

        // Create PDO instance
        
        $this->dbo = new PDO($dsn, $user, $pass, $options);
        $this->dbo->exec("SET SESSION group_concat_max_len = 1000000;");
        
    }


}