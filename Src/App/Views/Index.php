<?php 
use App\Core\Application; 
$data['sett_load_chart_libraries'] = true;

// $widget1 = new \App\Core\Gdashboards\Widgets(156, "widget_156", 6);

?>


<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>




<template id="tpl-main">
    
    <div>
        <page-title-row-component :title="pageTitle"></page-title-row-component>

        <homepage-widgets-component></homepage-widgets-component>
        
    </div>
</template>

<?= Application::getInstance()->view->renderView('Components/HomepageWidgetsComponent', (array)$data) ?>

<script>

    var vm = new Vue({
        
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            pageTitle: '<?= t("Bienvenido a IMS") ?>',
        },
        mounted() {
        },
        methods: {
           
        },
    })
</script>
