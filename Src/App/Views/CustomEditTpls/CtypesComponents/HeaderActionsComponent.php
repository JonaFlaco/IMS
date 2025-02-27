<template id="tpl-header-actions-component">
    <div class="display-inline">
            
        <div class="dropdown-divider"></div>
        <button class="dropdown-item" @click="GenerateSQLScript('create')"><i class="mdi mdi-code-braces me-1"></i><?= t("Generate Create SQL Script") ?></button>
        <button v-if="isEditMode && isFieldCollection != true" class="dropdown-item" @click="GenerateSQLScript('delete')"><i class="mdi mdi-code-braces me-1"></i><?= t("Generate Delete SQL Script") ?></button>
        <button v-if="isEditMode && isFieldCollection != true" class="dropdown-item" @click="CreateTpl(0)"><i class="mdi mdi-code-braces me-1"></i><?= t("Create Tpl") ?></button>
        <button v-if="isEditMode && isFieldCollection != true" class="dropdown-item" @click="CreateTpl(1)"><i class="mdi mdi-code-braces me-1"></i><?= t("Create Tpl (overwrite if exist)") ?></button>

        <button :class="{ 'disabled': isLoadingResetIds }" v-if="isEditMode && isFieldCollection != true" class="dropdown-item" @click="resetTableId()"><i class="mdi mdi-refresh me-1"></i><?= t("Reset Table ID") ?></button>

    </div>
</template>

<script>
    
    Vue.component('header-actions-component', {
        template: '#tpl-header-actions-component',
        data() {
            return {
                isLoadingResetIds: false,
            }
        },
        methods: {
            GenerateSQLScript(type){
                if(type == 'delete'){
                    window.open('/ctypes/generate_sql/' + this.id + '?type=delete','_blank');
                } else {
                    window.open('/ctypes/generate_sql/' + this.id,'_blank');
                }
            },
            CreateTpl(overwrite){

                axios({
                    method: 'POST',
                    url: '/ctypes/create_tpl/' + this.id + '?overwrite=' + overwrite + '&response_format=json',
                })
                .then(function(response){
                    if(response.data.status == 'success'){
                        $.toast({
                            heading: 'Success',
                            text: 'Tpl created successfuly',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        return;
                    }
                })
                .catch(function(error){
                    $.toast({
                        heading: 'Error',
                        text: error,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                });
            },

            resetTableId(){
                
                if(!confirm("Are you sure you want to reset identity column with the log for `" + this.id + '`?'))
                    return;

                let self = this;
                self.isLoadingResetIds = true;
                
                axios({
                    method: 'POST',
                    url: '/ctypes/restTableId/' + this.id + '&response_format=json',
                })
                .then(function(response){
                    if(response.data.status == 'success'){
                        self.isLoadingResetIds = false;
                        
                        $.toast({
                            heading: 'Success',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });
                        
                    } else {
                        self.isLoadingResetIds = false;

                        $.toast({
                            heading: 'Error',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        return;
                    }
                })
                .catch(function(error){
                    self.isLoadingResetIds = false;
                    $.toast({
                        heading: 'Error',
                        text: (error.response.data.message) ? error.response.data.message : error,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                });
            }
        },
        computed: {
            isEditMode() {
                return this.$parent.isEditMode;
            },
            isFieldCollection() {
                return this.$parent.is_field_collection;
            },
            id() {
                return this.$parent.id;
            }
        }
        
    });
</script>
