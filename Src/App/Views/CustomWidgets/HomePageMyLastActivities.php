<?php

use App\Core\Application;
use \App\Helpers\DateHelper;

$data = Application::getInstance()->coreModel->get_my_last_activities();
?>

<div class="card">
    <div class="card-body">
        
        <h4 class="header-title mb-2">My Last Activities</h4>

        <div data-simplebar style="max-height: 419px;"> 
            <div class="timeline-alt pb-0">
                
            <?php foreach($data as $item): ?>
                <div class="timeline-item">
                    <i class="mdi mdi-arrow-right bg-info-lighten text-info timeline-icon"></i>
                    <div class="timeline-item-info">
                        <a href="javascript: void(0);" class="text-info mb-1"><?= $item->title ?></a>
                        <span class="float-end"><?= DateHelper::humanify( strtotime($item->date)) ?></span>
                        <p class="mb-0">
                            <small><?= $item->ctype_name ?> #<?= $item->content_id ?></small>
                        </p>
                        <small class="d-block pb-2"><?= $item->justification ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
            <!-- end timeline -->
        </div> <!-- end slimscroll -->
    </div>
    <!-- end card-body -->
</div>