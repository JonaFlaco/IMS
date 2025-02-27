<template id="tpl-footer-actions-component">
    <div class="display-inline">
            
        <button @click="openView" v-if="isEditMode && isFieldCollection != true" class="btn btn-secondary">
            <i class="mdi mdi-folder-open"></i> 
            <?= t("Abrir") ?>
        </button>

    </div>
</template>

<script>
    
    Vue.component('footer-actions-component', {
        template: '#tpl-footer-actions-component',
        methods: {
            openView() {
                window.open('/' + this.$parent.id, '_blank').focus();
            }
        },
        computed: {
            isEditMode() {
                return this.$parent.isEditMode;
            },
            isFieldCollection() {
                return this.$parent.is_field_collection;
            }
        }
        
    });
</script>
