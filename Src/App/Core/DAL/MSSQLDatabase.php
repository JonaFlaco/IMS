<?php 

/*
 * This is MS SQL class, can be used to connect to 3rd party databases
 */

namespace App\Core\DAL;

use App\Core\Application;
    
use \PDO;

class MSSQLDatabase extends DAL {

    public function __construct($name,$host,$dbname,$user,$pass,$port) {

        if(_strlen($host) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("MSSQL database host name is empty");
        }

        if(_strlen($user) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("MSSQL database user name is empty");
        }

        if(_strlen($pass) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("MSSQL database password name is empty");
        }
        
        if(_strlen($dbname) == 0) {
            throw new \App\Exceptions\MissingDataFromRequesterException("MSSQL database database name is empty");
        }

        //Set DSN
        $dsn = 'sqlsrv:server=' . $host . (_strlen($port) > 0 ? ",$port" : "") . ';database=' . $dbname;
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