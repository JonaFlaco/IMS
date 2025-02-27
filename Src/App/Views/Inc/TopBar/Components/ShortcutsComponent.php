<?php use App\Core\Application; ?>

<template id="tpl-shortcuts-component">

<li class="dropdown notification-list d-none d-sm-inline-block">
            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="javascript: void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="dripicons-view-apps noti-icon"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg p-0">

                <div class="p-2">
                    Coming Soon
                    <!-- <div class="row g-0">
                        <div class="col">
                            <a class="dropdown-icon-item" href="javascript: void(0);">
                                <img src="/assets/theme/images/brands/slack.png" alt="slack">
                                <span>Slack</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="javascript: void(0);">
                                <img src="/assets/theme/images/brands/github.png" alt="Github">
                                <span>GitHub</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="javascript: void(0);">
                                <img src="/assets/theme/images/brands/dribbble.png" alt="dribbble">
                                <span>Dribbble</span>
                            </a>
                        </div>
                    </div>

                    <div class="row g-0">
                        <div class="col">
                            <a class="dropdown-icon-item" href="javascript: void(0);">
                                <img src="/assets/theme/images/brands/bitbucket.png" alt="bitbucket">
                                <span>Bitbucket</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="javascript: void(0);">
                                <img src="/assets/theme/images/brands/dropbox.png" alt="dropbox">
                                <span>Dropbox</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="javascript: void(0);">
                                <img src="/assets/theme/images/brands/g-suite.png" alt="G Suite">
                                <span>G Suite</span>
                            </a>
                        </div>

                    </div> -->
                </div>

            </div>
        </li>
        
</template>


<script type="text/javascript">

    var component = {
        template: '#tpl-shortcuts-component',
    }

    Vue.component('shortcuts-component', component)

</script>
