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
                    <h4 class="page-title text-danger">
                        <span class="dripicons-flag text-alert"></span>
                        Error #<?php echo $nodeData->id; ?>
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
                        
                        <p class="card-p"><strong>User:</strong> <span class="ml-2"> <?php echo $nodeData->created_user_id_display; ?> </span></p>
                        <p class="card-p"><strong>Date:</strong> <span class="ml-2"> <?php echo $nodeData->created_date; ?> </span></p>
                        <p class="card-p"><strong>Title:</strong> <span class="ml-2"> <?php echo $nodeData->title; ?> </span></p>
                        <p class="card-p"><strong>Code:</strong> <span class="ml-2"> <?php echo $nodeData->code; ?> </span></p>
                        <p class="card-p"><strong>Location:</strong> <span class="ml-2"> <?php echo $nodeData->location; ?> </span></p>
                        <p class="card-p"><strong>Line:</strong> <span class="ml-2"> <?php echo $nodeData->line; ?> </span></p>
                        <p class="card-p"><strong>Class:</strong> <span class="ml-2"> <?php echo $nodeData->class; ?> </span></p>
                        
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3">Extra Detail</h4>
                        <code><?php echo $nodeData->extra_detail ?? "N/A";?></code>
        
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3">Trace</h4>
                        <code><?php echo $nodeData->trace;?></code>
        
                    </div>
                </div>
            </div>
        </div>
    </div>    

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>