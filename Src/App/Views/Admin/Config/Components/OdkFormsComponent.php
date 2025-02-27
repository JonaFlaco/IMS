<template id="tpl-odk-form-component">
    
    <!-- Loading Panel -->
    <div v-if="loading" class="col-xl-3 col-lg-6 float-center">
        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        {{loadingMessage}}
    </div>
    <!-- End of Loading Panel -->

    <!-- Error in loading Panel -->
    <div v-else-if="errorInLoading">
        Error while loading data.
        <button 
            :disabled="loading"
            @click="refresh"
            type="button" 
            class="btn btn-secondary"
            >
            <i class="mdi mdi-refresh font-16"></i> 
            Retry
        </button>
    </div>
    <!-- End of Error in loading Panel -->
    
    <!-- Main Panel -->
    <div v-else>

        <!-- Top Bar -->
        <div class="row">
            <div class="col-sm-4">                            
                <h4 class="mb-3 header-title">
                    {{ title }}
                </h4>
            </div>

            <div class="col-sm-8">
                <div class="text-sm-end">
                    <button 
                        :disabled="loading"
                        @click="save"
                        type="button" 
                        class="btn btn-primary"
                        >
                        <i class="mdi mdi mdi-content-save font-16"></i> 
                        Save
                    </button>
                </div>
            </div>

        </div>
        <!-- End of Top Bar -->
        

        <!-- Form -->
        <form ref="form">
            
            <div class="mb-3">
                <label class="form-label" for="maxOdkForm">Max ODK Form Size (MB): <span class="ml-1 text-danger">*</span></label>
                <input 
                    :disabled="loading"
                    type="number" 
                    required
                    min="1000"
                    max="100000"
                    class="form-control" 
                    id="maxOdkForm" 
                    v-model="item.sys_odk_form_max_size_in_mb"
                    >
                <div class="invalid-feedback">
                    Enter a valid data. Value must be between (1000 - 100000)
                </div>
            </div>
            
        </form>
        <!-- End of Form -->
            
    </div>
    <!-- End of Main Panel -->
    
</template>


<script type="text/javascript">

    var securityComponent = {

        template: '#tpl-odk-form-component',
        data() {
            return {
                title: 'ODK Forms',
                group_name: 'ODK Forms',
                loading: false,
                loadingMessage: 'Loading, please wait...',
                errorInLoading: false,
                item: {
                    sys_odk_form_max_size_in_mb: 4000,
                },
            }
        },
        props: [],
        mounted() {
            this.refresh();
        },
        methods: {
            async refresh() {
                
                let self = this;
                self.loading = true;
                
                var response = await this.$parent.load(this.group_name);
                
                self.loading = false;

                if(response != false && response.status == 200) {
                    self.item = response.data.result;
                } else {
                    this.errorInLoading = true;
                }
            },
            async save() {
                
                if (!this.$refs.form.checkValidity()) {

                    this.$refs.form.classList.add('was-validated');

                    $.toast({
                        heading: 'Error',
                        text: 'Please enter valid values',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    
                    return;
                } else {
                    this.$refs.form.classList.remove('was-validated');
                }

                let self = this;
                self.loading = true;
                
                let formData = new FormData();
                formData.append('data', JSON.stringify(this.item));
                
                var response = await this.$parent.save(this.item);
                self.loading = false;

            }
        },
        computed: {
            
        },
    }

    Vue.component('odk-form-component', securityComponent)

</script>
