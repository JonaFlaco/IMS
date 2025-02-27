<?php use App\Core\Application;

$data = (object)$data;    

$pageTitle = 'Import ' . (isset($data->ctype_obj) ? $data->ctype_obj->name : "");
?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>
    
<template id="tpl-main">

    <div>
        <div id="PostErrorModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-filled bg-danger">
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <i class="dripicons-wrong h1"></i>
                            <h4 class="mt-2">Something Went Wrong!</h4>
                        </div>
                        
                        <div id="error-modal-body"></div>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Continue</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ pageTitle }}</h4>
                </div>
            </div>
        </div>     
        

        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <button @click="do_import()" type="button" class="btn btn-primary mb-2 me-1"><i class="mdi mdi-refresh"></i>Import</button>
                        
                        <button @click="select_50()" type="button" class="btn btn-secondary mb-2 me-1"><i class="mdi mdi-refresh"></i> Select Top 50</button>
                        <button @click="select_100()" type="button" class="btn btn-secondary mb-2 me-1"><i class="mdi mdi-refresh"></i>Select Top 100</button>
                        <button @click="un_select_all()" type="button" class="btn btn-secondary mb-2 me-1"><i class="mdi mdi-refresh"></i>Unselect All</button>

                    </div>
                    <div class="col-sm-4">
                        <div class="text-sm-end">
                            <button @click="do_refresh()" type="button" class="btn btn-secondary mb-2 me-1"><i class="mdi mdi-refresh"></i>Refresh</button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div v-if="total_waiting > 0" :class="{'col-md-3':counter_size == 4, 'col-md-4':counter_size == 3, 'col-md-6':counter_size == 2, 'col-md-12':counter_size == 1}">
                        <div class="alert alert-secondary bg-secondary text-white">
                            <strong>
                                <i class="dripicons-clock"></i>
                                WAITING: {{total_waiting}}
                            </strong>
                        </div>
                    </div>

                    <div v-if="total_importing > 0" :class="{'col-md-3':counter_size == 4, 'col-md-4':counter_size == 3, 'col-md-6':counter_size == 2, 'col-md-12':counter_size == 1}">
                        <div class="alert alert-info bg-info text-white">
                            <strong>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                IN PROGRRESS: {{total_importing}}
                            </strong>
                        </div>
                    </div>

                    <div v-if="total_success > 0" :class="{'col-md-3':counter_size == 4, 'col-md-4':counter_size == 3, 'col-md-6':counter_size == 2, 'col-md-12':counter_size == 1}">
                        <div class="alert alert-success bg-success text-white">
                            <strong>
                                <i class="dripicons-checkmark"></i>
                                SUCCESS: {{total_success}}
                            </strong>
                        </div>
                    </div>

                    <div v-if="total_failed > 0" :class="{'col-md-3':counter_size == 4, 'col-md-4':counter_size == 3, 'col-md-6':counter_size == 2, 'col-md-12':counter_size == 1}">
                        <div class="alert alert-danger bg-danger text-white">
                            <strong>    
                                <i class="dripicons-warning"></i>     
                                FAILED: {{total_failed}}
                            </strong>
                        </div>
                    </div>
                    
                </div>
                
                <table class="table table-hover table-striped table-bordered table-centered mb-0">
                    <thead>
                        <tr>
                            <th class="p-0 text-center"><input type="checkbox" @change="do_toggle_checkbox" v-model="toggle_checkbox"></th>  
                            <th class="p-0 text-center">Result</th>
                            <th class="p-0 text-center">Excel Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr @click="do_toggle_single_checkbox(rec.excel_index)" v-for="rec in items" :class="{'bg-info text-white':(rec.selected === true)}">
                            <td class="p-0 text-center">
                                <input :disabled="rec.import_status == 2" type="checkbox" v-model="rec.selected">
                            </td>
                            <td class ="p-0 text-center">
                                
                                <span v-if="rec.import_status == 2" class="badge bg-success p-2">
                                    <i class="dripicons-checkmark"></i> 
                                </span>
                                <span style="cursor: pointer" @click="show_error(rec.excel_index)" v-else-if="rec.import_status == 3" class="badge bg-danger p-2">
                                    <i class="dripicons-warning"></i>
                                </span>
                                <span v-else-if="rec.import_status == 1" class="badge bg-info p-2">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </span>
                                <span v-else="rec.import_status == 0" class="badge bg-secondary p-2">
                                    <i class="mdi mdi-timer"></i> 
                                </span>
                            </td>
                            <td class="p-0 text-center">{{rec.excel_index}}</td>
                        </tr>
                    </tbody>
                </table>

            </div>                    

        </div>
    </div>

