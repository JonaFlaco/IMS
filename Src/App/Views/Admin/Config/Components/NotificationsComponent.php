<template id="tpl-notifications-component">
    
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
                <h6 class="font-15">Notification Status</h6>
                <div class="custom-control custom-radio">
                    <input 
                        :disabled="loading"
                        type="radio" 
                        id="customRadio1" 
                        name="customRadio" 
                        value="1"
                        class="form-check-input"
                        v-model="item.send_notifications"
                        >
                    <label class="form-label" class="form-check-label" for="customRadio1">Send Notifications</label>
                </div>
                <div class="custom-control custom-radio">
                    <input 
                        :disabled="loading"
                        type="radio" 
                        id="customRadio2" 
                        name="customRadio" 
                        value="0"
                        class="form-check-input"
                        v-model="item.send_notifications"
                        >
                    <label class="form-label" class="form-check-label" for="customRadio2">Disable Notifications</label>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label" for="sms_provider_class">SMS Provider Class: <span class="ml-1 text-danger"></span></label>
                <input 
                    :disabled="loading"
                    type="text" 
                    class="form-control" 
                    id="sms_provider_class" 
                    v-model="item.sms_provider_class"
                    >
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

    var notificationsComponent = {

        template: '#tpl-notifications-component',
        data() {
            return {
                title: 'Notifications',
                group_name: 'Notifications',
                loading: false,
                loadingMessage: 'Loading, please wait...',
                errorInLoading: false,
                item: {
                    send_notifications: false,
                    sms_provider_class: null,
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

    Vue.component('notifications-component', notificationsComponent)

</script>
