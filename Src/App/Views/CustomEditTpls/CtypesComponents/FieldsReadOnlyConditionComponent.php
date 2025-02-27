<template id="tpl-fields-read-only-condition-component">
    <dependencies-component 
        :title="title"
        :value="value"
        :name="name"
        @update="updateValue"
        ref="dependencyComponent"
        >
    </dependencies-component>
</template>

<script>
    
    Vue.component('fields-read-only-condition-component', {
        template: '#tpl-fields-read-only-condition-component',
        props: ['title','value','name','isRequired'],
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            },
            beforeSave() {
                return this.$refs.dependencyComponent.beforeSave();
            },
        },
    });
</script>
