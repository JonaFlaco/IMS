<?php

use App\Core\Application; ?>

<template id="tpl-filters-dependencies-component">
    <dependencies-component :title="title" :value="value" :name="name" @update="updateValue" ref="dependencyComponent">
    </dependencies-component>
</template>

<script>
    Vue.component('filters-dependencies-component', {
        template: '#tpl-filters-dependencies-component',
        props: ['title', 'value', 'name', 'isRequired'],
        methods: {
            updateValue: function(value) {
                this.$emit('input', value);
            },
            beforeSave() {
                return this.$refs.dependencyComponent.beforeSave();
            },
        },
    });
</script>



<?= Application::getInstance()->view->renderView("Components/StyleFieldComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/CtypesComponents/DependenciesComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/ViewsComponents/FooterActionsComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/ViewsComponents/ConditionalFormattingComponent", []) ?>
