<?php

use App\Core\Application; ?>

<!-- Header -->
<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    <div>

        <page-title-row-component :title="pageTitle" :bread-crumb="breadCrumb"></page-title-row-component>

        <div class="row">

            <div class="row mb-2">
                
                <div class="col-sm-4">
                    <button @click="loadRootLevel" class="btn btn-primary btn-rounded mb-3">
                        <span v-if="loadingRootLevelList">Loading...</span>
                        <span v-else>Refresh</span>
                    </a>
                </div>
                
                <div class="col-sm-8">
                    <div class="text-sm-end">
                        
                        <div class="btn-group mb-3">
                            <button type="button" @click="filterType = 0" class="btn" :class="filterType == 0 ? 'btn-primary' : 'btn-light'">All</button>
                        </div>

                        <div class="btn-group mb-3 ms-1">
                            <button type="button" @click="filterType = 1" class="btn" :class="filterType == 1 ? 'btn-primary' : 'btn-light'">Waiting</button>
                            <button type="button" @click="filterType = 2" class="btn" :class="filterType == 2 ? 'btn-primary' : 'btn-light'">Loading</button>
                            <button type="button" @click="filterType = 3" class="btn" :class="filterType == 3 ? 'btn-primary' : 'btn-light'">Ready</button>
                        </div>

                        <div class="btn-group mb-3 ms-1">
                            <button type="button" @click="orderBy = 0" class="btn" :class="orderBy == 0 ? 'btn-info' : 'btn-light'">Name</button>
                            <button type="button" @click="orderBy = 1" class="btn" :class="orderBy == 1 ? 'btn-info' : 'btn-light'">Size</button>
                        </div>
                        
                    </div>
                </div><!-- end col-->
            </div>
            
            
            <div>
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
                            <tr v-for="item in rootLevelList.filter((itm) => filterType == 0 || (filterType == 1 && !itm.stats.loading && itm.stats.total_files_in_drive == null) || (filterType == 2 && itm.stats.loading) || (filterType == 3 && !itm.stats.loading && itm.stats.total_files_in_drive != null)).sort((a,b) => orderBy == 1 ? b.stats.total_files_in_drive - a.stats.total_files_in_drive : b.name - a.name )">
                                <td>
                                    <span class="ms-2 fw-semibold"> 
                                        <i v-if="item.isDir" class="mdi mdi-24px mdi-folder-open text-warning"></i>
                                        <i v-else class="mdi mdi-24px mdi-file text-info"></i>
                                        {{ item.name }}

                                        <i v-if="!item.ctypeFound" v-tooltip="'Content-Type not found'" class="ms-1 mdi mdi-alert text-danger"></i>
                                        
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
                                        <button :disabled="item.stats.loading" class="btn btn-link" @click="cleanup(item)"> 
                                            <i class="mdi mdi-trash-can text-danger"></i>
                                            Cleanup 
                                        </button>
                                    </span>

                                    <span v-if="item.isDir" class="ms-2 fw-semibold">
                                        <button :disabled="item.stats.loading" class="btn btn-link" @click="loadStats(item)"> 
                                            <i class="mdi mdi-reload"></i>
                                            Load Stats 
                                        </button>
                                    </span>
                                
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        
        </div>
        <!-- End of Row -->
    </div>

</template>

<!-- Js -->
<?= Application::getInstance()->view->renderView('admin/FileManager/index.js', (array)$data) ?>