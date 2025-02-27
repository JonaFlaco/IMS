<?php use App\Core\Application; ?>


<template id="tpl-user-menu-component">
    
    <li class="dropdown notification-list">
        <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="javascript: void(0);" role="button" aria-haspopup="false" aria-expanded="false">
            <span class="account-user-avatar"> 
                <img src="<?= Application::getInstance()->user->getProfilePicture() ?>" alt="user-image" class="rounded-circle">
            </span>
                
            <span>
                <span class="account-user-name"><?= Application::getInstance()->user->getFullName() ?></span>
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">

            <!-- item-->
            <a href="/users/edit/<?= Application::getInstance()->user->getId() ?>" class="dropdown-item notify-item">
                <i class="mdi mdi-account-circle me-1"></i>
                <span><?= t("Mi cuenta") ?></span>
            </a>
            <?php if(Application::getInstance()->settings->get("enable_oic")){ ?>
            <a href="/oic" class="dropdown-item notify-item">
                <i class="mdi mdi-account-star-outline me-1"></i>
                <span><?= t("Gestionar OIC") ?></span>
            </a>
            <?php } ?>
            <a href="/user/change_password" class="dropdown-item notify-item">
                <i class="mdi mdi-key-variant "></i>
                <span><?= t("Cambiar contraseña") ?></span>
            </a>

            <?php if (Application::getInstance()->user->isAdmin()): ?>

            <a href="/help" class="dropdown-item notify-item">
                <i class="mdi mdi-help-circle me-1"></i>
                <span><?= t("Help Center") ?></span>
            </a>

            <?php endif; ?>

            <!-- item-->
            <a href="/user/logout" class="dropdown-item notify-item">
                <i class="mdi mdi-logout me-1"></i>
                <span><?= t("Cerrar sesión") ?></span>
            </a>

        </div>
    </li>

</template>


<script type="text/javascript">

    var component = {
        template: '#tpl-user-menu-component',
    }

    Vue.component('user-menu-component', component)

</script>