</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            pageTitle: '<?= $pageTitle ?>',
            file_name: '<?= $data->file_name;?>',
            toggle_checkbox:'',
            importButtonLoading: false,
            total_waiting:0,
            total_importing:0,
            total_success:0,
            total_failed:0,
            items:[
                <?php foreach($data->items as $index):  ?>
                    {
                      error_message: null,
                      selected: false, 
                      import_status: 0, 
                      excel_index: '<?= $index; ?>'
                    },
                <?php endforeach; ?>
            ],
        },
        mounted(){
            this.update_overall_import_status();
        },
        methods: {
            do_toggle_single_checkbox(excel_index){
                this.items.forEach(function(itm){
                    if(itm.excel_index == excel_index){
                        itm.selected = (itm.selected == true ? false : true);
                    }
                });
            },
            
            do_toggle_checkbox(){
                let self = this;
                this.items.forEach(function(itm){
                    
                    if(itm.import_status == 2)
                        itm.selected = false;
                    else
                        itm.selected = self.toggle_checkbox == true ? true : false;
                });
            },
            un_select_all() {
                this.items.filter((item) => item.selected == true).forEach((item) => {
                    item.selected = false;
                });
            },
            select_100(){
                let found = 0;
                for(i = 0; i < this.items.length; i++){
                    itm = this.items[i];

                    if(itm.selected != true && itm.import_status != 1 && itm.import_status != 2){
                        itm.selected = true;
                        found++;
                        if(found >= 100){
                            break;
                        }
                    }

                }
            },

            select_50(){
                let found = 0;
                for(i = 0; i < this.items.length; i++){
                    itm = this.items[i];

                    if(itm.selected != true && itm.import_status != 1 && itm.import_status != 2){
                        itm.selected = true;
                        found++;
                        if(found >= 50){
                            break;
                        }
                    }

                }
            },
            
            async do_import(){
                
                let self = this;
                let found = 0;
                for(i = 0; i< this.items.length; i++){
                    itm = this.items[i];
                    if(itm.selected == true && itm.import_status != 1 && itm.import_status != 2){
                        itm.import_status = 1;
                        found++;
                        await self.do_import_request(itm);
                    }
                }

                if(found == 0){
                    alert('No record selected to import');
                }

                self.update_overall_import_status();
            },
            do_import_request(itm){
                
                let self = this;
                
                axios({
                    method: 'post',
                    url: '/dataimport/advanced/' + itm.excel_index + '?file_name=' + self.file_name + '&response_format=json',
                    data:null,
                    headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){
                    if(response.data.status == 'success'){
                        itm.import_status = 2;
                        itm.error_message = '';
                        self.update_overall_import_status();
        
                    } else {
                        itm.import_status = 3;
                        itm.error_message = response.data;
                        self.update_overall_import_status();
                    }
                    
                })
                .catch(function(error){

                    if(error.response != undefined &&error.response.data.status == 'failed') {
                        itm.error_message = error.response.data.message;
                    } else {
                        itm.error_message = error;
                    }
                    itm.import_status = 3;
                    
                    self.update_overall_import_status();
                });

                self.update_overall_import_status();
            },
            do_refresh(){
                location.reload()
            },
            show_error(excel_index){
                this.items.forEach(function(itm){
                    if(itm.excel_index == excel_index){
                        document.getElementById('error-modal-body').innerHTML = '<p>' + itm.error_message + '</p>';
                        var myModal = new bootstrap.Modal(document.getElementById('PostErrorModal'), {})
                        myModal.show();
                    }
                });
                
            },
            update_overall_import_status(){
                
                let waiting = 0;
                let importing = 0;
                let success = 0;
                let failed = 0;

                this.items.forEach(function(itm){
                    switch(itm.import_status){
                        case 3:
                            failed += 1;
                            break;
                        case 2:
                            success += 1;
                            break;
                        case 1:
                            importing += 1;
                            break;
                        default:
                            waiting += 1;
                            break;
                    }
                }); 

                this.total_waiting = waiting;
                this.total_importing = importing;
                this.total_success = success;
                this.total_failed = failed;
            }
        },
        computed: {
            counter_size: function() {
                size = 4;
                if(this.total_waiting == 0)
                    size -= 1;
                if(this.total_importing == 0)
                    size -= 1;
                if(this.total_success == 0)
                    size -= 1;
                if(this.total_failed == 0)
                    size -= 1;
                
                if(size == 0)
                    size = 4;

                return size;
            }
        }
    });

</script>