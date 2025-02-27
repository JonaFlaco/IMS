
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
                    <label class="form-label">Enter OTP</label>
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
                    <button type="button" :disabled="verifying_code_loading" class="btn btn-secondary" data-bs-dismiss="modal">Cerca</button>
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
        timeStartValue: 60,
        timerStarted: false,
    }
},
methods: {
    sendCode() {
        
        this.$parent.aplicant_email = this.email_address;
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
                        self.email_address = self.$parent.aplicant_email;
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
                this.email_address = this.$parent.aplicant_email;
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



<template id="tpl-consent-sharing-component">


<div id="div_consent_sharing" class="mb-3 col-md-12">
    <label for="consent_sharing" class="form-label"> ¿Está de acuerdo con compartir la información de su empresa con OIM, sus socios implementadores y que esta información sea almacenada bajo las políticas de protección de datos de la OIM?</label>
        <div>
            <div data-simplebar="init" style="max-height: 250px;">
                <div class="simplebar-wrapper" style="margin: 0px;">
                    <div class="simplebar-height-auto-observer-wrapper">
                        <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                            <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                <div class="simplebar-content" style="padding: 0px;">
                                    <div class="custom-control custom-radio ms-3">
                                        <input @change="updateValue" required v-model="vv" type="radio"
                                            name="consent_sharing" id="consent_sharing_0" value="0"
                                            class="form-check-input">
                                        <label for="consent_sharing_0"
                                            class="form-check-label">No</label></div>
                                    <div class="custom-control custom-radio ms-3">
                                        <input @change="updateValue" required type="radio"
                                            name="consent_sharing" v-model="vv" id="consent_sharing_1" value="1"
                                            class="form-check-input">
                                        <label for="consent_sharing_1"
                                            class="form-check-label">Sí</label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 43px;"></div>
                </div>
                <span class="text-danger" v-if="vv != 1"><i class="dripicons-warning me-1"></i>Se requiere consentimiento para continuar</span>
            </div>
        </div>
    </div>


</template>

<script>
Vue.component('consent-sharing-component', {
template: '#tpl-consent-sharing-component',
props: ['title','value','name','isRequired'],
data() {
    return {
        vv: 0,  
    }
},
mounted() {
    this.vv = this.$parent.consent_sharing;
},
methods: {
    updateValue: function (event) {
        this.$parent.consent_sharing = this.vv;
    },
},
});
</script>



<template id="tpl-consent-fam-component">

<div id="div_consent_fam" class="mb-3 col-md-12">
    <label for="consent_fam" class="form-label"> Al aceptar este texto, entiendo que todos los servicios y asistencia de la OIM y sus asociados son gratuitos. La OIM y sus asociados deben tratar respetuosamente a las personas a las que prestamos servicios. Si la OIM y sus asociados solicitan pagos, favores o actividades sexuales a cambio de servicios y/o le tratan a usted o a otras personas de manera irrespetuosa, sírvase ponerse en contacto con la OIM por correo electrónico en <a href="mailto:smecescucha@iom.int">smecescucha@iom.int</a></label>
        <div>
            <div data-simplebar="init" style="max-height: 250px;">
                <div class="simplebar-wrapper" style="margin: 0px;">
                    <div class="simplebar-height-auto-observer-wrapper">
                        <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                            <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                <div class="simplebar-content" style="padding: 0px;">
                                    <div class="custom-control custom-radio ms-3">
                                        <input @change="updateValue" required v-model="vv" type="radio"
                                            name="consent_fam" id="consent_fam_0" value="0"
                                            class="form-check-input">
                                        <label for="consent_fam_0"
                                            class="form-check-label">No</label></div>
                                    <div class="custom-control custom-radio ms-3">
                                        <input @change="updateValue" required type="radio"
                                            name="consent_fam" v-model="vv" id="consent_fam_1" value="1"
                                            class="form-check-input">
                                        <label for="consent_fam_1"
                                            class="form-check-label">Sí</label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 43px;"></div>
                </div>
                <span class="text-danger" v-if="vv != 1"><i class="dripicons-warning me-1"></i>Se requiere consentimiento para continuar</span>
            </div>
        </div>
    </div>
    

</template>

<script>
Vue.component('consent-fam-component', {
template: '#tpl-consent-fam-component',
props: ['title','value','name','isRequired'],
data() {
    return {
        vv: 0,  
    }
},
mounted() {
    this.vv = this.$parent.consent_fam
},
methods: {
    updateValue: function (event) {
        this.$parent.consent_fam = this.vv;
    },
},
});
</script>



<template id="tpl-consent-data-protection-component">


    <div id="div_consent_dataprotection" class="mb-3 col-md-12">
        <label for="consent_dataprotection" class="form-label"> He leído y estoy de acuerdo con  <a href="javascript: void(0);" data-bs-toggle="modal" data-bs-target="#dataprotection_modal">la política de protección de datos de OIM</a></label>
        <div>
            <div data-simplebar="init" style="max-height: 250px;">
                <div class="simplebar-wrapper" style="margin: 0px;">
                    <div class="simplebar-height-auto-observer-wrapper">
                        <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                            <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                <div class="simplebar-content" style="padding: 0px;">
                                    <div class="custom-control custom-radio ms-3">
                                        <input @change="updateValue" required v-model="vv" type="radio"
                                            name="consent_dataprotection" id="consent_dataprotection_0" value="0"
                                            class="form-check-input">
                                        <label for="consent_dataprotection_0"
                                            class="form-check-label">No</label></div>
                                    <div class="custom-control custom-radio ms-3">
                                        <input @change="updateValue" required type="radio"
                                            name="consent_dataprotection" v-model="vv" id="consent_dataprotection_1" value="1"
                                            class="form-check-input">
                                        <label for="consent_dataprotection_1"
                                            class="form-check-label">Sí</label></div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 43px;"></div>
                </div>
                <span class="text-danger" v-if="vv != 1"><i class="dripicons-warning me-1"></i>Se requiere consentimiento para continuar</span>
            </div>
        </div>
    </div>
    
</template>

<script>
Vue.component('consent-data-protection-component', {
template: '#tpl-consent-data-protection-component',
props: ['title','value','name','isRequired'],
data() {
    return {
        vv: 0,  
    }
},
mounted() {
    this.vv = this.$parent.consent_data_protection
},
methods: {
    updateValue: function (event) {
        this.$parent.consent_data_protection = this.vv;
    },
},
});
</script>



<div class="modal fade" id="dataprotection_modal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollableModalTitle">la política de protección de datos de OIM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
            <p>
                Expreso mi decisión informada de cooperar con la Organización Internacional para las Migraciones ("OIM") y participar voluntariamente en el Fondo de Desarrollo Empresarial de la OIM. 
            </p>

            <p>
                Declaro que la información proporcionada es veraz y correcta según mi leal saber y entender. Entiendo que si proporciono información falsa al firmar este formulario, la asistencia proporcionada por la OIM puede ser terminada en cualquier momento. 
            </p>

            <p>
                Comprendo que mis datos personales son necesarios para que la OIM revise y determine mi elegibilidad en el Fondo de Desarrollo Empresarial. He sido informado(a) sobre el(los) propósito(s) especificado(s) y adicional(es) y por la presente autorizo a la OIM y a cualquier persona o entidad autorizada que actúe en nombre de la OIM a recopilar, utilizar, divulgar, eliminar, almacenar y procesar, según sea necesario, los datos personales proporcionados en este formulario. 
            </p>

            <p>
                Entiendo que puedo realizar solicitudes relacionadas con mis datos personales, como acceder, rectificar, eliminar u objetar al procesamiento de los datos personales que comparto con la OIM, incluso si estos derechos no son absolutos. 
            </p>

            <p>
                Comprendo que, si tengo alguna pregunta sobre el procesamiento de mis datos personales, puedo contactar a <a href="mailto:smecescucha@iom.int">smecescucha@iom.int</a>. 
            </p>

            <p>
                Entiendo que la OIM retendrá mis datos personales durante el tiempo necesario para alcanzar los propósitos específicos del procesamiento de datos. Una vez que se hayan cumplido esos propósitos, la OIM almacenará los datos para registros históricos, propósitos legales y solicitudes válidas de información en bases de datos que no se utilicen activamente para su custodia segura. La OIM también puede continuar procesando datos no identificables con fines estadísticos e investigativos, es decir, generar estadísticas para uso interno y externo con datos anonimizados. 
            </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->