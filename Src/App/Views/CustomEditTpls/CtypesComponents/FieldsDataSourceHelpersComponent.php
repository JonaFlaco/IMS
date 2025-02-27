<?php use \App\Core\Application;

$dataSourceField = Application::getInstance()->coreModel->getFields("ctypes_fields", null, "data_source_id")[0];

?>

<template id="tpl-fields-data-source-helpers-component">

    <div>

        <div id="dataSourceChoicesModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-right">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" @click="closeChoices" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <h4> {{ selectedCtype?.name }} </h4>
                            <div v-if="loadingChoices && !loadingChoicesError"> Loading... </div>
                            <div v-else-if="loadingChoicesError" class="alert alert-danger" role="alert">
                                <strong>Error - </strong> {{ loadingChoicesError }}
                            </div>
                            <div v-else class="table-responsive-sm">
                                <table class="table table-striped">
                                    <thead>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tbody>
                                    <tr v-for="item in choices" :key="item.id">
                                        <td> {{ item.id }} </td>
                                        <td> {{ item.name }} </td>
                                        <td scope="row" class="table-action p-1">
                                            <a href="javascript: void(0);" class="action-icon text-info" @click="openChoiceExternal(item)"><i class="mdi mdi-open-in-new"></i></a>
                                            <a href="javascript: void(0);" @click="editChoice('edit', item);" class="action-icon text-primary"><i class="mdi mdi-pencil"></i></a>
                                            <a href="javascript: void(0);" class="action-icon text-danger" @click="editChoice('delete', item)"><i class="mdi mdi-delete"></i></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>

                            <button v-if="!loadingChoicesError" :disabled="loadingChoices" class="btn btn-info btn-sm" @click="editChoice('add')">
                                <i class="mdi mdi-plus"></i>
                                <span v-if="loadingNewChoice">Adding...</span>
                                <span v-else>Add New</span>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" @click="closeChoices">Close</button>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    
        <div class="col-sm-12 p-0 mt-2">
            <div class="btn-group">
                <button :disabled="loading || loadingTaxoInfo || loadingDataSources" type="button" @click="dropDownMenuClicked" class="mt-2 btn btn-secondary dropdown-toggle" aria-expanded="false" data-bs-toggle="dropdown" id="data_source_options_dropdown"> 
                    <span v-if="loading || loadingTaxoInfo || loadingDataSources">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </span>
                    <span v-else> Data Source Options</span>
                    <span class="caret"></span> 
                </button>
                <div class="dropdown-menu">
                    <h6 class="dropdown-header"> {{ selectedCtype ? selectedCtype.id : 'No Content-Type selected' }} </h6>
                    <a v-if="$parent.current_fields.field_type_id == 'relation'" class="dropdown-item" href="javascript: void(0);" @click="addNewTaxo">Add new Lookup Table</a>
                    <a class="dropdown-item" href="javascript: void(0);" @click="openAddNewCtypeWindow">
                        Add New Content-Type
                        <i class="mdi mdi-open-in-new pull-right"></i>
                    </a>
                    <a v-if="selectedCtype" class="dropdown-item" href="javascript: void(0);" @click="editLookupTable">
                        Edit Data Source
                        <i class="mdi mdi-open-in-new pull-right"></i>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript: void(0);" @click="RefreshDataSourcesList">Refresh Data Sources</a>
                    <div v-if="$parent.current_fields.field_type_id == 'relation' && selectedCtype && selectedCtype.is_field_collection != true" class="dropdown-divider"></div>
                    <a v-if="$parent.current_fields.field_type_id == 'relation' && selectedCtype && selectedCtype.is_field_collection != true" class="dropdown-item" href="javascript: void(0);" @click="showChoices">Choices</a>
                    <a v-if="$parent.current_fields.field_type_id == 'relation' && selectedCtype && selectedCtype.is_field_collection != true" class="dropdown-item" href="javascript: void(0);" @click="openChoicesWindow">
                        Open List
                        <i class="mdi mdi-open-in-new pull-right"></i>
                    </a>
                    
                </div>
            </div>
            
        </div>
        
    </div>
