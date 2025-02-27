<?php 

use App\Core\Application;
$data['sett_blank'] = true;

$data = (object)$data; 
$surveyObj = $data->surveyObj;

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

<div>

    <?= Application::getInstance()->view->renderView('surveys/surveyTopBar', (array)$data) ?>

    <div class="col-lg-8" style="margin: 0 auto; float:none;">


        <div class="row pt-2 pb-2">
                
            <div class="col-lg-12 mb-3 " >
                <table class="col-lg-12">
                    <tr>
                        <td>
                            <h1 class="ml-3"><?php echo $data->title; ?></h1>
                        </td>
                    </tr>
                </table>
            </div>
            
        </div>
        
        <div id="vue-cont">


        </div>

    </div>
</div> 

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>

<?= $data->script ?>
