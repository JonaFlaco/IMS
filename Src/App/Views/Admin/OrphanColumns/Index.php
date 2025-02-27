<?php

use App\Core\Application;

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>
<?= Application::getInstance()->view->renderView('Components/ListOrphanColumnsComponent', (array)$data) ?>

<template id="tpl-main">

    <div>

        <page-title-row-component 
            :title="pageTitle"
            :bread-crumb="breadCrumb">
        </page-title-row-component>
        
        <list-orphan-columns-component>

        </list-orphan-columns-component>

    </div>
</template>

<script>
    var vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            pageTitle: 'Orphan Columns',
            breadCrumb: [
                {title: 'Admin', link: '/admin'},
            ],
        },
        methods: {
            
        }
    })
</script>
