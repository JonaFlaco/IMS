<?php use \App\Core\Application; ?>

<?php Application::getInstance()->view->renderView('inc/authTemplate', (array)$data); ?>

<template id="tpl-main">
    
    <div>
        
        <div class="text-center w-75 m-auto">
            <h4 class="text-dark-50 text-center mt-0 font-weight-bold">Reset Password</h4>
            <p class="text-muted mb-4">Enter your email address and we'll send you an email with instructions to reset your password.</p>
        </div>

        <!-- Error Panel -->
        <div v-if="error" class="alert alert-danger">
            <i class="dripicons-warning me-2"></i>
            {{ error }}
        </div>

        <form action="/user/reset_request" @submit="loading = true" method="post">
            
            <div class="mb-3">
                <label for="emailaddress" class="form-label">Email address</label>
                <input 
                    class="form-control" 
                    type="email" 
                    name="email" 
                    v-model="email"
                    id="emailaddress" 
                    required="" 
                    placeholder="Enter your email">
            </div>
            
            <div class="d-grid mb-0 text-center">
                <button 
                    :disabled="loading" 
                    class="btn btn-primary" 
                    type="submit">
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Reset Password
                </button>
            </div>
        </form>


        <!-- Footer-->
        <footer class="footer footer-alt">
            <div class="d-grid mb-0 text-center">
                <a class="btn btn-link" href="/"><i class="mdi mdi-home me-1"></i> Back to Home</a>
            </div>
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
            email: '<?= x($data['email']  ?? '') ?>'
        },
    })
</script>
