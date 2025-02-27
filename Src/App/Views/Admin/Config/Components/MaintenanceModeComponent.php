<template id="tpl-maintenance-mode-component">
    
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
                <div class="form-check">
                    <input 
                        v-model="item.maintenance_mode_is_active" 
                        type="checkbox" 
                        class="form-check-input" 
                        id="isActive">
                    <label class="form-label" for="isActive">Put site under maintenance mode</label>
                </div>
            </div>

            <p>When enabled, only admins are able to access the app; all other users see the maintenance mode message configured below.</p>
            
            <div v-if="item.maintenance_mode_is_active" class="mb-3">
                <label class="form-label" for="siteUrl">Maintenance mode message<span class="ml-1 text-danger">*</span></label>
                <textarea 
                    :disabled="loading"
                    type="text" 
                    required
                    class="form-control" 
                    id="siteUrl" 
                    aria-describedby="siteUrlHelp"
                    v-model="item.maintenance_mode_message"
                    ></textarea>
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>

        </form>
        <!-- End of Form -->
            
    </div>
    <!-- End of Main Panel -->
    
</template>


<script type="text/javascript">

    var maintenanceModeComponent = {

        template: '#tpl-maintenance-mode-component',
        data() {
            return {
                title: 'Maintenance Mode',
                group_name: 'Maintenance Mode',
                loading: false,
                loadingMessage: 'Loading, please wait...',
                errorInLoading: false,
                item: {
                    maintenance_mode_is_active: false,
                    maintenance_mode_message: '',
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

    Vue.component('maintenance-mode-component', maintenanceModeComponent)

</script>
