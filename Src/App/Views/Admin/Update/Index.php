<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

    <div id="cont">
    
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right mt-0">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="/">Home</a>
                            </li> 
                            <li class="breadcrumb-item active"><?= t("System Update") ?></li>
                        </ol>
                    </div> 
                    <h4 class="page-title"><?= t("System Update") ?></h4>
                </div>
            </div>
        </div>
        
        <div class="col-12">
    
            <div class="row">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item">
                                    <a href="#introduction" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                        <i class="mdi mdi-home-variant d-lg-none d-block me-1"></i>
                                        <span class="d-none d-lg-block">Introduction</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#ctypes" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                        <i class="mdi mdi-account-circle d-lg-none d-block me-1"></i>
                                        <span class="d-none d-lg-block">Content-Types</span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane show active" id="introduction">
                                    <introduction-component/>
                                </div>
                                <div class="tab-pane" id="ctypes">
                                    
                                    <ctypes-component/>
                                    

                                </div>
                                
                            </div>

                        </div>
                    </div>
                </div>

            </div>     
            
            
               
        </div>


    </div>    

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>

<!-- Components -->
<?= Application::getInstance()->view->renderView('admin/Update/components/IntroductionComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('admin/Update/components/CtypesComponent', (array)$data) ?>

<!-- Js -->
<?= Application::getInstance()->view->renderView('admin/Update/index.js', (array)$data) ?>
