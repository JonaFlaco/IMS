<?php 

namespace App\Triggers\ctypes;

use App\Core\BaseTrigger;
use App\Core\Gctypes\Ctype;
use App\Models\CTypeLog;
use App\Core\Node;

class AfterSave extends BaseTrigger {
    
    private $db;
    public function __construct(){
        parent::__construct();
        
        $this->db = new \App\Core\DAL\MainDatabase;
    }
    
    public function index($id, $data, $is_update = false){

        $is_field_collection = false;
        if(property_exists($data->tables[0]->data,"is_field_collection"))
            $is_field_collection = $data->tables[0]->data->is_field_collection;

        $ctypeId = $data->tables[0]->data->id;


        if($is_update == false){

            $query = "
                declare @parent_id varchar(50) = :parent_id
                declare @users_ctype_id varchar(50) = :users_ctype_id
                
                IF((select count(*) from ctypes_fields WHERE name = 'id' and parent_id = @parent_id) = 0) 
                BEGIN
                    insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field) VALUES ('id', 'Machine Name', @parent_id, 'number', -1, 1, 1)
                END
                
                IF((select count(*) from ctypes_fields WHERE name = 'sync_id' and parent_id = @parent_id) = 0) 
                BEGIN
                    insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, is_read_only) VALUES ('sync_id', 'Sync Id', @parent_id, 'text', 0, 1, 1, 1)
                END
        
