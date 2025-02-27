<?php

use App\Core\Application;

$data['sett_blank'] = true;

$data = (object)$data;
$surveyObj = $data->surveyObj;

$coreModel = App\Core\Application::getInstance()->coreModel;
/*
if (!empty(\App\Core\Application::getInstance()->user->getLangId())) {

  $this->langObj = Application::getInstance()->coreModel->nodeModel("languages")
    ->id(\App\Core\Application::getInstance()->user->getLangId())
    ->loadFirstOrFail();
}
*/
$langList = Application::getInstance()->coreModel->nodeModel("languages")->load();
$lang = Application::getInstance()->user->getLangId();

$loggedin_survey_username = Application::getInstance()->survey->getUserName();

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

<?= Application::getInstance()->view->renderView('surveys/surveyTopBar', (array)$data) ?>


<div class="col-lg-8" style="margin: 0 auto; float:none;">

  <div class="row pt-2 pb-2">

    <div class="col-lg-12 mb-1">
      <table class="col-lg-12">
        <tr>
          <td>
              <center>
            <table>
                <tr>
                    <td>
                    <img class="me-2" width="160px" src="\assets\ext\images\logos\oim_logo.png">
                    </td>
                    <td>

                    </td>
                    <td>
                    <img class="ms-2" width="200px" src="\assets\ext\images\logos\edf_main_logo.png">
                    </td>
                </tr>
            </table>
            </center>

            
          </td>
        </tr>
        <tr>
          <td class="mb-2">
            <br>
            <center><h2 class="ml-2 mt-2 mb-2"><?php echo $data->title; ?></h2></center>
          </td>
        </tr>
        <tr>
            <td>


            <div class="row">
                <div class="col-12">
                    <div class="card ribbon-box">
                        <div class="card-body">
                            <div id="card_title_intro" class="ribbon ribbon-primary float-start">
                                <a data-bs-toggle="collapse" href="#card_intro" role="button" aria-expanded="false" aria-controls="card_intro" class="text-white p-0 m-0">
                                    Introducción
                                </a>
                            </div>
                            <div id="card_intro" class="ribbon-content row collapse  pt-3  show">
                            
                                <p>
                                    El Fondo de Desarrollo Empresarial (EDF) es una convocatoria que busca entregar fondos no reembolsables a pequeñas y medianas empresas en Ecuador. El objetivo general es fortalecer y dinamizar el sistema productivo de las PYMES beneficiarias y fomentar la inclusión laboral de personas en movilidad humana y en situación vulnerable.
                                </p>
                                <p>
                                Este es un proceso de 5 fases. Al enviar esta Expresión de Interés comprende que la información será revisada y de avanzar a la siguiente fase, se iniciará un proceso de verificación, en el que la OIM coordinará visitas a su establecimiento para verificar la información entregada en esta expresión de interés.
                                </p><p>
                                Luego de esta verificación y en caso de ser aprobado, se le solicitará presentar su aplicación completa. Tendrá que realizarlo en las fechas establecidas y proporcionar toda la información financiera y legal que se requiera.
                                </p>
                            
                                <p>
                                    <a href="javascript: void(0);" data-bs-toggle="modal" data-bs-target="#eligibility_modal"><i class="me-1 dripicons-arrow-thin-right"></i>Elegibilidad (quién puede solicitarlo)</a>
                                </p>

                                <div>
                                    <i class="me-1 dripicons-arrow-thin-right"></i> Para más información sobre esta aplicación, descarga el documento 
                                    <a href="/assets/ext/edf/convocatoria_expresion_edf2.pdf" target="_blank">
                                        <img src="/assets/app/images/icons/pdf.png" width="24px" height="24px"> 
                                        de convocatoria EDF Quito
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </td>
        </tr>
       

      </table>
    </div>

  </div>

  <?php if ($surveyObj->type_id == "protected" && ($surveyObj->allow_multiple_entry || $surveyObj->allow_edit_record) && Application::getInstance()->survey->isLoggedIn()) : ?>
    <h4> <?= t("Bienvenido") ?> <?= $loggedin_survey_username ?>, <a href="/SurveyManagement/logout/<?= $data->surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"> <?= t("Logout") ?> </a></h4>
    <h4><a href="/SurveyManagement/list/<?= $data->surveyObj->id ?>?lang=<?= Application::getInstance()->user->getLangId() ?>"> <?= t("Regresar") ?></a></h4>
  <?php endif; ?>

  <br>

  <div id="vue-cont">

  </div>

</div>

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>

<?= $data->script ?>



<script>
  $(document).ready(function() {
 
    $('.page-title').hide();
   
  });
</script>



<div class="modal fade" id="eligibility_modal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollableModalTitle">Elegibilidad (quién puede solicitarlo)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
            
<p>Todos los solicitantes deben cumplir con los criterios mínimos de elegibilidad detallados a continuación, o su solicitud no será considerada:</p>                        
                <ul>
                    <li>Pequeñas y medianas empresas (PYMEs), podrán ser personas naturales o jurídicas, domiciliadas o con operaciones en el Distrito Metropolitano de Quito, sin que esto pueda limitar su alcance a nivel nacional o global. </li>
                    <li>Copia del Registro Único de Contribuyentes - RUC que debe alinearse con la actividad económica del proyecto. </li>
                    <li>Certificado de cumplimiento tributario que refleje estar al día de sus obligaciones tributarias, emitido por el Servicio de Rentas Internas. </li>
                    <li>Certificado de cumplimiento de obligaciones patronales emitido por el Instituto Ecuatoriano de Seguridad Social, en el que se demuestre que no existen obligaciones pendientes. </li>
                    <li>Deberá pertenecer a una de las categorías de PYME según la siguiente definición del INEC:  </li>
                    <ul>
                        <li>Empresas Pequeñas: </li>
                        <ul>
                            <li>Con ingresos entre USD $100.001 a USD $1.000.000.  </li>
                            <li>Con dotación de personal entre 10 a 49 trabajadores.  </li>
                        </ul>
                        <li>Empresas Medianas “B”: </li>
                        <ul>
                            <li>Con facturación entre USD $1.000.001 a USD $2.000.000.  </li>
                            <li>Con dotación de personal entre 50 a 99 trabajadores.  </li>
                        </ul>
                    </ul>
                    <li>Las empresas deberán operar en uno de los siguientes sectores:  </li>
                    <ul>
                        <li>Manufactura </li>
                        <li>Industria de Alimentos </li>
                        <li>Hotelería y Turismo  </li>
                        <li>Prestación de servicios profesionales  </li>
                        <li>Restaurantes y servicio de comida </li>
                        <li>Tecnología </li>
                        <li>Construcción </li>
                        <li>Otros (durante esta convocatoria se permitirá la aplicación de otros sectores no identificados en este apartado, pero que puedan demostrar propuestas de proyectos sólidos y sostenibles para dinamizar su operación y generar fuentes de empleo). </li>
                    </ul>
                    <li>El negocio debe haber estado operativo durante al menos los últimos 4 años y estar activo en la actualidad. </li>
                    <li>Deberá estar al día en sus obligaciones: Tributarias, Seguridad Social, laboral, ambiental, sanitaria, (si aplica), entre otros que podrían ser requeridos de acuerdo al giro de negocio de cada PYME.  </li>
                    <li>Deben justificar, en el modelo de negocio propuesto, que cuentan con la capacidad necesaria y clientes potenciales para crecer y generar fuentes de trabajo sostenible.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->