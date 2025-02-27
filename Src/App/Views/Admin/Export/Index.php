<?php 

use App\Core\Application;

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

    <div id="cont">
        
    
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right mt-0">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="/">Home</a>
                            </li> 
                            <li class="breadcrumb-item active"><?= t("System Update - Export") ?></li>
                        </ol>
                    </div> 
                    <h4 class="page-title"><?= t("System Update - Export") ?></h4>
                </div>
            </div>
        </div>
        
        <div class="col-12">
    
            <div class="row">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item">
                                    <a href="#introduction" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                        <i class="mdi mdi-home-variant d-lg-none d-block me-1"></i>
                                        <span class="d-none d-lg-block">Introduction</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#ctypes" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                        <i class="mdi mdi-account-circle d-lg-none d-block me-1"></i>
                                        <span class="d-none d-lg-block">Content-Types</span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane show active" id="introduction">
                                    Welcome
                                </div>
                                <div class="tab-pane" id="ctypes">
                                    
                                    
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <h4>Content-Types</h4>    
                                        </div>

                                        <div class="col-sm-8">
                                            <div class="text-sm-end">
                                                
                                                <button :disabled="ctypesItemsStatus == 1" @click="refreshCtypes()" class="btn btn-primary">
                                                    <i v-if="ctypesItemsStatus != 1" class="mdi mdi-refresh me-2"></i> 
                                                    <i v-if="ctypesItemsStatus == 1" class="mdi mdi-spin mdi-refresh me-2"></i> 
                                                    Refresh
                                                </button> 
                                                
                                                <div class="btn-group">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>

                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
        
                                                        <a class="dropdown-item" @click='exportCtype()' href="javascript: void(0);">
                                                            <i class="mdi mdi-database-export"></i> 
                                                            <span>Export Selected</span>
                                                        </a>

                                                        <a class="dropdown-item text-danger" @click='removeCtype()' href="javascript: void(0);">
                                                            <i class="ml-1 mdi mdi-database-minus"></i> 
                                                            <span>Remove Selected</span>
                                                        </a>

                                                        <a class="dropdown-item" @click='exportCtypeData()' href="javascript: void(0);">
                                                            <i class="mdi mdi-database-export"></i> 
                                                            <span>Export Selected (Data)</span>
                                                        </a>

                                                        <a class="dropdown-item text-danger" @click='removeCtypeData()' href="javascript: void(0);">
                                                            <i class="ml-1 mdi mdi-database-minus"></i> 
                                                            <span>Remove Selected (Data)</span>
                                                        </a>
                                                        
                                                    </div>
                                                </div>

                                            </div>
                                            
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label" for="support_tickets_code">Name:</label>
                                    
                                            <input class="form-control" type="text" 
                                                v-model="ctypesFilterName"
                                                >
                                            </input>

                                        </div>
                                        
                                        <div v-if="ctypesItemsStatus == 1">
                                            Loading...
                                        </div>
                                        <div v-if="ctypesItemsStatus == 2" class="col-sm-12 table-responsive">
                                            <table class="table table-centered table-hover mb-0">
                                                <tbody>
                                                    <tr v-for="(item, index) in ctypesItems.filter((e) => ctypesFilterName.length == 0 || e.title.toLowerCase().includes(ctypesFilterName.toLowerCase().trim()))" class="cursor-pointer">
                                                        <td class="pe-0">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" v-model="item.selected" :id="'customCheck1' + item.id">
                                                                <label class="form-check-label" :for="'customCheck1' + item.id"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <img class="mr-2 rounded-circle" :src="item.icon" width="40" alt="Generic placeholder image">
                                                            <p class="m-0 d-inline-block align-middle font-16">
                                                                <span class="mt-0 mb-1">{{item.title}}<small class="font-weight-normal ms-3">{{item.last_update_date}}</small></span>
                                                                <br />
                                                                <span v-if="item.exported" class="font-13 badge bg-success">Exported</span>
                                                                <span v-if="!item.exported" class="font-13 badge bg-danger">Not Exported</span>
                                                            </p>
                                                            
                                                        </td>
                                                        <td>
                                                            <span class="text-muted font-13">Module</span> <br/>
                                                            <p class="mb-0">{{item.module ?? 'N/A'}}</p>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted font-13">Exported Records</span> <br/>
                                                            <p class="mb-0">{{item.exportedRecordsCount ?? 'N/A'}}</p>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted font-13">Category</span> <br/>
                                                            <p class="mb-0">{{item.category}}</p>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted font-13">Last Update By</span> <br/>
                                                            <p class="mb-0">{{item.last_updated_user_name ?? item.created_user_name}}</p>
                                                        </td>
                                                        <td class="text-end">
                                                            <button :disabled="item.export_data_status_id == 1" v-if="item.exported && item.exportedRecordsCount == 0" class="btn btn-primary" @click="exportCtypeData(item.name)">
                                                                <i class="mdi mdi-database-export"></i>
                                                                <span v-if="item.export_data_status_id == 1">Exporting Data...</span>
                                                                <span v-else>Export Data</span>
                                                            </button>
                                                            <button :disabled="item.export_data_status_id == 1" v-if="item.exported && item.exportedRecordsCount > 0" class="btn btn-danger" @click="removeCtypeData(item.name)">
                                                                <i class="mdi mdi-database-export"></i>
                                                                <span v-if="item.export_data_status_id == 1">Removing Data...</span>
                                                                <span v-else>Remove Data</span>
                                                            </button>
                                                            <button :disabled="item.status_id == 1" v-if="!item.exported" class="btn btn-primary" @click="exportCtype(item.name)">
                                                                <i class="mdi mdi-database-export"></i>
                                                                <span v-if="item.status_id == 1">Exporting...</span>
                                                                <span v-else>Export</span>
                                                            </button>
                                                            <button :disabled="item.status_id == 1" v-if="item.exported" class="btn btn-danger" @click="removeCtype(item.name)">
                                                                <i class="ml-1 mdi mdi-database-minus"></i>
                                                                <span v-if="item.status_id == 1">Removing...</span>
                                                                <span v-else>Remove</span>    
                                                            </button>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div> <!-- end table-responsive-->
                                        <div v-if="ctypesItemsStatus == 3">
                                            Error while loading data
                                        </div>

                                    </div>

                                </div>
                                
                            </div>

                        </div>
                    </div>
                </div>

            </div>     
            
            
               
        </div>


    </div>    

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>


