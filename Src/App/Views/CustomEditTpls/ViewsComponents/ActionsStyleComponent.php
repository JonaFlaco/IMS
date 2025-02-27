<template id="tpl-actions-style-component">
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
    Vue.component('actions-style-component', {
        template: '#tpl-actions-style-component',
        props: ['title', 'value', 'name', 'isRequired'],
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            },
        },
    });
</script>
