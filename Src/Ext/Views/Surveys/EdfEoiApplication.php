<?php

use App\Core\Application;

$data['sett_blank'] = true;

$data = (object)$data;
$surveyObj = $data->surveyObj;

$coreModel = App\Core\Application::getInstance()->coreModel;
/*
if (!empty(\App\Core\Application::getInstance()->user->getLangId())) {

  $this->langObj = Application::getInstance()->coreModel->nodeModel("languages")
    ->id(\App\Core\Application::getInstance()->user->getLangId())
    ->loadFirstOrFail();
}
*/
$langList = Application::getInstance()->coreModel->nodeModel("languages")->load();
$lang = Application::getInstance()->user->getLangId();
$loggedin_survey_username = Application::getInstance()->survey->getUserName();

$loggedin_survey_username = Application::getInstance()->survey->getUserName();

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

<?= Application::getInstance()->view->renderView('surveys/surveyTopBar', (array)$data) ?>

<?php
  $_SESSION["edf_app_online_lang"] = 'en';
 
  $eoi_data = $coreModel->nodeModel('edf_eoi')->where("m.code = '$loggedin_survey_username'")->loadFirstOrFail();
  
 
  ?>
  <?php if ($surveyObj->type_id == "protected" && ($surveyObj->allow_multiple_entry || $surveyObj->allow_edit_record) && Application::getInstance()->survey->isLoggedIn()) : ?>
    <h4> <?= t("Bienvenido") ?> <?= $loggedin_survey_username ?>, <a href="/SurveyManagement/logout/<?= $data->surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"> <?= t("Logout") ?> </a></h4>
    <h4><a href="/SurveyManagement/list/<?= $data->surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"> <?= t("Regresar") ?></a></h4>
  <?php endif; ?>
<div class="col-lg-8" style="margin: 0 auto; float:none;">

  <div class="row pt-2 pb-2">

    <div class="col-lg-12 mb-1">
      <table class="col-lg-12">
        <tr>
          <td>
              <center>
            <table>
                <tr>
                    <td>
                    <img class="me-2" width="160px" src="\assets\ext\images\logos\oim_logo.png">
                    </td>
                    <td>

                    </td>
                    <td>
                    <img class="ms-2" width="200px" src="\assets\ext\images\logos\edf_main_logo.png">
                    </td>
                </tr>
            </table>
            </center>

            
          </td>
        </tr>
        <tr>
          <td class="mb-2">
            <br>
            <center><h2 class="ml-2 mt-2 mb-2"><?php echo $data->title; ?></h2></center>
          </td>
        </tr>
        <tr>
            <td>
            <div class="row">
                     <div class="col-md-8 align-middle mt-3" style="margin: 0 auto; float:none;">
                         <div class="card">

                             <div class="card-body">
                                 <div >
                                     <div >
                                         <div class="text-center">
                                            <h2 class="mt-0"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M8 9a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m4 8H4v-1c0-1.33 2.67-2 4-2s4 .67 4 2zm8-9h-6v2h6zm0 4h-6v2h6zm0 4h-6v2h6zm2-12h-8v2h8v14H2V6h8V4H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2m-9 2h-2V2h2z"/></svg></h2>
                                            <h3 class="mt-0">¡Bienvenido!</h3>
                                         </div>
                                         <div class="text-start mx-4 mt-3">   
                                             <h4><strong class="text-info">EOI Code:</strong>  <?= $eoi_data->code?></h4>
                                             <h4><strong class="text-info">Nombre Comercial: </strong> <?= $eoi_data->business_name?></h4>
                                             <h4><strong class="text-info">Nombre legal: </strong> <?= $eoi_data->legal_name ?></h4>
                                             <h4><strong class="text-info">Nombre representante legal:</strong>  <?= $eoi_data->full_name_legal_rep ?></h4>
                                             <h4><strong class="text-info">Nombre del solicitante:</strong>  <?= $eoi_data->person_applying ?></h4>
                                             <h4><strong class="text-info">Antigüedad de la empresa:</strong>  <?= number_format($eoi_data->operation_years_company, 0) ?> años</h4>
                                         </div>
                                         <div class="text-center">
                                              <p class="w-75 mt-3 mb-2 mx-auto">Verifique sus datos</p>
                                         </div>
                                     </div> <!-- end col -->
                                 </div> <!-- end row -->
                             </div>
                         </div>
                     </div>
              </div>

           
            </td>
        </tr>

      </table>
    </div>

  </div>


  <div id="vue-cont">

  </div>

</div>

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>

<?= $data->script ?>



<script>
  $(document).ready(function() {


    vm.eoi_id = {
      id : <?= $eoi_data->id; ?>,
      name : '<?= $eoi_data->code; ?>'
   };
 



    $('.page-title').hide();
   
  });
</script>


