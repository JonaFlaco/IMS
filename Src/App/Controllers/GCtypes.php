<?php

/**
 * This controller is responsible for add, edit, delete, show, and index of Content-Types, surveys and pages
 */


namespace App\Controllers;

use App\Core\Controller;
use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypesHelper;
use App\Core\Gctypes\DbStructureGenerator;
use App\Core\ContentType\Show\GtplFactory;
use App\Core\ContentType\Show\Loader;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\MissingDataFromRequesterException;
use \App\Exceptions\IlegalUserActionException;
use App\Models\CTypeLog;

class Gctypes extends Controller
{

    private $isPublic = false;
    private $ctypeObj = null;



    public function __construct($ctypeObj = null, $isPublic = false)
    {
        parent::__construct();

        $this->ctypeObj = $ctypeObj;
        $this->isPublic = $isPublic;

        // Check if the user is logged in or not
        if ($this->app->user->isAuthenticated() != true && $isPublic != true) {
            throw new \App\Exceptions\AuthenticationRequiredException;
        }

        if(!in_array($this->ctypeObj->status_id, [82]) && $this->app->user->isNotAdmin()) {
            throw new ForbiddenException("This interface is not accessible");
        }
    }





    /**
     * index
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function handles index of ctypes
     */
    public function index($id = null, array $params = array())
    {

        //Load permission object for the Content-Type
        $permission_obj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);

        //Check if the user have permission to read data for the ctype
        if (($permission_obj->allow_read != true && $permission_obj->allow_read_only_your_own_records != true)) {
            throw new ForbiddenException();
        }

        CtypesHelper::checkExtraConditions(CtypesHelper::$TYPE_VIEW, $this->ctypeObj, $id, $params);

        //Check if we have custom view for this ctype
        //First check if we have custom file inside app dir
        $file =  APP_ROOT_DIR . DS . "Views" . DS . "CustomViews" . DS .  toPascalCase($this->ctypeObj->id) . ".php";

        //If not found then check inside ext dir
        if (!is_file($file)) {
            $file =  EXT_ROOT_DIR . DS . "Views" . DS . "CustomViews" . DS . toPascalCase($this->ctypeObj->id) . ".php";
        }

        //If we have it then load it
        if (is_file($file)) {
            require_once $file;
            exit;
        }


