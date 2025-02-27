<?php
use App\Core\Application;

$data = (object)$data; 
$surveyObj = $data->surveyObj;

$isSystemAuthenticated = Application::getInstance()->user->isAuthenticated();


$coreModel = App\Core\Application::getInstance()->coreModel;

if(!empty(\App\Core\Application::getInstance()->user->getLangId())){
    
    $this->langObj = Application::getInstance()->coreModel->nodeModel("languages")
        ->id(\App\Core\Application::getInstance()->user->getLangId())
        ->loadFirstOrFail();
}

$langList = Application::getInstance()->coreModel->nodeModel("languages")
    ->OrderBy("m.sort")
    ->load();

$loggedin_survey_username = Application::getInstance()->survey->getUserName();
    

$languagesWhere = "";
foreach($surveyObj->languages as $itm) {
    if(!empty($languagesWhere))
        $languagesWhere .= ",";
    $languagesWhere .= "'$itm->value'";
}


$languages = Application::getInstance()->coreModel->nodeModel("languages")
    ->where("m.id in ($languagesWhere)")
    ->fields(["id", "name", "is_default"])
    ->load();

$current_url = Application::getInstance()->request->getRequestUrl();
$prevLangLen = (_strlen(Application::getInstance()->request->getParam("lang")));

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

?>

<?php if(($isSystemAuthenticated != true && sizeof($surveyObj->languages) > 1) || $surveyObj->type_id == "protected"): ?>
    <div class="container">

        <header class="d-flex py-1 flex-wrap justify-content-center border-bottom">
            <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                <?php $i = 0; 
                foreach($languages as $item) : 
                    if($item->name != Application::getInstance()->user->getLangId(true)): ?>
                        <?php if ($i++ > 0): ?> • &nbsp;  <?php endif; ?>
                        <a href="<?= $new_url ?>&lang=<?= $item->id ?>"><?= $item->name ?></a> &nbsp;
                    <?php endif;
                endforeach; ?>

                <?php if($surveyObj->allow_multiple_entry && $surveyObj->type_id == "protected"): ?>
                    &nbsp; | &nbsp;
                    <a href="/surveymanagement/list/<?= $surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"><?= t("Regresar") ?></a> &nbsp;
                <?php endif; ?>
            </div>

            
            <ul class="nav nav-pills">
                <?php if($surveyObj->type_id == "protected"): ?>
                    <h5><?= t("Bienvenido") ?> <?= $loggedin_survey_username ?>, <a href="/SurveyManagement/logout/<?= $surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"><?= t("Logout") ?></a></h5>
                <?php endif; ?>
            </ul>
        </header>
    </div>
    <?php endif; ?>
