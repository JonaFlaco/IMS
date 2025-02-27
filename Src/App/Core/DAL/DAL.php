<?php 

/*
 * This is main database calss
 */

namespace App\Core\DAL;

use \PDO;
use \App\Core\Application;
use App\Exceptions\CriticalException;

class DAL {
    
    protected $dbo;
    protected $stmt;
    protected $hasTransaction = false;
    public $ignore_sql_begin_trans = false;

    public function beginTransaction(){
        if($this->ignore_sql_begin_trans != true){
            $this->dbo->beginTransaction();
            $this->hasTransaction = true;
        }
    }

    
    public function commit(){
        if($this->ignore_sql_begin_trans != true){
            $this->dbo->commit();
            $this->hasTransaction = false;
        }
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
            
        try {
            $this->stmt->bindValue($param, $value, $type);
        } catch (\Exception $exc) {
            throw (new \App\Exceptions\CriticalException($exc->getMessage()))->addExtraDetail(json_encode(["param" => $param, "value" => $value]));
        }
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

    
    public function resultSet($type = null){

        $this->execute();
        
        if(empty($type)) {
            $this->stmt->setFetchMode(PDO::FETCH_OBJ);
        } else if($type == "array") {
            $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
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
            
            throw new CriticalException(sprintf("Class (%s) not found", $class));
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



    public function queryDelete($delete) {
        $sth = $this->dbo->prepare($delete->__toString());
        $sth->execute($delete->getBindValues());
    }




    //Aura SqlQuery Methods
    private function prepareQuerySelect($select) {
        $sth = $this->dbo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());

        return $sth;
    }

    public function querySelect($select) {

        $sth = $this->prepareQuerySelect($select);
        return $sth->fetchAll(\PDO::FETCH_OBJ);

    }

    public function querySelectSingle($select) {

        $sth = $this->prepareQuerySelect($select);
        $result = $sth->fetch(\PDO::FETCH_OBJ);

        if(is_object($result) != true){
            $result = null;
        }

        return $result;
    }


    public function queryUpdate($update) {

        $sth = $this->dbo->prepare($update->getStatement());
        return $sth->execute($update->getBindValues());

    }
    //End of Aura SqlQuery Methods
    
}