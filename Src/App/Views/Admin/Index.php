<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div class="container-fluid">

        
    </div>

</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        
    });
</script>