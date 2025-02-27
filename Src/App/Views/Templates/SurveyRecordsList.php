<?php

use App\Core\Application;

$data['sett_blank'] = true;

$surveyObj = $data['surveyObj'];
$ctypeObj = $data['ctypeObj'];
$records = $data['records'];

$field_name = (_strlen($ctypeObj->display_field_name) > 0 ? $ctypeObj->display_field_name : "name");

$loggedin_survey_username = Application::getInstance()->survey->getUserName();

$allow_add_new = ($surveyObj->allow_multiple_entry || sizeof($records) == 0);

$languagesWhere = "'en'";
foreach($surveyObj->languages as $itm) {
    $languagesWhere .= ",";
    $languagesWhere .= "'$itm->value'";
}


$languages = Application::getInstance()->coreModel->nodeModel("languages")
    ->where("m.id in ($languagesWhere)")
    ->OrderBy("m.sort")
    ->fields(["id", "name", "is_default"])
    ->load();

$current_url = Application::getInstance()->request->getRequestUrl();
$ur = parse_url($current_url);
$u = [];
if(isset($ur['query'])){
    parse_str($ur['query'], $u);
    if(isset($u['lang'])) {
        unset($u['lang']);
    }
}
$new_url = $ur['path'] . "?" . http_build_query($u);

$flashContent = Application::getInstance()->session->flash();

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    <div>
        <div class="container">

            <header class="d-flex py-1 flex-wrap justify-content-center border-bottom">
                <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                    <?php $i = 0; 
                    foreach($languages as $item) : 
                        if($item->id != Application::getInstance()->user->getLangId(true)): ?>
                            <?php if ($i++ > 0): ?> • &nbsp;  <?php endif; ?>
                            <a href="<?= $new_url ?>&lang=<?= $item->id ?>"><?= $item->name ?></a> &nbsp;
                        <?php endif;
                    endforeach; ?>
                </div>

                <ul class="nav nav-pills">
                    <h5><?= t("Bienvenido") ?> <?= $loggedin_survey_username ?>, <a href="/SurveyManagement/logout/<?= $surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"><?= t("Logout") ?></a></h5>
                </ul>
            </header>
        </div>
        
        <div class="mt-5">
            <div class="row justify-content-center">

                <div class="card col-md-6 d-flex justify-content-center">
                    <div class='card-body p-4'>
                
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <h5><i class="dripicons-lock"></i> <?= $data['title'] ?></h5>
                            </div>
                            <div class="col-md-6 text-end">
                                
                            </div>
                        </div>
                        

                        <?= $flashContent ?>

                        <?php if(empty($records)): ?>
                            <div class="alert alert-info" role="alert">
                                <?= t("No record found") ?>
                            </div>
                        <?php else: ?>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?= t("Code") ?></th>
                                        <th><?= t("Date") ?></th>
                                        <?php if ($surveyObj->allow_edit_record): ?><th></th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php foreach($records as $rec): ?>
                                        <tr>
                                        
                                        <td><?= $rec->{$field_name} ?></td>
                                        <td><?= ($rec->created_date == null ? "" : date_format(date_create($rec->created_date),"d-m-Y")) ?></td>

                                        <?php if ($surveyObj->allow_edit_record): ?>
                                        <td class="text-end">
                                            <a target="_blank" href="/SurveyManagement/fill/<?= $surveyObj->id ?>?record_id=<?= $rec->id ?>&lang=<?= Application::getInstance()->user->getLangId() ?>">
                                                <i class="mdi mdi-pencil"></i>    
                                                <?= t("Editar") ?>
                                            </a>
                                        </td>
                                        <?php endif; ?>
                                        </tr>

                                    <?php endforeach; ?>
                                    
                                </tbody>
                            </table>

                        <?php endif; ?>
                                    



                        <?php if($allow_add_new): ?>
                            <a href="/SurveyManagement/fill/<?=$surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"><?= t("Agregar Registro") ?></a>
                        <?php else: ?>
                            <p><?= t("Este Formulario solo permite una participación") ?></p>
                        <?php endif; ?>
                    <div>

                </div>

            </div>
        </div>

    </div>
</template>


<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            loading: false, 
            error: '<?= $data['error'] ?? '' ?>',
            username: '<?= $data['username'] ?? '' ?>',
            showPassword: false,
        },
        mounted() {
            setTimeout(function () { 
                document.getElementById("username").focus();
            }, 200);
            
        },
        methods: {
            
        }
    })
</script>
