<template id="tpl-detail-component">
    <div>

        <div v-if="item.statusId == 1" class="col-xl-3 col-lg-6 float-center">
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            {{ item.loadingMessage }}
        </div>

        <div v-else class="row">

            <div class="col-sm-4">                            
                <h4 class="mb-3 header-title">
                    {{ item.name }} ({{ (selectedRecordsCount > 0 ? selectedRecordsCount + '/' : null) + totalRecordsCount }})
                </h4>
            </div>

            <div class="col-sm-8">
                <div class="text-sm-end">

                    <button 
                        :disabled="item.statusId == 1"
                        @click="load()"
                        type="button" 
                        class="btn btn-primary"
                        >
                        <i class="mdi mdi-refresh font-16"></i> 
                        Refresh
                    </button>

                    <button 
                        :disabled="item.statusId == 1"
                        @click="run"
                        type="button" 
                        class="btn btn-danger"
                        >
                        <i class="dripicons-trash font-16"></i> 
                        Execute
                    </button>
                </div>
            </div>

            <div v-if="item.errorMessage" class="alert alert-danger col-md-12 mt-1" role="alert">
                <strong>Error - </strong> {{ item.errorMessage }}
            </div>
            <div v-else class="col-md-12 table-responsive-sm mt-1">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                        <tr>
                            <th style="width: 20px;">
                                <div class="form-check"> 
                                    <input type="checkbox" @change="toggleSelection" v-model="item.selected" class="form-check-input" id="customChecktoggle"> 
                                    <label class="form-check-label" for="customChecktoggle"></label>
                                </div>
                            </th>
                            <th v-for="ta in item.tableStructure"> {{ ta.name }} </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="obj in item.records" :key="obj.id">
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" v-model="obj.selected" class="form-check-input" :id="'customCheck' + obj.id">
                                    <label class="form-check-label" :for="'customCheck' + obj.id">&nbsp;</label>
                                </div>
                            </td>
                            <td v-for="ta in item.tableStructure"> {{obj[ta.id]}} </td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- end table-responsive-->
            
        
        </div>
        <!-- End of Top Bar -->

    </div>
</template>

<script>

    Vue.component('detail-component', {
        template: '#tpl-detail-component',
        props: {
            item: {
                required: true,
            },
        },
        data() {
            return {
                data: [],
            }
        },
        mounted() {

            if(!this.$parent.selectedMenu.tableStructure || this.$parent.selectedMenu.tableStructure.length == 0) {
                this.$parent.selectedMenu.tableStructure = [
                    {id: "id", name: "ID"},
                    {id: "name", name: "Name"},
                ]
            }

            if(this.item.records.length == 0)
                this.load();
        },
        updated() {
            
        },
        methods: {
            toggleSelection() {
                this.item.records.forEach((x) => {
                    x.selected = this.item.selected;
                });
            },
            async load(){
                await this.$parent.load(this.item);
            },
            async run() {
                await this.$parent.run(this.item);
            },
        },
        computed: {
            totalRecordsCount() {
                return this.item.records.length;
            },
            selectedRecordsCount() {
                return this.item.records.filter((item) => item.selected).length;
            }
        }
    });

</script>