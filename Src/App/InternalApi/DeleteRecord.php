<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;

class DeleteRecord  extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();

        $this->app->csrfProtection->check();
    }

    public function index($id, $params = []){
        
        $ctype_id = null;
        if(isset($_POST['ctype_id'])){
            $ctype_id = $_POST['ctype_id'];
        }

        if($id == array()){
            $id = null;
        }

        if(!empty($id)){
            $this->coreModel->delete($ctype_id, $id);
        }  else {
            $id_list = null;
            if(isset($_POST['id_list'])){
                $id_list = $_POST['id_list'];
            }

            if(!isset($id_list)){
                throw new \App\Exceptions\MissingDataFromRequesterException("id_list not provided");
            }
            foreach(_explode(",",$id_list) as $itm){
                $this->coreModel->delete($ctype_id, $itm);
            }
        }

        $this->app->response->returnSuccess();

    }
}
