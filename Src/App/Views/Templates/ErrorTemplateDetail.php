<?php use App\Core\Application; 

$data['err_stack_trace'] = null;

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Error</h4>
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
                        
                        <div v-if="isAdmin">

                            <a data-bs-toggle="collapse" href="#errorDetail" role="button" aria-expanded="true" aria-controls="errorDetail">
                                <div class="card-widgets">
                                    <i class="mdi mdi-minus"></i>
                                </div>
                            
                                <h4 class="card-title text-uppercase text-danger mt-3">Something went wrong!!</h4>

                                <h5>Reference Code: {{ error.ref_code }}
                            </a>
                            <div id="errorDetail" class="collapse show">
                                
                                <pre class="text-start">{{ error.err_msg }}</pre>

                                <pre class="text-start">File: {{ error.err_file }} Line: {{ error.err_line}}</pre>
                                <pre class="text-start">Code: {{ error.err_code }}</pre>
                                
                            </div>
                        </div>
                    
                        <div v-else >

                            <h4 class="card-title text-danger mt-3">{{ error.err_msg }}</h4>

                            <h5>Reference ID: ERR-{{ error.ref_code }}

                        </div>

                    <a class="btn btn-info mt-3" href="/"><i class="mdi mdi-reply"></i> Return Home</a>
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
            error: <?= json_encode((array)$data); ?>,
            isAdmin: <?= Application::getInstance()->user->isAdmin() ? 'true' : 'false' ?>,
        }
    });
</script>