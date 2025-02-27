<?php 
use App\Core\Application;

$lang_id = Application::getInstance()->user->getLangId(true);
$lang_name = Application::getInstance()->user->getLangName();

$langList = Application::getInstance()->coreModel->nodeModel("languages")
    ->where("isnull(m.is_disabled,0) = 0")
    ->fields(["name"])
    ->useCache("load_all_languages", 86400)
    ->OrderBy("m.sort")
    ->load();

?>


<template id="tpl-language-menu-component">
    
    <?php if(Application::getInstance()->settings->get("enable_multi_language")){ ?>
    <li class="dropdown notification-list topbar-dropdown">
        <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="javascript: void(0);" role="button" aria-haspopup="false" aria-expanded="false">
            <img src="/assets/app/images/languages/<?= e($lang_id) ?>.jpg" alt="user-image" class="me-0 me-sm-1" height="12"> 
            <span class="align-middle d-none d-sm-inline-block"><?= e($lang_name) ?></span> <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i>
        </a>

        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu">

            <?php foreach($langList as $itm){ ?>

                <a href="/user/setlang/<?= e($itm->id) ?>" class="dropdown-item notify-item">
                    <img src="/assets/app/images/languages/<?= e($itm->id) ?>.jpg" alt="user-image" class="me-1" height="12"> <span class="align-middle"><?= e($itm->name) ?></span>
                </a>
                
            <?php } ?>

        </div>
    </li>
    <?php } ?>

</template>


<script type="text/javascript">

    var component = {

        template: '#tpl-language-menu-component',
        methods: {
        }
    }

    Vue.component('language-menu-component', component)

</script>
