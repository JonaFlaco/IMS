<?php

use App\Core\Application;
use \App\Helpers\DateHelper;

$data = Application::getInstance()->coreModel->bg_tasks_get();
?>

<div class="card">
    <div class="card-body">
        
        <h4 class="header-title mb-2">My BG Tasks</h4>

        <div data-simplebar style="max-height: 419px;"> 
            <div class="timeline-alt pb-0">
                
            <?php foreach($data as $item): 
                
                $item->theme = "secondary";
                if($item->status_id == 22) {
                    $item->theme = "success";
                }

                ?>
                <div class="timeline-item">
                    <i class="mdi mdi-arrow-right bg-<?= $item->theme ?>-lighten text-<?= $item->theme ?> timeline-icon"></i>
                    <div class="timeline-item-info pb-2">
                        <span class="text-<?= $item->theme ?> mb-1"><?= $item->name ?></span>
                        <span class="float-end badge bg-<?= $item->theme ?>"><?= $item->status_name ?></span>

                        <small class="d-block"><?= DateHelper::humanify( strtotime($item->created_date)) ?></small>

                        <?php if(isset($item->output_file_name)): ?>
                            <small class="d-block"><a href="/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=<?= $item->output_file_name ?>"><i class="mdi mdi-download"></i> Download File</a></small>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
            <!-- end timeline -->
        </div> <!-- end slimscroll -->
    </div>
    <!-- end card-body -->
</div>