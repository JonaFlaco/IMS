<?php 

/**
 * In /documents you can define documents which can be a file which is used by system or it can be a file which the users might download.
 * You can access the file as: /documents/show/13
 * To make it easier you can use this class the way it will work is you provide the document name to this class and 
 * this class will redirect you to the document's page as example: 
 * 
 * /actions/documents/${doc_name} => /actions/documents/sys_ila_files 
 * 
 * it will redirect you to the document's page
 * 
 */

namespace App\Actions;

use App\Core\Controller;
use App\Models\NodeModel;

class AddTokenToAllCtypes extends Controller {
    
    public function __construct(){
        parent::__construct();
    
        //Check if user is logged in
        $this->app->user->checkAuthentication();
    }

    /**
     * index
     *
     * @param  string $name => name of the document
     * @param  array $params => extra parameter (If available)
     * @return void
     */
    public function index(string $name = null) : void {
        
        if($name == "add")
            $this->add();
        else if ($name == "insert") {
            $this->insert();
        } else if($name == "add_constraint") {
            $this->add_constraint();
        } else {
            die("Command not found");
        }

    }

    private function add() {
        $ctypes = (new NodeModel("ctypes"))->load();
        foreach($ctypes as $ctype) {

            $query = "
                declare @parent_id bigint = :parent_id

                IF((select count(*) from ctypes_fields WHERE name = 'token' and parent_id = @parent_id) = 0) 
                BEGIN
                    insert into ctypes_fields (name, title, parent_id, field_type_id, sort, is_hidden, is_system_field, is_read_only) VALUES ('token', 'Token', @parent_id, 1, 0, 1,1,1)
                END

                ";
                
            $this->coreModel->db->query($query);

            $this->coreModel->db->bind(':parent_id', $ctype->id);

            $this->coreModel->db->execute();


            $this->coreModel->GenerateDbSchemaForCtype($ctype->id);
        }
    }

    private function insert() {
        $ctypes = (new NodeModel("ctypes"))->load();
        foreach($ctypes as $ctype) {

            $query = "UPDATE $ctype->id SET sync_id = newid() WHERE sync_id is null";
                
            $this->coreModel->db->query($query);

            $this->coreModel->db->execute();

        }
    }

    private function add_constraint() {
        $ctypes = (new NodeModel("ctypes"))->load();
        foreach($ctypes as $ctype) {

            $query = "
                declare @parent_id bigint = :parent_id

                IF(EXISTS(select * from ctypes_fields WHERE name = 'token' and parent_id = @parent_id)) 
                BEGIN
                    UPDATE ctypes_fields SET is_unique = 1, is_required = 1 WHERE parent_id = @parent_id and name = 'token'
                END

                ";
                
            $this->coreModel->db->query($query);

            $this->coreModel->db->bind(':parent_id', $ctype->id);

            $this->coreModel->db->execute();


            $this->coreModel->GenerateDbSchemaForCtype($ctype->id);
        }
    }
}
