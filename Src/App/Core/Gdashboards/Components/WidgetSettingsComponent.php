<?php

namespace App\Core\Gdashboards\Components;

use App\Core\Application;

class WidgetSettingsComponent {

    private $dashboardObj;

    private $coreModel;

    public function __construct($dashboardObj) {
        
        $this->dashboardObj = $dashboardObj;
        
        $this->coreModel = Application::getInstance()->coreModel;

    }

    public function generateModal(){
        
        ob_start(); ?>


        <!-- ColumnsRight modal content -->
        <div id="WidgetsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-right">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <h4 class="mt-0"><i class="mdi mdi-table-column-plus-after"></i> Show/Hide Widgets</h4>
                            <p>Uncheck the widgets you don't want to see in the dashboard.</p>
                        </div>

                        <div data-simplebar style="max-height: 250px;">

                        <?php foreach($this->dashboardObj->widgets as $widget): 

                            if($widget->is_hidden == true){
                                continue;
                            }
                            

                            $visibility_value = "1";
                            $checked_value = "checked";
                            if(isset($_COOKIE[$this->dashboardObj->id . "_widget_" . $widget->id . ""]) && $_COOKIE[$this->dashboardObj->id . "_widget_" . $widget->id . ""] == "0"){
                                $visibility_value = "0";
                                $checked_value = "unchecked";
                            }

                            ?>
                                <div class="form-check">
                                    <input type="checkbox" <?= $checked_value ?> @change="toggle_widget('<?= $widget->id ?>','<?= $widget->id ?>');" value="<?= $visibility_value ?>" class="form-check-input" id="widget_<?= $widget->id ?>">
                                    <label class="form-check-label" for="widget_<?= $widget->id ?>"><?= $widget->name ?></label>
                                </div>
                                
                        <?php endforeach; ?>

                        </div>
                        
                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-block btn-danger m-0 btn-sm" data-bs-dismiss="modal">
                                <i class="mdi mdi-window-close"></i>
                                Close
                            </button>
                        </div>
                        


                    </div>
                </div>
            </div>
        </div>


        <?php

        return ob_get_clean();
    }

    
    public function generateMethods(){
        
        ob_start(); ?>
    
        toggle_widget(widget_id, widget_name){
            var checkbox_val = document.getElementById('widget_' + widget_id).value;
            
            if(checkbox_val != "0")
            {
                document.getElementById('widget_' + widget_id).value = "0";
                document.getElementById('chart_parent_' + widget_name).style.display="none";
                document.cookie = "<?= $this->dashboardObj->id ?>_widget_" + widget_id + " = 0; expires=<?= date("D, d M Y", strtotime("+365 day")) ?> 12:00:00 UTC";
            }
                
            else
            {
                document.getElementById('widget_' + widget_id).value = "1";
                document.getElementById('chart_parent_' + widget_name).style.display="block";
                document.cookie = "<?= $this->dashboardObj->id ?>_widget_" + widget_id + " = 1; expires=<?= date("D, d M Y", strtotime("+365 day")) ?> 12:00:00 UTC";
                eval('this.get_data_' + widget_name + '()');
            }
        },

        getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        },
                    

        <?php

        return ob_get_clean();

    }


}