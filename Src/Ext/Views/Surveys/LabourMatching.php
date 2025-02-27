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
                                            Este formulario de matching laboral de la OIM Ecuador tiene como objetivo recopilar información de carácter básico y laboral para identificar perfiles y capacidades de los participantes en nuestros programas de integración socioeconómica, para derivarlos a empresas aliadas con ofertas laborales en sectores específicos.

La información brindada será tratada con total confidencialidad de acuerdo con las políticas de protección de datos de la OIM y solo será enviada a las empresas que nos soliciten derivar perfiles de nuestros programas de empleabilidad o con nuestros donantes para fines de reportería.

La información brindada será mantenida en los registros de OIM por el tiempo que sea necesario para poder cumplir con el objetivo indicado en líneas anteriores.

Como titular de la información tiene el derecho a acceder, corregir o solicitar que sus datos personales sean borrados, así como solicitar que el consentimiento de tratamiento de sus datos personales sea revocado en cualquier momento. Para el ejercicio de este derecho por favor contactarnos a través de la siguiente dirección smecedfecuador@iom.int  

Recuerde llenar el formulario una sola vez y completar todos los campos de este. Les rogamos ser lo más transparentes posible al proporcionar la información solicitada.

Es importante destacar que ni la OIM ni sus socios implementadores influyen en la selección de perfiles ni en las decisiones de contratación de las empresas. Nuestra función se limita a remitir los perfiles de quienes han pasado por procesos de capacitación con la OIM o sus socios y pueden ser de interés para nuestros aliados empresariales.

Recuerda que todos nuestros servicios son gratuitos. Si alguien te pide dinero a cambio de recibir cualquier tipo de asistencia, repórtalo a: smecescucha@iom.int o al 0989465831.
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