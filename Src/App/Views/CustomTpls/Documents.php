
<?php 

use App\Core\Application;

$data = (object)$data; 
$nodeData = $data->nodeData;

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

    <div id="cont">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title"><?php echo $nodeData->name; ?></h4>
                </div>
            </div>
        </div>     
        <!-- end page title --> 
        
        <div class="card">
            <!-- <div class="card-header alert-danger\">
                    <strong></strong>
            </div> -->
            <div class="card-body">
            

                <div class="col-md-12">

                    <p class="pt-1 pb-3">
                        <?php echo $nodeData->description; ?>
                    </p>

                    <?php foreach($nodeData->files as $file){ 
                        
                        foreach($file->attachments as $att){
                        ?>
                        <div class="card mb-1 mt-1 shadow-none border">
                            <div class="p-2">

                                <div class="row align-items-center">
                                    <div class="col">
                                        <p class="mb-2 text-muted font-weight-bold"><?php echo $file->title; ?></p>
                                    </div>

                                    <div class="col-auto">
                                        <a target="_blank" href="/filedownload?ctype_id=documents_files&field_name=attachments&size=orginal&file_name=<?php echo $att->name; ?>" class="btn btn-link btn-lg text-muted p-0 pe-1">
                                            <i class="dripicons-download"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <div class="col-auto">

                                        <?php if($att->extension == "png" || $att->extension == "jpg" || $att->extension == "gif"){ ?>
                                            <img src="/filedownload?ctype_id=documents_files&field_name=attachments&size=small&file_name=<?= $att->name; ?>" class="avatar-sm rounded" alt="file-image">
                                        <?php } else if ($att->extension == "doc" || $att->extension == "docx"){ ?>
                                            <img src="/assets/app/images/icons/doc.svg" class="avatar-sm rounded" alt="file-image">
                                        <?php } else if ($att->extension == "txt"){ ?>
                                            <img src="/assets/app/images/icons/doc.svg" class="avatar-sm rounded" alt="file-image">
                                        <?php } else if ($att->extension == "xls" || $att->extension == "xlsx"){ ?>
                                            <img src="/assets/app/images/icons/xls.svg" class="avatar-sm rounded" alt="file-image">
                                        <?php } else if ($att->extension == "pdf") { ?>
                                            <img src="/assets/app/images/icons/pdf.svg" class="avatar-sm rounded" alt="file-image">
                                        <?php } else { ?>
                                            <div class="avatar-sm">
                                                <span class="avatar-title bg-primary-lighten text-primary rounded">
                                                    <?php echo strtoupper(".$att->extension"); ?>
                                                </span>
                                            </div>
                                        <?php } ?>
                                        
                                    </div>
                                    
                                    <div class="col ps-0">
                                        <span class="text-muted"><?php echo $file->description; ?></span>
                                        <p class="mb-0"><?php echo (round(intval($att->size) / 1024,2)) . " KB"; ?></p>
                                    </div>

                                </div>
                                
                            </div>
                        </div>
                    <?php }} ?>

                </div>
            </div>
        </div>
        

    </div>    

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>