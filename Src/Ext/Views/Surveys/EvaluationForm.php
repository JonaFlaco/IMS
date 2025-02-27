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
                            <h2 class="ml-2 mt-2 mb-2"><?php echo $data->title; ?></h2>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-12">
                                <div class="card ribbon-box">
                                    <div class="card-body">
                                        <div id="card_title_intro" class="ribbon ribbon-primary float-start">
                                            <a data-bs-toggle="collapse" href="#card_intro" role="button" aria-expanded="false" aria-controls="card_intro" class="text-white p-0 m-0">
                                                Introducción
                                            </a>
                                        </div>
                                        <div id="card_intro" class="ribbon-content row collapse  pt-3  show">

                                            <p>
                                                Somos el equipo de gestión de casos de la OIM ecuador. La presente entrevista tiene como objetivo recopilar información para la evaluación de su caso ante una posible asistencia. Queremos recordarle que todas nuestras asistencias son gratuitas y nunca solicitamos ningún tipo de pago o favor a cambio de ellas. Además es importante mencionarle que este es un espacio seguro para población lgbtiq+ y otros grupos prioritarios de atención. </p>
                                            <p>
                                                <strong>Es importante que conozca que la entrevista no asegura una asistencia, no tiene que responder a ninguna pregunta que no desee, y puede detener la entrevista cuando guste. Toda la información que nos brinde será de total confidencialidad y se manejará exclusivamente dentro de los programas de OIM. Por favor asegúrese de proveer información verdadera y correcta a su leal saber y entender. Si hace una
                                                    declaración falsa o existe algún tipo de agresión
                                                    hacia la persona gestora del caso de OIM, la
                                                    entrevista y la asistencia podrán cancelarse
                                                    inmediatamente. Cualquier transferencia de información a terceros se hará previo al consentimiento informado del titular de la información.</strong>
                                            </p>

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

<?= $data->script ?>



<script>
    $(document).ready(function() {

        $('.page-title').hide();

    });
</script>