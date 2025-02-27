<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    <div>
        <page-title-row-component
            :title="pageTitle"
            :bread-crumb="breadCrumb"
            >
        </page-title-row-component>
    
        <div class="row">

            <!-- Right Sidebar -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- Left sidebar -->
                        <div class="page-aside-left">

                            <div class="d-grid">
                                <button type="button" class="btn btn-primary" @click="loadAll">Load All</button>
                            </div>

                            <!-- Links -->
                            <div class="email-menu-list mt-3">

                                <a v-for="item in menuList" :key="item.id"
                                    href="javascript: void(0);" 
                                    @click="openMenu(item)"
                                    :class="{'text-danger font-weight-bold': selectedMenu == item}"
                                    >
                                    <i class="mr-2" :class="item.icon"></i>
                                    
                                    {{ item.name }}

                                    <div v-if="item.statusId != 0" class="float-end ms-2">
                                        <span 
                                            v-if="item.statusId == 1" 
                                            class="badge bg-danger">
                                            <i class="mdi mdi-timer"></i>
                                        </span>
                                        <span 
                                            v-else-if="item.errorMessage" 
                                            class="badge bg-danger">
                                            <i class="mdi mdi-information-outline"></i>
                                        </span>
                                        <span 
                                            v-else-if="item.id >= 2 && item.id <= 28" 
                                            class="badge "
                                            :class="item.records.length == 0 ? 'bg-success' : 'bg-danger'">
                                            {{ item.records.length }}
                                        </span>
                                    </div>
                                </a>
                            
                            </div>

                        </div>
                        <!-- End Left sidebar -->

                        <!-- Right sidebar -->
                        <div v-if="selectedMenu" class="page-aside-right">

                            <div v-if="selectedMenu.id == 'introduction'">
                                <div>
                                    <!-- Top Bar -->
                                    <div class="row">
                                        <div class="col-sm-4">                            
                                            <h4 class="mb-3 header-title">
                                                Intro
                                            </h4>
                                        </div>

                                    </div>
                                    <!-- End of Top Bar -->

                                    <p>
                                        This page is designed to allow system administrators to reset the system completely, some part of it partialy or doing some kind of system maintenance. Before you start check below checkpoints:
                                    </p>

                                    <ul>
                                        <li>Take complete <strong>backup of database and files</strong></li>
                                        <li>Before reset any entity, <strong>review</strong> its conent</li>
                                    </ul>
                                </div>
                            </div>


                            <options-component 
                                v-else-if="selectedMenu.id == 'options'"
                                :item="selectedMenu"
                                :key="selectedMenu.id"
                                >
                            </options-component>

                            <ext-dir-component 
                                v-else-if="selectedMenu.id == 'ext_dir'"
                                :item="selectedMenu"
                                :key="selectedMenu.id"
                                >
                            </ext-dir-component>


                            <div v-else v-for="obj in menuList.filter(x => x.id != 'options' && x.id != 'ext_dir' && x.id != 'introduction')"> 
                                <detail-component 
                                    :item="obj"
                                    v-if="selectedMenu == obj"
                                    :key="obj.id"
                                    >
                                </detail-component>
                            </div>

                        </div> 
                        <!-- End of Right sidebar -->

                    </div>
                    
                </div>
                <!-- End of Card -->

            </div>
            
        </div> 


    </div>

</template>

<!-- Components -->
<?= Application::getInstance()->view->renderView('admin/reset/components/DetailComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/reset/components/OptionsComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/reset/components/ExtDirComponent', (array)$data) ?>

<?= Application::getInstance()->view->renderView('admin/reset/index.js', (array)$data) ?>