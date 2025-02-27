<?php

use App\Core\Application;
?>


<template id="tpl-sync-component">
    <div v-if="$parent.id" class="bg-light p-1">
        
        <h4>Run</h4>
        
        <p>Click the button below to run this cron.</p>
        
        <div class="row">
            <div class="col-sm-8">
        
                <div v-if="$parent.type_id == 'sync_odk_form'">
                    <span v-if="statistics.loading">Loading Statistics...</span>
                    <span v-else-if="statistics.errorMessage" class="text-danger"> Error while loading Statistics: {{ statistics.errorMessage }} </span>
                    <h4 v-else> 
                        <i class="mdi mdi-information-outline"></i>
                        Pending Records: <span class="text-info">{{ statistics.pending_records }}</span>,
                        Total Submitted Records: <span class="text-info">{{ statistics.all_records }}</span>,
                        Total size <span :class="size_style">{{ Math.round(statistics.size_kb / 1024,2) }} MB</span>
                    </h4>
                </div>

            </div>
            <div class="col-sm-4">
                <div class="text-sm-end">
                    <button :disabled="loading" class="btn btn-success" @click="sync">
                        <i v-if="loading" class="mdi mdi-loading mdi-spin"></i>
                        <i v-else class="mdi mdi-play"></i>
                        {{ loading ? "Running..." : 'Run' }}
                    </button>
                </div>
            </div>
        </div>
        


    </div>
</template>

<script>
    Vue.component('sync-component', {
        template: '#tpl-sync-component',
        data() {
            return {
                loading: false,
                statistics: {
                    all_records: 0,
                    pending_records: 0,
                    size_kb: 0,
                    errorMessage: null,
                    loading: false,
                },
            }
        },
        computed: {
            size_style: function() {
                if(this.statistics.size_kb / 1024 > 5000)
                    return "text-danger";
                else if(this.statistics.size_kb / 1024 > 999)
                    return "text-warning";
                else if(this.statistics.size_kb / 1024 > 10)
                    return "text-info";
                else 
                    return "text-secondary";
            },
        },
        mounted() {
            this.getStatistics();

            this.$watch(
                "$parent.type_id",
                (new_value, old_value) => {
                    this.getStatistics();
                }
            );
        },
        methods: {
            async sync() {
                this.loading = true;

                var id = this.$parent.id;
                if(id == null || id == undefined) {
                    alert('Id not found');
                    return;
                }

                var response = await axios.get('/Actions/runcron/' + id + '&response_format=json',   
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
                            text: 'Cron ran successfuly',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });

                        if(this.$parent.type_id == 'sync_odk_form') { //ODK Form
                            this.getStatistics();
                        }
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

                this.loading = false;
            },
            async getStatistics() {
                
                var self = this;
                var id = this.$parent.id;

                this.statistics.all_records = 0;
                this.statistics.pending_records = 0;
                this.statistics.errorMessage = null;

                if(this.$parent.type_id != 'sync_odk_form' || id == null) { //Odk Form
                    return;
                }

                this.statistics.loading = true;
                
                if(id == null || id == undefined) {
                    alert('Id not found');
                    return;
                }

                var response = await axios.get('/InternalApi/OdkFromStatistics/' + id + '&response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        self.statistics.errorMessage = message;
                    });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        this.statistics.all_records = response.data.result.all_records;
                        this.statistics.pending_records = response.data.result.pending_records;
                        this.statistics.size_kb = response.data.result.size_kb;
                    } else {
                        self.statistics.errorMessage = "Something went wrong";
                    }

                }

                this.statistics.loading = false;
            }
        }
    })
</script>




<template id="tpl-stats-component">
    <div class="p-1">
        <crons-stats-component limited="0" :cron-id="$parent.id"></crons-stats-component>
    </div>
</template>

<script>
    Vue.component('stats-component', {
        template: '#tpl-stats-component',
        data() {
            return {
                loading: false,
                errorMessage: null,
                list: [],
                loaded: false,
            }
        },
        mounted() {
        },
        methods: {
            
        }
    })
</script>





<template id="tpl-found-issues-component">

    <div v-if="$parent.id" class="bg-light p-1 rounded">
        <h4 class="text-primary">Check for Issues</h4>
        <p>Click the button below to check for any issues that may arise with this cron.</p>
        
        <div class="row">
            <div class="col-sm-12">
        
                <div v-if="$parent.type_id == 'sync_odk_form'">
                    <!-- <span v-if="loading">Loading...</span> -->
                    
                    <span v-if="errorMessage" class="text-danger"> Error while loading issues: {{ errorMessage }}  </span>
                    <ul v-else-if="issues.length > 0">
                        <i>Under development</i>
                        <li v-for="(item, index) in issues" :key="index">
                            {{ item.type }}: {{ item.detail }}
                        </li>
                    </ul>
                </div>

            </div>
            <div class="col-sm-12">
                <div class="text-sm-end">
                    <button :disabled="loading" class="btn btn-success" @click="get_issues">
                        <i v-if="loading" class="mdi mdi-loading mdi-spin"></i>
                        <i v-else class="mdi mdi-play"></i>
                        {{ loading ? "Loading..." : 'Check' }}
                    </button>
                </div>
            </div>
        </div>
        
    </div>

</template>

<script>
    Vue.component('found-issues-component', {
        template: '#tpl-found-issues-component',
        data() {
            return {
                loading: false,
                issues: [],
                errorMessage: null
            }
        },

        methods: {
            async get_issues() {

                let self = this;
                this.clear_issues();

                self.loading = true;

                var id = this.$parent.id;
                if(id == null || id == undefined) {
                    alert('Id not found');
                    return;
                }

                var response = await axios.get('/InternalApi/OdkFromsGetIssues/' + id + '&response_format=json',   
                    ).catch(function(error){
                            
                        if(error.response != undefined && error.response.data.status == "failed") {
                            self.errorMessage = error.response.data.message;
                        }

                        $.toast({
                            heading: 'Error',
                            text: self.errorMessage,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){

                        this.issues = response.data.result;
                        
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

            clear_issues(){
                this.loading = false
                this.issues = []
                this.errorMessage = null
            }
        }
    })
</script>


<?= Application::getInstance()->view->renderView("Components/CronsStatsComponent", []) ?>