<?php 

namespace App\InternalApi;

use App\Core\Application;
use \App\Core\BaseInternalApi;
use App\Core\Common\CTypeLoader;
use App\Core\Gctypes\CtypesHelper;
use App\Exceptions\ForbiddenException;
use App\Exceptions\MissingDataFromRequesterException;

class getCtypeShowDetail extends BaseInternalApi {

    public function __construct(){
        parent::__construct();
    }

    public function index($recordId, $params = []){
        
        $ctypeId = $params["ctype_id"] ?? null;

        if(empty($ctypeId)) {
            throw new MissingDataFromRequesterException();
        }
        
        $ctype = CTypeLoader::load($ctypeId);

        $recordData = $this->coreModel->nodeModel($ctype->getId())
            ->id($recordId)
            ->loadFirstOrFail();

        if($ctype->getUseGenericStatus()) {
            
            $statusData = $this->coreModel->nodeModel("status_list")
                ->id($recordData->status_id)
                ->loadFirstOrDefault();

            $lang = $this->app->user->getLangId();
            $field_name = "name";
            if(!empty($lang) && $lang != "en") {
                $field_name = "name_" . $lang;
            }

            $recordData->status = (object) [
                "id" => $statusData->id,
                "name" => $statusData->{$field_name},
                "style" => $statusData->style,
            ];
            
        }

        //for pages ctype ???
        $isPublic = false;
        
        //check if the read for this ctype is enabled
        if($ctype->getDisableRead() == true){
            throw new ForbiddenException();
        }

        // //get permission object
        // $permission_obj = Application::getInstance()->user->getCtypePermission($ctype->id);
        // //if it is not public check if current user has permission to read the ctype
        // if($isPublic != true){
        //     if(!isset($permission_obj) || ($permission_obj->allow_read != 1 && $permission_obj->allow_read_only_your_own_records != 1) ){
        //         throw new ForbiddenException();
        //     }
        // }

        // CtypesHelper::checkExtraConditions(CtypesHelper::$TYPE_SHOW, $ctype, $recordId, $params);

        //Load the record based on the id
        $item = $this->coreModel->nodeModel($ctype->getId())
            ->id($recordId)
            ->loadFirstOrFail();
        
        // if($isPublic != true){
        //     $this->app->user->checkCtypeExtraPermission($ctype, $recordData, "allow_read");
        // }

        
        if(property_exists($recordData,"is_system_object") && $recordData->is_system_object && !Application::getInstance()->user->isSuperAdmin()) {
            throw new ForbiddenException("You don't have permission to work on system objects");
        }
        
        if($isPublic != true && isset($permission_obj) && $permission_obj->allow_read_only_your_own_records == true && $recordData->created_user_id != \App\Core\Application::getInstance()->user->getId()){
            throw new ForbiddenException();
        }
        

        $result = (object)[
            "status" => "success",
            "result" => (object)[
                'ctype' => (object)[
                    "id" => $ctype->getId(),
                    "name" => $ctype->getName(),
                    "module" => (object)[
                        "id" => $ctype->getModuleId(),
                        "code" => $ctype->getModuleCode(),
                        "name" => $ctype->getModuleName(),
                        "icon" => $ctype->getModuleIcon(),
                    ],
                    "use_generic_status" => $ctype->getUseGenericStatus(),
                    "display_field_name" => $ctype->getDisplayFieldName(),
                ],
                'nodeData' => $recordData,
            ],
            
        ];

        return_json($result);
    }
}
