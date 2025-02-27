<?php 
use App\Core\Application;

$lang_id = Application::getInstance()->user->getLangId(true);
$lang_name = Application::getInstance()->user->getLangName();

$langList = Application::getInstance()->coreModel->nodeModel("languages")
    ->where("isnull(m.is_disabled,0) = 0")
    ->OrderBy("m.sort")
    ->load();

?>


<template id="tpl-bg-tasks-modal-component">

    <div id="topbar-bg-tasks-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="primary-header-modalLabel">
                        <?= t("Mis Descargas") ?>
                        <span v-if="loading"><i class="mdi mdi-spin mdi-loading"></i></span>
                    </h4>
                    
                    <button type="button" class="btn-close btn-close-white" @click="close" aria-hidden="true"></button>
                </div>
                <div class="modal-body pt-0">
                
                    <div>

                        <div class="page-title-box mb-2 mt-2 row">

                            <div class="col-4 d-flex justify-content-left align-items-center">
                                <span class="px-1"><?= t("Actualización Automática") ?></span>
                                <input type="checkbox" v-model="auto_refresh" id="auto_refresh" data-switch="primary"/>
                                <label for="auto_refresh" data-on-label="ON" data-off-label="OFF"></label>
                            </div>

                            <div class="col-8 d-flex flex-row-reverse align-items-center">

                                <div class="btn-group">

                                    <button @click="filter()" type="button" class="btn btn-primary btn-sm">
                                        <i class="mdi mdi-autorenew"></i><span class="fs-6"><?= t("Todos") ?></span> 
                                    </button>    
                                    <button @click="filter(1)" type="button" class="btn btn-secondary btn-sm">
                                        <i class="mdi mdi-clock-time-two-outline"></i><span class="fs-6"><?= t("En Espera") ?></span> 
                                    </button>
                                    <button @click="filter(73)" type="button" class="btn btn-danger btn-sm">
                                        <i class="mdi mdi-cancel"></i><span class="fs-6"><?= t("Fallido") ?></span> 
                                    </button>
                                    <button @click="filter(28)" type="button" class="btn btn-info btn-sm">
                                        <i class="mdi mdi-dots-horizontal"></i><span class="fs-6"><?= t("En curso") ?></span> 
                                    </button>
                                    <button @click="filter(22)" type="button" class="btn btn-success btn-sm">
                                        <i class="mdi mdi-check-outline"></i> <span class="fs-6"><?= t("Completado") ?></span> 
                                    </button>
                                </div>
                            </div>
                       
                           
                            

                        </div>

                        <div v-if="items.length == 0" class="text-center">
                            <h4 class="p-2"><?= t("No hay datos para mostrar") ?> </h4>
                        </div>
                        
                        <table v-else class="table table-sm table-centered mb-0 font-12">
                            <thead class="table-light">
                                <tr>
                                    <th><?= t("Nombre") ?></th>
                                    <th><?= t("Estado") ?></th>
                                    <th><?= t("Fecha de Solicitud") ?></th>
                                    <th><?= t("Tiempo Transcurrido") ?></th>
                                    <th><?= t("Accciones") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr v-for="(item,index) in items" :key="index">
                                    <td>
                                        {{ item.name }}
                                    </td>
                                    <td>
                                        <h5><span class="badge" v-tooltip="item.last_error"  :class="'badge-' + item.theme + '-lighten'">
                                            {{ item.status_name }}

                                            <i v-if="item.status_id == 73" class="mdi mdi-information"></i>
                                        <span></h5>
                                    </td>
                                    <td>
                                        {{ item.created_date_humanify }}
                                    </td>
                                    <td>
                                        <span v-tooltip="'Completed Date: ' + item.completion_date_humanify">
                                            {{ item.elapsed_time ?? "N/A" }}
                                        </span>
                                    </td>
                                    
                                    <td class="table-action">
                                        <div class="d-flex">
                                            <a v-tooltip="'Delete'" :disabled="item.deleting" href="javascript: void(0);" @click="del(item)" class="action-icon" v-if="item.status_id == 73 == 1 || item.status_id == 22 || item.status_id == 73"> 
                                                <i v-if="item.deleting" class="mdi mdi-spin mdi-loading text-warning"></i>
                                                <i v-else class="mdi mdi-trash-can-outline text-danger"></i>
                                            </a>
                                            <a v-tooltip="'Retry'" href="javascript: void(0);" @click="retry(item)" class="action-icon" v-if="item.status_id == 1 || item.status_id == 73"> 
                                                <i class="mdi mdi-play text-success"></i>
                                            </a>
                                            <a v-tooltip="'Download'":href="item.output_file_link" class="action-icon" v-if="item.output_file_link"> <i class="mdi mdi-download text-primary"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                                
                            </tbody>
                        </table>
                    </div>
                     
                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-light" @click="close"><?= t("Cerrar") ?></button>
                    <button type="button" :disabled="loading" @click="refresh()" class="btn btn-primary">
                        <?= t("Actualizar") ?>
                        <span v-if="loading">...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
            
