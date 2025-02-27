<template id="tpl-views-component">
    
    <!-- Loading Panel -->
    <div v-if="loading" class="col-xl-3 col-lg-6 float-center">
        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        {{loadingMessage}}
    </div>
    <!-- End of Loading Panel -->

    <!-- Error in loading Panel -->
    <div v-else-if="errorInLoading">
        Error while loading data.
        <button 
            :disabled="loading"
            @click="refresh"
            type="button" 
            class="btn btn-secondary"
            >
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
                    <button 
                        :disabled="loading"
                        @click="save"
                        type="button" 
                        class="btn btn-primary"
                        >
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
                <label class="form-label" for="views_pagination_records_per_page">Records per Page<span class="ml-1 text-danger">*</span></label>
                <select 
                    :disabled="loading"
                    v-model="item.views_pagination_records_per_page" 
                    class="form-select" 
                    id="views_pagination_records_per_page">
                    <option v-for="x in views_pagination_records_per_pageOptions"> {{ x }}</option>
                </select>
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label" for="views_pagination_buttons_count">Pagination Buttons<span class="ml-1 text-danger">*</span></label>
                <select 
                    :disabled="loading"
                    v-model="item.views_pagination_buttons_count" 
                    class="form-select" 
                    id="views_pagination_buttons_count">
                    <option v-for="x in views_pagination_buttons_countOptions"> {{ x }}</option>
                </select>
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="views_max_chars_to_show">Max Chars to Show<span class="ml-1 text-danger">*</span></label>
                <select 
                    :disabled="loading"
                    v-model="item.views_max_chars_to_show" 
                    class="form-select" 
                    id="views_max_chars_to_show">
                    <option v-for="x in views_max_chars_to_showOptions"> {{ x }}</option>
                </select>
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

    var viewsComponent = {

        template: '#tpl-views-component',
        data() {
            return {
                title: 'Views',
                group_name: 'Views',
                loading: false,
                loadingMessage: 'Loading, please wait...',
                errorInLoading: false,
                item: {
                    views_pagination_records_per_page: null,
                    views_pagination_buttons_count: null,
                    views_max_chars_to_show: null,
                },
                views_pagination_records_per_pageOptions: [
                    10,
                    25,
                    50,
                    100,
                    250
                ],
                views_pagination_buttons_countOptions: [
                    2,
                    5,
                    10
                ],
                views_max_chars_to_showOptions: [
                    10,
                    25,
                    50,
                    75,
                    100,
                    150,
                    200,
                    250,
                    500,
                    1000
                ],
                
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

                if(response != false && response.status == 200) {
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

    Vue.component('views-component', viewsComponent)

</script>
