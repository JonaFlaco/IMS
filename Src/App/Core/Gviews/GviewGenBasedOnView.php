<?php

/**
 * This class is is used to generate gview interface dynamically based on views
 */


namespace App\Core\Gviews;

use \App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Core\Gctypes\CtypeField;
use App\Core\Gviews\Components\NoDataMessageComponent;

use App\Models\CoreModel;
use App\Core\Gviews\Components\CrudComponent;
use App\Core\Gviews\Components\TopExtraActionComponent;
use App\Core\Gviews\Components\GenericExportComponent;
use App\Core\Gviews\Components\PageTopRowComponent;
use App\Core\Gviews\Components\MainTableComponent;
use App\Core\Gviews\Components\KeyBindsComponent;
use App\Core\Gviews\Components\ColumnSettingsComponent;
use App\Core\Gviews\Components\VerificationComponent;
use App\Core\Gviews\Components\UpdateStatusComponent;
use App\Core\Gviews\Components\PageHeaderComponent;
use App\Core\Gviews\Components\BulkDeleteComponent;
use App\Core\Gviews\Components\ExportComponent;
use App\Core\Gviews\Components\ExtensionsComponent;
use App\Core\Gviews\Components\FilterationPanelComponent;
use App\Core\Gviews\Components\jsHelperMethods;
use App\Core\Gviews\Components\LoadingComponent;
use App\Core\Gviews\Components\LogComponent;
use App\Core\Gviews\Components\PaginationComponent;

