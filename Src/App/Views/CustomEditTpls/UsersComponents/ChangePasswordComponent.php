<?php use App\Core\Application; ?>

<script type="text/x-template" id="tpl-change-password-component">
    <div>

        <!-- Top modal -->
        <div id="change_password_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="topModalLabel">Change Password</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                        <div class="modal-body">
                                    
                        <div class="mb-3">
                            <label for="current_password">Current Password</label>
                            <input 
                                class="form-control" 
                                v-model="current_password" 
                                type="password" 
                                name="current_password" 
                                required 
                                placeholder="Enter current password">
                        </div>

                        <div class="mb-3">
                            <label for="new_password">New Password</label>
                            <input 
                                class="form-control" 
                                v-model="new_password" 
                                type="password" 
                                name="new_password" 
                                required 
                                placeholder="Enter password">
                        </div>

                        <div class="mb-3">
                            <label for="repassword">Retype Password</label>
                            <input 
                                class="form-control" 
                                v-model="new_password2" 
                                type="password" 
                                name="repassword" 
                                required 
                                placeholder="Enter password again">
                        </div>

                        <div class="mt-3 bg-light">
                            <div class="form-check">
                                <input type="checkbox" v-model="update_odk_password"  class="form-check-input" id="update_odk_password_also">
                                <label class="form-check-label" for="update_odk_password_also">Update ODK accounts password also</label>
                            </div>
                        </div>
                            
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button @click="changePassword" :disabled="loading" type="button" class="btn btn-primary">
                            Change Password
                            <span v-if="loading">...</span>
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

            <button @click="showChangePasswordModal" class="btn btn-link">
                <i class="mdi mdi-account-key"></i>
                Change Password
            </button>

        </div>
    </div>
</script>



<script>
    Vue.component('change-password-component', {
        template: '#tpl-change-password-component',
        props: {
            title: {},
            value: {},
        },
        data() {
            return {
                loading: false,
                current_password: null,
                new_password: null,
                new_password2: null,
                update_odk_password: false,
            }
        },
        mounted() {
        },
        methods: {
            showChangePasswordModal() {

                this.current_password = null;
                this.new_password = null;
                this.new_password2 = null;

                var myModal = new bootstrap.Modal(document.getElementById('change_password_modal'), {
                    backdrop: 'static',
                    keyboard: false,
                });
                myModal.show();
            },
            hideChangePasswordModal() {
                loadingModal = bootstrap.Modal.getInstance(document.getElementById('change_password_modal'))
                loadingModal.hide();
            },
            async changePassword() {

                let self = this;

                if(self.$parent.id == null) {
                    $.toast({
                            heading: 'Error',
                            text: 'This feature does not work in add new code',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    return;
                }

                self.loading = true;

                let formData = new FormData();
                formData.append('username', this.$parent.name);
                formData.append('current_password', this.current_password);
                formData.append('new_password', this.new_password);
                formData.append('new_password2', this.new_password2);
                formData.append('update_odk_password', this.update_odk_password);

                var response = await axios.post(
                        '/user/change_password/?response_format=json',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'Csrf-Token': '<?= Application::getInstance()->csrfProtection->create("change_password");?>',
                            }
                        }
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });

                    });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        
                        $.toast({
                            heading: 'Success',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });

                        self.hideChangePasswordModal();
                        location.reload();


                    } else {
                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }

                }

                self.loading = false;
            },
        },        
    });
</script>
