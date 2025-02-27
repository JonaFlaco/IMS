<?php 

namespace Ext\InternalApi;

use App\Core\BaseInternalApi;
use \App\core\Application;
class IlaGetServcies extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        if(_strlen($id) == 0)
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");

        $services = \App\Core\Application::getInstance()->coreModel->nodeModel('b_services')  
                                                                    ->where("m.bnf_id = :bnf_id") 
                                                                    ->bindValue(":bnf_id",$id)                   
                                                                    ->load();           

                                                                 
        $result = (object)["status" => "success",
                            "result" => (object)[                                
                                "services" => sizeof($services) > 0 ? $services : null                    
                            ]];            
        return_json($result); 
    }
}
