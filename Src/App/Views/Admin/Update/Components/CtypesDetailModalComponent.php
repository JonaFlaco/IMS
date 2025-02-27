<template id="tpl-ctypes-detail-modal-component">

    <div id="ctypeDetailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="dark-header-modalLabel">
                            <span v-if="previousCtypeList && previousCtypeList.length > 0" class="cursor-pointer" @click="gotoPreviousCtype()"><i class="mdi mdi-keyboard-backspace"></i></span> Content-Type: {{item?.title ?? 'N/A'}}</h4>
                    <button type="button" @click="close" class="btn-close" aria-hidden="true"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <!-- Left Panel -->
                        <div class="col-lg-5">

                            <!-- Content-Type Icon -->
                            <img :src="item?.icon" class="img-fluid" style="max-width: 280px;" alt="Content-Type Icon" />
                        </div>
                        
                        <!-- Right Panel -->
                        <div class="col-lg-7">

                            <h3 class="mt-0">{{item?.title}}</h3>
                            <p class="mb-1">Last Update: {{item?.last_update_date}}</p>
                            
                            <div class="mt-3">
                                <h4 v-if="item?.installed"><span class="font-13 badge bg-success">Installed</span></h4>
                                <h4 v-if="!item?.installed"><span class="font-13 badge bg-danger">Not Installed</span></h4>
                            </div>

                            <div class="mt-4">
                                <h6 class="font-14">Category:</h6>
                                <p>{{item?.category ?? 'N/A'}}</p>
                            </div>

                            <div v-if="item?.exportedRecordsCount > 0" class="mt-4">
                                <h6 class="font-14">Exported Records:</h6>
                                <p>{{item?.exportedRecordsCount}}</p>
                            </div>

                            <div class="mt-4">
                                <h6 class="font-14">Module:</h6>
                                <p>{{item?.module ?? 'N/A'}}</p>
                            </div>

                            <div class="mt-4">
                                <h6 class="font-14">Description:</h6>
                                <p>{{item?.description}}</p>
                            </div>


                        </div>
                    </div>


                    <h4>Dependancies</h4>
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <tbody>
                                <tr v-for="dep in item?.dependancies" class="cursor-pointer" @click="openDetail(dep.name)">
                                    <td>
                                        <img class="mr-2 rounded-circle" :src="item?.icon" width="40" alt="Generic placeholder image">
                                        <p class="m-0 d-inline-block align-middle font-16">
                                            <span class="mt-0 mb-1">{{dep.title}}</span>
                                            <br />

                                            <span v-if="dep.installed" class="font-13 badge bg-success">Installed</span>
                                            <span v-if="!dep.installed" class="font-13 badge bg-danger">Not Installed</span>
                                        </p>
                                    </td>
                                    
                                    <td>
                                        <span class="text-muted font-13">Category</span> <br/>
                                        <p class="mb-0">{{dep.category}}</p>
                                    </td>

                                    <td>
                                        <span class="text-muted font-13">Last Update By</span> <br/>
                                        <p class="mb-0">{{dep.last_updated_user_name ?? dep.created_user_name}}</p>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                
                
                </div>

                <div class="modal-footer">
                    <!-- Close Button -->
                    <button 
                        type="button" 
                        class="btn btn-light" 
                        @click="close"
                        >
                        <?= t("Cerrar") ?>
                    </button>

                    <!-- Install/Update Button -->
                    <button 
                        type="button" 
                        class="btn btn-primary" 
                        @click="install"
                        >
                        <span>{{installStatusText}}</span>
                    </button>
                    
                    <!-- Insert Date Button -->
                    <button 
                        v-if="item?.exportedRecordsCount > 0" 
                        type="button" 
                        class="btn btn-primary" 
                        @click="importData(item?.name)"
                        >
                        <?= t("Import Data") ?>
                    </button>
                </div>

            </div>
            
        </div>
    </div>

</template>


<script type="text/javascript">

    var CtypesDetailModalComponent = {

        template: '#tpl-ctypes-detail-modal-component',
        data() {
            return {
            }
        },
        props: ['item', 'previousCtypeList'],
        methods: {
            openDetail(name) {
                this.$emit('open-detail', name);
            },
            close(name) {
                this.$emit('close');
            },
            gotoPreviousCtype() {
                this.$emit('goto-previous-ctype');
            },
            async install() {
                let self = this;

                this.item.status_id = 1;

                var response = await axios.get('/InternalApi/systemupdate/' + this.item.name + '?cmd=install_ctype&response_format=json'
                ).catch(function(error){
                        
                    message = error;
                    if(error.response != undefined && error.response.data.message != undefined){
                        message = error.response.data.message;
                    }
                    
                    self.item.status_id = 3;
                    
                    $.toast({
                        heading: 'Error',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });   
                });

                if(response.status == 200) {
                    this.item.status_id = 0;
                    this.item.installed = 1;
                }
            },
            async importData(name) {
                
                this.item.status_id = 1;
                    
                let self = this;

                var response = axios.get( '/InternalApi/systemupdate/' + name + '?cmd=insert_records&response_format=json',
                    ).catch(function(error){
                        
                        var message = error;
                        
                        if(error.response != undefined && error.response.data.message != undefined){
                            message = error.response.data.message;
                        }
                        
                        self.item.status_id = 3;
                        
                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });   
                    });

                if(response.status == 200) {
                    this.item.status_id = 0;
                }

            }
        },
        computed: {
            installStatusText(){
                if(this.item?.status_id == 1)
                    return this.item?.installed ? 'Updating...' : 'Installing...'
                else if (this.item?.status_id == 3)
                    return this.item?.installed ? 'Retry Update' : 'Retry Install'
                else 
                    return this.item?.installed ? 'Update' : 'Install'
                
            },
        },
    }

    Vue.component('ctypes-detail-modal-component', CtypesDetailModalComponent)

</script>
