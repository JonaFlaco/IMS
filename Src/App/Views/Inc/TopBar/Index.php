<?php 
    use App\Core\Application;

    $show_bg_tasks_modal_on_load = isset($_GET['show_bg_tasks_modal_on_load']) && $_GET['show_bg_tasks_modal_on_load'] ? 1 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .header-custom {
    position: sticky;
    top: 0px; 
    z-index: 900;
    background-color: green; 
}

.content-container {
    margin-top: 0px; 
}

.table-container {
    overflow-x: auto;
    max-height: 500px; 
}
.custom-dropdown-menu {
    z-index: 1050; 
}

.dropdown {
    position: relative;
}


    </style>
</head>
<body>

<div id="topbar_cont">

    <bg-tasks-modal-component v-if="topbar_bg_tasks_modal_visivile" @close="handle_close_bg_tasks_modal"></bg-tasks-modal-component>

    <div class="navbar-custom">
        <ul class="list-unstyled topbar-menu float-end mb-0">
            <!-- <language-menu-component></language-menu-component> -->

            <li class="dropdown notification-list topbar-dropdown">
                <a 
                    class="nav-link dropdown-toggle arrow-none" 
                    @click="show_bg_tasks_modal()" 
                    href="javascript: void(0);" 
                    role="button" aria-haspopup="false" 
                    v-tooltip="'My Downloads'"
                    aria-expanded="false">
                    <i class="dripicons-view-apps noti-icon text-primary"></i>
                </a>
            </li>
            

            <!-- <notification-menu-component></notification-menu-component> -->

            <!-- <shortcuts-component></shortcuts-component> -->

            <user-menu-component></user-menu-component>
            
        </ul>

        <button class="button-menu-mobile open-left">
            <i class="mdi mdi-menu"></i>
        </button>
        
        <!-- <top-search-component></top-search-component> -->
    
    </div>
    

</div>


<!-- Components -->
<?= Application::getInstance()->view->renderView('inc/TopBar/Components/NotificationMenuComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('inc/TopBar/Components/UserMenuComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('inc/TopBar/Components/LanguageMenuComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('inc/TopBar/Components/TopSearchComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('inc/TopBar/Components/ShortcutsComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('inc/TopBar/Components/BgTasksModalComponent', (array)$data) ?>

<script>
    var topBarVm = new Vue({
        el: '#topbar_cont',
        data: {
            demoPlatform: {
                isActive: '<?= Application::getInstance()->settings->get('IS_LIVE_PLATFORM') != 1?>',
                dbName: '<?= Application::getInstance()->env->get("DB_NAME") ?>',
                hostName: '<?= Application::getInstance()->env->get("DB_HOST") ?>',
                gitBranch: '<?= Application::getInstance()->git->getCurrentBranch() ?>',
                isAdmin: '<?= Application::getInstance()->user->isAdmin() ?>',
            },
            topbar_bg_tasks_modal_visivile: false,
            show_bg_tasks_modal_on_load: <?= $show_bg_tasks_modal_on_load ?>,
        },
        mounted() {
            if(this.show_bg_tasks_modal_on_load) {
                this.show_bg_tasks_modal();
            }
        },
        methods: {
            show_bg_tasks_modal() {
                this.topbar_bg_tasks_modal_visivile = true;
                setTimeout(function(){
                    var myModal = new bootstrap.Modal(document.getElementById('topbar-bg-tasks-modal'), {
                        backdrop: 'static',
                        keyboard: false,
                    })
                    myModal.show();
                }, 250);
            },
            handle_close_bg_tasks_modal() {
                this.topbar_bg_tasks_modal_visivile = false;
            },
        }
    });
</script>


</body>
</html>
