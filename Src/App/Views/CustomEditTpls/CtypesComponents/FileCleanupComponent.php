

<template id="tpl-file-cleanup-component">
    
    <div class="col-lg-12" v-if="$parent.id">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <h4> <i class="mdi mdi-chart-areaspline"></i> Maintenance - File cleanup</h4>


                    <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Name</th>
                                <th class="border-0">Stats</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>

                            <td>
                                <span class="ms-2 fw-semibold"> 
                                    <i class="mdi mdi-24px mdi-folder-open text-warning"></i>
                                    {{ item.name }}
                                    
                                    <span v-if="item.stats.total_files_in_drive != null">
                                        <i v-if="item.stats.total_files_in_drive < 1000" v-tooltip="'Small'" class="ms-1 mdi mdi-alpha-s-circle text-secondary"></i>
                                        <i v-else-if="item.stats.total_files_in_drive < 10000" v-tooltip="'Medium'" class="ms-1 mdi mdi-alpha-m-circle text-info"></i>
                                        <i v-else-if="item.stats.total_files_in_drive < 100000" v-tooltip="'Large'" class="ms-1 mdi mdi-alpha-l-circle text-warning"></i>
                                        <i v-else v-tooltip="'Very large'" class="ms-1 mdi mdi-alpha-x-circle text-danger"></i>
                                    </span>
                                </span>

                            </td>
                            
                            <td>
                                <span v-if="item.stats.loading">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Loading</div>
                                    </div>
                                </span>
                                <div v-else-if="item.stats.total_files_in_drive != null">
                                    <span class="ms-2 fw-semibold"> <i class="mdi mdi-harddisk"></i>: {{ item.stats.total_files_in_drive }} </span>
                                    <span class="ms-2 fw-semibold"> <i class="mdi mdi-database"></i>: {{ item.stats.total_files_in_db }} </span>
                                    <span class="ms-2 fw-semibold"> <i class="mdi mdi-alert"></i>: {{ item.stats.orphans }} </span>
                                    
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" :style="'width: ' + (100 - item.stats.orphan_perc).toFixed(2) + '%'" :aria-valuenow="(100 - item.stats.orphan_perc).toFixed(2)" aria-valuemin="0" aria-valuemax="100"> %{{ (100 - item.stats.orphan_perc).toFixed(2) }} </div>
                                        <div class="progress-bar bg-danger" role="progressbar" :style="'width: ' + item.stats.orphan_perc.toFixed(2) + '%'" :aria-valuenow="item.stats.orphan_perc.toFixed(2)" aria-valuemin="0" aria-valuemax="100">% {{ item.stats.orphan_perc.toFixed(2) }} </div>
                                    </div>
                                    
                                </div>
                            </td>
                            <td>
                                
                                <span class="ms-2 fw-semibold"> 
                                    <button :disabled="item.stats.loading" class="btn btn-link" @click="cleanup()"> 
                                        <i class="mdi mdi-trash-can text-danger"></i>
                                        Cleanup 
                                    </button>
                                </span>

                                <span class="ms-2 fw-semibold">
                                    <button :disabled="item.stats.loading" class="btn btn-link" @click="loadStats()"> 
                                        <i class="mdi mdi-reload"></i>
                                        Load Stats 
                                    </button>
                                </span>
                            
                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col-->

</template>

<script>
    Vue.component('file-cleanup-component', {
        template: '#tpl-file-cleanup-component',
        data() {
            return {
                loading: false,
                errorMessage: null,
                loading: false,
                item: {
                    id: null,
                    name: null,
                    stats: {
                        loading: false,
                        total_files_in_drive: null,
                        total_files_in_db: null,
                        linked_files: null
                    }
                },
            }
        },
        async mounted() {
            this.item.id = this.$parent.id;
            this.item.name = this.$parent.name;
        },
        methods: {
            async loadStats() {

                let self = this;
                var item = self.item;
                self.item.stats.loading = true;

                var response = await axios.get('/InternalApi/FileManagerLoadDetail/' + item.id + '?response_format=json').catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    $.toast({
                        heading: 'Error',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    item.stats.loading = false;
                });

                if (response.status == 200) {
                    item.stats = response.data.result;
                }

                item.stats.loading = false;

            },
            async cleanup() {

                let self = this;
                var item = self.item;

                if(confirm("Are you sure you want to cleanup " + item.name + "?") != true)
                    return;

                
                item.stats.loading = true;

                formData = new FormData();
                formData.append('directory', item.id);

                var response = await axios.post(
                    '/InternalApi/FileManagerCleanup/?response_format=json',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    }
                ).catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    $.toast({
                        heading: 'Error',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    item.stats.loading = false;
                });

                if (response.status == 200) {
                    item.stats = response.data.result;
                }

                item.stats.loading = false;

            },
        }
    })
</script>