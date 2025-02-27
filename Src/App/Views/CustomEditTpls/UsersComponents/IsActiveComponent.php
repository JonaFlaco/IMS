<script type="text/x-template" id="tpl-is-active-component">
    <div>
        <label class="form-label">Account Status:</label>
        <span class="badge" :class="'bg-' + (value ? 'success' : 'danger')"> {{ value ? "Active" : "Locked" }}</span>
        
        <div>
            <button v-if="value" @click="disableAccount" class="d-block text-danger btn btn-link">
                <i class="mdi mdi-account-cancel"></i> 
                <?= t("Lock Account") ?>
                <span v-if="loading">...</span>
            </button>
            <button v-else @click="enableAccount" class="d-block text-success btn btn-link">
                <i class="mdi mdi-account-check"></i> 
                <?= t("Unlock Account") ?>
                <span v-if="loading">...</span>
            </button>
        </div>
    </div>
</script>



<script>
    Vue.component('is-active-component', {
        template: '#tpl-is-active-component',
        props: {
            title: {},
            value: {},
        },
        data() {
            return {
                loading: false,
            }
        },
        mounted() {

        },
        methods: {
            async disableAccount() {
                await this.changeAccountIsActive(false);
            },
            async enableAccount() {
                await this.changeAccountIsActive(true);
            },
            async changeAccountIsActive(new_value) {
                
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
                var response = await axios.get('/InternalApi/ChangeAccountIsActive/' + this.$parent.id + '?new_value=' + (new_value ? 1 : 0) + '&response_format=json',   
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
                            text: 'Task finished successfuly',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });

                        this.$parent.is_active = new_value;

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
            }
        },        
    });
</script>
