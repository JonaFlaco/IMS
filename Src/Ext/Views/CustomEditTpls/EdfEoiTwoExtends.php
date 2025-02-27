<script>
    var mix = {
        data: {  
            email_verification_modal_open: false,
            email_verified: false,
        },
    };
</script>



<template id="tpl-email-verification-component">


    <div class="modal fade" id="verify_email_modal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scrollableModalTitle">Confirme su dirección de correo electrónico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
            
                <div class="alert alert-warning" role="alert">
                    <i class="dripicons-warning me-2"></i> 
                    Es importante proporcionar su dirección de correo electrónico correcta, ya que se utilizará para enviarle notificaciones sobre sus solicitudes durante todo el proceso.
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección de correo electrónico: </label>
                    <div class="input-group">
                        <input type="text" class="form-control" :disabled="sending_code_loading" v-model="email_address" placeholder="Email address" aria-label="Recipient's username">
                        <button class="btn btn-dark" :disabled="sending_code_loading || !email_address || timerStarted" @click="sendCode" type="button">
                            <span v-if="timer > 0">
                                Reenviar código de confirmación ({{ timer }})
                            </span>
                            <span v-else-if="is_code_sent">
                                Reenviar código de confirmación
                            </span>
                            <span v-else>
                                Enviar código de confirmación
                                <span v-if="sending_code_loading">...</span>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="mb-3" v-if="is_code_sent">
                    <label class="form-label">Ingrese el código</label>
                    <input type="text" :disabled="verifying_code_loading" class="form-control" v-model="code" aria-label="Recipient's username">
                </div>

                <div v-if="is_code_sent">
                    <span class="mx-1">
                        ¿Aún no has recibido el código de confirmación?
                    </span>
                    <span v-if="timer > 0">
                        Puedes enviar otro código en <strong>{{timer}}</strong> segundos
                    </span>
                    <span v-else>
                        Puedes enviar otro código
                    </span>
                </div>

                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="verifying_code_loading" class="btn btn-primary" v-if="is_code_sent" @click="verifyEmail">Verificar y enviar solicitud</button>
                    <button type="button" :disabled="verifying_code_loading" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


</template>

<script>
Vue.component('email-verification-component', {
template: '#tpl-email-verification-component',
props: ['title','value','name','isRequired'],
data() {
    return {
        email_address: null,
        is_code_sent: false,  
        code: null,
        sending_code_loading: false,
        verifying_code_loading: false,
        timer: 0,
        timeStartValue: 300,
        timerStarted: false,
    }
},
methods: {
    sendCode() {
        
        this.$parent.email  = this.email_address;
        let self = this;
                
        this.sending_code_loading = true;

        let formData = new FormData();
        formData.append('email_address', this.email_address);

        axios({
            method: 'post',
            url: '/externalapi/EdfEoiEmailSendOtp/?response_format=json',
            data:formData,
            headers: {
                'Content-Type': 'form-data',
            }
        })
        .then(function(response){

            if(response.data.status == 'success'){

                self.is_code_sent = true;
                self.sending_code_loading = false;
                self.settingTimer();

            } else {
                
                self.sending_code_loading = false;

                $.toast({
                    heading: 'error',
                    text: 'Something went wrong',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });


            }
            
        })
        .catch(function(error){

            self.sending_code_loading = false;
           
            if(error.response != undefined && error.response.data.status == 'failed') {
                $.toast({
                    heading: 'error',
                    text: error.response.data.message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
            
            } else {
                
                $.toast({
                    heading: 'error',
                    text: 'Something went wrong',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
            
            }

        });
        
    },
    settingTimer() {

        let self = this;
        this.timer = this.timeStartValue;
        this.timerStarted = true;

        const myInterval = setInterval(() => {
            self.timer = self.timer - 1;

            if (self.timer == 0) {
                this.timerStarted = false;
                clearInterval(myInterval);
            }

        }, 1000)

    },
    verifyEmail() {
        let self = this;
                
                this.verifying_code_loading = true;
        
                let formData = new FormData();
                formData.append('email_address', this.email_address);
                formData.append('code', this.code);
        
                axios({
                    method: 'post',
                    url: '/externalapi/EdfEoiEmailVerifyOtp/?response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){
        
                    if(response.data.status == 'success'){
        
                        self.verifying_code_loading = false;
                        self.email_address = self.$parent.email ;
                        self.$parent.email_verified = true;
                        var logModal = bootstrap.Modal.getInstance(document.getElementById('verify_email_modal'))
                        logModal.hide();
                        self.$parent.postData();
        
                    } else {
                        
                        self.verifying_code_loading = false;
        
                        $.toast({
                            heading: 'error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
        
        
                    }
                    
                })
                .catch(function(error){
        
                    self.verifying_code_loading = false;
                   
                    if(error.response != undefined && error.response.data.status == 'failed') {
                        $.toast({
                            heading: 'error',
                            text: error.response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    
                    } else {
                        
                        $.toast({
                            heading: 'error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    
                    }
        
                });
                
    },
    beforeSave() {
        
        this.$parent.$refs.form.classList.add('was-validated');
        this.$parent.form_validated = true;

        
        if (!this.$parent.validate() || !this.$parent.$refs.form.checkValidity()) {
            $.toast({
                heading: 'Error',
                text: 'Please enter valid values',
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'error'
            });
            
            return;
        }


        if(this.$parent.isEditMode != true && this.$parent.email_verified != true) {
            
            if(this.$parent.email_verification_modal_open != true) {
                this.email_address = this.$parent.email ;
                myModal = new bootstrap.Modal(document.getElementById('verify_email_modal'), {})
                myModal.show();
            }
            return false;
        } else{

            if(this.$parent.isEditMode == true){
                var myModal = new bootstrap.Modal(document.getElementById('editJustificationModal'), {})
                myModal.show();
                
                setTimeout(function () { 
                    document.getElementById("editJustification").focus();
                }, 500);
                return false;
            }
     
            this.$parent.postDataAction();
            return false;
        }
    },
},
});
</script>