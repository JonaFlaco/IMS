<template id="tpl-mail-component">
    
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
            
            <div class="row">
            
                <div class="col-md-12 mb-3">
                    <label class="form-label" for="mail_provider_class">Mail Sender: <span class="ml-1 text-danger">*</span></label>
                    <select 
                        :disabled="loading"
                        v-model="item.mail_provider_class" 
                        class="form-select" 
                        id="mail_provider_class">
                        <option value="DefaultEmailSender" > SMTP using default php function </option>
                        <option value="SendEmailWithPhpMailer" > SMTP using PhpMailer library </option>
                        <option value="SendEmailWithSendGrid" > Using SendGrid API </option>
                    </select>
                    
                    <div class="invalid-feedback">
                        Enter a valid data
                    </div>
                </div>

                <div class="col-md-9 mb-3">
                    <label class="form-label" for="server">SMTP Server: <span class="ml-1 text-danger">*</span></label>
                    <input 
                        :disabled="loading"
                        type="text" 
                        required
                        class="form-control" 
                        id="server" 
                        v-model="item.mail_smtp"
                        >
                    <div class="invalid-feedback">
                        Enter a valid data
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="port">Port: <span class="ml-1 text-danger">*</span></label>
                    <input 
                        :disabled="loading"
                        type="text" 
                        required
                        class="form-control" 
                        id="port" 
                        v-model="item.mail_smtp_port"
                        >
                    <div class="invalid-feedback">
                        Enter a valid data
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" for="sendFrom">Send From: <span class="ml-1 text-danger">*</span></label>
                    <input 
                        :disabled="loading"
                        type="text" 
                        required
                        class="form-control" 
                        id="sendFrom" 
                        v-model="item.mail_sendmail_from"
                        >
                    <div class="invalid-feedback">
                        Enter a valid data
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" for="sendFromDisplayName">Send From (Display Name): <span class="ml-1 text-danger">*</span></label>
                    <input 
                        :disabled="loading"
                        type="text" 
                        required
                        class="form-control" 
                        id="sendFromDisplayName" 
                        v-model="item.mail_sendmail_from_name"
                        >
                    <div class="invalid-feedback">
                        Enter a valid data
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" for="username">Username: </label>
                    <input 
                        :disabled="loading"
                        type="text" 
                        class="form-control" 
                        id="username" 
                        v-model="item.mail_username"
                        >
                    <div class="invalid-feedback">
                        Enter a valid data
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" for="password">Password: </label>
                    <input 
                        :disabled="loading"
                        type="password" 
                        class="form-control" 
                        id="password" 
                        v-model="item.mail_password"
                        >
                    <div class="invalid-feedback">
                        Enter a valid data
                    </div>
                </div>
            
            </div>
            
        </form>
        <!-- End of Form -->
            
    </div>
    <!-- End of Main Panel -->
    
</template>


<script type="text/javascript">

    var mailComponent = {

        template: '#tpl-mail-component',
        data() {
            return {
                title: 'Mail',
                group_name: 'Mail',
                loading: false,
                loadingMessage: 'Loading, please wait...',
                errorInLoading: false,
                item: {
                    mail_provider_class: '',
                    mail_smtp: '',
                    mail_smtp_port: '',
                    mail_sendmail_from: '',
                    mail_username: '',
                    mail_password: null
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

    Vue.component('mail-component', mailComponent)

</script>
