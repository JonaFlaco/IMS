<?php 
use \App\Core\Application;
$appTitle = Application::getInstance()->settings->get('APP_TITLE');
?>

<?php Application::getInstance()->view->renderView('inc/authTemplate', (array)$data); ?>

<template id="tpl-main">
    <div>
        
        <div class="text-center m-auto">
            <img src="/assets/theme/images/mail_sent.svg" alt="mail sent image" height="64" />
            <h4 class="text-dark-50 text-center mt-4 font-weight-bold">Please check your email</h4>
            <p class="text-muted mb-4">
                An email has been send to <b>{{ email }}</b>.
                Please check for an email from <b>{{ appTitle }}</b> and click on the included link to
                reset your password. 
            </p>
        </div>

        <footer class="footer footer-alt">
            <a class="btn btn-link" href="/"><i class="mdi mdi-home me-1"></i> Back to Home</a>
        </footer>

    </div>
</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            appTitle: '<?= x($appTitle) ?>',
            email: '<?= x($data['email'] ?? '') ?>'
        }
    })
</script>