        //If no custom view then load generic view
        //If the ctype has its own view then load it otherwise load then basic view
        if (isset($this->ctypeObj->view_id)) {

            $data = null;//Application::getInstance()->cache->get("gviews_based_on_view." . $this->ctypeObj->view_id);
            if(!isset($data)) {
                    
                $data = (new \App\Core\Gviews\GviewGenBasedOnView($this->ctypeObj->view_id))->generate();

                Application::getInstance()->cache->set("gviews_based_on_view." . $this->ctypeObj->view_id, $data, 600);
            }

            Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data, true);
            echo $data['script'];
            exit;
        } else {

            $data = null;//Application::getInstance()->cache->get("gviews_based_on_ctype." . $this->ctypeObj->id);
            if(!isset($data)) {
                $data = (new \App\Core\Gviews\GviewGenBasedOnCtype($this->ctypeObj))->generate();

                Application::getInstance()->cache->set("gviews_based_on_ctype." . $this->ctypeObj->id, $data, 600);
            }
            
            Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data);

            echo $data['script'];
            return;
        }
    }



    public function show($id, array $params = [])
    {
        
        //check if id is empty
        if (empty($id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
        }

        //check if the read for this ctype is enabled
        if ($this->ctypeObj->disable_read == true) {
            throw new ForbiddenException();
        }

        //get permission object
        $permission_obj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);
        //if it is not public check if current user has permission to read the ctype
        if ($this->isPublic != true) {
            if (!isset($permission_obj) || ($permission_obj->allow_read != 1 && $permission_obj->allow_read_only_your_own_records != 1)) {
                throw new ForbiddenException();
            }
        }

        CtypesHelper::checkExtraConditions(CtypesHelper::$TYPE_SHOW, $this->ctypeObj, $id, $params);

        //Load the record based on the id
        $item = $this->coreModel->nodeModel($this->ctypeObj->id)
            ->id($id)
            ->loadFirstOrFail();

        if ($this->isPublic != true) {
            $this->app->user->checkCtypeExtraPermission($this->ctypeObj, $item, "allow_read");
        }

        
        //if ctype name is pages then call renderPages() to handle it        
        if ($this->ctypeObj->id == "pages") {

            $this->renderPages($id, $params);

            return;
        }

        if ($this->isPublic != true && isset($permission_obj) && $permission_obj->allow_read_only_your_own_records == true && $item->created_user_id != \App\Core\Application::getInstance()->user->getId()) {
            throw new ForbiddenException();
        }


        if ($this->ctypeObj->use_custom_tpl) {

            //check if the ctype has custom tpl
            $file =  ($this->ctypeObj->is_system_object == true ? APP_ROOT_DIR : EXT_ROOT_DIR) . DS . "Views" . DS . "CustomTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";

            //if the file does not exist then generate new one
            if (!is_file($file)) {
                $contents = (new \App\Core\Gtpl\TplGenerator($this->ctypeObj))->generate();
                file_put_contents($file, $contents);
            }


            //if the file still exist
            if (file_exists($file)) {

                $item = array();

                if (isset($id)) {
                    $item = $this->coreModel->nodeModel($this->ctypeObj->id)
                        ->id($id)
                        ->loadFirstOrFail();

                    if ($this->ctypeObj->use_generic_status) {

                        $statusObj = $this->coreModel->nodeModel("status_list")
                            ->id($item->status_id)
                            ->loadFirstOrDefault();

                        $item->status = (object) [
                            "id" => $statusObj->id,
                            "name" => $statusObj->name,
                            "style" => $statusObj->style,
                        ];
                    }
                }

                $data = [
                    'title' => "Show " . $this->ctypeObj->name,
                    'nodeData' => (isset($item) ? $item : null),
                    'ctype_id' => $this->ctypeObj->id,
                    'ctype_name' => $this->ctypeObj->name,
                    'ctypeObj' => $this->ctypeObj
                ];

                Application::getInstance()->view->renderView("CustomTpls/" . toPascalCase($this->ctypeObj->id), $data);

                return;

                //if the file doesn't exist then call edit
            } else {
                $this->edit($this->ctypeObj->id, $id, $params);
            }
        } else {

            
            $data = [
                'title' => "Show " . $this->ctypeObj->name,
            ];

            $tpl = null;//Application::getInstance()->cache->get("gctypes_show." . $this->ctypeObj->id);
            if(!isset($tpl)) {
                    
                $tpl = requireVarToVar(Loader::generate($this->ctypeObj->id), $data);

                Application::getInstance()->cache->set("gviews_based_on_view." . $this->ctypeObj->id, $tpl, 600);
            }

            echo $tpl;
            
            return;
        }

        return;
    }

    


    private function renderPages($id, $params)
    {

        //load the record
        $item = $this->coreModel->nodeModel($this->ctypeObj->id)
            ->id($id)
            ->loadFirstOrFail();

        //if the user is not logged in and the the page is not public
        if (Application::getInstance()->user->isAuthenticated() != true && $item->is_public != true) {
            Application::getInstance()->user->checkAuthentication();
        }

        //If the page is not published yet, show error for anonymous users
        $message = "This page is not published yet";

        if ($item->is_published != true) { 

            if (Application::getInstance()->user->isAdmin()) {
                Application::getInstance()->session->flash('flash_warning', $message);
            } else {
                throw new \App\Exceptions\ForbiddenException($message);
            }
        }


        //get file for the page
        $file =  APP_ROOT_DIR . DS . "Views" . DS . "Pages" . DS . toPascalCase($item->id) . ".php";

        if (!file_exists($file)) {

            $file =  EXT_ROOT_DIR . DS . "Views" . DS . "Pages" . DS . toPascalCase($item->id) . ".php";

            if (!file_exists($file)) {
                throw new NotFoundException("Page not found");
            }
        }

        //prepare necessory info
        $data = [
            'title' => "Show " . $this->ctypeObj->name,
            'nodeData' => (isset($item) ? $item : null),
            'ctype_name' => $this->ctypeObj->name,
            'ctype_id' => $this->ctypeObj->id,
            'params' => $params
        ];

        //load the page
        Application::getInstance()->view->renderView("pages/" . $item->id, $data);
    }





    /**
     * delete
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function handles delete of ctypes
     */
    public function delete($id = null, $params = array())
    {

        //Check if id is empty
        if (empty($id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
        }

        //Check if delete is disabled for this ctype or not
        if ($this->ctypeObj->disable_delete == true) {
            throw new ForbiddenException();
        }

        //Get permission object for this ctype and check if current user has permission to delete this record
        $permission_obj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);
        //If current use doesn't have permission to delete then show error
        if ($permission_obj->allow_delete != 1) {
            throw new ForbiddenException();
        }


        CtypesHelper::checkExtraConditions(CtypesHelper::$TYPE_DELETE, $this->ctypeObj, $id, $params);

        //Load the record based on the id
        $item = $this->coreModel->nodeModel($this->ctypeObj->id)
            ->id($id)
            ->loadFirstOrFail();

        $this->app->user->checkCtypeExtraPermission($this->ctypeObj, $item, "allow_read");

        //If the user is not admin
        if (Application::getInstance()->user->isAdmin() !== true) {
            //Check if the the user has access to delete other users records or not
            if ($permission_obj->allow_delete_only_your_own_records == true && $item->created_user_id != Application::getInstance()->user->getId()) {
                throw new ForbiddenException();
            }
        }



        //Prepare the data with required info
        $data = [
            'title' => "Delete " . $this->ctypeObj->name,
            'ctype_obj' => $this->ctypeObj,
            'id' => $id
        ];

        //return delete interface
        Application::getInstance()->view->renderView('node/delete', $data);
    }





    /**
     * edit
     *
     * @param  int $id
     * @param  array $params
     *
     * This function handles edit of ctypes
     * @return void
     */
    public function edit($id = null, $params = null)
    {

        //Check if id is not empty
        if (empty($id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
        }

        //Check if current user has permission to edit this record
        if (Application::getInstance()->user->isAdmin() !== true && (Application::getInstance()->user->getId() != $id || $this->ctypeObj->id != "users")) {

            if ($this->ctypeObj->disable_edit == true) {
                throw new ForbiddenException();
            }

            $permission_obj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);
            if (!isset($permission_obj) || ($permission_obj->allow_edit != 1 && $permission_obj->allow_edit_only_your_own_records != 1)) {
                throw new ForbiddenException();
            }
        }

        CtypesHelper::checkExtraConditions(CtypesHelper::$TYPE_EDIT, $this->ctypeObj, $id, $params);

        //If request type is post
        if (Application::getInstance()->request->isPost()) {

            //check if the csrf is valid
            Application::getInstance()->csrfProtection->check();

            //get justification
            $justification = "";
            if (isset($_POST['justification'])) {
                $justification = $_POST['justification'];
            }

            //get record token
            $recordToken = null;
            if (isset($_POST['token'])) {
                $recordToken = $_POST['token'];
            }

            //get data
            $data = "";
            if (isset($_POST['data'])) {
                $data = json_decode($_POST['data']);
            } else {
                throw new \App\Exceptions\MissingDataFromRequesterException("Data is empty");
            }

            //send the data to node_save()
            $id = $this->coreModel->node_save($data, array("justification" => $justification, "token" => $recordToken));

            //remove the csrf token
            // Application::getInstance()->csrfProtection->remove();

            //flash the success flag
            Application::getInstance()->session->flash('flash_success', 'Content Saved');

            //return sucess 
            $result = (object)[
                "status" => "success",
                "id" => $id
            ];

            return_json($result);

            // If not request type is not POST
        } else {

            //Check if the ctype has custom edit template
            $found = false;
            //search in app dir
            $file =  APP_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";
            //if not found check in ext dir
            if (!is_file($file)) {

                $file =  EXT_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";

                if (is_file($file)) {
                    $found = true;
                }
            } else {
                $found = true;
            }

            //If custom edit template found then use it.
            if ($found == true) {

                //load the record data
                $item = $this->coreModel->nodeModel($this->ctypeObj->id)
                    ->id($id)
                    ->loadFirstOrFail();

                $data = [
                    'title' => $this->ctypeObj->name,
                    'nodeData' => $item,
                    'isEditMode' => true
                ];

                //show the custom tpl
                Application::getInstance()->view->renderView("CustomEditTpls/" . toPascalCase($this->ctypeObj->id), $data);
                return;

                //if custom edit template not found then use the generic one
            } else {

                $data = null;//Application::getInstance()->cache->get("ctypes_edit." . $this->ctypeObj->id);
                if(!isset($data)) {

                    //generate a generic tpl
                    $crudGenerator = new \App\Core\Gctypes\AddEditGen($this->ctypeObj, $id);
                    $data = $crudGenerator->generate();

                    Application::getInstance()->cache->set("ctypes_edit." . $this->ctypeObj->id, $data, 600);
                }

                Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data, true);
                echo $data['script'];
                exit;
            }
        }
    }


    public function newedit(int $id = null, $params = null)
    {

        if ($this->app->user->isNotAdmin()) {
            throw new ForbiddenException();
        }
        
        //Check if id is not empty
        if (empty($id)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Id is missing");
        }

        //Check if current user has permission to edit this record
        if (Application::getInstance()->user->isAdmin() !== true && (Application::getInstance()->user->getId() != $id || $this->ctypeObj->id != "users")) {

            if ($this->ctypeObj->disable_edit == true) {
                throw new ForbiddenException();
            }

            $permission_obj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);
            if (!isset($permission_obj) || ($permission_obj->allow_edit != 1 && $permission_obj->allow_edit_only_your_own_records != 1)) {
                throw new ForbiddenException();
            }
        }

        CtypesHelper::checkExtraConditions(CtypesHelper::$TYPE_EDIT, $this->ctypeObj, $id, $params);

    
        //Check if the ctype has custom edit template
        $found = false;
        //search in app dir
        $file =  APP_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";
        //if not found check in ext dir
        if (!is_file($file)) {

            $file =  EXT_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";

            if (is_file($file)) {
                $found = true;
            }
        } else {
            $found = true;
        }

        //If custom edit template found then use it.
        if ($found == true) {

            //load the record data
            $item = $this->coreModel->nodeModel($this->ctypeObj->id)
                ->id($id)
                ->loadFirstOrFail();

            $data = [
                'title' => $this->ctypeObj->name,
                'nodeData' => $item,
                'isEditMode' => true
            ];

            //show the custom tpl
            Application::getInstance()->view->renderView("CustomEditTpls/" . toPascalCase($this->ctypeObj->id), $data);
            return;

            //if custom edit template not found then use the generic one
        } else {

            //generate a generic tpl
            $crudGenerator = new \App\Core\ContentType\Add\Generator((int)$this->ctypeObj->id, (int)$id);
            $data = $crudGenerator->createTemplate();

            Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data);
            echo $data['script'];
            exit;
        }

    }



    /**
     * add
     *
     * @param  bool $isCustomTpl
     * @param  array $params
     * @return void
     *
     * This function handles add of ctypes
     */
    public function add($isCustomTpl = false, $params = [])
    {

        //get survey id and object
        $survey_obj = null;
        $survey_id = null;
        if (isset($params['survey_id'])) {

            $survey_id = $params['survey_id'];

            $survey_obj = $this->coreModel->nodeModel("surveys")
                ->id($survey_id)
                ->loadFirstOrFail();

            $is_public = $survey_obj->type_id != "internal";
        } else {

            $is_public = false;
        }

        //if add is disabled for this ctype
        if ($this->ctypeObj->disable_add == true) {
            throw new ForbiddenException("Add disabled for this Content-Type");
        }

        //If survey object is not empty
        if ($survey_obj != null) {

            //if user is not logged in and the survey is not public
            if ($survey_obj != null && Application::getInstance()->user->isAuthenticated() != true && $survey_obj->type_id == "internal") {
                Application::getInstance()->user->checkAuthentication();
            }

            //get start date and end date for the survey
            $start_date = new \DateTime($survey_obj->start_date == null ? Date('Y-m-d') : date('Y-m-d', strtotime($survey_obj->start_date)));
            $end_date = new \DateTime($survey_obj->end_date == null ? Date('Y-m-d') : date('Y-m-d', strtotime($survey_obj->end_date)));

            //if the survey is published
            if ($survey_obj->status_id != 82) { // 82 => Published

                if($survey_obj->status_id == 90){ // 90 => Closed
                    $message = "This survey is closed";
                }else{
                    $message = "This survey is not published";
                }

                if (Application::getInstance()->user->isAdmin()) {
                    Application::getInstance()->session->flash('flash_warning', $message);
                } else {
                    echo $message;
                    exit;
                }

                //If the survey is finished
            }else if ($start_date > new \DateTime(Date('Y-m-d'))) {
                $message = "This survey is not started yet";
                if (Application::getInstance()->user->isAdmin()) {
                    Application::getInstance()->session->flash('flash_warning', $message);
                } else {
                    echo $message;
                    exit;
                }

                //if the survey is expired
            } else if ($end_date < new \DateTime(Date('Y-m-d'))) {
                $message = "This survey is expired!";
                if (Application::getInstance()->user->isAdmin()) {
                    Application::getInstance()->session->flash('flash_warning', $message);
                } else {
                    echo $message;
                    exit;
                }
            }
        }


        // if it is not public then check permission
        if (!$is_public) {
            $permission_obj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);

            if (!isset($permission_obj) || $permission_obj->allow_add != 1) {
                throw new ForbiddenException();
            }
        }

        CtypesHelper::checkExtraConditions(CtypesHelper::$TYPE_ADD, $this->ctypeObj, null, $params);

        //If the request type is POST
        if (Application::getInstance()->request->isPost()) {

            //check the csrf token
            Application::getInstance()->csrfProtection->check();

            $postData = Application::getInstance()->request->getBody();

            //get justification
            $justification = "";
            if (isset($postData['justification'])) {
                $justification = $postData['justification'];
            }

            //get data
            $data = null;

            if (isset($postData['data'])) {

                $data = jsonDecode($postData['data']);
            } else {
                throw new \App\Exceptions\MissingDataFromRequesterException("Data is empty");
            }

            //send the data to node_save()
            $id = $this->coreModel->node_save($data, array("justification" => $justification));

            // Application::getInstance()->csrfProtection->remove();

            //flash the result
            if ($survey_id != null) {
                // \SessionHelper::flash('flash_success', 'Content added from survey');
            } else {
                Application::getInstance()->session->flash('flash_success', 'Content added');
            }

            $result = (object)[
                "status" => "success",
                "id" => $id
            ];

            return_json($result);

            //if the request type is not POST
        } else {


            //check if the ctype has custom file template for add
            $found = false;
            $file =  APP_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";

            if (!is_file($file)) {

                $file =  EXT_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";

                if (is_file($file)) {
                    $found = true;
                }
            } else {
                $found = true;
            }

            //if custom tpl found then use it otherwise use the generic one
            if ($found != true) {


                $data = null;//Application::getInstance()->cache->get("ctypes_add." . $this->ctypeObj->id);
                if(!isset($data)) {
                        
                    $crudGenerator = new \App\Core\Gctypes\AddEditGen($this->ctypeObj, null, $survey_obj);
                    $data = $crudGenerator->generate();

                    if ($isCustomTpl == true) {
                        return $data;
                    }

                    Application::getInstance()->cache->set("ctypes_add." . $this->ctypeObj->id, $data, 600);
                }

                Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data, true);
                echo $data['script'];
                exit;
            } else {

                $data = [
                    'title' => $this->ctypeObj->name,
                    'nodeData' => null,
                    'isEditMode' => false
                ];

                Application::getInstance()->view->renderView("CustomEditTpls/" . toPascalCase($this->ctypeObj->id), $data, true);
                return;
            }
        }
    }

    public function newadd($isCustomTpl = false, $params = [])
    {
        
        if ($this->app->user->isNotAdmin()) {
            throw new ForbiddenException();
        }

        //get survey id and object
        $survey_obj = null;
        $survey_id = null;
        if (isset($params['survey_id'])) {

            $survey_id = $params['survey_id'];

            $survey_obj = $this->coreModel->nodeModel("surveys")
                ->id($survey_id)
                ->loadFirstOrFail();

            $is_public = $survey_obj->type_id == "public";
        } else {

            $is_public = false;
        }

        //if add is disabled for this ctype
        if ($this->ctypeObj->disable_add == true) {
            throw new ForbiddenException("Add disabled for this Content-Type");
        }

        //If survey object is not empty
        if ($survey_obj != null) {

            //if user is not logged in and the survey is not public
            if ($survey_obj != null && Application::getInstance()->user->isAuthenticated() != true && $survey_obj->type_id == "intenal") { 
                Application::getInstance()->user->checkAuthentication();
            }

            //get start date and end date for the survey
            $start_date = new \DateTime($survey_obj->start_date == null ? Date('Y-m-d') : date('Y-m-d', strtotime($survey_obj->start_date)));
            $end_date = new \DateTime($survey_obj->end_date == null ? Date('Y-m-d') : date('Y-m-d', strtotime($survey_obj->end_date)));

            //if the survey is published
            if ($survey_obj->status_id != 82) { // 82 => Published

                if($survey_obj->status_id == 90){ // 90 => closed
                    $message = "This survey is closed";
                }else{
                    $message = "This survey is not published";
                }

                if (Application::getInstance()->user->isAdmin()) {
                    Application::getInstance()->session->flash('flash_warning', $message);
                } else {
                    echo $message;
                    exit;
                }

                //If the survey is finished
            }else if ($start_date > new \DateTime(Date('Y-m-d'))) {
                $message = "This survey is not started yet";
                if (Application::getInstance()->user->isAdmin()) {
                    Application::getInstance()->session->flash('flash_warning', $message);
                } else {
                    echo $message;
                    exit;
                }

                //if the survey is expired
            } else if ($end_date < new \DateTime(Date('Y-m-d'))) {
                $message = "This survey is expired!";
                if (Application::getInstance()->user->isAdmin()) {
                    Application::getInstance()->session->flash('flash_warning', $message);
                } else {
                    echo $message;
                    exit;
                }
            }
        }


        // if it is not public then check permission
        if (!$is_public) {
            $permission_obj = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);

            if (!isset($permission_obj) || $permission_obj->allow_add != 1) {
                throw new ForbiddenException();
            }
        }

        CtypesHelper::checkExtraConditions(CtypesHelper::$TYPE_ADD, $this->ctypeObj, null, $params);

        //If the request type is POST
        if (Application::getInstance()->request->isPost()) {

            //check the csrf token
            Application::getInstance()->csrfProtection->check();

            $postData = Application::getInstance()->request->getBody();

            //get justification
            $justification = "";
            if (isset($postData['justification'])) {
                $justification = $postData['justification'];
            }

            //get data
            $data = null;

            if (isset($postData['data'])) {

                $data = jsonDecode($postData['data']);
            } else {
                throw new \App\Exceptions\MissingDataFromRequesterException("Data is empty");
            }

            //send the data to node_save()
            $id = $this->coreModel->node_save($data, array("justification" => $justification));

            // Application::getInstance()->csrfProtection->remove();

            //flash the result
            if ($survey_id != null) {
                // \SessionHelper::flash('flash_success', 'Content added from survey');
            } else {
                Application::getInstance()->session->flash('flash_success', 'Content added');
            }

            $result = (object)[
                "status" => "success",
                "id" => $id
            ];

            return_json($result);

            //if the request type is not POST
        } else {


            //check if the ctype has custom file template for add
            $found = false;
            $file =  APP_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";

            if (!is_file($file)) {

                $file =  EXT_ROOT_DIR . DS . "Views" . DS . "CustomEditTpls" . DS . toPascalCase($this->ctypeObj->id) . ".php";

                if (is_file($file)) {
                    $found = true;
                }
            } else {
                $found = true;
            }

            //if custom tpl found then use it otherwise use the generic one
            if ($found != true) {


                //$data = \App\Libraries\AddEditGenerator::Run($ctypeObj, null,$survey_obj);
                $crudGenerator = new \App\Core\ContentType\Add\Generator((int)$this->ctypeObj->id, null, $survey_obj);
                $data = $crudGenerator->createTemplate();

                if ($isCustomTpl == true) {
                    return $data;
                }

                Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data);
                echo $data['script'];
                exit;
            } else {

                $data = [
                    'title' => $this->ctypeObj->name,
                    'nodeData' => null,
                    'isEditMode' => false
                ];

                Application::getInstance()->view->renderView("CustomEditTpls/" . toPascalCase($this->ctypeObj->id), $data);
                return;
            }
        }
    }




    /**
     * delete_action
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function handles delete action of ctypes
     */
    public function delete_action($id = null, $params = array())
    {

        $this->app->coreModel->delete($this->ctype_obj->id, $id);

        $this->app->response->redirect("/" . $this->ctype_obj->id);
    }



    public function reset_numbering($id, $params = []) {
        
        // SELECT IDENT_CURRENT ('genders') AS Current_Identity;  
                
        // select * from tickets

        // declare @id bigint
        // select top 1 @id = id from tickets order by id desc

        // if(@id is null)
        //     set @id = 0

            
        // select @id

        // -- delete from tickets

        // -- delete from ctypes_logs where ctype_id = 'tickets'

        // -- delete from ctypes_logs where ctype_id = 'tickets_tasks'

        // DBCC CHECKIDENT ('tickets', RESEED, @id)



    }

    /**
     * create_tpl
     *
     * @param  id $id
     * @param  array $params
     * @return void
     *
     * This function creates tpl for the ctype
     */
    public function create_tpl(int $id, array $params = [])
    {

        //if the current user is not admin then redirect to home
        if (Application::getInstance()->user->isAdmin() !== true) {
            Application::getInstance()->response->redirect("/");
        }

        if (Application::getInstance()->request->isGet()) {
            throw new \App\Exceptions\IlegalUserActionException("This action is available in POST only");
        }

        $overwrite = false;
        if (isset($params['overwrite'])) {
            $overwrite = $params['overwrite'];
        }

        $destCtypeObj = (new Ctype)->load($id);

        //if the ctype does not allow read then show error
        if ($destCtypeObj->disable_read == true) {
            throw new \App\Exceptions\ForbiddenException("Tpl is disabled for this Content-Type");
        }


        //get the file path
        $file =  ($destCtypeObj->is_system_object == true ? APP_ROOT_DIR : EXT_ROOT_DIR) . DS . "Views" . DS . "CustomTpls" . DS . toPascalCase($destCtypeObj->id) . ".php";

        //if the file exist and overwrite is not true then show error
        if (is_file($file) && $overwrite != true) {
            throw new \App\Exceptions\IlegalUserActionException("Tpl file already exist, if you want to overwrite click 'Create Tpl (overwrite if exist)'", "json");
        }


        $is_overwrite = is_file($file);

        //create the tpl
        $contents = (new \App\Core\Gtpl\TplGenerator($destCtypeObj))->generate();
        //put the content to the file
        file_put_contents($file, $contents);

        // //add it to the ctype log
        (new CTypeLog("ctypes"))
            ->setContentId($id)
            ->setJustification(sprintf("Tpl %s", ($is_overwrite == true ? "Updated" : "Created")))
            ->setGroupNam("create_tpl")
            ->save();

        $this->app->response->returnSuccess();
    }


    /**
     * generate_sql
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function creates delete and create sql script
     */
    public function generate_sql(string $id, array $params = [])
    {

        if ($this->app->user->isAdmin() == false) {
            $this->app->response->redirect("/");
        }

        $type = null;
        if (isset($params['type'])) {
            $type = $params['type'];
        }

        if ($id == array() || $id == "null") {
            $id = null;
        }

        if($id == null) {
            throw new MissingDataFromRequesterException("Content-Type ID is missing");
        }

        echo "<pre>";
        if ($type == "delete") {
            echo ctypes_generate_delete_tsql_code((new Ctype)->load($id)->id);
        } else {
            echo (new DbStructureGenerator($id))->generate();
        }
        echo "</pre>";
    }

     /**
     * This function resets table ID
     * @param  string $ctypeId
     * @return void
     **/
     public function restTableId(string $ctypeId){
        $this->coreModel->resetTableNumbering($ctypeId);
        $this->app->response->returnSuccess(sprintf("Table identity column and the log reseted successfuly for `%s`", $ctypeId));
     }
}
