

<template type="text/x-template" id="tpl-list-orphan-columns-component">
    
    <div class="col-lg-12" >
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <h4> <i class="mdi mdi-chart-areaspline"></i> Maintenance - Orphan Columns</h4>

                    <div v-if="loading">
                        Loading...
                    </div>
                    <div v-else-if="items.length == 0">
                        No orphan columns found
                    </div>
                    <div v-else>
                        <p>System found (<strong class="text-primary">{{items.length}}</strong>) orphan columns</p>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Table</th>    
                                        <th>Column</th>
                                        <th>Type</th>
                                        <th>Is Required</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in items">
                                        <td> {{ item.table_name }} </td>
                                        <td> {{ item.column_name }} </td>
                                        <td> {{ item.data_type }} </td>
                                        <td>
                                            <span v-if="item.is_required" class="text-danger">Yes</span> 
                                            <span v-else>No</span>
                                        </td>
                                        <td>
                                        <button class="btn btn-link text-danger" @click="deleteColumn(item)">
                                            <i class="mdi mdi-24px mdi-trash-can"></i> <span v-if="item.loading"> ... </spna>
                                        </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col-->

</template>

<script>

    Vue.component('list-orphan-columns-component', {
        template: '#tpl-list-orphan-columns-component',
        data() {
            return {
                loading: false,
                errorMessage: null,
                items: [],
            }
        },
        props: ['ctypeId'],
        async mounted() {
            await this.loadData();
        },
        methods: {
            async loadData() {
                
                var self = this;

                this.errorMessage = null;
                let id = this.ctypeId == undefined == null || this.ctypeId == undefined ? "" : this.ctypeId;

                this.loading = true;
                
                var response = await axios.post('/InternalApi/CtypeOrphanColumnsGet/' + id + '?response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        self.errorMessage = message;
                    });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        self.items = response.data.result;
                    } else {
                        self.errorMessage = "Something went wrong";
                    }

                }

                this.loading = false;
            },

            async deleteColumn(item) {
                
                var self = this;

                this.errorMessage = null;

                item.loading = true;
                
                var response = await axios.post('/InternalApi/CtypeOrphanColumnsDelete/' + item.table_name + '?column_name=' + item.column_name + '&response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }
                        
                        item.loading = false;
                        item.errorMessage = message;
                    });

                if(response) {
                    
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        
                        $.toast({
                            heading: 'Success',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });

                        self.items = self.items.filter((itm) => itm != item);

                    } else {
                        item.errorMessage = "Something went wrong";
                    }

                }

                item.loading = false;
            },
        }
    });

</script>