<template id="tpl-cache-component">

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
                <div class="form-check">
                    <input v-model="item.sys_cache_tpl" type="checkbox" class="form-check-input" id="cacheTpl">
                    <label class="form-label" for="cacheTpl">Cache Tpl</label>
                </div>
            </div>

            <div v-if="item.sys_cache_tpl" class="mb-3">
                <label class="form-label" for="sys_cache_tpl_expire_after_sec">Tpl Cache Expire After (Sec):</label>
                <input :disabled="loading" type="number" required class="form-control" id="sys_cache_tpl_expire_after_sec" v-model="item.sys_cache_tpl_expire_after_sec">
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>

        </form>
        <!-- End of Form -->

    </div>
    <!-- End of Main Panel -->

</template>


<script type="text/javascript">
    var cacheComponent = {

        template: '#tpl-cache-component',
        data() {
            return {
                title: 'Cache',
                group_name: 'Cache',
                loading: false,
                loadingMessage: 'Loading, please wait...',
                errorInLoading: false,
                item: {
                    sys_cache: false,
                    sys_cache_tpl_expire_after_sec: 0,
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

    Vue.component('cache-component', cacheComponent)
</script>