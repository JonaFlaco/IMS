<?php

use App\Core\Application;

$pageTitle = (isset($data['title']) ? $data['title'] : "Untitled Page") . " - " . Application::getInstance()->settings->get('APP_TITLE');
$appDescription = Application::getInstance()->settings->get('APP_DESCRIPTION');

$loadDhtmlx = $data['sett_load_dhtmlx'] ?? false;
$loadRichTextEditor = $data['sett_load_rich_text_editor'] ?? false;
$blank = $data['sett_blank'] ?? false;
$addPreLoader = !($data['sett_disable_preloader'] ?? false);

$lang = Application::getInstance()->user->getLangId(true);
$langDir = Application::getInstance()->user->getLangDirection();
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="utf-8" />
    <title><?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?= $appDescription ?>" name="description" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="/assets/ext/images/favicon.ico">

    <link href="/assets/theme/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/theme/css/app<?= $langDir == "rtl" ? "-rtl" : null ?>.min.css?v=1.0" rel="stylesheet" type="text/css" id="light-style" />

    <link href="/assets/app/css/style.css?v=1.0" rel="stylesheet" type="text/css" />

    <link href="/assets/app/css/vue-multiselect.min.css" rel="stylesheet" type="text/css" />

    <script src="/assets/app/js/vue-multiselect.min.js"></script>
    <link href="/assets/app/js/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css" />

    <script href="/assets/app/js/main.js?v=1.0"></script>

    <script src="/assets/app/js/vue.min.js"></script>
    <script src="/assets/app/js/axios.min.js"></script>

    <?php if ($loadDhtmlx) : ?>
        <link rel="stylesheet" href="/assets/app/js/dhtmlx/diagram/diagram.css?v=3.0.2">
        <link rel="stylesheet" href="/assets/app/js/dhtmlx/diagram/menu/menu.css">

        <link href="/assets/app/js/dhtmlx/gantt/dhtmlxgantt.css?v=7.0.11" rel="stylesheet" type="text/css" />
        <link href="/assets/app/js/dhtmlx/scheduler/dhtmlxscheduler_material.css?v=5.3.10" rel="stylesheet" type="text/css" />
        <link href="/assets/app/js/dhtmlx/diagram/diagram.css?v=3.0.2" rel="stylesheet" type="text/css" />
    <?php endif; ?>

    <?php if ($loadRichTextEditor) : ?>
        <link href="/assets/theme/css/vendor/quill.snow.css" rel="stylesheet">
    <?php endif; ?>
</head>

<body <?= $addPreLoader ? 'class="loading"' : '' ?>>

    <?php if ($addPreLoader) : ?>
        <!-- Pre-loader -->
        <div id="preloader">
            <div id="status">
                <div class="bouncing-loader">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
        <!-- End Preloader-->
    <?php endif; ?>

    <!-- Begin page -->
    <div class="wrapper">

        <?php if (!$blank) : ?>

            <?= Application::getInstance()->view->renderView('inc/LeftSideBar', (array)$data) ?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <?= Application::getInstance()->view->renderView('inc/TopBar/index', (array)$data) ?>


                    <!-- Start Content-->
                    <div class="container-fluid ps-2 pe-2">

                        <!-- Demo Alert -->
                        <?php if (Application::getInstance()->settings->get('IS_LIVE_PLATFORM') != 1) { ?>
                            <div class="alert alert-warning mt-2 mb-0" role="alert">
                                <strong>DEMO PLATFORM - </strong> You are using demo platform
                                <?php
                                if (Application::getInstance()->user->isAdmin()) {
                                    echo ", connected to <i class='mdi mdi-database'></i> <strong>" . Application::getInstance()->env->get("DB_NAME") . "</strong> db on <i class='mdi mdi-server'></i> <strong>" . Application::getInstance()->env->get("DB_HOST") . "</strong> server. git branch <i class='mdi mdi-git'></i> <strong>" . Application::getInstance()->git->getCurrentBranch() . "</strong>";
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <!-- End of Demo Alert -->

                        <!-- Maintenance Mode Alert -->
                        <?php if (Application::getInstance()->settings->get('MAINTENANCE_MODE_IS_ACTIVE') == 1) { ?>
                            <div class="alert alert-danger mt-2" role="alert">
                                <strong>MAINTENANCE MODE IS ACTIVE - </strong> <?= Application::getInstance()->settings->get('MAINTENANCE_MODE_message'); ?>
                            </div>
                        <?php } ?>
                        <!-- End of Maintenance Mode -->
                    <?php endif; ?>