</template>

<script>
    Vue.component('fields-data-source-helpers-component', {
        template: '#tpl-fields-data-source-helpers-component',
        data() {
            return {
                loading: false,
                choices: [],
                loadingChoices: false,
                loadingChoicesError: null,
                loadingNewChoice: false,
                loadingTaxoInfo: false,
                loadingDataSources: false,
                selectedCtype: null,
                dataSourceField: '<?= $dataSourceField->id ?>'
            }
        },
        async mounted() {
            this.$watch(
                "$parent.current_fields.data_source_id",
                async (new_value, old_value) => {
                    await this.getTaxoInfo();
                }
            );

            let self = this;
            $('#data_source_options_dropdown').on('show.bs.dropdown', async function () {
                if(self.selectedCtype == null) {
                    await self.getTaxoInfo();
                }
            })
        },
        methods: {
            async dropDownMenuClicked() {
                
                if(this.selectedCtype == null) {
                    await this.getTaxoInfo();
                }
            },
            async addNewTaxo() {

                var name = prompt("Enter new Lookup-Table display Name");
                name = name ? name.trim() : '';
                if(name.length == 0) {
                    alert('Operation abort');
                    return;
                }
                var id = name.replace(/[^a-zA-Z0-9 _]/g, '');
                id = name.replace(/[ ]/g, '_').toLowerCase();

                this.loading = true;

                var dataObj = {
                    sett_ctype_id: 'ctypes',
                    justification_for_edit_is_required: false,
                    sett_is_update: false,
                    id: id,
                    name: name,
                    category_id: 'lookup_table',
                    is_field_collection: 0,
                    fields: [
                        {
                            id: null,
                            name: "name",
                            title: "Name",
                            field_type_id: "text",
                            size: 12,
                            str_length: null,
                            appearance_id: '1_string',
                            location: "top",
                            is_required: true,
                            is_unique: true,
                        }
                    ]

                };

                let formData = new FormData();
                formData.append('data', JSON.stringify(dataObj));

                var response = await axios({
                    method: 'POST',
                    url: '/ctypes/add/&response_format=json',
                    data: formData,
                    headers: {
                        'Content-Type': 'form-data',
                        'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("add_ctypes") ?>',
                    }
                }).catch(function(error){
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
                });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        //success
                        var newObj = {id: response.data.id, name: name};
                        
                        this.$parent.pl_data_source_id_ctypes.push(newObj);
                        this.$parent.current_fields.data_source_id = newObj;

                        $.toast({
                            heading: 'Success',
                            text: "Lookup-Table" + name + " added successfuly",
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });

                    } else {
                        $.toast({
                            heading: 'Error',
                            text: "Something went wrong",
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }

                }

                this.loading = false;
            },
            async showChoices() {
                
                if(this.selectedCtype == null) {
                    alert('error getting ctype info');
                    return;
                } else if (this.selectedCtype.is_field_collection != 0) {
                    alert('This feature does not work on Field-Collections');
                    return;
                }

                var myModal = new bootstrap.Modal(document.getElementById('dataSourceChoicesModal'), {})
                myModal.show();

                await this.refreshChoices();
            },
            async RefreshDataSourcesList() {
                
                this.loadingDataSources = true;
                
                var self = this;
                var response = await axios('/InternalApi/genericPreloadList/0?field_id=' + this.dataSourceField + '&lang=&response_format=json'
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
                });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        //success
                        this.$parent.pl_data_source_id_ctypes = response.data.result;
                        
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: "Something went wrong",
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }

                }
                
                this.loadingDataSources = false;
            },
            async openAddNewCtypeWindow() {
                window.open('/ctypes/add', '_blank').focus();
            },
            async openChoicesWindow() {
                
                if(this.selectedCtype == null) {
                    alert('error getting ctype info');
                    return;
                }
                if(this.selectedCtype.is_field_collection != 0) {
                    alert('This feature does not work on Field-Collections');
                    return;
                }

                window.open('/' + this.selectedCtype.id + '', '_blank').focus();
            },
            async editLookupTable() {
                
                if(this.selectedCtype == null) {
                    alert('error getting ctype info');
                    return;
                }

                window.open('/ctypes/edit/' + this.selectedCtype.id + '', '_blank').focus();
            },
            async getTaxoInfo() {
                
                this.selectedCtype = null;

                if(!this.$parent.current_fields.data_source_id) {
                    return;
                }

                this.loadingTaxoInfo = true;
                
                var id = this.$parent.current_fields.data_source_id;
                if(id && typeof id === 'object') {
                    id = id.id;
                }

                var response = await axios('/InternalApi/GetCtypeBasicInfo/' + id + '/&response_format=json'
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
                });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        //success
                        this.selectedCtype = response.data.result;
                        
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: "Something went wrong",
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }

                }

                this.loadingTaxoInfo = false;

                if(this.selectedCtype) {
                    if(this.$parent.current_fields.field_type_id == "field_collection" && this.selectedCtype.is_field_collection != true) {
                        alert("Field-Collection should be selected for data source");
                        this.selectedCtype = null;
                        this.$parent.current_fields.data_source_id = null;
                        return;
                    }
                }
            },
            async editChoice(action = 'add', item = null) {
                item = JSON.parse(JSON.stringify(item));

                if(action == 'add') {
                    var name = prompt("Add New Option");
                    item = {
                        id: null,
                        name: name,
                    }
                } else if(action == 'edit') {
                    var name = prompt("Add New Option", item.name);
                    item.name = name;
                }

                if(item.name.length == 0) {
                    alert('Operation abort');
                    return;
                }

                item.sett_ctype_id = this.selectedCtype.id;
                
                this.loadingNewChoice = true;

                var url = '/' + this.selectedCtype.id + '/' + action + '/' + item.id + '&response_format=json';
                let formData = new FormData();
                formData.append('data', JSON.stringify(item));
                formData.append('justification', 'Using express interface');

                if(action == 'delete') {
                    url = '/InternalApi/deleteRecord/?response_format=json';
                    formData = new FormData();
                    formData.append('id_list', item.id);
                    formData.append('ctype_id', this.selectedCtype.id);
                }
                
                var response = await axios({
                    method: 'POST',
                    url: url,
                    data: formData,
                    headers: {
                        'Content-Type': 'form-data',
                        'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("add_ctypes") ?>',
                    }
                }).catch(function(error){
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
                });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        //success
                        
                        
                        $.toast({
                            heading: 'Success',
                            text: name + " added successfuly",
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });

                        await this.refreshChoices();


                    } else {
                        $.toast({
                            heading: 'Error',
                            text: "Something went wrong",
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }

                }

                this.loadingNewChoice = false;
            },
            async openChoiceExternal(item) {
                
                if(this.selectedCtype == null) {
                    alert('Content-Type Info is empty');
                    return;
                }

                window.open('/' + this.selectedCtype.id + '/edit/' + item.id, '_blank').focus();
            },
            async refreshChoices() {
                let self = this;
                this.choices = [];
                this.loadingChoices = true;
                this.loadingChoicesError = null;
                var response = await axios(
                    '/GViews/loadData/' + this.selectedCtype.id +'?load_based_ctype=1&page=all&response_format=json',
                ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        self.loadingChoicesError = message;

                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });

                });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        //success
                        this.choices = response.data.records;
                        
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: "Something went wrong",
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });

                        this.loadingChoicesError = 'Something went wrong'
                    }

                }

                this.loadingChoices = false;
                
            },
            closeChoices() {
                logModal = bootstrap.Modal.getInstance(document.getElementById('dataSourceChoicesModal'))
                logModal.hide();
            },
        }
    });
</script>
