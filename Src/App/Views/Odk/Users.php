<?php 

use App\Core\Application; 
$data = (object)$data;
$title = $data->title;
$odk_db_list_json = json_encode($data->odkDbList);

?>


<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>


<template id="tpl-main">
    <div>
        <page-title-row-component :title="pageTitle"></page-title-row-component>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-lg-8">
                                <form class="row gy-2 gx-2 align-items-center">
                                   
                                    <div class="col-auto">
                                        <div class="me-sm-2">
                                            <select v-model="odk_id" class="form-select" id="status-select">
                                                <option selected value="">Choose a Server</option>
                                                <option v-for="item in odkDbList" :value="item.id"> {{ item.name }} </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <label for="keyword" class="visually-hidden">Search</label>
                                        <input type="search" v-model="keyword" class="form-control" id="keyword" placeholder="Search...">
                                    </div>
                                </form>                            
                            </div>
                            <div class="col-lg-4">
                                <div class="text-lg-end">
                                    <button type="button" @click="loadData()" class="btn btn-primary mb-2"><i class="mdi mdi-database-search-outline me-1"></i> Search</button>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div v-if="loading">Loading...</div>
                        
                        <div v-else class="table-responsive">
                            <table class="table table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th>Has IMS Account</th>
                                        <th>Created By</th>
                                        <th>Creation Date</th>
                                        <th style="width: 125px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in users.filter((e) => !keyword || e.name.toLowerCase().includes(keyword.toLowerCase()))">
                                        <td> {{ item.name }} </td>
                                        <td> {{ item.full_name }} </td>
                                        <td>
                                            <span v-if="item.is_admin == 1" class="ms-1 badge badge-danger-lighten rounded-pill">Admin</span>
                                            <span v-if="item.can_access_admin == 1" class="ms-1 badge badge-warning-lighten rounded-pill">Can Access Admin</span>
                                            <span v-if="item.can_collect_data == 1" class="ms-1 badge badge-dark-lighten rounded-pill">Can Collect Data</span>
                                        </td>
                                        <td>
                                            <span v-if="item.has_ims_account == 1" class="ms-1 badge badge-success-lighten rounded-pill">Yes</span>
                                            <span v-else class="ms-1 badge badge-danger-lighten rounded-pill">No</span>
                                        </td>
                                        <td>
                                            {{ item.created_user_id }}
                                        </td>
                                        <td>
                                            {{ item.created_date }}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-eye"></i></a>
                                            <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                            <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-delete"></i></a>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>

                            <p class="mt-2">
                                Showing {{ users.filter((e) => !keyword || e.name.toLowerCase().includes(keyword.toLowerCase())).length }} record(s)
                            </p>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>

        </div>
    </div>
</template>

<script>

    var vm = new Vue({
        
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            pageTitle: '<?= $title ?>',
            odkDbList: <?= $odk_db_list_json ?>,
            users: [],
            loading: false,
            odk_id: "",
            keyword: null,
        },
        async mounted() {
        },
        methods: {
            async loadData() {

                if(!this.odk_id) {
                    alert('Please select a server');
                    return;
                }

                let self = this;
                self.users = [];

                self.loading = true;
                var response = await axios.get('/InternalApi/OdkGetAllUsers/' + this.odk_id + '?response_format=json',   
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
                        
                        self.users = response.data.result;

                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }

                }

                self.loading = false;
            },
        },
    })
</script>
