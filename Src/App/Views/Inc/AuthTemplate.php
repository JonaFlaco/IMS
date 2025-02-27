<?php
use \App\Core\Application;
$data['sett_blank'] = true;
$data['sett_body_class'] = "authentication-bg";

$appTitle = Application::getInstance()->settings->get('APP_TITLE');
$appEmail = Application::getInstance()->settings->get('APP_EMAIL');

$pageTitle = (isset($data['title']) ? $data['title'] : "Untitled Page") . " - " . Application::getInstance()->settings->get('APP_TITLE');
$appDescription = Application::getInstance()->settings->get('APP_DESCRIPTION');

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
        <link href="/assets/theme/css/app<?= $langDir == "rtl" ? "-rtl" : null ?>.min.css" rel="stylesheet" type="text/css" id="light-style" />

        <link href="/assets/app/css/style.css" rel="stylesheet" type="text/css" />

        <script src="/assets/theme/js/vendor.min.js"></script>
        
        <script src="/assets/app/js/vue.min.js"></script>
        <script src="/assets/app/js/axios.min.js"></script>

    </head>

    <body class="pb-0">

        
        <div class="auth-fluid">
        
            <div class="auth-fluid-form-box">
                
                <!--Auth fluid left content -->
                <div class="align-items-center d-flex h-100">
                    <div class="card-body">

                        <!-- Logo -->
                        <div class="auth-brand text-center text-lg-start">
                            <a href="javascript: void(0);" class="logo-dark">
                                <span><img src="/assets/ext/images/logo-dark.png" alt="" height="18"></span>
                            </a>
                            <a href="javascript: void(0);" class="logo-light">
                                <span><img src="/assets/ext/images/logo.png" alt="" height="18"></span>
                            </a>

                            <h3 class="mb-2"> <?= $appTitle ?> </h3>

                        </div>


                        <div id="vue-cont">

                        </div>
                    </div>
                </div>
                
            </div>

        </div>

        <script src="/assets/theme/js/app.min.js"></script>

    </body>
</html>
