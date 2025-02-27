<?php 

/*
 * This controller is for generic view, which is responsible for loading the view, loading data, exporting data.
 */

namespace App\Controllers;

use App\Core\Controller;
use \App\core\Application;
use App\Core\Gviews\GviewGenBasedOnView;
use App\Core\Response;

class Gviews extends Controller{

    public function __construct(){
        parent::__construct();
        //Set execution timeout to long time so it will not timeout if the excel file is big
        $this->app->response->setMaxExecutionTime(Response::$MAX_EXECUTION_TIME_LONG);
        
        $url = Application::getInstance()->request->getUrl();

        $ignore_login_on_local = false;
        $params = Application::getInstance()->request->getParams();
        if(isset($params['ignore_login_on_local'])){
            $ignore_login_on_local = $params['ignore_login_on_local'];
        }
        
        // Check if the user is logged in or not
        if(Application::getInstance()->user->isAuthenticated() || ($ignore_login_on_local == true && $this->app->request->isLocal() || $this->app->request->isCli())){
            //If not logged in then redirect to login page
        } else {
            Application::getInstance()->response->returnNeedsLogin();
        }

    }

    



    /**
     * index
     *
     * @param  string $id
     * @return void
     *
     * This function is responsible to load the view either by passing id or name of the view
     */
    public function index($id){

        //Make sure the use is logged in
        if(!Application::getInstance()->user->isAuthenticated()){
            Application::getInstance()->response->returnNeedsLogin();
        }

        //Check if keyword is provided, which should be either id or name of the view
        if(!isset($id) || _strlen($id) == 0){
            throw new \App\Exceptions\MissingDataFromRequesterException("Id not provided");
        }
        

        $data = null;//Application::getInstance()->cache->get("gviews_based_on_view." . $id);
        if(!isset($data)) {
                
            $data = (new GviewGenBasedOnView($id, false))->generate();

            Application::getInstance()->cache->set("gviews_based_on_view." . $id, $data, 600);
        }

        Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data, true);
        echo $data['script'];
        exit;

    }

    



    /**
     * loadData
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function loads data either based on view id or Content-Type id (in case the Content-Type does not have apesific view)
     */
    public function loadData($id, $params){
        
        //Check in parameters if load data based on ctype or view
        if(isset($params['load_based_ctype']) && $params['load_based_ctype'] == 1){

            (new \App\Core\Gviews\LoadDataBasedOnCtype($id, $params))->main();

        } else {
         
            (new \App\Core\Gviews\LoadDataBasedOnView($id, $params))->main();

        }

    }

    
    
        
    /**
     * export
     *
     * @param  int $id
     * @param  array $params
     * @return void
     * 
     * This function will export data to excel or csv
     */
    public function export($id, $params){
        
        $viewObj = $this->app->coreModel->nodeModel("views")->id($id)->fields(["id", "name","ctype_id"])->loadFirst();

        (new \App\Core\BgTask())
            ->setName(sprintf("Export %s", $viewObj->name))
            ->setMainValue($id)
            ->setActionName("CoreGviewsExport")
            ->setPostData(Application::getInstance()->request->POST())
            ->addToQueue();
            
        Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;

    }

    /**
     * exportByName
     *
     * @param  string $name
     * @param  array $params
     * @return void
     * 
     * This function will export data to excel or csv
     */
    public function exportByName($id, $params){
        
        // $viewObj = $this->app->coreModel->nodeModel("views")
        //     ->id($id)
        //     ->fields(["id", "name","ctype_id"])
        //     ->loadFirst();

        (new \App\Core\BgTask())
            ->setName(sprintf("Export %s", $id))
            ->setMainValue($id)
            ->setActionName("CoreGviewsExport")
            ->setPostData(Application::getInstance()->request->POST())
            ->addToQueue();
            
        Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;

    }

    /**
     * gexport
     *
     * @param  int $id
     * @param  array $params
     * @return void
     * 
     * This function will export data by /dataexport/export which meant for modifying the excel and be able to reimport again later
     */
    public function gexport($id, $params = []){

        $viewObj = $this->app->coreModel->nodeModel("views")->id($id)->fields(["id", "name", "title","ctype_id"])->loadFirst();

        (new \App\Core\BgTask())
            ->setName(sprintf("Export %s", $viewObj->ctype_id_display))
            ->setMainValue($id)
            ->setActionName("CoreCtypeExportBasedOnGview")
            ->setPostData(Application::getInstance()->request->POST())
            ->addToQueue();
            
        Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;
        
    }

    /**
     * gexportByName
     *
     * @param  string $name
     * @param  array $params
     * @return void
     * 
     * This function will export data by /dataexport/export which meant for modifying the excel and be able to reimport again later
     */
    public function gexportByName($id, $params = []){

        $viewObj = $this->app->coreModel->nodeModel("views")
            ->id($id)
            ->fields(["id", "name","ctype_id"])
            ->loadFirst();

        (new \App\Core\BgTask())
            ->setName(sprintf("Export %s", $viewObj->ctype_id_display))
            ->setMainValue($viewObj->id)
            ->setActionName("CoreCtypeExportBasedOnGview")
            ->setPostData(Application::getInstance()->request->POST())
            ->addToQueue();
            
        Application::getInstance()->response->returnSuccess("Task added to queue");
        exit;
        
    }


}
