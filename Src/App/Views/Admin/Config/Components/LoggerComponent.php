<template id="tpl-logger-component">

    <!-- Loading Panel -->
    <div v-if="loading" class="col-xl-3 col-lg-6 float-center">
        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        {{loadingMessage}}
    </div>
    <!-- End of Loading Panel -->

    <!-- Error in loading Panel -->
    <div v-else-if="errorInLoading">
        Error while loading data.
        <button :disabled="loading" @click="refresh" type="button" class="btn btn-secondary">
            <i class="mdi mdi-refresh font-16"></i>
            Retry
        </button>
    </div>
    <!-- End of Error in loading Panel -->

    <!-- Main Panel -->
    <div v-else>

        <!-- Top Bar -->
        <div class="row">
            <div class="col-sm-4">
                <h4 class="mb-3 header-title">
                    {{ title }}
                </h4>
            </div>

            <div class="col-sm-8">
                <div class="text-sm-end">
                    <button :disabled="loading" @click="save" type="button" class="btn btn-primary">
                        <i class="mdi mdi mdi-content-save font-16"></i>
                        Save
                    </button>
                </div>
            </div>

        </div>
        <!-- End of Top Bar -->


        <!-- Form -->
        <form ref="form">

            <div class="mb-3">
                <div class="custom-control custom-radio">
                    <input :disabled="loading" type="radio" id="customRadio0" name="customRadio" value="0" class="form-check-input" v-model="item.logger_level">
                    <label class="form-label" class="form-check-label" for="customRadio0"><span class="text-primary">OFF:</span> Disable all types of errors</label>
                </div>
                <div class="custom-control custom-radio">
                    <input :disabled="loading" type="radio" id="customRadio1" name="customRadio" value="1" class="form-check-input" v-model="item.logger_level">
                    <label class="form-label" class="form-check-label" for="customRadio1"><span class="text-primary">High:</span> Log only high severity errors</label>
                </div>
                <div class="custom-control custom-radio">
                    <input :disabled="loading" type="radio" id="customRadio2" name="customRadio" value="2" class="form-check-input" v-model="item.logger_level">
                    <label class="form-label" class="form-check-label" for="customRadio2"><span class="text-primary">Medium:</span> Only log erros severity Medium or above</label>
                </div>
                <div class="custom-control custom-radio">
                    <input :disabled="loading" type="radio" id="customRadio3" name="customRadio" value="3" class="form-check-input" v-model="item.logger_level">
                    <label class="form-label" class="form-check-label" for="customRadio3"><span class="text-primary">Low:</span> Only log erros severity Low or above</label>
                </div>
                <div class="custom-control custom-radio">
                    <input :disabled="loading" type="radio" id="customRadio4" name="customRadio" value="4" class="form-check-input" v-model="item.logger_level">
                    <label class="form-label" class="form-check-label" for="customRadio4"><span class="text-primary">Debug:</span> Log all types of errors</label>
                </div>
            </div>

        </form>
        <!-- End of Form -->


    </div>
    <!-- End of Main Panel -->

</template>


<script type="text/javascript">
    var loggerComponent = {

        template: '#tpl-logger-component',
        data() {
            return {
                title: 'Logger Settings',
                group_name: 'Logger Settings',
                loading: false,
                loadingMessage: 'Loading, please wait...',
                errorInLoading: false,
                item: {
                    logger_level: 0,
                },
            }
        },
        props: [],
        mounted() {
            this.refresh();
        },
        methods: {
            async refresh() {

                let self = this;
                self.loading = true;

                var response = await this.$parent.load(this.group_name);

                self.loading = false;

                if (response != false && response.status == 200) {
                    self.item = response.data.result;
                } else {
                    this.errorInLoading = true;
                }
            },
            async save() {

                if (!this.$refs.form.checkValidity()) {

                    this.$refs.form.classList.add('was-validated');

                    $.toast({
                        heading: 'Error',
                        text: 'Please enter valid values',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    return;
                } else {
                    this.$refs.form.classList.remove('was-validated');
                }

                let self = this;
                self.loading = true;

                let formData = new FormData();
                formData.append('data', JSON.stringify(this.item));

                var response = await this.$parent.save(this.item);
                self.loading = false;

            }
        },
        computed: {

        },
    }

    Vue.component('logger-component', loggerComponent)
</script>