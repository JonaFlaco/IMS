<template id="tpl-fields-validation-component">
</template>

<script>
    
    Vue.component('fields-validation-component', {
        template: '#tpl-fields-validation-component',
        props: ['title','value','name','isRequired'],
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            },
            beforeSave() {

                if(this.$parent.current_fields.sys_is_edit_mode != true) {
                    let name = this.$parent.current_fields.name;

                    if(this.$parent.fields.map((item) => item.name).includes(name)){
                        $.toast({
                            heading: 'Error',
                            text: 'Another field with the same name already exist',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                        return false;
                    }
                }
                 
            },
        },
    });
</script>
