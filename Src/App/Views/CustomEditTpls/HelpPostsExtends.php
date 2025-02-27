<?php use App\Core\Application; ?>

<script type="text/x-template" id="tpl-body-preview-component">
    <div class="border border-secondary p-1">
        <button class="btn btn-link" @click="refresh()">
            <span v-if="loading">
                <i class="mdi mdi-spin mdi-loading me-1"></i>        
                Parsing...
            </span>
            <span v-else>
                Preview
            </span>
        </button>
        
        <hr class="mt-0">

        <span v-if="!loading" v-html="result"></span>

    </div>
</script>



<script>
    Vue.component('body-preview-component', {
        template: '#tpl-body-preview-component',
        props: {
            title: {},
            value: {},
        },
        data() {
            return {
                loading: false,
                result: null,
            }
        },
        mounted() {
        },
        methods: {
            async refresh() {
            
                let self = this;

                self.loading = true;
                self.result = null;

                let formData = new FormData();
                formData.append('text', this.$parent.body);
                
                var response = await axios.post(
                        '/InternalApi/HelpParseMd/?response_format=json',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
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
                        
                        self.result = response.data.result;

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
