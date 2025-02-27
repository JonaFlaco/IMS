<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    <div>

    <page-title-row-component :title="item.title" :bread-crumb="breadCrumb"></page-title-row-component>

    <div class="container-fluid">

    <iframe v-if="item.custom_url" :title="item.title" width="100%" height="720px" :src="item.custom_url" frameborder="0" allowFullScreen="true"></iframe>
    <div v-else class="alert alert-warning" role="alert">
        Please provide a custom URL for your Power BI dashboard.
    </div>
        
    </div>
</div>

</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            item: <?= json_encode($data["item"]) ?>,
            breadCrumb: [],
        
       
        }
    });
</script>