<script>

    var vm = new Vue({
        el: '#cont',
        data: {

            dialog: false,
            currentCtype: null,
            previousCtypeList: [],
            drawer: true,
            drawerItem: 0,
            db_name: '<?php echo \App\Core\Application::getInstance()->env->get("DB_NAME") ; ?>',
            host_name: '<?php echo \App\Core\Application::getInstance()->env->get("DB_HOST"); ?>',

            ctypesItems: [],
            ctypesItemsStatus: 0,
            ctypesFilterName:'',
            ctypesErrorInLoading: null,
            ctypesSearch: null,
            ctypesErrorInExecution: null,
        },
        methods: {
            
            refreshCtypes: function(){
                
                let self = this;
                self.ctypesItems = [];
                self.ctypesItemsStatus = 1;
                self.ctypesErrorInLoading = '';
                axios.get( '/InternalApi/systemupdateexport/?cmd=get_ctypes&response_format=json',
                    ).then(function(response){
                        self.ctypesItems = response.data.result;
                        self.ctypesItemsStatus = 2;
                    }).catch(function(error){
                        self.ctypesItemsStatus = 3;
                        if(error.response.data.message != undefined){
                            self.ctypesErrorInLoading = error.response.data.message;
                        } else {
                            self.ctypesErrorInLoading = 'Something went wrong while loading data';
                        }
                    });
            },
            exportCtype: function(name = ''){

                let self = this;

                this.ctypesItems.filter((e) => (name.length > 0 && e.name == name) || (name.length == 0 && e.exported == 0 && e.selected == true)).forEach((item) => {
                    item.status_id = 1;
                    item.error_message = null;
                
                    axios.get( '/InternalApi/SystemUpdateExport/' + item.name + '?cmd=export_ctype&response_format=json',
                        ).then(function(response){
                            
                            if(response.data.status == "success") {
                                response.data.result.forEach((x) => {
                                    
                                    self.ctypesItems.filter((e) => e.name == x).forEach((obj) => {
                                        obj.status_id = 2;
                                        obj.exported = 1;
                                    }); 
                                });
                            } else {
                                item.status_id = 3;
                                alert('something went wrong');
                            }
                            
                        }).catch(function(error){
                            
                            var message = "Something went wrong while loading data";

                            if(error.response != undefined && error.response.data.message != undefined){
                                message = error.response.data.message;
                            }
                            
                            item.status_id = 3;
                            item.error_message = message;
                            
                            $.toast({
                                heading: 'Error',
                                text: message,
                                showHideTransition: 'slide',
                                position: 'top-right',
                                icon: 'error'
                            });   
                    });

                });
            },
            exportCtypeData: function(name = '', export_data = 0){
                
                let self = this;

                this.ctypesItems.filter((e) => (name.length > 0 && e.name == name) || (name.length == 0 && e.exportedRecordsCount == 0 && e.selected == true)).forEach((item) => {
                    item.export_data_status_id = 1;
                    item.error_message = null;
                
                    axios.get( '/InternalApi/SystemUpdateExport/' + item.name + '?cmd=export_ctype_data&response_format=json',
                        ).then(function(response){
                            
                            if(response.data.status == "success") {
                            
                                item.export_data_status_id = 2;
                                item.exportedRecordsCount = response.data.result;

                            } else {
                                item.export_data_status_id = 3;
                                alert('something went wrong');
                            }
                            
                        }).catch(function(error){
                            
                            var message = "Something went wrong while loading data";

                            if(error.response != undefined && error.response.data.message != undefined){
                                message = error.response.data.message;
                            }
                            
                            item.export_data_status_id = 3;
                            item.error_message = message;
                            
                            $.toast({
                                heading: 'Error',
                                text: message,
                                showHideTransition: 'slide',
                                position: 'top-right',
                                icon: 'error'
                            });   
                    });

                });
            },
            removeCtype: function(name){
                let self = this;

                this.ctypesItems.filter((e) => e.exported == 1 && ((name.length > 0 && e.name == name) || (name.length == 0 && e.exported == 1 && e.selected == true))).forEach((item) => {
                    item.status_id = 1;
                    item.error_message = null;

                    axios.get( '/InternalApi/SystemUpdateExport/' + item.name + '?cmd=remove_ctype&response_format=json',
                        ).then(function(response){
                            
                            if(response.data.status == "success") {
                                item.status_id = 2;
                                item.exported = 0;
                                item.exportedRecordsCount = 0;
                            } else {
                                item.status_id = 3;
                                alert('something went wrong');
                            }
                            
                        }).catch(function(error){
                            
                            var message = "Something went wrong while loading data";

                            if(error.response != undefined && error.response.data.message != undefined){
                                message = error.response.data.message;
                            }
                            
                            item.status_id = 3;
                            item.error_message = message;
                            
                            $.toast({
                                heading: 'Error',
                                text: message,
                                showHideTransition: 'slide',
                                position: 'top-right',
                                icon: 'error'
                            });   
                    });

                });
            },
            removeCtypeData: function(name){
                let self = this;

                this.ctypesItems.filter((e) => (name.length > 0 && e.name == name) || (name.length == 0 && e.exportedRecordsCount > 0 && e.selected == true)).forEach((item) => {
                    item.export_data_status_id = 1;
                    item.error_message = null;

                    axios.get( '/InternalApi/SystemUpdateExport/' + item.name + '?cmd=remove_ctype_data&response_format=json',
                        ).then(function(response){
                            
                            if(response.data.status == "success") {
                            
                                item.export_data_status_id = 2;
                                item.exportedRecordsCount = 0;

                            } else {
                                item.export_data_status_id = 3;
                                alert('something went wrong');
                            }
                            
                        }).catch(function(error){
                            
                            var message = "Something went wrong while loading data";

                            if(error.response != undefined && error.response.data.message != undefined){
                                message = error.response.data.message;
                            }
                            
                            item.export_data_status_id = 3;
                            item.error_message = message;
                            
                            $.toast({
                                heading: 'Error',
                                text: message,
                                showHideTransition: 'slide',
                                position: 'top-right',
                                icon: 'error'
                            });   
                    });

                });
            }
            
        },
        
    })

</script>