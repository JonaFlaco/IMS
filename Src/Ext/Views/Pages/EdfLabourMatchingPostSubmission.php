<?php

use App\Core\Application;

$data['sett_blank'] = true;
$lang = Application::getInstance()->user->getLangId();
$langDir = Application::getInstance()->user->getLangDirection();


?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>


<div class="row">
    <div class="col-6 align-middle mt-3" style="margin: 0 auto; float:none;">
        <div class="card">
            <div class="card-header" <?php echo ($langDir == "rtl" ? " style=\"text-align: right !important;\"" : "") ?>>
                Mensaje
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            <h4 class="mt-0">Estimado/a postulante:</h4>
                        </div>
                            <p class="w-75 mb-2 mx-auto">Agradecemos sinceramente su tiempo y dedicación al completar el formulario de matching laboral. Este paso es crucial para que empresas aliadas de la OIM Ecuador, puedan encontrar perfiles que se alineen con sus ofertas laborales.</p>
                            <p class="w-75 mb-2 mx-auto">En caso de que su perfil resulte de interés para alguna de estas empresas, ellas se pondrán en contacto directamente con usted utilizando la información de contacto proporcionada en el formulario. Es importante destacar que la OIM ni sus socios implementadores no tienen ninguna injerencia en la selección de perfiles ni en las decisiones de contratación por parte de las empresas. Nuestra función se limita a remitir los perfiles de aquellos que han pasado por procesos de capacitación junto a la OIM o algunos de sus socios y que pueden ser de interés para nuestros aliados empresariales.</p>
                            <p class="w-75 mb-2 mx-auto">Recuerda que todos nuestros servicios son gratuitos. Si alguien te pide dinero a cambio de recibir cualquier tipo de asistencia, repórtalo a: <strong>smecescucha@iom.int</strong>  o al <strong>0989465831</strong>.

                                La información que nos compartas será manejada con absoluta confidencialidad.

                                Le agradecemos nuevamente por su participación y le deseamos mucho éxito en su búsqueda laboral.</p>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div>
        </div>
    </div>
</div>

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>