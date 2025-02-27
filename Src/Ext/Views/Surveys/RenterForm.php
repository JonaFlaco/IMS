<!-- FORMULARIO PARA PODER REGISTRAR LA CASA Y LA INFORMACION DE LOS ARRENDADORES  -->
<?php

use App\Core\Application;

$data['sett_blank'] = true;

$data = (object)$data;
$surveyObj = $data->surveyObj;

$coreModel = App\Core\Application::getInstance()->coreModel;
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