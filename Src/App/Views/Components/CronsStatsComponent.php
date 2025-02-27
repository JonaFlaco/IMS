<template id="tpl-crons-stats-component">
    <div class="p-1">
    
        <button v-if="loaded != true" :disabled="loading" class="btn btn-link" @click="refresh">
            <span v-if="loading">Loading...</span>
            <span v-else>Load Stats</span>
        </button>

        <div v-if="errorMessage" class="alert alert-danger" role="alert">
            <strong>Error - </strong> {{ errorMessage }}
        </div>
        
        
        <div v-for="(year, yearIndex) in list" :key="yearIndex">

            <table class="table table-bordered">
                <tr>
                    <th colspan="32">
                        <center><h5 class="m-0">{{ year.name }}</h5></center>

                    </th>
                </tr>        
                <tr >
                    <th class="p-1"></th>
                    <th class="p-1" v-for="day in 31" :key="day">{{ day }} </th>
                </tr>
                <tr v-for="(month, monthIndex) in year.items" :key="monthIndex">
                    <th class="p-1">
                        {{ month.name }}
                    </th>
                    <td v-for="(item, itemIndex) in month.items" :key="itemIndex" class="p-1">
                        <div class="d-grid">
                            <span v-tooltip="item.started + ' started on ' + item.date" class="badge badge-primary-lighten" v-if="item.started > 0">{{ item.started }}</span>
                    
                            <span v-tooltip="item.data_synced + ' data synced on ' + item.date" class="badge badge-success-lighten mt-1" v-if="item.data_synced > 0">{{ item.data_synced }}</span>
                            
                            <span v-tooltip="item.failed + ' failed on ' + item.date" class="badge badge-danger-lighten mt-1" v-if="item.failed > 0">{{ item.failed }}</span>
                        </div>
                    </td>
                </tr>
            </table>
            

        </div>
    </div>
</template>

<script>
    var CronsStatsComponent = {
        template: '#tpl-crons-stats-component',
        props: ["cronId","autoLoad", "limited"],
        data() {
            return {
                loading: false,
                errorMessage: null,
                list: [],
                loaded: false,
            }
        },
        mounted() {
            if(this.autoLoad)
                this.refresh();
        },
        methods: {
            async refresh() {
                var self = this;

                this.list = [];
                
                this.loading = true;
                this.errorMessage = null;
                
                var response = await axios.get('/InternalApi/OdkFromDashboardGetStats/' + this.cronId + '&limited=' + this.limited + '&response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        self.errorMessage = message;
                    });

                if(response) {
                    if(response.status == 200 && response.data && response.data.status == "success"){
                        this.list = response.data.result;
                        this.loaded = true;
                    } else {
                        self.errorMessage = "Something went wrong";
                    }

                }

                this.loading = false;
            }
        }
    };

    Vue.component('crons-stats-component', CronsStatsComponent);
</script>
