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
                            <p class="w-75 mb-2 mx-auto">Gracias por brindarnos su información, validaremos sus datos de acuerdo a los criterios de la OIM Ecuador, y nos contactaremos con usted en caso de que podamos evaluarlo para una posible asistencia.</p>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div>
        </div>
    </div>
</div>

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>