                IF((select count(*) from ctypes_fields WHERE name = 'token' and parent_id = @parent_id) = 0) 
                BEGIN
                    insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, is_read_only) VALUES ('token', 'Token', @parent_id, 'text', 0, 1, 1, 1)
                END
                
                ";
                
                $this->db->query($query);

                $this->db->bind(':parent_id', $id);
                $this->db->bind(':users_ctype_id', (new Ctype)->load("users")->id);

                $this->db->execute();

            if($is_field_collection != true){
                $this->db = new \App\Core\DAL\MainDatabase;
                $query = "
                    declare @parent_id varchar(50) = :parent_id
                    declare @status_ctype_id varchar(50) = 'status_list'
                    declare @users_ctype_id varchar(50) = 'users'

                    IF((select count(*) from ctypes_fields WHERE name = 'created_user_id' and parent_id = @parent_id) = 0) 
                    BEGIN
                        insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, data_source_id, data_source_display_column, data_source_value_column, is_read_only) VALUES ('created_user_id', 'Creado por el Usuario', @parent_id, 'relation', 0, 1, 1, @users_ctype_id, 'full_name', 'id', 1)
                    END
                        
                    IF((select count(*) from ctypes_fields WHERE name = 'updated_user_id' and parent_id = @parent_id) = 0) 
                    BEGIN
                        insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, data_source_id, data_source_display_column, data_source_value_column, is_read_only) VALUES ('updated_user_id', 'Actualizado por el Usuario', @parent_id, 'relation', 0, 1, 1, @users_ctype_id, 'full_name', 'id', 1)
                    END

                    IF((select count(*) from ctypes_fields WHERE name = 'created_date' and parent_id = @parent_id) = 0) 
                    BEGIN
                        insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, is_read_only) VALUES ('created_date', 'Fecha de creacion', @parent_id, 'date', 0, 1, 1, 1)
                    END

                    IF((select count(*) from ctypes_fields WHERE name = 'last_update_date' and parent_id = @parent_id) = 0) 
                    BEGIN
                        insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, is_read_only) VALUES ('last_update_date', 'Fecha Ultima Actualizacion', @parent_id, 'date', 0, 1, 1, 1)
                    END

                    "; 
                
                if($data->tables[0]->data->category_id != "lookup_table") {

                    $query .= "
                    
                    IF((select count(*) from ctypes_fields WHERE name = 'odk_auri' and parent_id = @parent_id) = 0) 
                    BEGIN
                        insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, is_unique) VALUES ('odk_auri', 'ODK Auri', @parent_id, 'text', 0, 1, 1, 1) 
                    END
                    
                    IF((select count(*) from ctypes_fields WHERE name = 'odk_form_version' and parent_id = @parent_id) = 0) 
                    BEGIN
                        insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field) VALUES ('odk_form_version', 'ODK Form Version', @parent_id, 'number', 0, 1, 1) 
                    END
                    
                    IF((select count(*) from ctypes_fields WHERE name = 'status_id' and parent_id = @parent_id) = 0) 
                    BEGIN
                        insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, data_source_id, data_source_display_column, data_source_value_column, default_value) VALUES ('status_id', 'Estado', @parent_id, 'relation', 0, 1, 1, @status_ctype_id, 'name', 'id','1')
                    END

                    IF((select count(*) from ctypes_fields WHERE name = 'code' and parent_id = @parent_id) = 0) 
                    BEGIN
                        insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, data_source_id, data_source_display_column, data_source_value_column, is_read_only) VALUES ('code', 'Code', @parent_id, 'text', 0, 0, 0, @users_ctype_id, null, null, 1)
                        update ctypes set display_field_name = 'code' where id = @parent_id
                    END
                    ";
                }
                    
                $this->db->query($query);

                $this->db->bind(':parent_id', $id);
                
                $this->db->execute();

            } else {

                $parent_ctype_id = $data->tables[0]->data->parent_ctype_id;
                if(empty($parent_ctype_id)) {
                    throw new \App\Exceptions\NotFoundException("Parent Content-Type is not found");
                }

                $query = "
                declare @parent_id varchar(50) = :parent_id
                IF((select count(*) from ctypes_fields WHERE name = 'parent_id' and parent_id = @parent_id) = 0) 
                BEGIN
                    insert into ctypes_fields (name, title, parent_id, field_type_id, data_source_id, data_source_value_column,data_source_display_column, sort, is_hidden, is_system_field, delete_rule) VALUES ('parent_id', 'Parent Id', @parent_id, 'relation', :parent_ctype_id, 'id','id', 0, 1, 1, 'Cascade')
                END
                
                 ";
                    
                $this->db->query($query);

                $this->db->bind(':parent_id', $id);
                $this->db->bind(':parent_ctype_id', $parent_ctype_id);
                

                $this->db->execute();

            } 
        }
        
        
        $this->coreModel->GenerateDbSchemaForCtype($ctypeId);


        // if view is not defined by default, create a default view and assign it to the ctype        
        if($is_update == false && $data->tables[0]->data->category_id == "content_type" && $is_field_collection == false){

            if(empty($data->tables[0]->data->view_id)){
                // create a new view
                $node = new Node("views");
                $node->sett_is_update =  false;
                $node->ctype_id = $ctypeId;
                $node->id = $ctypeId . "_" . "view";
                $node->name = $data->tables[0]->data->name . " " . "View";
                $node->fields = array(
                    (object)array('ctype_id' => $ctypeId, 'field_name' => "code", 'is_link' => 1, 'link_to_ctype_type' => 'main', 'link_to_ctype_mode' => 'read' ,'sort' => 0),
                    (object)array('ctype_id' => $ctypeId, 'field_name' => "status_id", "add_special_effects" => 1, 'sort' => 1),
                    (object)array('ctype_id' => $ctypeId, 'field_name' => "created_user_id", 'sort' => 2), 
                    (object)array('ctype_id' => $ctypeId, 'field_name' => "created_date", 'sort' => 3),
                );
                $node->filters = array(
                    (object)array('ctype_id' => $ctypeId, 'field_name' => 'code', 'operator_id' => 'text_contain', 'add_to_quck_access_panel' => 1, 'sort' => 0),
                    (object)array('ctype_id' => $ctypeId, 'field_name' => 'status_id', 'operator_id' => 'relation_equal', 'add_to_quck_access_panel' => 1, 'sort' => 1),
                    (object)array('ctype_id' => $ctypeId, 'field_name' => 'created_user_id', 'sort' => 2),
                    (object)array('ctype_id' => $ctypeId, 'field_name' => 'created_date', 'sort' => 3),
                );

                $view_id = $node->save();
    
    
                // assign the view to the ctype
                $ctype = $this->coreModel->nodeModel("ctypes")  
                                        ->where("m.id = :id")
                                        ->bindValue(":id", $ctypeId)                     
                                        ->loadFirstOrFail();
    
                $ctype->view_id = $view_id;
                $this->coreModel->node_save($ctype, array("dont_add_log" => true));
            }
        }
        

        //Commented since we no longer save the dynamic files
        // //Regenerate tpl if it is not custom
        // if(!empty($data->tables[0]->data->name) && isset($data->tables[0]->data->use_custom_tpl) && $data->tables[0]->data->use_custom_tpl != true){
            
        //     $dest_ctype_obj = $this->coreModel->nodeModel("ctypes")
        //          ->id($id)
        //          ->loadFirstOrFail();

        //     if($dest_ctype_obj->is_system_object == true){
        //         $file =  APP_ROOT_DIR . "\\views\\CustomTpls\\" . $dest_ctype_obj->name . ".php";
        //     } else {
        //         $file =  EXT_ROOT_DIR . "\\views\\CustomTpls\\" . $dest_ctype_obj->name . ".php"; 
        //     }
            
        //     if(is_file($file)){
        //         $contents = (new \App\Core\Gtpl\TplGenerator($dest_ctype_obj))->generate();
        //         file_put_contents($file, $contents);
        //     }
            
        // }
        
    }
}