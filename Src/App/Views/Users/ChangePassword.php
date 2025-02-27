<?php use \App\Core\Application;

$user_obj = empty($data['user_obj']) ? null : $data['user_obj'];
$user_id = empty($user_obj) ? null : $user_obj->id;
$user_full_name = empty($user_obj) ? null : $user_obj->full_name;


?>

<?php Application::getInstance()->view->renderView('inc/authTemplate', $data); ?>

<template id="tpl-main">

    <div>
        
        <div class="text-center w-75 m-auto">
            <h4 class="text-dark-50 text-center mt-0 font-weight-bold"><?= t("Change Password") ?> <span v-if="user_id != null && user_id.length > 0">({{user_full_name}})</h4>
        </div>

        <!-- Error Panel -->
        <div v-if="error" v-html="error" class="mb-2 text-danger"> 
        </div>

        <form method="POST" autocomplete="off">
            <div v-if="user_id == null || user_id.length == 0" class="mb-3">
                <label for="current_password"><?= t("Current Password") ?></label>
                <input 
                    class="form-control" 
                    v-model="current_password" 
                    type="password" 
                    name="current_password" 
                    required 
                    @keyup.enter="submit"
                    placeholder="Enter current password">
            </div>

            <div class="mb-3">
                <label for="new_password"><?= t("New Password") ?></label>
                <input 
                    class="form-control" 
                    v-model="new_password" 
                    type="password" 
                    name="new_password" 
                    required 
                    @keyup.enter="submit"
                    placeholder="Enter password">
            </div>

            <div class="mb-3">
                <label for="repassword"><?= t("Retype Password") ?></label>
                <input 
                    class="form-control" 
                    v-model="new_password2" 
                    type="password" 
                    name="repassword" 
                    required 
                    @keyup.enter="submit"
                    placeholder="Enter password again">
            </div>

            
            <div class="mb-3">
                <div class="form-check bg-light">
                    <input type="checkbox" v-model="update_odk_password" name="update_odk_password"  class="form-check-input" id="update_odk_password">
                    <label class="form-check-label" for="update_odk_password"><?= t("Update ODK accounts password also") ?></label>
                </div>
            </div>

            <div class="d-grid mb-0 text-center">
                <button :disabled="loading" class="btn btn-primary" @click="submit()" type="button">
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <?= t("Change Password") ?>
                </button>

            </div>
            
        </form>

        
        <footer class="footer footer-alt">
            <div class="d-grid mb-0 text-center">
                <a class="btn btn-link" href="/"><i class="mdi mdi-home me-1"></i> <?= t("Back to Home") ?></a>
            </div>
        </footer>
    </div>
    
</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            current_password: null,
            new_password: null,
            new_password2: null,
            loading: false,
            user_id: '<?= x($user_id) ?>',
            user_full_name: '<?= x($user_full_name) ?>',
            error: '',
            update_odk_password: false,
        },
        methods: {
            submit: function(){

                this.error = null;
                this.loading = true;
                let self = this;
                var formData = new FormData();
                formData.append('current_password', this.current_password);
                formData.append('new_password', this.new_password);
                formData.append('new_password2', this.new_password2);
                formData.append('update_odk_password', this.update_odk_password);
                formData.append('user_id', this.user_id);

                axios({
                    method: 'post',
                    url: '/user/change_password?response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                        'Csrf-Token': '<?= Application::getInstance()->csrfProtection->create("change_password");?>',
                    }
                })
                .then(function(response){
                    if(response.data.status == 'success'){
                        
                        $.toast({heading: 'Success',text: response.data.message == undefined ? 'Task completed successfuly' : response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'success'});
                        window.location.href = '/';
                    } else {
                        
                        self.loading = false;

                        if(response.data.message != null && response.data.message.length > 0){
                            self.error = response.data.message;
                        } else {
                            self.error = 'Something went wrong';
                        }
                        
                    }
                    
                })
                .catch(function(error){

                    if(error.response != undefined && error.response.data.status == 'failed') {
                        self.error = error.response.data.message;
                    } else {
                        self.error = 'Something went wrong';
                    }
                    self.loading = false;
                    
                });
            }
        },
    })
</script>
