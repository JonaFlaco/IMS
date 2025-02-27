<?php use App\Core\Application; 

$flashContent = Application::getInstance()->session->flash();
?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Import Generic Cron</h4>
                </div>
            </div>
        </div>     
        
        <?= $flashContent ?>

        <div class="card">
            <div class="card-body">

                <form name="frm_upload" @submit="loading = true" method="post" action="/dataimport/importGcron" enctype='multipart/form-data'>
                    <label for="file">Please select the excel file that you want to import</label><br>
                    <input name="file" type="file"><br><br>
                    <button 
                        :disabled="loading" 
                        class="btn btn-primary" 
                        type="submit">
                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Import
                    </button>
                </form>
            </div>
        </div>

    </div>    

</template>

<script>

new Vue({
    el: '#vue-cont',
    template: '#tpl-main',
    data: {
        loading: false,
    },
});

</script>