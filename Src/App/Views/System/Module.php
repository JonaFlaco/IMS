<?php 

use App\Core\Application;

$data = (object)$data; 
$nodeData = $data->nodeData;


$items = $data->items;

if(!!empty($nodeData->icon)){
    $nodeData->icon = "module.png";
}

$flashContent = Application::getInstance()->session->flash();

?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>

    <div id="cont">
        <!-- start page title -->

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right mt-0">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="/">Home</a>
                            </li> 
                            <li class="breadcrumb-item active"><?php echo $nodeData->name; ?></li>
                        </ol>
                    </div> 
                    <h4 class="page-title"><?php echo $nodeData->name; ?></h4>
                </div>
            </div>
        </div>
        
        <?php 
            if(sizeof($items) == 0){
                \App\Core\Application::getInstance()->session->flash("flash_info","This module is empty");
            }
            echo $flashContent; 
        ?>
        
        <div class="col-12">
    
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box text-center mt-2">
                        <img class="" src="/assets/app/images/icons/<?php echo $nodeData->icon; ?>" height="100" width="100"/>    
                        <h3 class="page-title"><?php echo $nodeData->name; ?></h3>
                        <?php if(!empty($nodeData->description)){ ?>
                        <p class="text-muted mt-2 ms-5 pe-2 ps-2 me-5">
                            <?php echo $nodeData->description;?>
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>     
            
            
            <div class="col-12 ms-5 me-5">
                <?php 
                
                $current_group = "-EMPTY-";
                $i = 0;
                foreach($items as $itm){ 
                    
                    if($current_group != $itm->group_name){
                        
                        echo "<h2 " . ($i++ > 0 ? "class=\"mt-4\"" : "") . ">$itm->group_name</h2>";
                        $current_group = $itm->group_name;
                    }

                    if(!!empty($itm->icon)){
                        $itm->icon = "default.png";
                    }
                    ?>

                    <div class="d-flex align-items-start mt-1 mb-1">
                        <a href="<?= $itm->url; ?>"><img class="me-3 rounded-circle" src="/assets/app/images/icons/<?php echo $itm->icon; ?>" width="40" alt="Generic placeholder image"></a>
                        <div class="w-100 overflow-hidden">
                            <a class="text-dark" target="_blank" href="<?php echo $itm->url; ?>"><h5 class="mt-0 mb-1"><?php echo $itm->name; ?></h5></a>
                            <span class="font-13"><?= $itm->description ?> </span>
                        </div>
                    </div>
                    
                <?php } ?>  
            </div>


               
        </div>


    </div>    

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>