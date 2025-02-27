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

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

<?= Application::getInstance()->view->renderView('surveys/surveyTopBar', (array)$data) ?>


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
                                        <img width="250px" height="100px" src="\assets\ext\images\logos\oim_logo.png">
                                    </td>

                                </tr>
                            </table>
                        </center>

                    </td>
                </tr>
                <tr>
                    <td class="mb-2">
                        <br>
                        <center>
                            <!-- <h2 class="ml-2 mt-2 mb-2"><?php echo $data->title; ?></h2> -->
                        </center>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="row mx-auto" style="width: 530px;">
                            <div class="col-12">
                                <div class="card ribbon-box">
                                    <div class="card-body">
                                        <!-- <div id="card_title_intro" class="ribbon ribbon-primary float-start">
                                            <a data-bs-toggle="collapse" href="#card_intro" role="button" aria-expanded="false" aria-controls="card_intro" class="text-white p-0 m-0">
                                                Introducción
                                            </a>
                                        </div> -->
                                        <div id="card_intro" class="ribbon-content row collapse  pt-3  show" style="width: 500px;">

                                            <!-- <p>
                                                A través de este cuestionario recopilaremos información básica para el registro de su caso.
                                            </p>
                                            <p>
                                                La información brindada será tratada con total confidencialidad de acuerdo con las políticas de OIM y será usada sólo para fines de atención humanitaria de OIM ecuador. </p>
                                            <p>
                                                Le informamos que todas las asistencias de la OIM y sus socios son gratuitas y que están sujetas a un proceso de evaluación.
                                            </p>
                                            <p>
                                                <strong>Recuerde inscribirse una sola vez y que es una inscripción por familia.</strong>
                                            </p> -->
                                            <h5>
                                                Les informamos que, siguiendo las instrucciones de la Orden Ejecutiva referida por los Estados Unidos sobre la Reevaluación y el Reajuste de la Asistencia Internacional, hemos tenido que suspender de manera temporal los servicios y las asistencias hasta nuevo aviso. Lamentamos cualquier inconveniente que esto pueda ocasionar.
                                            </h5>
                                            <h5>
                                                Por favor, le solicitamos mantenerse al tanto de cualquier actualización o información relevante únicamente a través de nuestros canales oficiales. Recuerde que la asistencia de OIM es gratuita.
                                            </h5>
                                            <br><p>

                                            
                                            </p>
                                            <h5>
                                                Atentamente,
                                            </h5>
                                            <h5>
                                                Organización Internacional para las Migraciones en Ecuador
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>

            </table>
        </div>

    </div>

    <?php if ($surveyObj->type_id == "protected" && ($surveyObj->allow_multiple_entry || $surveyObj->allow_edit_record) && Application::getInstance()->survey->isLoggedIn()) : ?>
        <h4> <?= t("Bienvenido") ?> <?= $loggedin_survey_username ?>, <a href="/SurveyManagement/logout/<?= $data->surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"> <?= t("Logout") ?> </a></h4>
        <h4><a href="/SurveyManagement/list/<?= $data->surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"> <?= t("Regresar") ?></a></h4>
    <?php endif; ?>

    <br>

    <div id="vue-cont">

    </div>

</div>

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>




<script>
    $(document).ready(function() {

        $('.page-title').hide();

    });
</script>