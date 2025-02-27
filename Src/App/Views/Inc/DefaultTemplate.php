<?php use \App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/header', (array)$data) ?>
    
    <div id="vue-cont">
        
    </div>    

<!-- Load Components -->
<?= Application::getInstance()->view->renderView('inc/Components/PageTitleRowComponent', (array)$data) ?>

<?= Application::getInstance()->view->renderView('inc/footer', (array)$data) ?>