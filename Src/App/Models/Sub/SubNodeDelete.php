<?php namespace App\Models\Sub;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\CTypeLog;

class SubNodeDelete {

        private static $coreModel;
        
        public static function main($ctype_id, $id, $ignore_if_not_found = false){

            self::$coreModel = Application::getInstance()->coreModel;

            if(!empty($id) != true){
                throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
            }
    
            if(is_array($id)){
                throw new \App\Exceptions\CriticalException(e($id) . " is in incorrect format");
            }

            $ctype_obj = (new Ctype)->load($ctype_id);
            
            $permission_obj = Application::getInstance()->user->getCtypePermission($ctype_obj->id);

            if($ctype_obj->disable_delete == true || $permission_obj->allow_delete != 1){
                throw new ForbiddenException("You don't have permission to delete");
            }

            $recordData = Application::getInstance()->coreModel->nodeModel($ctype_obj->id)
                ->id($id)
                ->load();
            
            if(isset($recordData) && sizeof($recordData) == 1){
                $recordData = $recordData[0];
            } else if ($recordData == array()){

                if($ignore_if_not_found){
                    return false;
                } else {
                    throw new NotFoundException("Record #$id not found");
                }
                
            }

            Application::getInstance()->user->checkCtypeExtraPermission($ctype_obj, $recordData, "allow_delete");
            
            
            if($permission_obj->allow_delete_only_your_own_records == true && $recordData->created_user_id != \App\Core\Application::getInstance()->user->getId()){
                throw new ForbiddenException();
            }


            //Run app base trigger
            $classToRun = '\App\Triggers\Base\BeforeDelete';
            if(class_exists($classToRun)){
                $classObj = new $classToRun();

                if(method_exists($classObj, "index")){
                    
                    $classObj->ctypeObj = $ctype_obj;

                    $classObj->index($id, $recordData);
                } 
            }

            //Run ext base trigger
            $classToRun = '\Ext\Triggers\Base\BeforeDelete';
            if(class_exists($classToRun)){
                $classObj = new $classToRun();

                if(method_exists($classObj, "index")){
                    
                    $classObj->ctypeObj = $ctype_obj;

                    $classObj->index($id, $recordData);
                } 
            }

            $className = toPascalCase($ctype_obj->id);
                                
            if($ctype_obj->is_system_object) {
                $classToRun = sprintf('\App\Triggers\%s\BeforeDelete', $className);
                if(class_exists($classToRun)){
                    
                    $classObj = new $classToRun();
                
                    if(method_exists($classObj, "index")){

                        $classObj->ctypeObj = $ctype_obj;

                        $trigger_exist = true;

                        $classObj->index($id, $recordData);

                    }
                }
            }

            $classToRun = sprintf('\Ext\Triggers\%s\BeforeDelete', $className);
            if(class_exists($classToRun)){
                
                $classObj = new $classToRun();
            
                if(method_exists($classObj, "index")){

                    $classObj->ctypeObj = $ctype_obj;

                    $trigger_exist = true;

                    $classObj->index($id, $recordData);

                }
            }


            self::$coreModel->db->query("DELETE FROM $ctype_obj->id WHERE id = :id");
                
            self::$coreModel->db->bind(':id', $id);

            $results = self::$coreModel->db->execute();

            (new CTypeLog($ctype_obj->id))
                ->setContentId($id)
                ->setUserId(Application::getInstance()->user->getId())
                ->setTitle("Deleted the record")
                ->setGroupNam("delete")
                ->save();

            //Run app base trigger
            $classToRun = '\App\Triggers\Base\AfterDelete';
            if(class_exists($classToRun)){
                $classObj = new $classToRun();

                if(method_exists($classObj, "index")){
                    
                    $classObj->ctypeObj = $ctype_obj;

                    $classObj->index($id, $recordData);
                } 
            }

            //Run ext base trigger
            $classToRun = '\Ext\Triggers\Base\AfterDelete';
            if(class_exists($classToRun)){
                $classObj = new $classToRun();

                if(method_exists($classObj, "index")){
                    
                    $classObj->ctypeObj = $ctype_obj;

                    $classObj->index($id, $recordData);
                } 
            }

            $className = toPascalCase($ctype_obj->id);
                  
            if($ctype_obj->is_system_object) {
                $classToRun = sprintf('\App\Triggers\%s\AfterDelete', $className);
                if(class_exists($classToRun)){
                    
                    $classObj = new $classToRun();
                
                    if(method_exists($classObj, "index")){

                        $classObj->ctypeObj = $ctype_obj;

                        $trigger_exist = true;

                        $classObj->index($id, $recordData);

                    }
                }
            }

            $classToRun = sprintf('\Ext\Triggers\%s\AfterDelete', $className);
            if(class_exists($classToRun)){
                
                $classObj = new $classToRun();
            
                if(method_exists($classObj, "index")){

                    $classObj->ctypeObj = $ctype_obj;

                    $trigger_exist = true;

                    $classObj->index($id, $recordData);

                }
            }

        }

    }