<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class CrudComponent {
    
    private $ctypeObj;
    private $permissions;

    public function __construct($ctypeObj, $permissions) {

        $this->ctypeObj = $ctypeObj;
        $this->permissions = $permissions;

    }

    public function generateMethods(){

        $result = "";
        $result .= $this->addRecordMethod();
        $result .= $this->showRecordMethod();
        $result .= $this->editRecordMethod();
        $result .= $this->deleteRecordMethod();
        
        return $result;
    }
    
    private function addRecordMethod(){

        if($this->ctypeObj->disable_add != true && $this->permissions->allow_add == 1){
            return null;
        }

        return sprintf('addRecord(){ window.location.href = "/%s/add"; },', $this->ctypeObj->id);
        
    }


    private function showRecordMethod(){
        
        return sprintf('showRecord(id){ window.open("/%s/show/" + id,"_blank"); },',$this->ctypeObj->id);
        
    }

    private function editRecordMethod(){
        
        return sprintf('editRecord(id){ window.open("/%s/edit/" + id,"_blank"); },',$this->ctypeObj->id);
        
    }

    private function deleteRecordMethod(){
        
        return sprintf('deleteRecord(id){ window.open("/%s/delete/" + id,"_blank"); },',$this->ctypeObj->id);
        
    }

}