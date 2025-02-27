<?php

use App\Core\Application;

$data = (object)$data; 
$nodeData = $data->nodeData;

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

    <div id="cont">
        
        <!-- start page title -->
        <div class="row col-md-9" style="margin: 0 auto; float:none;">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title text-gray">
                        <span class="dripicons-flag"></span>
                        <?php echo $nodeData->title; ?>
                    </h4>
                </div>
            </div>
        </div>     
        <!-- end page title --> 
        
        <div class="row col-md-9" style="margin: 0 auto; float:none;">
        
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3">Details</h4>
                        
                        <p class="card-p"><strong>Id:</strong> <span class="ml-2"> <?php echo $nodeData->id; ?> </span></p>
                        <p class="card-p"><strong>Date:</strong> <span class="ml-2"> <?php echo $nodeData->created_date; ?> </span></p>
                        <p class="card-p"><strong>Title:</strong> <span class="ml-2"> <?php echo $nodeData->title; ?> </span></p>
                        <p class="card-p"><strong>From:</strong> <span class="ml-2"> <?php echo $nodeData->from_user_id_display; ?> </span></p>
                        <p class="card-p"><strong>Ctype:</strong> <span class="ml-2"> <?php echo $nodeData->ctype_id_display; ?> </span></p>
                        <p class="card-p"><strong>Record Id:</strong> <span class="ml-2"> <?php echo $nodeData->record_id; ?> </span></p>
        
                        <p class="card-np"><strong>To Users:</strong> <span class="ml-2"> 
                            <?php 
                                if(!empty($nodeData->to_users_display)){
                                    echo "<ul>";
                                    foreach(_explode("\n", $nodeData->to_users_display) as $itm){
                                        echo "<li>$itm</li>";
                                    }
                                    echo "</ul>";
                                }
                            
                            ?> </span></p><p class="card-p"></p>
                        <p class="card-p"><strong>Message:</strong> <span class="ml-2"> <?php echo $nodeData->message; ?> </span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>    

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>