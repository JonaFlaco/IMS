<?php 
use \App\Core\Application; 

$menuId = $data['menu_id'] ?? "main_menu";
?>

<!-- Left sidebar -->
<div class="leftside-menu">

    <!-- Logo -->
    <a href="/" class="logo text-center">
        <span class="logo-lg">
            <img src="/assets/ext/images/logo_white.png" alt="" height="<?= MENU_ICON_SIZE ?>" id="side-main-logo">
        </span>
        <span class="logo-sm">
            <img src="/assets/ext/images/logo_sm.png" alt="" height="<?= MENU_ICON_SIZE ?>" id="side-sm-main-logo">
        </span>
    </a>
    <!-- End of Logo -->

    <div class="h-100" id="leftside-menu-container" data-simplebar>
        
        <!--- Sidemenu -->
        <ul class="side-nav">

            <!-- <li class="side-nav-item">
                <a href="javascript: void(0);" id="btnToggleLeftsidebar" class="side-nav-link">
                </a>
            </li> -->
            <?php if(\App\Core\Application::getInstance()->user->isAdmin()) : ?>
            <li class="side-nav-title side-nav-item">Admin Panel</li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarAdminStructure" aria-expanded="false" aria-controls="sidebarAdminStructure" class="side-nav-link">
                    <!-- <i class="mdi mdi-cogs"></i> -->
                    <img width="24" height="24" bgcolor="#5d4037" avatar="STR">
                    <span> <?= t("Structure") ?> </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarAdminStructure">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="/ctypes"> <?= t("Content-Types") ?> </a>
                        </li>
                        <li>
                            <a href="/views"> <?= t("Views") ?> </a>
                        </li>
                        <li>
                            <a href="/modules"> <?= t("Modules") ?> </a>
                        </li>
                        <li>
                            <a href="/documents"> <?= t("Documents") ?> </a>
                        </li>
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarAdminSurveys" aria-expanded="false" aria-controls="sidebarAdminSurveys">
                                <span> <?= t("Surveys") ?> </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAdminSurveys">
                                <ul class="side-nav-third-level">
                                    <li>
                                        <a href="/surveys"> <?= t("Surveys") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/survey_credentials"> <?= t("Survey Credentials") ?> </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="/dataimport"> <?= t("Data Import") ?> </a>
                        </li>
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarAdminCrons" aria-expanded="false" aria-controls="sidebarAdminCrons">
                                <span> <?= t("Crons") ?> </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAdminCrons">
                                <ul class="side-nav-third-level">
                                    <li>
                                        <a href="/crons_dashboard"> <?= t("Crons Dashboard") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/odk_forms_dashboard"> <?= t("ODK Forms Dashboard") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/crons"> <?= t("Crons") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/dataimport/importGcron"> <?= t("Import Cron") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/crons_jobs"> <?= t("Jobs") ?> </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                             
                        <li>
                            <a href="/menu"> <?= t("Menu") ?> </a>
                        </li>
                        <li>
                            <a href="/status_workflow_templates"> <?= t("Status Workflow Templates") ?> </a>
                        </li>
                        <li>
                            <a href="/status_list"> <?= t("Status List") ?> </a>
                        </li>
                        <li>
                            <a href="/ctypes_logs_reason_list"> <?= t("Ctype Log Reason List") ?> </a>
                        </li>
                        <li>
                            <a href="/dashboards"> <?= t("Dashboards") ?> </a>
                        </li>
                        <li>
                            <a href="/widgets"> <?= t("Widgets") ?> </a>
                        </li>
                        <li>
                            <a href="/validation_patterns"> <?= t("Validation Patterns") ?> </a>
                        </li>
                        <li>
                            <a href="/pages"> <?= t("Pages") ?> </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarSecurity" aria-expanded="false" aria-controls="sidebarSecurity" class="side-nav-link">
                    <!-- <i class="mdi mdi-security"></i> -->
                    <img width="24" height="24" bgcolor="#ff8f00" avatar="SEC">
                    <span> <?= t("Security") ?> </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarSecurity">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="/users"> <?= t("Users") ?> </a>
                        </li>
                        <li>
                            <a href="/user_groups"> <?= t("User Groups") ?> </a>
                        </li>
                        <li>
                            <a href="/roles"> <?= t("Roles") ?> </a>
                        </li>
                        <li>
                            <a href="/positions"> <?= t("Positions") ?> </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarConfigurations" aria-expanded="false" aria-controls="sidebarConfigurations" class="side-nav-link">
                    <!-- <i class="mdi mdi-cog-outline"></i> -->
                    <img width="24" height="24" bgcolor="#7b1fa2" avatar="SYS">
                    <span> <?= t("System") ?> </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarConfigurations">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="/settings"> <?= t("Settings") ?> </a>
                        </li>
                        <li>
                            <a href="/admin/config"> <?= t("Configurations") ?> </a>
                        </li>
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarAdminLocalization" aria-expanded="false" aria-controls="sidebarAdminLocalization">
                                <span> <?= t("Localization") ?> </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAdminLocalization">
                                <ul class="side-nav-third-level">
                                    <li>
                                        <a href="/languages"> <?= t("Languages") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/keywords"> <?= t("Keywords") ?> </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="/db_connection_strings"> <?= t("Database Connection Strings") ?> </a>
                        </li>
                        <li>
                            <a href="/custom_url"> <?= t("Custom URL") ?> </a>
                        </li>
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarAdminMaintenance" aria-expanded="false" aria-controls="sidebarAdminMaintenance">
                                <span> <?= t("Maintenance") ?> </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAdminMaintenance">
                                <ul class="side-nav-third-level">
                                    <li>
                                        <a href="/admin/filemanager"> <?= t("File Manager") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/admin/reset"> <?= t("Reset") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/admin/orphancolumns"> <?= t("Orphan Columns") ?> </a>
                                    </li>
                                    <li>
                                        <a href="/admin/orphantables"> <?= t("Orphan Tables") ?> </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="/core/releases"> <?= t("Core Releases") ?> </a>
                        </li>
                        <li>
                            <a href="/core/update"> <?= t("Core Update") ?> </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarDevelopment" aria-expanded="false" aria-controls="sidebarDevelopment" class="side-nav-link">
                    <!-- <i class="mdi mdi-dev-to"></i> -->
                    <img width="24" height="24" bgcolor="#283593" avatar="DEV">
                    <span> <?= t("Development") ?> </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarDevelopment">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="/error_log"> <?= t("Error Log") ?> </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarHelp" aria-expanded="false" aria-controls="sidebarHelp" class="side-nav-link">
                    <!-- <i class="mdi mdi-help-circle"></i> -->
                    <img width="24" height="24" bgcolor="#1b5e20" avatar="?">
                    <span> <?= t("Help Center") ?> </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarHelp">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="/help"> <?= t("Help Center") ?> </a>
                        </li>
                        <li>
                            <a href="/help_posts"> <?= t("Posts") ?> </a>
                        </li>
                        <li>
                            <a href="/help_categories"> <?= t("Categories") ?> </a>
                        </li>
                        <li>
                            <a href="/help_sub_categories"> <?= t("Sub Categories") ?> </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a href="/reportCenter" class="side-nav-link ">
                    <img width="24" height="24" bgcolor="#ef476f" avatar="RC">
                    <span class="badge bg-light text-dark float-end">New</span>
                    <span> <?= t("Report Center") ?> </span> 
                </a>
            </li>

            

            <li class="side-nav-title side-nav-item"><?= t("Navigation") ?></li>
            <?php endif; ?>

            

            <?php
                if(Application::getInstance()->user->isAuthenticated()){
                    
                    foreach(Application::getInstance()->coreModel->getMenu($menuId) as $menu){
                        
                        echo generateMenuLeft($menu, 1);

                    } 
                }
            ?>

        </ul>
        <!-- Sidemenu End -->
            
    </div>

</div>
<!-- Left Sidebar End -->