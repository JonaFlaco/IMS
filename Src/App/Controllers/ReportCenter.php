<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\DateHelper;
use App\Exceptions\ForbiddenException;
class ReportCenter extends Controller {

    public function __construct(){
        parent::__construct();

        $this->app->user->checkAuthentication();

        // We remove this check once we publish the report center
        if(!$this->app->user->isAdmin()){
            throw new \App\Exceptions\ForbiddenException();
        } 
        
    }
    
    /**
     * index
     *
     * @return void
     *
     * Home function
     */
    public function index(){

        $items = $this->coreModel->nodeModel("dashboards")
                                    ->where("m.status_id = :status_id")
                                    ->bindValue("status_id", 82) // 82 = > published 
                                    ->load();

        $is_admin = $this->app->user->isAdmin();

        $current_user_roles = $this->app->user->getRoles();

        foreach($items as $key => $item) {

            $item->last_update_humanify = isset($item->updated_date) ? DateHelper::humanify(strtotime($item->updated_date)) : DateHelper::humanify(strtotime($item->created_date));

            $item->module_id = isset($item->module_id) ? $item->module_id: 'uncategorized';
            $item->module_id_display = isset($item->module_id_display) ? $item->module_id_display: 'Uncategorized';


            if(!$is_admin){

                if(!empty($item->roles)) {
                    $has_access = false;
                    
                    foreach($item->roles as $role) {
                        if(in_array($role->value, explode(",", $current_user_roles))){
                            $has_access = true;
                        }
                    }
    
                    if(!$has_access){
                        // remove the dashabord that has no access
                        unset($items[$key]);
                    }
                }else{
                    // remove the dashabord that has no roles
                    unset($items[$key]);
                }
                
            }
        }

        if(!$is_admin){
            // re-index the items after unset
            $items = array_values($items);
        }


        $data = [
            "title" => "Report Center",
            "items" => $items,
        ];

        $this->app->view->renderView('reportCenter/index',$data);
    }

}

