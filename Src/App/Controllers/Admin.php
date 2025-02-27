<?php 

/*
 * Home controller
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Exceptions\ForbiddenException;
use App\Models\NodeModel;

class Admin extends Controller {

    public function __construct(){
        parent::__construct();

        if(!$this->app->user->isAdmin())
            throw new ForbiddenException();
        
    }
    
    /**
     * index
     *
     * @return void
     *
     * Home function
     */
    public function index($ctypeId, $params){

        $data['title'] = "Admin Panel";

        $this->app->view->renderView("admin/index", $data, true);
        
    }

    public function config($ctypeId, $params){

        $data['title'] = "System Configuration";

        $this->app->view->renderView("admin/Config/index", $data, true);
        
    }

    public function update(){

        //Reset works only in Maintenance mode or Demo Mode, it will not work on LIVE servers.
        if($this->app->settings->get('MAINTENANCE_MODE_IS_ACTIVE') != 1 && $this->app->settings->get('IS_LIVE_PLATFORM') == 1){
            throw new ForbiddenException("This page is accessible in maintenance or demo mode only!");
        }

        $data['title'] = "System Update";

        $this->app->view->renderView("admin/Update/index", $data);
    }

    public function export($ctypeId, $params) {
        
        //Reset works only in Maintenance mode or Demo Mode, it will not work on LIVE servers.
        if($this->app->settings->get('MAINTENANCE_MODE_IS_ACTIVE') != 1 && $this->app->settings->get('IS_LIVE_PLATFORM') == 1){
            throw new ForbiddenException("This page is accessible in maintenance or demo mode only!");
        }

        $data['title'] = "System Update";

        $this->app->view->renderView("admin/export/index", $data);
        exit;

        $dataExport = new \App\Core\SystemUpdate\DataExport($ctypeId);
    }

    public function reset($ctypeId, $params) {
        
        //Reset works only in Maintenance mode or Demo Mode, it will not work on LIVE servers.
        if($this->app->settings->get('MAINTENANCE_MODE_IS_ACTIVE') != 1 && $this->app->settings->get('IS_LIVE_PLATFORM') == 1){
            throw new \App\Exceptions\ForbiddenException("This page is accessible in maintenance or demo mode only!");
        }
        
        $data['title'] = "System Reset";

        $this->app->view->renderView("admin/reset/index", $data);
        exit;

        $dataExport = new \App\Core\SystemUpdate\DataExport($ctypeId);
    }

    public function filemanager($ctypeName, $params) {
        
        $data['title'] = "File Manager";

        $this->app->view->renderView("admin/filemanager/index", $data);

        // //Reset works only in Maintenance mode or Demo Mode, it will not work on LIVE servers.
        // if($this->app->settings->get('MAINTENANCE_MODE_IS_ACTIVE') != 1 && $this->app->settings->get('IS_LIVE_PLATFORM') == 1){
        //     throw new \App\Exceptions\ForbiddenException("This page is accessible in maintenance or demo mode only!");
        // }
        
        // $data['title'] = "System Reset";

        // $this->app->view->renderView("admin/reset/index", $data);
        // exit;

        // $dataExport = new \App\Core\SystemUpdate\DataExport($ctypeId);
    }


    public function orphanColumns($id, $params) {
        
        $data['title'] = "Orphan Columns";

        $this->app->view->renderView("admin/orphanColumns/index", $data);
    }

    public function orphanTables($id, $params) {
        
        $data['title'] = "Orphan Tables";

        $this->app->view->renderView("admin/orphanTables/index", $data);
    }
}

