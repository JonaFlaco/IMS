<?php

use App\Core\Application; ?>

<!-- Header -->
<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    <div>

        <page-title-row-component :title="pageTitle" :bread-crumb="breadCrumb"></page-title-row-component>

        <div class="row">

            <!-- Right Sidebar -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- Left sidebar -->
                        <div class="page-aside-left">

                            <h4>Menu</h4>

                            <!-- Links -->
                            <div class="email-menu-list mt-3">
                                <a href="javascript: void(0);" @click="selectedCategory = 0" :class="{'text-danger font-weight-bold': selectedCategory == 0}">
                                    <i class="dripicons-information me-2"></i>
                                    Introduction
                                </a>

                                <a href="javascript: void(0);" @click="selectedCategory = 1" :class="{'text-danger font-weight-bold': selectedCategory == 1}">
                                    <i class="dripicons-gear me-2"></i>
                                    General Info
                                </a>

                                <a href="javascript: void(0);" @click="selectedCategory = 2" :class="{'text-danger font-weight-bold': selectedCategory == 2}">
                                    <i class="dripicons-gear me-2"></i>
                                    Maintenance Mode
                                </a>

                                <a href="javascript: void(0);" @click="selectedCategory = 3" :class="{'text-danger font-weight-bold': selectedCategory == 3}">
                                    <i class="dripicons-gear me-2"></i>
                                    Demo Mode
                                </a>

                                
                                <a href="javascript: void(0);" @click="selectedCategory = 4" :class="{'text-danger font-weight-bold': selectedCategory == 4}">
                                    <i class="mdi mdi-bell-outline me-2"></i>
                                    Notifications
                                </a>
                                <a href="javascript: void(0);" @click="selectedCategory = 5" :class="{'text-danger font-weight-bold': selectedCategory == 5}">
                                    <i class="mdi mdi-email-outline me-2"></i>
                                    Mail
                                </a>
                                

                               

                                <a href="javascript: void(0);" @click="selectedCategory = 6" :class="{'text-danger font-weight-bold': selectedCategory == 6}">
                                    <i class="mdi mdi-table me-2"></i>
                                    Views
                                </a>
                                



                                <a href="javascript: void(0);" @click="selectedCategory = 7" :class="{'text-danger font-weight-bold': selectedCategory == 7}">
                                    <i class="mdi mdi-security me-2"></i>
                                    Security
                                </a>

                                <a href="javascript: void(0);" @click="selectedCategory = 8" :class="{'text-danger font-weight-bold': selectedCategory == 8}">
                                    <i class="mdi mdi-security me-2"></i>
                                    ODK Forms
                                </a>

                                <a href="javascript: void(0);" @click="selectedCategory = 9" :class="{'text-danger font-weight-bold': selectedCategory == 9}">
                                    <i class="mdi mdi-android-debug-bridge me-2"></i>
                                    Logger Settings
                                </a>

                                <a href="javascript: void(0);" @click="selectedCategory = 10" :class="{'text-danger font-weight-bold': selectedCategory == 10}">
                                    <i class="mdi mdi-database-clock-outline me-2"></i>
                                    Cache Settings
                                </a>

                                <a href="javascript: void(0);" @click="selectedCategory = 11" :class="{'text-danger font-weight-bold': selectedCategory == 11}">
                                    <i class="mdi mdi-dots-vertical-circle me-2"></i>
                                    Misc. Settings
                                </a>

                                <!-- End of Links -->


                            </div>

                        </div>
                        <!-- End Left sidebar -->

                        <!-- Right sidebar -->
                        <div class="page-aside-right">

                            <intro-component v-if="selectedCategory == 0"></intro-component>

                            <general-info-component v-else-if="selectedCategory == 1"></general-info-component>

                            <maintenance-mode-component v-else-if="selectedCategory == 2"></maintenance-mode-component>

                            <demo-mode-component v-else-if="selectedCategory == 3"></demo-mode-component>

                            <notifications-component v-else-if="selectedCategory == 4"></notifications-component>

                            <mail-component v-else-if="selectedCategory == 5"></mail-component>

                            <views-component v-else-if="selectedCategory == 6"></views-component>

                            <security-component v-else-if="selectedCategory == 7"></security-component>

                            <odk-form-component v-else-if="selectedCategory == 8"></odk-form-component>

                            <logger-component v-else-if="selectedCategory == 9"></logger-component>

                            <cache-component v-else-if="selectedCategory == 10"></cache-component>

                            <misc-component v-else-if="selectedCategory == 11"></misc-component>

                        </div>
                        <!-- End of Right sidebar -->

                    </div>

                </div>
                <!-- End of Card -->

            </div>

        </div>
        <!-- End of Row -->
    </div>

</template>

<!-- Components -->
<?= Application::getInstance()->view->renderView('admin/Config/Components/IntroComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/GeneralInfoComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/MaintenanceModeComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/DemoModeComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/NotificationsComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/MailComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/SecurityComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/ViewsComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/OdkFormsComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/LoggerComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/CacheComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Config/Components/MiscComponent', (array)$data) ?>

<!-- Js -->
<?= Application::getInstance()->view->renderView('admin/Config/Index.js', (array)$data) ?>