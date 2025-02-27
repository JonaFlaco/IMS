<?php

/**
 * This class is is used to generate gview interface dynamically based on ctypes
 */


namespace App\Core\Gviews;

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gviews\Components\MainContentBasedOnCtypeComponent;
use App\Core\Gviews\Components\GenericExportComponent;
use App\Core\Gviews\Components\TopExtraActionComponent;
use App\Core\Gviews\Components\PageTopRowComponent;
use App\Core\Gviews\Components\CrudComponent;
use App\Core\Gviews\Components\PageHeaderComponent;
use App\Core\Gviews\Components\KeyBindsComponent;
use App\Core\Gviews\Components\FilterationPanelComponent;
use App\Core\Gviews\Components\LoadingComponent;
use App\Core\Gviews\Components\LogComponent;
use App\Core\Gviews\Components\NoDataMessageComponent;
use App\Core\Gviews\Components\PaginationComponent;
use App\Models\CoreModel;

Class GviewGenBasedOnCtype {

    private object $coreModel;
    private object $ctypeObj;
    private object $permissions;
    private array $fields;
    private PageHeaderComponent $pageHeaderComponent;
    private LoadingComponent $loadingComponent;
    private PaginationComponent $paginationComponent;
    private NoDataMessageComponent $noDataMessageComponent;
    private CrudComponent $crudComponent;
    private PageTopRowComponent $pageTopRowComponent;
    private FilterationPanelComponent $filterationPanelComponent;
    private TopExtraActionComponent $topExtraActionComponent;
    private GenericExportComponent $genericExportComponent;
    private LogComponent $logComponent;
    private MainContentBasedOnCtypeComponent $mainContentBasedOnCtypeComponent;
    
    public function __construct(object $ctypeObj) {
        
        $this->coreModel = CoreModel::getInstance();    
        
        $this->ctypeObj = (new Ctype)->load($ctypeObj->id);

        $this->fields = $this->ctypeObj->getFields(); 
        $this->permissions = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);
    
        if(!empty(\App\Core\Application::getInstance()->user->getLangId()) && \App\Core\Application::getInstance()->user->getLangId() != "en" && !empty($this->ctypeObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()})){
            $this->ctypeObj->name = $this->ctypeObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()};
        }

        $this->languageDirection = Application::getInstance()->user->getLangDirection();

        $this->loadingComponent = new LoadingComponent();
        $this->paginationComponent = new PaginationComponent();
        $this->pageHeaderComponent = new PageHeaderComponent($this->ctypeObj);
        $this->noDataMessageComponent = new NoDataMessageComponent();

        $this->topExtraActionComponent = new TopExtraActionComponent($this->ctypeObj, $this->permissions, null, null);
        $this->filterationPanelComponent = new FilterationPanelComponent($this->ctypeObj, null);

        $this->crudComponent = new CrudComponent($this->ctypeObj, $this->permissions);
        $this->pageTopRowComponent = new PageTopRowComponent($this->ctypeObj, $this->permissions, $this->filterationPanelComponent, $this->topExtraActionComponent, null);

        $this->genericExportComponent = new GenericExportComponent(null, $this->ctypeObj, $this->permissions, $this->filterationPanelComponent);
        
        $this->logComponent = new LogComponent($this->ctypeObj, $this->permissions);

        $this->mainContentBasedOnCtypeComponent = new MainContentBasedOnCtypeComponent($this->ctypeObj, $this->fields, $this->permissions);
    }

    
    /**
     * basedOnView
     *
     * @param  int $id
     * @param  array $params
     * @return void
     *
     * This function will load data based on view id provided
     */
    public function generate() : array {

        ob_start(); ?>
        
        <?= Application::getInstance()->view->renderView('Components/LogComponent') ?>
        
        <template id="tpl-main">
            <div>
                <?= $this->loadingComponent->generateModal() ?>
                <?= $this->logComponent->generateModal() ?>
                <?= $this->pageHeaderComponent->generate() ?>
                <?= Application::getInstance()->session->flash() ?>
                
                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body p-1">
                                
                                <div class="row mb-2">
                                    <?= $this->pageTopRowComponent->generate() ?>
                                </div>

                                <?= $this->loadingComponent->generate() ?>
                                <?= $this->noDataMessageComponent->generate() ?>
                                <?= $this->generateContent() ?>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </template>

        <script type="module">
            let vm = new Vue({
                el: '#vue-cont',
                components: {
                    Multiselect: window.VueMultiselect.default
                },
                template:'#tpl-main',
                data: <?= $this->getDataObject() ?>,
                beforeDestroy() {
                    document.removeEventListener('keydown', this._keyListener);
                },
                mounted(){
                    <?= (new KeyBindsComponent())->generate() ?>
                    this.filter();
                },
                methods: {
                    <?= $this->crudComponent->generateMethods() ?>
                    <?= $this->loadingComponent->generateMethods() ?>
                    <?= $this->generateButtonMethods() ?>
                    <?= $this->genericExportComponent->generateMethodsBasedOnCtype() ?>
                    <?= $this->filterationPanelComponent->generateFilterMethodBasedOnCtype() ?>
                    <?= $this->logComponent->generateMethods() ?>
                    <?= $this->topExtraActionComponent->generateMethods() ?>
                }
    
            });
        </script>
    
        <?php

        $data = [
            'title' => $this->ctypeObj->name ,
            'script' => ob_get_clean(),
        ];
    
        return $data;
      
    }

    private function generateContent(){

        $result = "";

        $result .= $this->mainContentBasedOnCtypeComponent->generate();
        $result .= $this->paginationComponent->generate();

        return $result;
    }

    private function getDataObject() {

        $result = [];
        $result["mainCtypeId"] = $this->ctypeObj->id;
        $result["mainCtypeName"] = $this->ctypeObj->name;
        $result = array_merge($result, $this->logComponent->getDataObject());
        $result = array_merge($result, $this->loadingComponent->getDataObject());
        $result = array_merge($result, $this->paginationComponent->getDataObject());
        $result = array_merge($result, $this->mainContentBasedOnCtypeComponent->getDataObject());
        
        return json_encode($result);
        
    }


    private function generateButtonMethods() {
        
        $result = "";
        foreach($this->fields as $field){
            if($field->field_type_id == "button"  && $field->is_hidden != true){

                $result .= sprintf("run%s(id){ window.open('/%s/' + id, '_blank'); },",$field->name, $field->method);
                        
            }
        }

        return $result;
                
    }


}