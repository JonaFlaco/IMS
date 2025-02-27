<?php

/*
 * This class generates dashboards
 */
namespace App\Core\Gdashboards;

use App\Core\Application;
use App\Exceptions\ForbiddenException;
use App\Core\Gdashboards\Components\jsHelperMethods;
use App\Core\Gdashboards\Components\WidgetSettingsComponent;
use App\Core\Gdashboards\Components\FilterationPanelComponent;
use App\Core\Gdashboards\Components\PageHeaderComponent;
use App\Core\Gdashboards\Components\PageContentComponent;
use App\Core\Gdashboards\Components\DetailPopupComponent;
use App\Core\Gdashboards\Components\ActionsComponent;
use App\Core\Gdashboards\Components\MountedComponent;
use App\Models\CTypeLog;

class DashboardGenerator {

    private $coreModel;
    private $id;
    private $dashboardObj;
    private $app;
    
    private jsHelperMethods $jsHelperMethods;
    private WidgetSettingsComponent $widgetSettingsComponent;
    private FilterationPanelComponent $filterationPanelComponent;
    private PageHeaderComponent $pageHeaderComponent;
    private PageContentComponent $pageContentComponent;
    private DetailPopupComponent $detailPopupComponent;
    private ActionsComponent $actionsComponent;
    private MountedComponent $mountedComponent;

    public function __construct($id) {
        $this->id = $id;

        $this->app = Application::getInstance();
        $this->coreModel = $this->app->coreModel;
        
        $this->dashboardObj = $this->coreModel->nodeModel("dashboards")
            ->id($this->id)
            ->loadFirstOrFail();


        if(!empty($this->dashboardObj->roles)) {
            
            $has_access = false;
            
            foreach($this->dashboardObj->roles as $role) {
                if(in_array($role->value, explode(",", $this->app->user->getRoles())))
                    $has_access = true;

            }

            if(!$has_access){
                throw new ForbiddenException();
            }

        }

        // add ctype log
        if(!$this->app->user->isAdmin()){
            (new CTypeLog($this->dashboardObj->sett_ctype_id))
            ->setContentId($this->dashboardObj->id)
            ->setGroupNam("visit")
            ->setTitle("Visited")
            ->setIsPrivate(true)
            ->save();
        }
        
        // check if its custom dashboard -> PowerBI
        if($this->dashboardObj->is_custom_url){
            $data = [
                "title" => "Custom Dashboard",
                "item" => $this->dashboardObj,
            ];
           
            $this->app->view->renderView('reportCenter/custom_url_dashboard', $data);
            exit;
        }
        

        $this->dashboardObj->widgets = $this->coreModel->nodeModel("widgets")
            ->where("m.dashboard_id = :dashboard_id")
            ->bindValue(":dashboard_id", $this->id)
            ->orderBy("m.sort")
            ->load();

        if(!empty($this->dashboardObj->module_id)){
            $this->dashboardObj->moduleObj = $this->coreModel->nodeModel("modules")
            ->id($this->dashboardObj->module_id)
            ->loadFirstOrFail();
        }

        
        $this->jsHelperMethods = new jsHelperMethods();
        $this->widgetSettingsComponent = new WidgetSettingsComponent($this->dashboardObj);
        $this->filterationPanelComponent = new FilterationPanelComponent($this->dashboardObj);
        $this->pageHeaderComponent = new PageHeaderComponent($this->dashboardObj);
        $this->pageContentComponent = new PageContentComponent($this->dashboardObj);
        $this->detailPopupComponent = new DetailPopupComponent($this->dashboardObj);
        $this->actionsComponent = new ActionsComponent($this->dashboardObj);
        $this->mountedComponent = new MountedComponent($this->dashboardObj);
    }

