<?php

namespace App\Core\Gdashboards\Components;

use App\Core\Application;
use App\Core\Gctypes\CtypeField;

class MountedComponent {

    private $dashboardObj;

    private $coreModel;

    public function __construct($dashboardObj) {
        $this->dashboardObj = $dashboardObj;

        $this->coreModel = Application::getInstance()->coreModel;
    }

    
    public function getMountedMethod() {

        ob_start(); ?>

        let self = this;
        $('#cardFilteration').hide()
            
        <?php foreach($this->dashboardObj->filters as $filter):
            $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($filter->ctype_id, $filter->field_name); 
            if($filter->is_hidden != true && $thisField->field_type_id == "date"): ?>

                $("#<?= $filter->name ?>_from").datepicker({
                    dateFormat: "dd/mm/yy",
                    onSelect:function(selectedDate, datePicker) {            
                        self.<?= $filter->name ?>_from = selectedDate;
                    },
                });   
                $("#<?= $filter->name ?>_to").datepicker({
                    dateFormat: "dd/mm/yy",
                    onSelect:function(selectedDate, datePicker) {            
                        self.<?= $filter->name ?>_to = selectedDate;
                    },
                });
                <?php endif; ?>
            <?php endforeach; ?>
            
        this.get_data();

        <?php

        return ob_get_clean();
    }


}