<?php 

use App\Core\Application;
$data['sett_blank'] = true;
$lang = Application::getInstance()->user->getLangId();
$langDir = Application::getInstance()->user->getLangDirection();


?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>


<div class="row">
    <div class="col-md-4 align-middle mt-3" style="margin: 0 auto; float:none;">
        <div class="card">
            <div class="card-header" <?php echo ($langDir == "rtl" ? " style=\"text-align: right !important;\"" : "")?> >      
                Mensaje
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">                             
                            <h2 class="mt-0"><i class="mdi mdi-check-all"></i></h2>
                            <h3 class="mt-0">¡Gracias!</h3>
                            <p class="w-75 mb-2 mx-auto">Su solicitud ha sido actualizada. </p>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div>
        </div>
    </div>
</div>

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>