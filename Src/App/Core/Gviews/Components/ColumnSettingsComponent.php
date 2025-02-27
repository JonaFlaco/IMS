<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;
use App\Core\Gctypes\CtypeField;

class ColumnSettingsComponent {

    private $viewData;
    private $coreModel;
    public function __construct($viewData) {
        
        $this->viewData = $viewData;
        $this->coreModel = Application::getInstance()->coreModel;

    }

    public function generateModal(){
        
        ob_start(); ?>



        <!-- Column Settings Modal -->
        <div id="columnsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-right">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <h4 class="mt-0"><i class="mdi mdi-table-column-plus-after"></i> <?= t("Modificar Columnas") ?></h4>
                            <p><?= t("Check the columns you want to see in the result table") ?>.</p>
                        </div>

                        <div data-simplebar style="max-height: 250px;">
                        
                        <?php foreach($this->viewData->fields as $field){ 

                            if($field->is_hidden == true){
                                continue;
                            }
                            
                            $thisField = null;
                            if(isset($field->field_name)){
                                $thisField = (new CtypeField)->loadByCtypeIdAndFieldName($field->ctype_id, $field->field_name);
                            } 

                            if(isset($thisField) && $thisField->field_type_id == "button"){
                                continue;
                            }

                            $visibility_value = "1";
                            $checked_value = "checked";
                            if(isset($_COOKIE[$this->viewData->id . "_col_" . $field->id]) && $_COOKIE[$this->viewData->id . "_col_" . $field->id] == "0"){
                                $visibility_value = "0";
                                $checked_value = "unchecked";
                            }

                            $lang = \App\Core\Application::getInstance()->user->getLangId();
                            
                            $title_field_name = "title";
                            
                            if(empty($lang) && isset($field->custom_title)) {
                                $title_field_name = "custom_title";
                            } else {
                                if(isset($field->{"custom_title_" . $lang}))
                                    $title_field_name = "custom_title_" . $lang;
                                else if(isset($thisField->{"title_" . $lang}))
                                        $title_field_name = "title_" . $lang;
                                
                            }

                            ?>
                            
                                <div class="form-check">
                                    <input type="checkbox" <?= $checked_value ?> onchange="toggle_table_col(this.id);" value="<?= $visibility_value ?>" class="form-check-input" id="col_<?=$field->id?>">
                                    <label class="form-check-label" for="col_<?= $field->id?>"><?= (isset($field->{$title_field_name}) && !empty($field->{$title_field_name}) ? $field->{$title_field_name} : $thisField->title) ?></label>
                                </div>
                            
                        <?php } ?>

                        </div>
                        
                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-danger m-0 btn-sm" data-bs-dismiss="modal">
                                <i class="mdi mdi-window-close"></i>
                                <?= t("Cerrar") ?>
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
        
        <script>
            function toggle_table_col(col_name)
            {
                var checkbox_val = document.getElementById(col_name).value;
                
                if(checkbox_val != "0")
                {
                    var all_col=document.getElementsByClassName(col_name);
                    for(var i=0;i<all_col.length;i++)
                    {
                        all_col[i].style.display="none";
                    }
                    document.getElementById(col_name+ "_head").style.display = "none";
                    document.getElementById(col_name).value = "0";

                    document.cookie = "<?= $this->viewData->id ?>_" + col_name + " = 0; expires=<?= date("D, d M Y", strtotime("+365 day")) ?> 12:00:00 UTC";

                }
                    
                else
                {
                    var all_col=document.getElementsByClassName(col_name);
                    for(var i=0;i<all_col.length;i++)
                    {
                        all_col[i].style.display="table-cell";
                    }
                    document.getElementById(col_name + "_head").style.display="table-cell";
                    document.getElementById(col_name).value="1";

                    document.cookie = "<?= $this->viewData->id ?>_" + col_name + " = 1; expires=<?= date("D, d M Y", strtotime("+365 day")) ?> 12:00:00 UTC";
                }
            }

        </script>

        <?php

        return ob_get_clean();

    }


}