<?php use \App\Core\Application; ?>

<?php Application::getInstance()->view->renderView('inc/authTemplate', (array)$data); ?>

<template id="tpl-main">

    <div>
        
        <div class="text-center w-75 m-auto">
            <h4 class="text-dark-50 text-center mt-0 font-weight-bold">Reset Password for {{ userFullName }}</h4>
            <p class="text-muted mb-4">Enter new password.</p>
        </div>

        <div v-if="error" class="alert alert-danger">
            {{ error }}
        </div>

        <form action="/user/reset" @submit="loading = true" method="post" autocomplete="off">
            <div class="mb-3">
                <label for="emailaddress">New Password</label>
                <input class="form-control" type="password" name="password" required placeholder="Enter password">
            </div>

            <div class="mb-3">
                <label for="emailaddress">Retype Password</label>
                <input class="form-control" type="password" name="repassword" required placeholder="Enter password again">
            </div>

            <div class="mb-3 bg-light">
                <div class="form-check">
                    <input type="checkbox" v-model="update_odk_password"  class="form-check-input" name="update_odk_password" id="update_odk_password">
                    <label class="form-check-label" for="update_odk_password">Update ODK accounts password also</label>
                </div>
            </div>

            <input class="form-control" type="hidden" name="ukey" v-model="ukey">

            <div class="mb-0 text-center">
                <button 
                    :disabled="loading" 
                    class="btn btn-primary" 
                    type="submit">
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Reset Password
                </button>
                
            </div>

        </form>

        
        <footer class="footer footer-alt">
            <a class="btn btn-link" href="/"><i class="mdi mdi-home me-1"></i> Back to Home</a>
        </footer>

    </div> 
    
</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            loading: false,
            error: '<?= x($data['error'] ?? '') ?>',
            ukey:'<?= x($data['ukey'] ?? '') ?>',
            userFullName: '<?= x($data['user_full_name'] ?? '') ?>',
            update_odk_password : false,
        },
    })
</script>
