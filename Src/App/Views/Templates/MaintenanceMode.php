<?php use \App\Core\Application; 
$pageTitle = "Maintenance Mode - " . Application::getInstance()->settings->get('APP_TITLE');
$appDescription = Application::getInstance()->settings->get('APP_DESCRIPTION');

$message = Application::getInstance()->settings->get('MAINTENANCE_MODE_MESSAGE');

$data['sett_blank'] = true;
?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div class="mt-5 mb-5 ">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 ">

                    <div class="text-center">
                        <img src="/assets/theme/images/maintenance.svg" height="140" alt="File not found Image">
                        <p class="text-muted">We'll be back, {{ message }}</p>

                        <a href='/user/login'>Click here to Login</a>
                    </div>

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
            message: '<?= $message ?>',
        }
    });
</script>