<template id="tpl-update-status-component">

    <!-- Change Status Modal -->
    <div id="UpdateStatusModal" class="modal fade" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
        <form id='form_update_status' ref="form_update_status" v-on:submit.prevent class="was-validated" enctype="multipart/form-data" novalidate  autocomplete="off">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-dark">
                        <h4 class="modal-title" id="dark-header-modalLabel">
                        <?= t("Actualizando estado a: ") ?> {{ (selectedChoice ? selectedChoice.status_name : 'N/A') }}</h4>

                        <button type="button" :disabled="loading" class="btn-close text-white" @click="close"></button>
                    </div>
                    <div class="modal-body">

                        <span v-if="currentStatusText" class="text-dark"><strong><?= t("Estado actual") ?>:</strong> {{ currentStatusText }}</span>

                        <!-- Loading Choices Loader -->
                        <div v-if="loadingChoices" class="text-center">
                            <div class="spinner-border avatar-sm text-primary m-2" role="status"></div>
                        </div>

                        <!-- After Choices are loaded -->
                        <div v-else-if="!selectedChoice">
                            
                            <!-- If Not choice available -->
                            <div v-if="choices.length == 0" class="text-center">
                                <i class="mdi mdi-alert-circle-outline mdi-48px"></i>
                                <p><?= t("No action available") ?></p>
                            </div>

                            <!-- Render list of choices -->
                            <div v-else class="text-center text-dark">
                                <p class="text-start"><?= t("Por favor, elija un estado a continuación") ?>:</p>
                                <button @click="setChoice(x)" v-for="x in choices" class="btn m-2" :class="x.style">
                                    {{ x.status_name }}
                                </button>

                            </div>

                        </div>

                        <!-- If is multi -->
                        <div v-if="selectedChoice">
                            
                            <div class="mt-3">
                                
                                <div class="form-check" v-for="(opt, index) in selectedChoice.reasons_list">
                                    <input 
                                        type="checkbox" 
                                        v-model="selectedReasons" 
                                        :value="opt.id" 
                                        :required="selectedChoice.is_justification_required && selectedReasons.length == 0"
                                        name="reason_item" 
                                        class="form-check-input" 
                                        :id="'reason_item_' + opt.id">
                                    <label class="form-check-label" :for="'reason_item_' + opt.id"> {{ opt.name }}</label>
                                </div>
                                
                                <div v-if="selectedChoice && selectedChoice.reasons_list && selectedChoice.reasons_list.length > 0"class="form-check">
                                    <input 
                                        type="checkbox" 
                                        v-model="selectedReasons" 
                                        value="0" 
                                        :required="selectedChoice.is_justification_required && selectedReasons.length == 0"
                                        name="reason_item" 
                                        class="form-check-input" 
                                        :id="'reason_item_0'">
                                    <label class="form-check-label" for="reason_item_0"> <?= t("Other") ?> </label>
                                </div>
                            </div>

                            
                            <!-- Justification -->
                            <div class="mb-3" v-if="showJustification && this.ctypeId !== 'beneficiaries'">
                                <label class="form-label" for="changeStatusJustification"><?= t("Justification") ?></label>
                                <textarea 
                                    :disabled="loading" 
                                    :required="justificationRequired" 
                                    v-model="justification" 
                                    class="form-control rounded-0" 
                                    row="5"
                                    id="justification"
                                    ></textarea>
                                <div class="invalid-feedback">
                                    <?= t("Justification is required") ?>
                                </div>
                            </div>
                            <div v-if="this.ctypeId == 'beneficiaries' && this.selectedChoice.status_id == '3'">
                            <label class="form-label" for="changeStatusJustification"><?= t("Justificación") ?></label>
                                <select :disabled="loading" 
                                        :required="justificationRequired" 
                                        v-model="justification" 
                                        @change="toggleOtherInput"
                                        class="form-control rounded-0" 
                                        id="justification">
                                    <option value="">Seleccionar</option> 
                                    <option value="Error en los documentos">Error en los documentos</option> 
                                    <option value="No cumple los criterios de seleccion">No cumple los criterios de selección</option> 
                                    <option value="Caso Duplicado">Caso Duplicado</option>
                                    <option value="Caso con perfil previo aprobado">Caso con perfil previo aprobado</option>
                                    <option value="Otro">Otro</option>
                                </select>

                                <input v-if="justification === 'Otro'" 
                                       v-model="otherJustification" 
                                       :required="justificationRequired" 
                                       class="form-control rounded-0"
                                       type="text"
                                       placeholder="Especifique el motivo"
                                       @click.stop  
                                       id="otherJustification">

                            </div>

                            <!-- Actual Date -->
                            <div v-if="selectedChoice.is_actual_date_required" class="mb-3">
                                <label class="form-label" for="actualDate"><?= t("Actual date") ?></label>
                                <input 
                                    :disabled="loading" 
                                    type="date" 
                                    required 
                                    v-model="actualDate" 
                                    class="form-control rounded-0" 
                                    id="actualDate"
                                    ></input>
                                <div class="invalid-feedback">
                                    Actual Date is required
                                </div>
                            </div>

                            <!-- Table of records with progress -->
                            <table class="table"></head><tr>
                            
                                </tr></thead><tbody>
                                <tr v-for="itm in records" :key="itm.id">
                                            <td class="p-1 align-middle"><a :href="'https://ecuadorims.iom.int/beneficiaries/show/' + itm.id" target="_blank">{{ itm.title }}</a></td>

                                            <td class="p-1 align-middle text-center">
                                                <span v-if="itm.progress_id == 2" class="badge bg-success p-2">
                                                    <i class="dripicons-checkmark"></i>
                                                </span>
                                                <span v-else-if="itm.progress_id == 3" class="badge bg-danger p-2">
                                                    <i class="dripicons-warning"></i>
                                                </span>
                                                <span v-else-if="itm.progress_id == 4" class="badge bg-warning p-2">
                                                    <i class="dripicons-warning"></i>
                                                </span>
                                                <span v-else-if="itm.progress_id == 1" class="badge bg-info p-2">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </span>
                                                <span v-else="itm.progress_id == 0" class="badge bg-secondary p-2">
                                                    <i class="mdi mdi-timer"></i>
                                                </span>
                                            </td>
                                            <td class="p-1 align-middle">
                                                <span v-html="itm.error"></span>
                                            </td>
                                            <td class="p-1 align-middle">
                                                <button 
                                                    :disabled="loading" 
                                                    class="btn btn-secondary" 
                                                    v-if="itm.ask_for_confirmation" 
                                                    @click="changeStatus(itm.id, true)"
                                                >
                                                    <?= t("Lo entiendo y continúo") ?>
                                                </button>
                                            </td>
                                        </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>
                    <div class="modal-footer">
                                
                        <button :disabled="loading" type="button" class="btn btn-light" @click="close"><?= t("Cerrar") ?></button>

                        <button :disabled="loading" v-if="selectedChoice" class="btn btn-secondary" @click="clearChoice"><?= t("Atras") ?></button>

                        <button 
                            :disabled="loading" 
                            v-if="selectedChoice"
                            type="button" 
                            @click="changeStatus(null, false)" 
                            class="btn btn-dark"
                            >
                            <span v-if="loading == true" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <?= t("Actualizar estado") ?>
                            <span class="badge bg-info">{{RecordIndex}} / {{RecordCount}}</span>
                        </button>
                        
                    </div>

                </div>
            </div>
        </form>
    </div>

