<?php

namespace App\Core\Gviews\Components\FilterComponents;

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
        
        return $this->field->ctype_id . '_' . $this->field->name;
        
    }

    public function title(){
        
        $lang = \App\Core\Application::getInstance()->user->getLangId();
        $title_field_name = "title";
        
        if(empty($lang) && isset($this->filter->custom_title)) {
            $title_field_name = "custom_title";
        } else {

            if(isset($this->filter->{"custom_title_" . $lang}))
                $title_field_name = "custom_title_" . $lang;
            else if(isset($this->filter->custom_title))
                $title_field_name = "custom_title";
            else 
                if(isset($this->field->{"title_" . $lang}))
                    $title_field_name = "title_" . $lang;
            
        }
        
        return (!empty($this->filter->{$title_field_name}) ? $this->filter->{$title_field_name} : $this->field->{$title_field_name});
        
    }

    public function filterChoices($id) {

        return Application::getInstance()->coreModel->getPreloadList("filter_operators","id", "name", array("filter_by_column_name" => "field_type_id", "filter_value" => array($id), "sort_field_name" => "sort"));

    }
}
