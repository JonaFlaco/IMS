<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>


<style>
    table td, table th {
        border: 1px solid;
    }

</style>


<div class="row">
    <div class="col-lg-12 pt-3">
        <div class="page-title-box">
            <div class="page-title-right mt-0">
                <ol class="breadcrumb m-0">
                    <?php 
                        $total = sizeof($data['levels']);
                        $i = 1;
                        foreach($data['levels'] as $itm) { 
                            if($i++ < $total): ?>
                                <?= sprintf('<li class="breadcrumb-item"><a href="/%s"> %s </a></li>', $itm['link'], $itm['title']) ?>
                            <?php else: ?>                            
                                <?= sprintf('<li class="breadcrumb-item active">%s</li>', $itm['title']) ?>
                            <?php endif; ?>
                    <?php } ?>
                </ol>
            </div>
            <h4 class="page-title"><?= $data['title'] ?></h4>
        </div>
    </div>
</div> 

<div class="row">
    <div class="col-md-12">

        <div class="card p-1" >
            <div class="card-body p-0">
                <div class="col-md-8" style="margin: 0 auto; float:none;">
  
                    <?= $data['content'] ?>

                    <?php if(!empty($data['parentLink'])): ?>
                        <hr>
                        <a href="/<?= $data['parentLink'] ?>">Go back</a>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>
