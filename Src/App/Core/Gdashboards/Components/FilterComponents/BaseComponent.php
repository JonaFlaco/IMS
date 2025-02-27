<?php

namespace App\Core\Gdashboards\Components\FilterComponents;

use App\Core\Application;

class BaseComponent {

    public $filter;
    public $field;
    public function __construct($filter, $field) {
        
        $this->filter = $filter;
        $this->field = $field;

    }

    public function dependency(){
        
        if(!empty($this->filter->dependencies)){
            return sprintf(' v-if="computed_%s" ', $this->uniqueName());
        }
    }

    public function uniqueName(){
        
        $alias = $this->field->ctype_id;

        return $alias . '_' . $this->field->name;
        
    }

    public function title(){
        
        return (!empty($this->filter->custom_title) ? $this->filter->custom_title : $this->field->title);
        
    }

    public function filterChoices($id) {

        return Application::getInstance()->coreModel->getPreloadList("filter_operators","id", "name", array("filter_by_column_name" => "field_type_id", "filter_value" => array($id), "sort_field_name" => "sort"));

    }
}
