<?php

use App\Core\Application;
?>

<?= Application::getInstance()->view->renderView('Components/ListOrphanColumnsComponent', (array)$data) ?>

<template id="tpl-orphan-columns-component">
    
    <list-orphan-columns-component 
        :ctype-id="$parent.id">
    </list-orphan-columns-component>
</template>

<script>
    Vue.component('orphan-columns-component', {
        template: '#tpl-orphan-columns-component',
    })
</script>