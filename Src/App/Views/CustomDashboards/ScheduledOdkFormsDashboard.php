<?php 
use App\Core\Application; 

$isAdmin = Application::getInstance()->user->isAdmin();

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    
    <div>

    <?php if($isAdmin): ?>
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

        <!-- Incomplete Records Modal -->
        <div id="incompleteRecordsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-full-width modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-dark">
                        <h4 class="modal-title" id="dark-header-modalLabel">Incomplete Records</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">

                        <div v-if="loadingIncompleteRecords">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                        </div>

                        <div v-else-if="incompleteRecords.length == 0" class="alert alert-info">
                            No incomplete record found
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-centered table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th class="p-1">ID</th>
                                        <th class="p-1">Created User</th>
                                        <th class="p-1">Created Date</th>
                                    </tr>
                                    <tr v-if="!loadingIncompleteRecords" v-for="itm in incompleteRecords">
                                        <td class="p-1">{{itm.id}}</td>
                                        <td class="p-1">{{itm.created_user_id}}</td>
                                        <td class="p-1">{{itm.created_date}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                            
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Orphan Forms Modal -->
        <div id="orphanFormsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-full-width modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-dark">
                        <h4 class="modal-title" id="dark-header-modalLabel">Orphan Forms</h4>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">

                        <div v-if="loadingOrphanForms">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                        </div>

                        <div v-else-if="orphanForms.length == 0" class="alert alert-info">
                            No data found
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-centered table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th class="p-1">Server</th>
                                        <th class="p-1">Form Name</th>
                                        <th class="p-1">Form Title</th>
                                        <th class="p-1">Allow Download</th>
                                        <th class="p-1">Allow Submissions</th>
                                        <th class="p-1">Created User</th>
                                        <th class="p-1">Created Date</th>
                                        <th class="p-1">Status</th>
                                    </tr>
                                    <tr v-if="!loadingOrphanForms" v-for="itm in orphanForms">
                                        <td class="p-1">{{itm.server}}</td>
                                        <td class="p-1">{{itm.name}}</td>
                                        <td class="p-1">{{itm.title}}</td>
                                        <td class="p-1">
                                            <span :class="'badge bg-' + (itm.allow_download == 1 ? 'success' : 'danger')">
                                                {{ itm.allow_download == 1 ? "Yes" : "No" }}
                                            </span>
                                        </td>
                                        <td class="p-1">
                                            <span :class="'badge bg-' + (itm.allow_submission == 1 ? 'success' : 'danger')">
                                                {{ itm.allow_submission == 1 ? "Yes" : "No" }}
                                            </span>    
                                        </td>
                                        <td class="p-1">{{itm.created_user}}</td>
                                        <td class="p-1">{{itm.created_date}}</td>
                                        <td class="p-1">{{itm.status}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                            
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
        <page-title-row-component :title="pageTitle"></page-title-row-component>

        <div class="row">
            <div class="col-md-2">
                <div class="d-grid">
                    <button :disabled="loadingCrons" type="button" class="btn btn-primary mb-3" @click="refresh()">
                        <i v-if="loadingCrons" class="mdi mdi-loading mdi-spin"></i>
                        <i v-else class="mdi mdi-refresh"></i>    
                        Refresh
                    </button>

                    <?php if($isAdmin): ?>

                    <button :disabled="loadingOrphanForms" type="button" class="btn btn-info mb-3" @click="showOrphanForms()">
                        <i v-if="loadingOrphanForms" class="mdi mdi-loading mdi-spin"></i>
                        <i v-else class="mdi mdi-refresh"></i>    
                        Orphan Forms
                    </button>
            
                    <div class="btn-group-vertical mb-3">
                        <button type="button" class="btn" :class="pendingStatus == 0 ? 'btn-secondary' : 'btn-light'" @click="filterPending(0)">All</button>
                        <button type="button" class="btn" :class="pendingStatus == 1 ? 'btn-secondary' : 'btn-light'" @click="filterPending(1)">
                            <i v-tooltip="'Forms with pending submission'"
                                class="mdi mdi-progress-clock mdi-24px text-danger"
                            ></i>
                        </button>
                        <button type="button" class="btn" :class="pendingStatus == 2 ? 'btn-secondary' : 'btn-light'" @click="filterPending(2)">
                            <i v-tooltip="'Forms with incomplete submission'"
                                class="mdi mdi-cloud-question mdi-24px text-warning"
                            ></i>
                        </button>
                    </div>

                    <div class="btn-group-vertical mb-3">
                        <button type="button" class="btn" :class="allowDownloadStatus == null ? 'btn-secondary' : 'btn-light'" @click="filterAllowDownload(null)">All</button>
                        <button type="button" class="btn" :class="allowDownloadStatus == 1 ? 'btn-secondary' : 'btn-light'" @click="filterAllowDownload(1)">
                            <i v-tooltip="'Allow Download'"
                                class="mdi mdi-download mdi-24px text-success"
                            ></i>
                        </button>
                        <button type="button" class="btn" :class="allowDownloadStatus == 0 ? 'btn-secondary' : 'btn-light'" @click="filterAllowDownload(0)">
                            <i v-tooltip="'Disable Download'"
                                class="mdi mdi-download-off mdi-24px text-danger"
                            ></i>
                        </button>
                    </div>

                    <div class="btn-group-vertical mb-3">
                        <button type="button" class="btn" :class="acceptSubmissionStatus == null ? 'btn-secondary' : 'btn-light'" @click="filterAcceptSubmission(null)">All</button>
                        <button type="button" class="btn" :class="acceptSubmissionStatus == 1 ? 'btn-secondary' : 'btn-light'" @click="filterAcceptSubmission(1)">
                            <i v-tooltip="'Accept Submission'"
                                class="mdi mdi-send mdi-24px text-success"
                            ></i>
                        </button>
                        <button type="button" class="btn" :class="acceptSubmissionStatus == 0 ? 'btn-secondary' : 'btn-light'" @click="filterAcceptSubmission(0)">
                            <i v-tooltip="'Disable Submission'"
                                class="mdi mdi-send-lock mdi-24px text-danger"
                            ></i>
                        </button>
                    </div>

                            
                    <div class="btn-group-vertical mb-3">
                        <button type="button" class="btn" :class="lastRunStatus == null ? 'btn-secondary' : 'btn-light'" @click="filterLastRun()">All</button>
                        <button type="button" class="btn" :class="lastRunStatus == 'started' ? 'btn-secondary' : 'btn-light'" @click="filterLastRun('started')"
                            v-tooltip="'Last run: Started'">
                            <i class="mdi mdi-clock text-info mdi-24px"></i>
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
                            :class="groupStatus == null ? 'btn-secondary' : 'btn-light'" 
                            @click="filterGroup()">All Groups</button>

                        <button type="button" class="btn" 
                            v-for="(group, indexGroup) in allGroups" :key="indexGroup"
                            :class="groupStatus == group ? 'btn-secondary' : 'btn-light'" 
                            @click="filterGroup(group)">{{ group }}</button>
                    </div>

                    
                    <div class="btn-group-vertical mb-3">
                        <button type="button" class="btn" :class="lastSubmissionStatus == 0 ? 'btn-secondary' : 'btn-light'" @click="filterLastSubmission(0)">All</button>
                        <button type="button" class="btn" :class="lastSubmissionStatus == 1 ? 'btn-secondary' : 'btn-light'" @click="filterLastSubmission(1)" v-tooltip="'Has submission last month'">
                            <i class="mdi mdi-fire text-danger mdi-24px"></i>
                        </button>
                        <button type="button" class="btn" :class="lastSubmissionStatus == 2 ? 'btn-secondary' : 'btn-light'" @click="filterLastSubmission(2)" v-tooltip="'Has submission last 6 months'">
                            <i class="mdi mdi-fire text-warning mdi-24px"></i>
                        </button>
                        <button type="button" class="btn" :class="lastSubmissionStatus == 3 ? 'btn-secondary' : 'btn-light'" @click="filterLastSubmission(3)" v-tooltip="'Has submission last year'">
                            <i class="mdi mdi-fire text-secondary mdi-24px"></i>
                        </button>
                        <button type="button" class="btn" :class="lastSubmissionStatus == 4 ? 'btn-secondary' : 'btn-light'" @click="filterLastSubmission(4)" v-tooltip="'No submission in the last year or more'">
                            <i class="mdi mdi-fire text-light mdi-24px"></i>
                        </button>
                    </div>


                    <div class="btn-group-vertical mb-3">
                        <button type="button" class="btn" :class="sizeStatus == 0 ? 'btn-secondary' : 'btn-light'" @click="filterSize(0)">All</button>
                        <button type="button" class="btn" :class="sizeStatus == 1 ? 'btn-secondary' : 'btn-light'" @click="filterSize(1)">
                            <i  class="mdi mdi-harddisk mdi-24px text-secondary"></i>
                        </button>
                        <button type="button" class="btn" :class="sizeStatus == 2 ? 'btn-secondary' : 'btn-light'" @click="filterSize(2)">
                            <i  class="mdi mdi-harddisk mdi-24px text-info"></i>
                        </button>
                        <button type="button" class="btn" :class="sizeStatus == 3 ? 'btn-secondary' : 'btn-light'" @click="filterSize(3)">
                        <i  class="mdi mdi-harddisk mdi-24px text-warning"></i>
                        </button>
                        <button type="button" class="btn" :class="sizeStatus == 4 ? 'btn-secondary' : 'btn-light'" @click="filterSize(4)">
                            <i  class="mdi mdi-harddisk mdi-24px text-danger"></i>
                        </button>
                    </div>
                    
                    <?php endif; ?>

                </div>
            </div>
            <div class="col-md-10">

            
                <div v-if="loadingCrons">
                    Loading...
                </div>
                <div v-else-if="errorLoadingCrons" class="text-danger">
                    {{ errorLoadingCrons }}
                </div>
                <div v-else-if="cronsFiltered.length == 0">
                    No result found
                </div>
                <div v-else>

                    <div v-if="crons.filter((e) => e.last_run_status_id == 'failed').length" class="alert alert-danger alert-dismissible border-0 fade show" role="alert">
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						<strong>Critical - </strong> 
						<button @click="filterLastRun('failed')" class="btn btn-link btn-sm text-danger p-1"><strong>{{ crons.filter((e) => e.last_run_status_id == 'failed').length }}</strong> </button>
						crons require attention
					</div>

                    <div class="col-xl-12 col-lg-12" v-for="(group, indexGroup) in groups" :key="indexGroup" >
                        <div class="card">
                            <div class="card-body">
                                <!-- <a href="javascript: void(0);" class="p-0 float-end">Export <i class="mdi mdi-download ms-1"></i></a> -->
                                <h4 class="header-title mt-1 mb-3">{{ group }} ({{cronsByGroup(group).length}})</h4>

                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-centered mb-0 font-13">
                                        <thead class="table-light mb-1">
                                            <tr>
                                                <th style="width: 20%;">Title</th>
                                                <th>Version</th>
                                                <th>Created Date</th>
                                                <?php if($isAdmin): ?>
                                                    <th>Batch Size</th>
                                                <?php endif; ?>
                                                <th>Status</th>
                                                <?php if($isAdmin): ?>
                                                <th>Server</th>
                                                <th>Schedule</th>
                                                <th>Last Run</th>
                                                <th>Duration</th>
                                                <th>Last 24h</th>
                                                <th class="p-0 text-center" colspan="2">Actions</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(cron, indexCron) in cronsByGroup(group)" :key="indexCron">
                                                <td>
                                                    <a class="text-secondary" target="_blank" :href="'/crons/edit/' + cron.id">
                                                        <i class="mdi mdi-open-in-new font-16"></i>
                                                        {{ cron.name }}
                                                    </a>
                                                </td>
                                                <td>{{ cron.version }}</td>
                                                <td>{{ cron.odk_created_date_humanify }}</td>
                                                <?php if($isAdmin): ?>
                                                    <td>{{ cron.batch_size }}</td>
                                                <?php endif; ?>
                                                <td>
                                                    <span v-if="cron.all_records">
                                                        <span v-if="cron.pending_records > 0" class="badge bg-danger font-13">
                                                            {{ cron.pending_records }}
                                                        </span>
                                                        <span v-else>
                                                            0
                                                        </span>

                                                        of {{ cron.all_records }}
                                                    </span>
                                                </td>
                                                <?php if($isAdmin): ?>
                                                <td>
                                                    {{ cron.server }}
                                                </td>
                                                <td>
                                                    {{ cron.job_name }}
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

                                                    <span v-if="!cron.error_message" v-tooltip="(Math.round(cron.size * 100) / 100) + ' MB'">
                                                        <i v-if="cron.size > 5000"
                                                            class="mdi mdi-harddisk text-danger mdi-24px"
                                                        ></i>    
                                                        <i v-else-if="cron.size > 999"
                                                            class="mdi mdi-harddisk text-warning mdi-24px"
                                                        ></i>
                                                        <i v-else-if="cron.size > 10"
                                                            class="mdi mdi-harddisk text-info mdi-24px"
                                                        ></i>
                                                        <i v-else
                                                            class="mdi mdi-harddisk text-secondary mdi-24px"
                                                        ></i>
                                                    </span>

                                                    <span v-if="!cron.error_message" v-tooltip="'Last submission: ' + cron.last_submission_date_humanify">
                                                        <i v-if="cron.last_submission_date_diff_day && cron.last_submission_date_diff_day <= 30"
                                                            class="ms-1 mdi mdi-fire text-danger mdi-24px"
                                                        ></i>
                                                        
                                                        <i v-else-if="cron.last_submission_date_diff_day && cron.last_submission_date_diff_day <= 180"
                                                            class="ms-1 mdi mdi-fire text-warning mdi-24px"
                                                        ></i>

                                                        <i v-else-if="cron.last_submission_date_diff_day && cron.last_submission_date_diff_day <= 360"
                                                            class="ms-1 mdi mdi-fire text-secondary mdi-24px"
                                                        ></i>
                                                        
                                                        <i v-else class="ms-1 mdi mdi-fire text-light mdi-24px"
                                                        ></i>
                                                    </span>
                                                
                                                    
                                                    <i v-if="!cron.error_message && cron.download_allowed == 0" v-tooltip="'Download Disabled'" 
                                                        class="ms-1 mdi mdi-download-off text-danger"
                                                    ></i>

                                                    <i v-if="!cron.error_message && cron.submission_allowed == 0" v-tooltip="'Submission Disabled'" 
                                                        class="ms-1 mdi mdi-send-lock text-danger"
                                                    ></i>

                                                    <i v-if="!cron.error_message && cron.incomplete_records> 0" v-tooltip="'This form has ' + cron.incomplete_records + ' incomplete submissions'"
                                                        class="mdi mdi-cloud-question mdi-24px text-warning me-1 cursor-pointer" 
                                                        @click="showIncompleteRecords(cron.id)"
                                                    ></i>
                                                

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
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive-->
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>

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
        incompleteRecords: [],
        loadingIncompleteRecords: false,
        crons: [],
        cronsFiltered: [],
        loadingCrons: false,
        pendingStatus: 0, //0: All, 1: With pending, 2: Incomplete
        sizeStatus: 0, //0: All, 1: Small, 2: Normal, 3: Big, 4: Very  Big
        lastRunStatus: 0, //0: All, 1: N/A, 2: Success, 3: Failed,
        lastSubmissionStatus: 0, //0:All, 1: last month, 2: last 6 months, 3: last 1 year, 4: more than 1 year
        groupStatus: null,
        errorLoadingCrons: null,
        loadingOrphanForms: false,
        orphanForms: [],
        allowDownloadStatus: null,
        acceptSubmissionStatus: null,

    },
    async mounted() {
        await this.refresh();
    },
    computed: {
        groups: function() {
            return new Set(this.cronsFiltered.map((item) => item.group_name));
        },
        allGroups: function() {
            return new Set(this.crons.map((item) => item.group_name));
        },
    },
    methods: {
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
        showIncompleteRecords(id){

            let self = this;
            self.incompleteRecords = [];
            self.loadingIncompleteRecords = true;

            var myModal = new bootstrap.Modal(document.getElementById('incompleteRecordsModal'), {})
            myModal.show();

            axios({
                method: 'GET',
                url: '/InternalApi/OdkFormGetInCompleteForms/' + id + '?response_format=json',
            })
            .then(function(response){
                if(response.data.status == 'success'){

                    self.incompleteRecords = response.data.result;

                    self.loadingIncompleteRecords = false;

                    
                } else {
                    
                    self.loadingIncompleteRecords = false;

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
                self.loadingIncompleteRecords = false;
                $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
            });
        },
        async showOrphanForms(id){

            let self = this;
            self.orphanForms = [];
            self.loadingOrphanForms = true;

            var myModal = new bootstrap.Modal(document.getElementById('orphanFormsModal'), {})
            myModal.show();

            axios({
                method: 'GET',
                url: '/InternalApi/OdkFromDashboardGetOrphanForms/?response_format=json',
            })
            .then(function(response){
                if(response.data.status == 'success'){

                    self.orphanForms = response.data.result;

                    self.loadingOrphanForms = false;

                    
                } else {
                    
                    self.loadingOrphanForms = false;

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
                self.loadingOrphanForms = false;
                $.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
            });
            },
        async refresh() {
            
            let self = this;
            self.loadingCrons = true;
            self.crons = [];
            self.errorLoadingCrons = null;
            
            self.pendingStatus = 0;
            self.sizeStatus = 0;
            self.lastRunStatus = 0;
            self.lastSubmissionStatus = 0;
            self.groupStatus = null;

            var response = await axios.get('/InternalApi/OdkFromDashboardGetFroms?type_id=sync_odk_form&cron_job_id=job_sync_odk_forms&load_detail=1&response_format=json',   
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
        cronsByGroup: function(group) {
            return this.cronsFiltered.filter((item) => item.group_name == group);
        },
        filterPending(status_id) {
            
            this.pendingStatus = status_id;

            this.internalFilter();

        },
        filterAllowDownload(status_id) {
            
            this.allowDownloadStatus = status_id;

            this.internalFilter();

        },
        filterAcceptSubmission(status_id) {
            
            this.acceptSubmissionStatus = status_id;

            this.internalFilter();

        },
        filterSize(status_id) {
        
            this.sizeStatus = status_id;

            this.internalFilter();
        },
        filterLastRun(status_id) {
            this.lastRunStatus = status_id;
            this.internalFilter();
        },
        filterGroup(group) {
            this.groupStatus = group;
            this.internalFilter();
        },
        filterLastSubmission(status_id) {
            this.lastSubmissionStatus = status_id;
            this.internalFilter();
        },
        internalFilter() {
            
            this.cronsFiltered = this.crons;

            if(this.pendingStatus == 1) {
                this.cronsFiltered = this.crons.filter((item) => item.pending_records > 0);
            } else if(this.pendingStatus == 2) {
                this.cronsFiltered = this.crons.filter((item) => item.incomplete_records > 0);
            }

            if(this.allowDownloadStatus != null) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.download_allowed == this.allowDownloadStatus);
            }

            if(this.acceptSubmissionStatus != null) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.submission_allowed == this.acceptSubmissionStatus);
            }

            if(this.sizeStatus == 1) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.size < 10);
            } else if(this.sizeStatus == 2) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.size >= 10 && item.size < 1000);
            } else if(this.sizeStatus == 3) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.size >= 1000 && item.size < 5000);
            } else if(this.sizeStatus == 4) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.size > 5000);
            }
            

            if(this.lastRunStatus == 'started') {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_run_status_id == 'started');
            } else if(this.lastRunStatus == 'finished') {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_run_status_id == 'finished');
            } else if(this.lastRunStatus == 'failed') {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_run_status_id == 'failed');
            } else if(this.lastRunStatus == 'created') {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_run_status_id == null || item.last_run_status_id.length == 0);
            }

            if(this.lastSubmissionStatus == 1) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_submission_date_diff_day && item.last_submission_date_diff_day <= 30);
            } else if(this.lastSubmissionStatus == 2) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_submission_date_diff_day && item.last_submission_date_diff_day > 30 && item.last_submission_date_diff_day <= 180);
            } else if(this.lastSubmissionStatus == 3) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.last_submission_date_diff_day && item.last_submission_date_diff_day > 180 && item.last_submission_date_diff_day <= 360);
            } else if(this.lastSubmissionStatus == 4) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => !item.last_submission_date_diff_day || item.last_submission_date_diff_day > 360);
            }

            if(this.groupStatus != null) {
                this.cronsFiltered = this.cronsFiltered.filter((item) => item.group_name == this.groupStatus);
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

                    cron.all_records = response.data.result.all_records;
                    cron.pending_records = response.data.result.pending_records;
                    cron.incomplete_records = response.data.result.incomplete_records;
                    cron.size = response.data.result.size;

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