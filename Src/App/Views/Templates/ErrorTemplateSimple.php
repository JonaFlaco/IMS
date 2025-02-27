<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ error.title }}</h4>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="text-center">
                    <img src="/assets/theme/images/file-searching.svg" height="90" alt="File not found Image">

                    <h1 class="text-primary mt-4">Error!</h1>                                    

                    <div class="card">
                        <div class="card-body">
                        
                            <h4 class="card-title text-danger mt-3">{{ error.message }}</h4>

                        </div>
                    </div> 

                </div>
            </div>
        </div>
                
    </div>

</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            error: <?= json_encode((array)$data) ?>,
        }
    });
</script>