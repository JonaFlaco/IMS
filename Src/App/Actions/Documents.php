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

class Documents extends Controller {
    
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
    public function index(string $id = null) : void {

        if(_strlen($id) == 0){
            throw new \App\Exceptions\MissingDataFromRequesterException("Id not provided");
        }
        
        $data = $this->coreModel->nodeModel("documents")
            ->id($id)
            ->loadFirstOrFail();
        
        $this->app->response->redirect("/documents/show/{$data->id}");

    }
}