Class GviewGenBasedOnView {

    private CoreModel $coreModel;
    private object $ctypeObj;
    private object $viewData;
    private object $viewCtypeObj;
    private FilterationPanelComponent $filterationPanelComponent;
    private BulkDeleteComponent $bulkDeleteComponent;
    private UpdateStatusComponent $UpdateStatusComponent;
    private VerificationComponent $verificationComponent;
    private LogComponent $logComponent;
    private LoadingComponent $loadingComponent;
    private ColumnSettingsComponent $columnSettingsComponent;
    private object $permissions;
    private ExtensionsComponent $extensionsComponent;
    private MainTableComponent $mainTableComponent;
    private PaginationComponent $paginationComponent;
    private PageTopRowComponent $pageTopRowComponent;
    private GenericExportComponent $genericExportComponent;
    private ExportComponent $exportComponent;
    private TopExtraActionComponent $topExtraActionComponent;
    private CrudComponent $crudComponent;
    private NoDataMessageComponent $noDataMessageComponent;
    private CrudComponent $curdComponent;
    private PageHeaderComponent $pageHeaderComponent;

    private bool $basedOnCtype = true;

    public function __construct($view_id, $basedOnCtype = true) {

        $this->coreModel = CoreModel::getInstance();

        $this->basedOnCtype = $basedOnCtype;

        $this->viewCtypeObj = (new Ctype)->load("views");

        $this->viewData = $this->coreModel->nodeModel($this->viewCtypeObj->id)
            ->id($view_id)
            ->loadFirstOrFail();
        
        $lang = Application::getInstance()->user->getLangId();
        $field_name = "name";
        if(!empty($lang) && $lang != "en"){
            $field_name = "name_" . $lang;
        }
        $this->viewData->name = $this->viewData->{$field_name};
        
        $this->extensionsComponent = new ExtensionsComponent($this->viewData);
        $this->viewData->extends = $this->extensionsComponent->loadExtended();

        $this->ctypeObj = (new Ctype)->load($this->viewData->ctype_id);
        
        $this->permissions = Application::getInstance()->user->getCtypePermission($this->ctypeObj->id);

        $this->filterationPanelComponent = new FilterationPanelComponent($this->ctypeObj, $this->viewData);
        $this->bulkDeleteComponent = new BulkDeleteComponent($this->ctypeObj, $this->permissions);
        $this->verificationComponent = new VerificationComponent($this->ctypeObj);
        $this->UpdateStatusComponent = new UpdateStatusComponent($this->viewData, $this->ctypeObj);
        $this->logComponent = new logComponent($this->ctypeObj, $this->permissions);
        $this->loadingComponent = new LoadingComponent();
        $this->columnSettingsComponent = new columnSettingsComponent($this->viewData);
        
        $this->mainTableComponent = new MainTableComponent($this->viewData, $this->ctypeObj, $this->permissions);
        $this->paginationComponent = new PaginationComponent($this->viewData);
        $this->pageHeaderComponent = new PageHeaderComponent($this->ctypeObj, $this->viewData, $basedOnCtype);
        $this->genericExportComponent = new GenericExportComponent($this->viewData, $this->ctypeObj, $this->permissions, $this->filterationPanelComponent);
        $this->exportComponent = new ExportComponent($this->viewData, $this->permissions, $this->filterationPanelComponent);
        $this->topExtraActionComponent = new TopExtraActionComponent($this->ctypeObj, $this->permissions, $this->viewData, $this->verificationComponent);
        $this->pageTopRowComponent = new PageTopRowComponent($this->ctypeObj, $this->permissions, $this->filterationPanelComponent, $this->topExtraActionComponent, $this->viewData);
        $this->crudComponent = new CrudComponent($this->ctypeObj, $this->permissions);
        $this->noDataMessageComponent = new NoDataMessageComponent();

        if(!empty(\App\Core\Application::getInstance()->user->getLangId()) && \App\Core\Application::getInstance()->user->getLangId() != "en" && !empty($this->ctypeObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()})){
            $this->ctypeObj->title = $this->ctypeObj->{"name_" . \App\Core\Application::getInstance()->user->getLangId()};
        }
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

        ob_start(); 
        
        echo $this->viewData->extends; ?>
        
        <?= $this->extensionsComponent->loadExtendStyle() ?>
        <?= $this->columnSettingsComponent->generateMethods() ?>
        
        <?= Application::getInstance()->view->renderView('Components/LogComponent') ?>
        <?= Application::getInstance()->view->renderView('Components/UpdateStatusComponent') ?>
        
        <template id="tpl-main">
            <div>
                <?= $this->extensionsComponent->loadExtendedHtml() ?>
                <?= $this->UpdateStatusComponent->generateModal() ?>
                <?= $this->loadingComponent->generateModal() ?>
                <?= $this->bulkDeleteComponent->generateModal() ?>
                <?= $this->logComponent->generateModal() ?>
                <?= $this->columnSettingsComponent->generateModal() ?>
                <?= $this->filterationPanelComponent->generateModal() ?>
                <?= $this->pageHeaderComponent->generate() ?>
                <?= Application::getInstance()->session->flash() ?>

                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body p-1">
                                
                                <div class="row">

                                    <?= $this->filterationPanelComponent->generateQuickAccess() ?>
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

        <script>

            var vm = new Vue({
                mixins: [(typeof mix == 'undefined' ? [] : mix)],
                el: '#vue-cont',
                components: {
                    Multiselect: window.VueMultiselect.default
                },
                template: '#tpl-main',
                data: <?= $this->getDataObject() ?>,
                beforeDestroy() {
                    document.removeEventListener("keydown", this._keyListener);
                },
                mounted(){
                    <?= $this->mountedScript() ?>
                },
                methods: {
                    
                    <?= $this->loadingComponent->generateMethods() ?>
                    <?= $this->UpdateStatusComponent->generateMethods() ?>
                    <?= $this->UpdateStatusComponent->generateButtonActions() ?>
                    <?= $this->crudComponent->generateMethods(); ?>
                    <?= $this->mainTableComponent->generateMethods() ?>
                    <?= $this->topExtraActionComponent->generateMethods() ?>
                    <?= $this->exportComponent->generateMethods() ?>
                    <?= $this->genericExportComponent->generateMethods() ?>
                    <?= $this->filterationPanelComponent->prepareFilterConditionMethod() ?>
                    <?= $this->filterationPanelComponent->generateFilterMethod() ?>
                    <?= $this->filterationPanelComponent->generateResetFilterMethod() ?>
                    <?= $this->logComponent->generateMethods() ?>
                    <?= $this->bulkDeleteComponent->generateMethod() ?>
                    <?= $this->UpdateStatusComponent->generateButtonActions() ?>
                    <?= (new jsHelperMethods())->generate() ?>
                    <?= $this->filterationPanelComponent->generateViewFilterRefereshList() ?>
                },
            watch: {
                <?= $this->mainTableComponent->watchScript() ?>
            },
            computed:{
                    <?= $this->filterationPanelComponent->generateFilterFieldVisibility(); ?>
                    <?= $this->filterationPanelComponent->generateFilterFieldDependency(); ?>
                }

            });

            <?= $this->extensionsComponent->loadExtendScript() ?>

        </script>

        <?php

        $data = [
            'title' => ($this->basedOnCtype ? $this->ctypeObj->name : $this->viewData->name),
            'script' => ob_get_clean(),
        ];
        
        return $data;

    }


    private function generateContent(){

        $result = "";
        $custom_body = "";

        if(_strlen($this->viewData->extends) > 0) {
                
            if(_strpos($this->viewData->extends, 'id="tpl-result-component"') !== false){
                
                $custom_body .= '<result-component :records="records" ctype-id="' . $this->ctypeObj->id . '" ></result-component>';

            }
        }

        if(_strlen($custom_body) > 0){
            $result .= $custom_body;
        } else {
            $result .= $this->mainTableComponent->generate();
            $result .= $this->paginationComponent->generate();
        }

        return $result;
    }

    private function getDataObject(){
        
        $result = [];
        
        $result["mainCtypeId"] = $this->ctypeObj->id;
        $result["mainCtypeName"] = $this->ctypeObj->name;
        $result["filtersList"] = [];
        $result = array_merge($result, $this->UpdateStatusComponent->getDataObject());
        $result = array_merge($result, $this->logComponent->getDataObject());
        $result = array_merge($result, $this->loadingComponent->getDataObject());
        $result = array_merge($result, $this->mainTableComponent->getDataObject());
        $result = array_merge($result, $this->paginationComponent->getDataObject());
        $result = array_merge($result, $this->bulkDeleteComponent->getDataObject());
        $result = array_merge($result, $this->exportComponent->getDataObject());
        $result = array_merge($result, $this->filterationPanelComponent->getDataObject());

        $result = json_encode($result);

        $result = substr($result, 0, -1);

        $result .= ", moment: moment";
         
        $result .= "}";
        
        return $result;
    }


    private function mountedScript(){
        
        ob_start(); ?>
        
        var self = this;

        $('#cardfiltration').hide()

        <?= (new KeyBindsComponent())->generate() ?>

        <?php

        foreach($this->viewData->filters as $filter){
            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
            $fieldFullName = $thisField->ctype_id . "_" . $thisField->name;
            if($filter->is_hidden != true && $thisField->field_type_id == "date"): ?>

                $("#<?= $fieldFullName ?>").datepicker({
                    dateFormat: "dd/mm/yy",
                    onSelect:function(selectedDate, datePicker) {
                        self.<?= $fieldFullName ?> = selectedDate;
                    },
                });   
                $("#<?= $fieldFullName ?>_2nd_value").datepicker({
                    dateFormat: "dd/mm/yy",
                    onSelect:function(selectedDate, datePicker) {            
                        self.<?= $fieldFullName ?>_2nd_value = selectedDate;
                    },
                });   
            <?php endif; ?>
        <?php } ?>

        <?= $this->filterationPanelComponent->finalizeParameterValues() ?>

        this.filter();

        <?php

        return ob_get_clean();
    }

}