    public function generate(){
        
        
        $this->checkIfIsCustom();


        foreach($this->dashboardObj->widgets as $widget){
            if($widget->is_hidden != true){
                

                if(!empty($widget->roles)) {
            
                    $has_access = false;
                    
                    foreach($widget->roles as $role) {
                        if(in_array($role->value, explode(",", $this->app->user->getRoles())))
                            $has_access = true;
                        
                    }
        
                    if(!$has_access){
                        $widget->is_hidden = true;
                    }
        
                }
                
            }
        }

        if(isset($_GET['filter'])) {
            Application::getInstance()->globalVar->set('CHART_GLOBAL_FILTER_1', array(
                "name" => $_GET['filter']
            ));
        }


        $data = [
            'title' => $this->dashboardObj->name,
            'script' => $this->getPageScript(),
            'sett_load_chart_libraries' => true,
        ];
        
        Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data);
        echo $data['script'];
        exit;
        
    }


    private function checkIfIsCustom() {
        if($this->dashboardObj->is_custom){

            $data = [
                "title" => $this->dashboardObj->name,
                "dashboard" => $this->dashboardObj
            ];
            
            //Check if we have custom file for this dashboard
            if($this->dashboardObj->is_system_object){
                $file =  APP_ROOT_DIR . "\\Views\\CustomDashboards\\" . toPascalCase($this->dashboardObj->id) . ".php";
            } else {
                $file =  EXT_ROOT_DIR . "\\Views\\CustomDashboards\\" . toPascalCase($this->dashboardObj->id) . ".php"; 
            }
            if(is_file($file) ){
                require_once $file;
                exit;
            } else {
                throw new \App\Exceptions\NotFoundException("Custom file not found for this dashboard");
            }
        }
    }
    
    private function getPageScript() : string {

        ob_start(); ?>

            <script type="text/x-template" id="tpl-main">
                <div>
                    <?= $this->widgetSettingsComponent->generateModal() ?>
                    <?= $this->filterationPanelComponent->generateModal() ?>
                    <?= $this->detailPopupComponent->generateModal() ?>
                    <?= $this->pageHeaderComponent->generate() ?>
                    <?= $this->pageContentComponent->generate() ?>
                </div>
            </script>

            <script>

                var vm = new Vue({
                    el: '#vue-cont',
                    template: '#tpl-main',
                    components: {
                        Multiselect: window.VueMultiselect.default
                    },
                    data: <?= $this->getDataObject() ?>,
                    mounted(){
                        <?= $this->mountedComponent->getMountedMethod() ?>

                        <?= $this->filterationPanelComponent->finalizeParameterValues() ?>
                    },
                    methods:{
                        <?= $this->jsHelperMethods->generate() ?>
                        <?= $this->widgetSettingsComponent->generateMethods() ?>
                        <?= $this->filterationPanelComponent->generateViewFilterRefereshList() ?>
                        <?= $this->filterationPanelComponent->generateResetFilterMethod() ?>
                        <?= $this->filterationPanelComponent->updateFiltersInUrl() ?>
                        <?= $this->actionsComponent->exportChartData() ?>
                        <?= $this->actionsComponent->exportChartAsImage() ?>
                        <?= $this->detailPopupComponent->ChartGetDetails() ?>
                        <?= $this->detailPopupComponent->ChartExportDetails() ?>
                        <?= $this->actionsComponent->getData() ?>   
                    },
                    computed:{
                        <?= $this->filterationPanelComponent->generateViewFiltersOperatorFieldVisibility(); ?>
                        <?= $this->filterationPanelComponent->generateViewFiltersdependenciesComputedFields(); ?>
                    }
                });
                
            </script>


        <?php

        return ob_get_clean();
    }

    public function getDataObject() {

        $result = [];
        $result["filtersList"] = [];
        $result = array_merge($result, $this->filterationPanelComponent->getDataObject());
        
        foreach($this->dashboardObj->widgets as $widget){

            if($widget->is_hidden == true)
                continue;
            $result[$widget->id . "_series"] = [];
            $result[$widget->id . "_labels"] = [];
            $result[$widget->id . "_categories"] = [];
            $result[$widget->id . "_drilldown"] = [];
            
        }
        

        return json_encode($result);
    }
}
