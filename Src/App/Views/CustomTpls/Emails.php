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
                        <span class="mdi mdi-email-mark-as-unread"></span>
                        <?php echo $nodeData->subject; ?>
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
                        
                        <p class="card-p"><strong>To:</strong> <span class="ml-2"> <?php echo $nodeData->email_to; ?> </span></p>
                        <p class="card-p"><strong>cc:</strong> <span class="ml-2"> <?php echo $nodeData->email_cc; ?> </span></p>
                        <p class="card-p"><strong>bcc:</strong> <span class="ml-2"> <?php echo $nodeData->email_bcc; ?> </span></p>
                        <p class="card-p"><strong>Planned Send Date:</strong> <span class="ml-2"> <?php echo $nodeData->planned_send_date; ?> </span></p>
                        
                        <p class="card-p"><strong>Content-Type:</strong> <span class="ml-2"> <?php echo $nodeData->ctype_id_display; ?> </span></p>
                        <p class="card-p"><strong>Record Id:</strong> <span class="ml-2"> <?php echo $nodeData->record_id; ?> </span></p>
                        <p class="card-p"><strong>Sent Date:</strong> <span class="ml-2"> <?php echo $nodeData->sent_date; ?> </span></p>
    
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3">Body</h4>
                        <?= $nodeData->body; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
       
<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>            