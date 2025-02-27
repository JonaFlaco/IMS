<script type="text/x-template" id="tpl-odk-account-component">
    <div>

        <!-- Top modal -->
        <div id="odk_change_password_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="topModalLabel">
                            Change ODK Account Password 
                            <span v-if="changePasswordItem">for {{ changePasswordItem.odk_name }} </span>
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                             
                        <form autocomplete="off">
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
                        </form>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button @click="changePassword" :disabled="change_pwd_loading" type="button" class="btn btn-primary">
                            Change Password
                            <span v-if="change_pwd_loading">...</span>
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div v-if="loading">Loading...</div>
        
        <div v-else-if="userData && userData.length > 0">

            <div v-if="lastPassword" class="alert alert-warning" role="alert">
                <i class="mdi mdi-key"> </i> Password for the newly created account is: <strong>{{ lastPassword }} </strong>
                <br />
                Keep it somewhere, you will see the password only once.
            </div>

            <div v-for="item in userData" :key="item.odk_id">

                <h4>
                    <i class="mdi mdi-database"></i> 
                    {{ item.odk_name }} 
                </h4>

                <div v-if="item.user">
                    <p>
                        Status: 
                        <span v-if="item.user.deleted == 1" class="ms-1 badge bg-danger rounded-pill">Disabled</span>
                        <span v-else class="ms-1 badge bg-success rounded-pill">Active</span>
                    </p>
                    <p>
                        Roles: 
                        <span v-if="item.user.is_admin == 1" class="ms-1 badge badge-danger-lighten rounded-pill">Admin</span>
                        <span v-if="item.user.can_access_admin == 1" class="ms-1 badge badge-warning-lighten rounded-pill">Can Access Admin</span>
                        <span v-if="item.user.can_collect_data == 1" class="ms-1 badge badge-dark-lighten rounded-pill">Can Collect Data</span>
                    </p>
                    
                        
                    <button @click="showChangePasswordModal(item)" class="btn btn-link ps-0 pe-0">
                        <i class="mdi mdi-account-key"></i>
                        Change Password
                    </button>
                </div>

                <div v-else>

                    <p>
                        <i class="mdi mdi-alert-octagon-outline"></i>
                        Account not found
                    </p>

                    <button class="btn btn-link px-0" @click="createOdkAccount(item)">
                        <i class="mdi mdi-account"></i>
                        Create Account
                        <span v-if="create_odk_account_loading">...</span>
                    </button>
                </div>

                <hr>
            </div>

        </div>
        <div v-else-if="error">
            <span class="text-danger"> {{ error }} </span>
        </div>
        <div v-else >
            <i class="mdi mdi-alert-octagon-outline"></i>
            No ODK database found
        </div>
    </div>
</script>



<script>
    Vue.component('odk-account-component', {
        template: '#tpl-odk-account-component',
        props: {
            title: {},
            value: {},
        },
        data() {
            return {
                loading: false,
                change_pwd_loading: false,
                create_odk_account_loading: false,
                userData: [],
                error: null,
                current_password: null,
                new_password: null,
                new_password2: null,
                changePasswordItem: null,
                lastPassword: null,
            }
        },
        mounted() {
            this.loadData();
        },
        methods: {
            async loadData() {

                let self = this;
                self.userData = [];

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
                var response = await axios.get('/InternalApi/OdkGetUser/' + this.$parent.name + '?response_format=json',   
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
                        self.error = 'Something went wrong while loading data';
                    });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        
                        self.userData = response.data.result;

                    } else {

                        $.toast({
                            heading: 'Error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });

                        self.error = 'Something went wrong while loading data';
                    }

                }

                self.loading = false;
            },
            showChangePasswordModal(item) {

                this.current_password = null;
                this.new_password = null;
                this.new_password2 = null;
                this.changePasswordItem = item;

                var myModal = new bootstrap.Modal(document.getElementById('odk_change_password_modal'), {
                    backdrop: 'static',
                    keyboard: false,
                });
                myModal.show();
            },
            hideChangePasswordModal() {
                loadingModal = bootstrap.Modal.getInstance(document.getElementById('odk_change_password_modal'))
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

                self.change_pwd_loading = true;

                let formData = new FormData();
                formData.append('username', this.$parent.name);
                formData.append('current_password', this.current_password);
                formData.append('new_password', this.new_password);
                formData.append('new_password2', this.new_password2);
                formData.append('odk_id', this.changePasswordItem.odk_id);

                var response = await axios.post(
                        '/InternalApi/odkChangePassword/?response_format=json',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
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

                self.change_pwd_loading = false;
            },
            async createOdkAccount(item) {

                let self = this;
                self.lastPassword = null;
                
                if(self.$parent.id == null) {
                    $.toast({
                            heading: 'Error',
                            text: 'This feature does not work in add new user',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    return;
                }


                if(!confirm('Are you sure?')) {
                    return;
                }

                self.create_odk_account_loading = true;

                let formData = new FormData();
                formData.append('user_id', this.$parent.id);
                formData.append('odk_id', item.odk_id);
                
                var response = await axios.post(
                        '/InternalApi/odkCreateAccount/?response_format=json',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
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
                        self.lastPassword = response.data.newpwd;
                        self.loadData();

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

                self.create_odk_account_loading = false;
            }
        },        
    });
</script>