</template>

<script>

    Vue.component('update-status-component', {
        template: '#tpl-update-status-component',
        props: {
            ctypeId: {},
            records: {}, // [ {id: 3, title: 'RR-008475'},...]
            updateTo: {
                default: null,
            },
        },
        data(){
            return {
                loading: false,
                
                justification: '',
                otherJustification: '',
                actualDate: null,

                RecordIndex: 0,
                RecordCount: 0, 
                
                currentStatusText: '',

                choices: [],
                selectedChoice: null,
                selectedReasons: [],
                choiceIsSet: false,
                loadingChoices: false,
            }
        },
        mounted() {
            this.RecordCount = this.records.length;
            
            this.getStatusOptions();
            
            var myModal = new bootstrap.Modal(document.getElementById('UpdateStatusModal'), {
                backdrop: 'static',
                keyboard: false,
            })
            myModal.show();
        },
        methods: {
            close() {
                
                logModal = bootstrap.Modal.getInstance(document.getElementById('UpdateStatusModal'))
                logModal.hide();

                this.$emit('clean-up');
            },
            setChoice(item) {
                this.selectedReasons = [];
                this.selectedChoice = item;
                
                this.choiceIsSet = true;
            },
            clearChoice() {
                this.selectedReasons = [];
                this.selectedChoice = null;
                
                this.choiceIsSet = false;
            },
            async getStatusOptions(){
                
                let self = this;
                self.loadingChoices = true;
                self.statusOptions = [];
                self.currentStatusText = null;

                var response = await axios('/InternalApi/getstatusoptions/' + this.records[0].id + 
                        '?ctype_id=' + this.ctypeId + 
                        '&to_status=' + this.updateTo + 
                        '&response_format=json'
                    ).catch(function(error){

                    self.loadingChoices = false;
                    
                    message = 'Something went wrong';

                    if(error.response != undefined && error.response.data.status == 'failed') {
                        
                        message =  error.response.data.message;
                        
                    }
                    
                    $.toast({heading: 'Error',text: message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});

                });
                
                this.loadingChoices = false;

                if(response && response.status == 200){
                    if(response.data.status == "success") {

                        this.choices = response.data.result;
                        this.choices.forEach((itm) => {
                            itm.reasons_list = JSON.parse(itm.reasons_list);
                        })

                        if(this.updateTo != null && this.choices.length > 0) {
                            this.selectedChoice = this.choices[0];
                        }

                        this.currentStatusText = response.data.currentStatus;
                    } else {
                        $.toast({heading: 'Error',text: "Something went wrong",showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    }
                }
            },

            async changeStatus(content_id, confirmed = false){
    
                if(confirmed == true){
                    this.RecordCount = 1;
                }

                if (!this.$refs.form_update_status.checkValidity()) {
                    $.toast({
                        heading: 'Error',
                        text: 'Please enter valid values',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    
                    return;
                }

                this.RecordIndex = 0;
            
                var count = 0;
                for(var i = 0; i< this.records.length; i++){

                    var itm = this.records[i];
                    
                    if(itm.progress_id == 1 || itm.progress_id == 2) {
                        continue;
                    }

                    if(content_id == null || itm.id == content_id){
                        count++;
                        await this.changeStatus_action(itm, confirmed,true);
                    }

                };
                   
                if (count == 0) {
                    $.toast({
                        heading: 'Success',
                        text: 'All updated',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'success'
                    });
                    
                    return;
                }

                if(this.records.length == 1 && this.records[0].progress_id == 2) {
                    $.toast({
                        heading: 'Success',
                        text: '<?= t("Status updated successfuly") ?>',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'success'
                    });
                    this.close();
                }
            },

            filterOptions() {
                var input, filter, options, option, i, txtValue;
              input = this.justification.toUpperCase();
              options = document.getElementsByTagName("option");
              for (i = 0; i < options.length; i++) {
                  option = options[i];
                  txtValue = option.textContent || option.innerText;
                  if (txtValue.toUpperCase().indexOf(input) > -1) {
                      option.style.display = ""; 
                  } 
              }
            },
            toggleOtherInput() {
                 if (this.justification === 'Otro') {
                     document.getElementById('justification').setAttribute('row', '1'); 
                 } else {
                     document.getElementById('justification').removeAttribute('row');
                 }
            },

            async changeStatus_action(item, confirmed = false, is_bulk = false){
                this.loading = true;
                
                if (!this.$refs.form_update_status.checkValidity()) {
                    $.toast({
                        heading: 'Error',
                        text: 'Please enter valid values',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    this.RecordIndex++;
                    if(is_bulk != true || this.RecordIndex == this.RecordCount){
                        this.loading = false;
                    }
                    return;
                }


                item.progress_id = 1;
                item.error = '';
                
                let self = this;

                var formData = new FormData();
                formData.append('justification', this.justification);
                if(this.justification == 'Otro'){
                    formData.append('justification', this.otherJustification);
                }
                formData.append('confirmed', confirmed ? 1 : 0);
                formData.append('to_status', this.selectedChoice.status_id);
                formData.append('actual_date', this.actualDate);
                formData.append('ctype_id', this.ctypeId);
                formData.append('reasons', this.selectedReasons);

                var response = await axios(
                    {
                        method: 'POST',
                        url: '/InternalApi/updatestatus/' + item.id + '?response_format=json',
                        data: formData,
                        headers: {
                            'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("update_status") ?>',
                        },
                    }).catch(function(error){

                        item.progress_id = 3;
                    
                    message = 'Something went wrong';
                        
                    if(error.response != undefined && error.response.data.message != undefined){
                        message = error.response.data.message;
                    }
                    
                    $.toast({heading: 'Error',text: message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                    
                    item.progress_id = 3;
                    item.error = message;

                });
                
                if(response && response.status == 200) {
                    if(response.data.status == "success"){
                    
                        item.progress_id = 2;
                        item.error = 'Status changed successfuly';
                        item.ask_for_confirmation = 0;
                        
                        var r = {
                            id: item.id,
                            status: response.data.result,
                        };
                        
                        this.$emit('after-update', r)

                    } else if(response.data.status == "warning") {
                        
                        item.progress_id = 4;
                        item.error = response.data.message;
                        item.ask_for_confirmation = 1;
                        item.confirmed = 0;
                        
                    } else {

                        item.progress_id = 3;
                        item.error = 'Something went wrong';
                        
                    }

                    self.RecordIndex++;
                }

                this.loading = false;
            },
        },
        computed: {
            showJustification() {
                return !this.selectedChoice || !this.selectedChoice.reasons_list || this.selectedChoice.reasons_list.length == 0 || this.selectedReasons.includes("0");
            },
            justificationRequired() {
                return this.selectedChoice && this.selectedChoice.is_justification_required && this.showJustification;
            }
        }
    })

</script>