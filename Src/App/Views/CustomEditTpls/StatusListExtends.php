<?php

use App\Core\Application;
?>

<?= Application::getInstance()->view->renderView("Components/StyleFieldComponent", []) ?>

<template id="tpl-style1-component" >
    <field-style-component
        :title="title"
        class="col-md-12"
        :value="value"
        :name="name"
        @update="updateValue"
        ref="styleFieldComponent">
    </field-style-component>
</template>

<script>
    Vue.component('style-component', {
        template: '#tpl-style-component',
        props: ['title', 'value', 'name', 'isRequired'],
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            },
        },
    });
</script>
