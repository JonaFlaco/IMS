<?php 
use App\Core\Application; 

$isAdmin = Application::getInstance()->user->isAdmin();

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>
<?= (new \App\Views\Components\WidgetComponent())->generate() ?>

<template id="tpl-main">
    
    <div>

        <!-- Log Modal -->
        <div id="logModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-full-width modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-dark">
                        <h4 class="modal-title" id="dark-header-modalLabel">Log</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">

                        <div v-if="loadingLog">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                        </div>

                        <div v-else-if="log.length == 0 && !loadingLog" class="alert alert-info">
                            No log found
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-centered table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th class="p-1">Date</th>
                                        <th class="p-1">Status</th>
                                        <th class="p-1">Message</th>
                                        <th class="p-1">Record Id</th>
                                        <th class="p-1">Reference Id</th>
                                        <th class="p-1">User</th>
                                    </tr>
                                    <tr v-if="!loadingLog" v-for="lg in log">
                                        <td class="p-1">{{lg.created_date}}</td>    
                                        <td class="p-0">
                                                <span v-if="lg.type_id == 'started'" class="mb-0"><i class="mdi mdi-circle text-primary"></i> {{lg.type_name}}</span>
                                                <span v-if="lg.type_id == 'data_synced'" class="mb-0"><i class="mdi mdi-circle text-info"></i> {{lg.type_name}}</span>
                                                <span v-if="lg.type_id == 'finished'" class="mb-0"><i class="mdi mdi-circle text-success"></i> {{lg.type_name}}</span>
                                                <span v-if="lg.type_id == 'failed'" class="mb-0"><i class="mdi mdi-circle text-danger"></i> {{lg.type_name}}</span>
                                        </td>
                                        
                                        <td class="p-1">{{lg.message}}</td>
                                        <td class="p-1">{{lg.record_id}}</td>
                                        <td class="p-1">{{lg.reference_id}}</td>
                                        <td class="p-1">{{lg.user_full_name}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> 
                            
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div id="statsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-full-width modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-dark">
                        <h4 class="modal-title" id="dark-header-modalLabel">{{ cronNameForStats }} Stats</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">

                        <div class="p-1">
                            <crons-stats-component 
                                v-if="cronIdForStats" 
                                :cron-id="cronIdForStats" 
                                limited="0"
                                auto-load="1"></crons-stats-component>
                        </div>
                            
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <page-title-row-component :title="pageTitle"></page-title-row-component>

        <div class="row">

            <!-- <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-1 mb-3">Overall Stats</h4>
                        
                        <crons-stats-component  
                            cron-id=""
                            limited="1" 
                            auto-load="1"></crons-stats-component>
                    </div>
                </div>
            </div> -->
                

            <div class="col-md-2">
                <div class="d-grid">
                    <button :disabled="loadingCrons" type="button" class="btn btn-primary mb-3" @click="refresh()">
                        <i v-if="loadingCrons" class="mdi mdi-loading mdi-spin"></i>
                        <i v-else class="mdi mdi-refresh"></i>    
                        Refresh
                    </button>

                    <div class="btn-group-vertical mb-3">
                        <button type="button" class="btn" :class="lastRunStatus == null ? 'btn-secondary' : 'btn-light'" @click="filterLastRun()">All</button>
                        <button type="button" class="btn" :class="lastRunStatus == 'started' ? 'btn-secondary' : 'btn-light'" @click="filterLastRun('started')"
                            v-tooltip="'Last run: Started'">
                            <i class="mdi mdi-clock text-primary mdi-24px"></i>
                        </button>
                        <button type="button" class="btn" :class="lastRunStatus == 'finished' ? 'btn-secondary' : 'btn-light'" @click="filterLastRun('finished')"
                            v-tooltip="'Last run: Finished'">
                            <i class="mdi mdi-check-circle text-success mdi-24px"></i>
                        </button>
                        <button type="button" class="btn" :class="lastRunStatus == 'failed' ? 'btn-secondary' : 'btn-light'" @click="filterLastRun('failed')"
                            v-tooltip="'Last run: Failed'">
                            <i class="mdi mdi-close-circle text-danger mdi-24px"></i>
                        </button>
                        <button type="button" class="btn" :class="lastRunStatus == 'created' ? 'btn-secondary' : 'btn-light'" @click="filterLastRun('created')"
                            v-tooltip="'Last run: N/A'">
                            <i class="mdi mdi-checkbox-blank-circle-outline text-secondary mdi-24px"></i>
                        </button>
                    </div>

                    <div class="btn-group-vertical mb-3">
                        <button type="button" class="btn" 
                            :class="typeStatus == null ? 'btn-secondary' : 'btn-light'" 
                            @click="filterType()">All Types</button>

                        <button type="button" class="btn" 
                            v-for="(type, indexType) in allTypes" :key="indexType"
                            :class="typeStatus == type ? 'btn-secondary' : 'btn-light'" 
                            @click="filterType(type)">{{ type }}</button>
                    </div>


                </div>
            </div>
            <div class="col-md-10">

                <div v-if="loadingCrons" class="mb-2">
                    Loading...
                </div>
                <div v-else-if="errorLoadingCrons" class="text-danger" class="mb-2">
                    {{ errorLoadingCrons }}
                </div>
                <div v-else-if="cronsFiltered.length == 0" class="mb-2">
                    No result found
                </div>
                <div v-else>

                    <div v-if="crons.filter((e) => e.last_run_status_id == 'failed').length" class="alert alert-danger alert-dismissible border-0 fade show" role="alert">
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						<strong>Critical - </strong> 
						<button @click="filterLastRun('failed')" class="btn btn-link btn-sm text-danger p-1"><strong>{{ crons.filter((e) => e.last_run_status_id == 'failed').length }}</strong> </button>
						crons require attention
					</div>

                    <div class="col-xl-12 col-lg-12" v-for="(type, indexType) in types" :key="indexType" >
                        <div class="card">
                            <div class="card-body">
                                <!-- <a href="javascript: void(0);" class="p-0 float-end">Export <i class="mdi mdi-download ms-1"></i></a> -->
                                <h4 class="header-title mt-1 mb-3">{{ type }} ({{cronsByType(type).length}})</h4>

                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-centered mb-0 font-13">
                                        <thead class="table-light mb-1">
                                            <tr>
                                                <th style="width: 20%;">Title</th>
                                                <th>Created Date</th>
                                                <th>Server</th>
                                                <th>Schedule</th>
                                                <th>Last Run</th>
                                                <th>Duration</th>
                                                <th>Last 24h</th>
                                                <th class="p-0 text-center" colspan="2">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(cron, indexCron) in cronsByType(type)" :key="indexCron">
                                                <td>
                                                    <a class="text-secondary" target="_blank" :href="'/crons/edit/' + cron.id">
                                                        <i class="mdi mdi-open-in-new font-16"></i>
                                                        {{ cron.name }}
                                                    </a>
                                                </td>
                                                <td>{{ cron.created_date_humanify }}</td>
                                                <td>
                                                    {{ cron.server }}
                                                </td>
                                                <td>
                                                    <span v-if="cron.job_name">{{ cron.job_name }}</span>
                                                    <span v-else class="p-1 bg-warning">Not scheduled</span>
                                                </td>
                                                <td>
                                                    {{ cron.last_run_humanify}}
                                                </td>
                                                <td>
                                                    <span v-if="cron.duration > 0">
                                                        {{ cron.duration / 1000 }} Sec
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-grid">
                                                        <span v-tooltip="cron.started_count + ' ran in the last 24 hours'" class="badge badge-primary-lighten mt-1" v-if="cron.started_count > 0">{{ cron.started_count }}</span>
                                                        <span v-tooltip="cron.data_synced_count + ' data synced in the last 24 hours'" class="badge badge-success-lighten mt-1" v-if="cron.data_synced_count > 0">{{ cron.data_synced_count }}</span>
                                                        <span v-tooltip="cron.failed_count + ' failed in the last 24 hours'" class="badge badge-danger-lighten mt-1" v-if="cron.failed_count > 0">{{ cron.failed_count }}</span>
                                                    </div>
                                                </td>
                                                <td>

                                                    <span v-if="!cron.error_message" v-tooltip="'Last run: ' + cron.last_run_status_name ?? 'N/A'">
                                                        <i v-if="cron.last_run_status_id == 'started'"
                                                            class="mdi mdi-clock text-primary mdi-24px"
                                                        ></i>
                                                        <i v-else-if="cron.last_run_status_id == 'finished'"
                                                            class="mdi mdi-check-circle text-success mdi-24px"
                                                        ></i>
                                                        <i v-else-if="cron.last_run_status_id == 'failed'"
                                                            class="mdi mdi-close-circle text-danger mdi-24px"
                                                        ></i>
                                                        <i v-else
                                                            class="mdi mdi-checkbox-blank-circle-outline text-secondary mdi-24px"
                                                        ></i>
                                                    </span>


                                                    <i v-tooltip="'This cron is custom'" 
                                                        v-if="cron.is_custom == 1" 
                                                        class="ms-1 mdi mdi-file-code mdi-24px text-primary"
                                                    ></i>
                                                    <i v-if="cron.error_message" v-tooltip="cron.error_message" 
                                                        class="ms-1 mdi mdi-alert mdi-24px text-danger"
                                                    ></i>


                                                </td>
                                                <td class="table-action p-0 text-center" style="width: 50px;">
                                                    <div class="btn-group mb-2">
                                                        <button :disabled="cron.loadingRun" type="button" class="btn btn-light btn-sm" @click="runCron(cron)">
                                                            <i v-if="cron.loadingRun" v-tooltip="'Run the cron'" class="mdi mdi-loading mdi-spin"></i>
                                                            <i v-else v-tooltip="'Run the cron'" class="mdi mdi-play-circle"></i>
                                                        </button>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> Actions <span class="caret"></span> </button>
                                                            <div class="dropdown-menu">
                                                                <a @click="showLog(cron.id)" href="javascript: void(0);" class="dropdown-item">Show Log</a>
                                                                <a @click="showStats(cron)" href="javascript: void(0);" class="dropdown-item">Show Stats</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive-->
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
<!-- 
                <widget-component 
                        id="crons_stats_last_week"
                        size="12"
                        type="html"
                    >
                </widget-component> -->

            </div>
        </div>

    </div>
    
</template>


<script>
    
var vm = new Vue({
    el: '#vue-cont',
    template: '#tpl-main',
    data: {
        pageTitle: '<?= $data['title'] ?>',
        log: [],
        loadingLog: false,
        crons: [],
        cronsFiltered: [],
        loadingCrons: false,
        lastRunStatus: 0, //0: All, 1: N/A, 2: Success, 3: Failed,
        lastSubmissionStatus: 0, //0:All, 1: last month, 2: last 6 months, 3: last 1 year, 4: more than 1 year
        typeStatus: null,
        errorLoadingCrons: null,
        cronIdForStats: null,
        cronNameForStats: null,

    },
    async mounted() {
        await this.refresh();
    },
    computed: {
        types: function() {
            return new Set(this.cronsFiltered.map((item) => item.type_name));
        },
        allTypes: function() {
            return new Set(this.crons.map((item) => item.type_name));
        },
    },
    methods: {
        showStats(cron){
            this.cronIdForStats = null;
            this.cronNameForStats = null;

            let self = this;

            var myModal = new bootstrap.Modal(document.getElementById('statsModal'), {})
            myModal.show();

            setTimeout(function () { 
                self.cronIdForStats = cron.id;
                self.cronNameForStats = cron.name;
            }, 500)
        },
        showLog(id){

            let self = this;
            self.log = [];
            self.loadingLog = true;

            var myModal = new bootstrap.Modal(document.getElementById('logModal'), {})
            myModal.show();

            axios({
                method: 'get',
                url: '/InternalApi/getcronlog/' + id + '?response_format=json',
            })
            .then(function(response){
                if(response.data.status == 'success'){
                            
                    self.log = response.data.result;

                    self.loadingLog = false;


                } else {
                    
                    self.loadingLog = false;

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

                self.loadingLog = false;
                $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
            });
        },
        
        async refresh() {
            
            let self = this;
            self.loadingCrons = true;
            self.crons = [];
            self.errorLoadingCrons = null;
            
            self.lastRunStatus = 0;
            self.lastSubmissionStatus = 0;
            self.typeStatus = null;

            var response = await axios.get('/InternalApi/OdkFromDashboardGetFroms&response_format=json',   
                ).catch(function(error){
                    message = error;
                    
                    if(error.response && error.response.data && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    $.toast({
                        heading: 'Error',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    self.errorLoadingCrons = message;
                    self.loadingCrons = false;
                });

            if(response.status == 200) {
                
                this.crons = response.data.result;
                this.cronsFiltered = response.data.result;
                
                self.loadingCrons = false;
            }

        },
        cronsByType: function(type) {
            return this.cronsFiltered.filter((item) => item.type_name == type);
        },
        filterLastRun(status_id) {
            this.lastRunStatus = status_id;
            this.internalFilter();
        },
        filterType(type) {
            this.typeStatus = type;
            this.internalFilter();
        },
        internalFilter() {
            
            this.cronsFiltered = this.crons;

            if(this.lastRunStatus == 'started') {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_run_status_id == 'started');
            } else if(this.lastRunStatus == 'finished') {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_run_status_id == 'finished');
            } else if(this.lastRunStatus == 'failed') {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_run_status_id == 'failed');
            } else if(this.lastRunStatus == 'created') {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_run_status_id == null || item.last_run_status_id.length == 0);
            }

            if(this.typeStatus != null) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.type_name == this.typeStatus);
            }
            
        },
        async runCron(cron) {
            cron.loadingRun = true;
        
            var response = await axios.get('/InternalApi/OdkFromDashboardSyncForm/' + cron.id + '&response_format=json',   
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

                    $.toast({
                        heading: 'Success',
                        text: 'Cron ran successfuly',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'success'
                    });

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

            cron.loadingRun = false;
        }
    },

});

</script>


<?= Application::getInstance()->view->renderView("Components/CronsStatsComponent", []) ?>