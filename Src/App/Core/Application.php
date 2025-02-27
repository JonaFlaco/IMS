<?php

/*
 * This class is the main class in the system.
 * It is the loader of the app and handles all the necessory classes and helpers
 */

namespace App\Core;

use App\Core\Cache\CacheMemory;
use App\Core\Cache\CacheRedis;
use App\Core\Cache\ICacheHandler;
use App\Core\Common\Singleton;
use App\Core\Router\Router;
use \App\Models\CoreModel;
use App\Models\NodeModel;
use \App\Models\UserModel;
use App\Core\Communications\Sms;
use App\Core\Communications\Email;
use App\Core\Communications\ViberMessaging;
use App\Core\Communications\PushNotification;
use App\Core\Communications\WhatsappMessaging;

class Application extends Singleton {

    public Router $router;
    public Response $response;
    public Request $request;
    public View $view;
    public Session $session;
    public GlobalVar $globalVar;
    public CoreModel $coreModel;
    public UserModel $userModel;
    public Settings $settings;
    public User $user;
    public CsrfProtection $csrfProtection;
    public Env $env;
    public PushNotification $pushNotification;
    public Email $email;
    public Sms $sms;
    public ICacheHandler $cache;
    public QueryFactory $queryFactory;
    public Git $git;
    public Survey $survey;

    private bool $authRequired = true;
    
    private bool $IS_TESTING_ENV = false;
    private $is_initialized = false;

    public function prepare() {

        if (!defined('DS')) {
            /**
             * Defines DS as short form of DS.
             */
            define('DS', DIRECTORY_SEPARATOR);
        }
        
        
        require_once dirname(__FILE__, 3) . DS . "bootstrap.php";

        $this->request = new Request();
        
        $this->response = new Response();
        
        $this->IS_TESTING_ENV = $this->request->getParam("sec_is_automated_test") == 1;
        
        $this->env = new Env($this->IS_TESTING_ENV ? ".env.testing" : null);
        
        $this->globalVar = new GlobalVar();
        $this->session = new Session();

        $cacheProviderClassName = '\App\Core\Cache\\' . Application::getInstance()->env->get("cache_provider", "CacheDefault");
        $this->cache = new $cacheProviderClassName;
        
        $this->view = new View();
        
        //Prepare the app for next step
        (new AppConfig())->load();
        
        //$this->checkIfInitialSetup();

        $this->queryFactory = new QueryFactory();
        
        $this->coreModel = CoreModel::getInstance();
        $this->userModel = UserModel::getInstance();

        $this->settings = new Settings();


        $this->csrfProtection = new CsrfProtection();

        //set timezone
        date_default_timezone_set(\App\Core\Application::getInstance()->settings->get("timezone","Asia/Baghdad"));
        
        $this->user = new User();
        
        $this->setDynamicStyleVars();

        $this->git = new Git();
        
        $this->pushNotification = new PushNotification();
        $this->email = new Email();
        $this->sms = new Sms();
        $this->survey = new Survey();
        
        
        $this->router = new Router(new NodeModel(), $this->request, $this->response);
        
        //Check if current user's secret key valid
        //$this->userModel->checkUserSecretKey();

        $this->is_initialized = true;

        $this->user->loadSysUsers();
        return $this;
    }

    public function isInitialized() : bool {
        return $this->is_initialized;
    }
    public function isNotInitialized() : bool {
        return !$this->is_initialized;
    }

    private function checkIfInitialSetup() {

        $url = $this->request->getUrl();
        if(isset($url) && isset($url[0]) && _strtolower($url[0]) == _strtolower("InitialSetup")) {
            (new InitialSetup\InitialSetup())
                ->index($this->request->getParams());
            exit;
        }
    }

    public function setDynamicStyleVars(){
        
        $this->user->checkLanguage();
        
        $langDir = Application::getInstance()->user->getLangDirection();
        
        $this->globalVar->set('s:lang_dir-dyn', ($langDir == 'rtl' ? 'float-start' : 'float-end'));
        $this->globalVar->set('s:lang_dir-dyn-r', ($langDir == 'rtl' ? 'float-end' : 'float-start'));
        $this->globalVar->set('s:lang_dir-dyn-f', ($langDir == 'rtl' ? 'text-align: right !important;' : ''));
        $this->globalVar->set('s:lang_dir-dyn-f2', ($langDir == 'rtl' ? 'left: 0 !important; right: auto !important;text-align: right !important;' : ''));

        $rtl_prop = "";
        if($langDir == "rtl"){
            $rtl_prop .= ' dir="RTL" ';
            $rtl_prop .= ' style="text-align: right" ';
        }
        $this->globalVar->set('s:rtl-prop', $rtl_prop);

        $this->globalVar->set('s:rtl-prop2', ($langDir == 'rtl' ? 'style="margin-left: 0px !important; margin-right: 250px !important"' : ''));
        
    }


    //Get if Authentication required
    public function getAuthRequired() : bool {
        return $this->authRequired;
    }

    //Set Authentication required or not
    public function setAuthRequired(bool $value) {
        $this->authRequired = $value;
    }


    //This method runs the app
    public function run(){

        $this->router->resolve();
        
    }

    


}
