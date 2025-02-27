<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class PaginationComponent {

    private $viewData;

    public function __construct($viewData = null) {
        $this->viewData = $viewData;
    }

    public function generate(){
        
        ob_start(); ?>

        <!-- pagination-->
        <nav class="mt-2" v-if="is_loading != 1 && records && records.length > 0">
            <ul class="pagination pagination-rounded justify-content-center mb-0">
                <li class="page-item float-end"><a class="page-link"><?= t("Displaying") ?> {{footerNoOfRecords}}{{selected_status && selected_status.length >= 0 ? ' | <?= t("Selected") ?> ' + selected_status : ''}}</a></li>

                <li v-for="page in paginationButtons" v-bind:class="{'page-item':true, 'active':(page.is_current_page == 1), 'disabled':(page.is_disabled == 1)}">
                    <button role="button" @click="filter(page.pageNo)" class="page-link" >{{page.title}}</button>
                </li>
                
            </ul>
        </nav>

        <div v-if="paginationButtonLoading"class="d-flex justify-content-center">
            <div class="spinner-border" role="status"></div>
        </div>

        <!-- end of pagination-->

        <?php

        return ob_get_clean();

    }

    public function getDataObject(){
        
        $result = [];

        $result["selected_status"] = false;
        $result["paginationButtons"] = [];
        $result["footerNoOfRecords"] = '';
        $result["paginationButtonLoading"] = false;


        $records_per_page = 50;
        if(isset($this->viewData) && isset($this->viewData->pagination_records_per_page)){
            $records_per_page = $this->viewData->pagination_records_per_page;
        } else if (Application::getInstance()->settings->get('views_pagination_records_per_page') !== null){
            $records_per_page = Application::getInstance()->settings->get('views_pagination_records_per_page');
        }
        $result["pagination_records_per_page"] = $this->getParamValue("pagination_records_per_page", (empty($records_per_page) ? 50 : $records_per_page ));
        
        return $result;

    }


    private function getParamValue($key, $defaultValue) {
        $value = Application::getInstance()->request->getParam($key);
        if(_strlen($value) > 0)
            return $value;
        else 
            return $defaultValue;
    }

}