</template>


<script type="text/javascript">

    var component = {

        template: '#tpl-bg-tasks-modal-component',
        data() {
            return {
                items: [],
                loading: false,
                auto_refresh: true,
                timer: null,
                filter_status_id: null,
            }
        },
        async mounted() {
            this.refresh();

            if(this.auto_refresh)
                this.autoRefresh();
        },
        watch: {
            auto_refresh: function(value) {
                if(value != true) {
                    clearInterval(self.timer);
                } else {
                    this.autoRefresh();
                }
            },
        },
        methods: {
            filter(status_id = null) {
                this.filter_status_id = status_id;
                this.refresh();
            },
            autoRefresh() {
                let self = this;

                this.timer = setInterval(function() {
                    
                    if(self.auto_refresh) {
                        self.refresh();
                    } else {
                        clearInterval(self.timer);
                    }
                }, 10000);
            },
            close() {
                this.items = []
                this.auto_refresh = false;
                var logModal = bootstrap.Modal.getInstance(document.getElementById('topbar-bg-tasks-modal'))
                logModal.hide();
                this.$emit('close');
            },
            async refresh() {
                let self = this;
                self.loading = true;
                
                var response = await axios.get('/InternalApi/BgTasksGet/' + this.filter_status_id + '?response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                        return false;
                    });

                if(response.status == 200) {
                    self.items = response.data.result;
                }

                self.loading = false;

                return false;
            },
            async cancel(item) {
                
                let self = this;
                item.cancelling = true;
                
                var response = await axios.post('/InternalApi/BgTasksCancel/' + item.id + '?response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                        return false;
                    });

                if(response.status == 200) {
                    $.toast({
                            heading: 'Success',
                            text: 'Task cancelled successfuly',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });
                    self.items = self.items.filter((e) => e.id != item.id);
                }

                item.cancelling = false;

                return false;
            },
            async del(item) {
                
                let self = this;
                item.deleting = true;
                
                var response = await axios.post('/InternalApi/BgTasksDelete/' + item.id + '?response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                        return false;
                    });

                if(response.status == 200) {
                    self.items = self.items.filter((e) => e.id != item.id);
                    $.toast({
                            heading: 'Success',
                            text: 'Task deleted successfuly',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });
                }

                item.deleting = false;

                return false;
            },
            async retry(item) {
                
                let self = this;
                
                var response = await axios.post('/InternalApi/BgTasksRun/' + item.id + '?response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                        return false;
                    });

                if(response.status == 200) {
                    
                    $.toast({
                            heading: 'Success',
                            text: 'Task started successfuly',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });
                }

                item.deleting = false;

                return false;
            },
        }
    }

    Vue.component('bg-tasks-modal-component', component)

</script>
