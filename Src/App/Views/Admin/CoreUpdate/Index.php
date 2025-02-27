<?php 
$data["title"] = "Core Update";

use App\Core\Application; 

$flashContent = Application::getInstance()->session->flash();
?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Update Core</h4>
                </div>
            </div>
        </div>     
        
        <?= $flashContent ?>

        <div class="card">
            <div class="card-body">

                <form name="frm_upload" @submit="loading = true" method="post" action="/core/update" enctype='multipart/form-data'>
                    <label for="file">Please select the update package</label><br>
                    <input name="file" type="file"><br><br>
                    <button 
                        :disabled="loading" 
                        class="btn btn-primary" 
                        type="submit">
                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Update
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