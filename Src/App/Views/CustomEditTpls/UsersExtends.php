<?php use \App\Core\Application; ?>

<?= Application::getInstance()->view->renderView("CustomEditTpls/UsersComponents/IsActiveComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/UsersComponents/OdkAccountComponent", []) ?>
<?= Application::getInstance()->view->renderView("CustomEditTpls/UsersComponents/ChangePasswordComponent", []) ?>