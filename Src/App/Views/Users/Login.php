<?php 
    use \App\Core\Application; 
    $appTitle = Application::getInstance()->settings->get("app_title");
    $adminEmail = Application::getInstance()->settings->get('app_email');
    $allowLoginUsingAzureAd = Application::getInstance()->settings->get('sys_allow_login_using_azure_ad');
    $allowRequestAccountCreation = Application::getInstance()->settings->get('sys_allow_request_account_creation');
    $useAzureAD = isset($data['useAzureAD']) && $data['useAzureAD'] ? true : false;

    $flashContent = Application::getInstance()->session->flash();
?>

<?php Application::getInstance()->view->renderView('inc/authTemplate', (array)$data); ?>

<template id="tpl-main">

   <div>

        <?= $flashContent ?>
        
        <!-- title-->
        <h4 class="mt-0">Sign In</h4>
        <p v-if="loginUsingAzureAD" class="text-muted mb-4">Enter your email address and password <strong>that you use with your email</strong> to login.</p>
        <p v-else class="text-muted mb-4">Enter your email address and password <strong>specific to IMS</strong> to login.</p>

        <div v-if="error" class="mb-2 text-danger"> 
            <i class="mdi mdi-close-circle me-1"></i>
            <span>{{ error }}</span>
        </div>

        <!-- form -->
        <form :action="'/user/login/' + (loginUsingAzureAD ? 1 : 0)" id="login_form" @submit="loading = true" method="post" autocomplete="off">
            <div class="mb-3">
            <label for="emailaddress" class="form-label">Username/Email</label>
            <input 
                class="form-control" 
                type="text" 
                tabindex="10"
                id="username" 
                name="username" 
                required
                v-model="username"
                placeholder="Enter your username or email"
                >
            </div>
            <div class="mb-3">
                <a href="/user/reset_request" class="text-muted float-end"><small>Forgot your password?</small></a>
                <label for="password" class="form-label">Password</label>
                <div class="input-group input-group-merge">
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        id="password" 
                        name="password"
                        tabindex="11" 
                        autocomplete="on"
                        class="form-control" 
                        placeholder="Enter your password"
                        required
                        >
                    <div class="input-group-text cursor-pointer" @click="showPassword = !showPassword">
                        <i :class="showPassword ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline'"></i>
                    </div>
                </div>
            </div>

            <div class="d-grid mb-0 text-center">
                <button tabindex="12" id="btnLogin" :disabled="loading" class="btn btn-primary" type="submit"> 
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Log In 
                </button>
                
                <?php if($allowLoginUsingAzureAd): ?>
                    <a href="/user/loginWithAzureAD" tabindex="13" id="btnloginWithAzureAD" class="btn btn-success mt-1" type="button"> 
                        <i class="mdi mdi-microsoft"></i>
                        Login using IOM Email 
                    </a>
                <?php endif; ?>
                <?php if($allowRequestAccountCreation): ?>
                    <a href="/user/requestAccountCreation" tabindex="13" id="btnRequestAccount" class="btn btn-link mt-3" type="button"> 
                        <i class="mdi mdi-plus"></i>
                        Request for Account Creation
                    </a>
                <?php endif; ?>
            </div>
            
        </form>
        <!-- end form-->

        <?php if(isset($adminEmail)): ?>
            <footer class="footer footer-alt">
                If you have trouble login, please contact <strong><?= x($adminEmail) ?></strong>
            </footer>
        <?php endif ?>

    </div>

</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            loading: false, 
            loginUsingAzureAD: '<?= x($useAzureAD) ?>',
            error: '<?= filter_var(_strip_tags($data['error'] ?? '') , FILTER_SANITIZE_ADD_SLASHES) ?>',
            username: '<?= filter_var(_strip_tags($data['username'] ?? '') , FILTER_SANITIZE_ADD_SLASHES) ?>',
            showPassword: false,
        },
        mounted() {
            setTimeout(function () { 
                //document.getElementById("username").focus();
            }, 200);
            
        },
        methods: {

        }
    })
</script>
