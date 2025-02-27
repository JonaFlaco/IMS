

<template id="tpl-no-of-records-component">
    
    <div class="col-lg-12" v-if="$parent.id">
        <div class="card text-white"
            :class="bgStyle"
        >
            <div class="card-body">
                <div class="text-center">
                    <h4> <i class="mdi mdi-chart-areaspline"></i> No of Records : {{ no_of_records }}</h4>
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col-->

</template>

<script>
    Vue.component('no-of-records-component', {
        template: '#tpl-no-of-records-component',
        data() {
            return {
                loading: false,
                no_of_records: 0,
                errorMessage: null,
                loading: false,
            }
        },
        computed: {
            bgStyle: function(){
                if(this.no_of_records == 0) {
                    return 'bg-secondary';
                } else if (this.no_of_records < 99) {
                    return 'bg-success';
                } else if (this.no_of_records < 999) {
                    return 'bg-info';
                } else if (this.no_of_records < 99999) {
                    return 'bg-warning';
                } else {
                    return 'bg-danger';
                }
            },
        },
        mounted() {
            this.getStatistics();
        },
        methods: {
            async getStatistics() {
                
                var self = this;
                var id = this.$parent.id;

                this.no_of_records = 0;
                this.errorMessage = null;

                this.loading = true;
                
                if(id == null || id == undefined) {
                    return;
                }

                var response = await axios.get('/InternalApi/getCtypeRecordCount/' + id + '&response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        self.errorMessage = message;
                    });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        self.no_of_records = response.data.result;
                    } else {
                        self.errorMessage = "Something went wrong";
                    }

                }

                this.loading = false;
            }
        }
    })
</script>