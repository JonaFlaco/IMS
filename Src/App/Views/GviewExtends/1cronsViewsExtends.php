<script>

    var mix = {
    }

</script>

<style scoped>

    .bg-color1 {
        background-color: #5B2C6F !important
    }
    .bg-color1-lighten {
        background-color: #F5EEF8 !important;
    }

    .bg-color2 {
        background-color: #884EA0 !important
    }
</style>

<template id="tpl-result-component">
    <div>
        

        <div class="row ms-0 me-0">
            <div 
                v-for="group in groups" 
                class="card-body m-0 p-0 col-md-12"
                >
                <div 
                    class="card m-0 mt-1 mb-3 bg-color1-lighten border border-secondary"
                    >
                    <div class="card-body p-0">
                        <div class="card-widgets p-1 bg-color1 text-white">
                    
                            <div
                                class="dropdown-menu dropdown-menu-right">
                                    <!-- <a href="javascript:void(0);" @click="addGroup(location)" class="dropdown-item">
                                        <i class="mdi mdi-plus me-1"></i>
                                        Add Group
                                    </a> 
                                    <a href="javascript:void(0);" @click="openDependency(location)" class="dropdown-item">
                                        <i class="mdi mdi-code-array me-1"></i>
                                        Group Dependency Condition
                                    </a>
                                    <a href="javascript:void(0);" @click="deleteLocation(tab, location)" class="dropdown-item">
                                        <i class="mdi mdi-trash-can me-1"></i>
                                        Delete Region
                                    </a> -->
                            </div>
                        </div>
                        <h5 class="capitalize p-1 m-0 bg-color1">
                            <a 
                                data-bs-toggle="collapse" 
                                class="text-white"
                                :href="'#cardtab' + group" 
                                role="button" 
                                aria-expanded="false" 
                                aria-controls="'#cardtab' + group"
                                >
                                Region: {{ group }} <span v-tooltip="'Number of crons inside this group'">({{records.filter((item) => (item.crons_group_name ?? 'UnCategorized') == group).length}})</span>
                            </a>
                        </h5>
                    
                        <div 
                            :id="'cardtab' + group" 
                            class="collapse show p-2">

                            
                            <div 
                                v-for="cron in records.filter((item) => (item.crons_group_name ?? 'UnCategorized') == group)" 
                                class="row p-0 m-0 border-bottom border-light bg-white align-items-center"
                                >
                                <div class="col-auto">
                                    <img height="32" :src="'/assets/app/images/field_type_icons/component.png'">
                                </div>
                                <div class="col ps-0">
                                    <a href="javascript:void(0);" @click="$parent.editRecord(cron.crons_id_main)" class="text-body">{{cron.crons_title}}</a>
                                    <p class="mb-0 text-muted"><small>{{ cron.crons_name }}</small></p>
                                </div>
                                
                                <div class="col-auto">
                                    <span :class="cron.crons_is_custom ? 'badge bg-danger' : 'badge bg-success'">{{ cron.crons_is_custom ? 'Custom' : 'Generic'}} </span>
                                </div>

                                <div class="col-auto">
                                    <span>v{{ cron.crons_version}} </span>
                                </div>

<!-- 
                                <div class="col-auto">
                                    <i v-if="field.is_required" v-tooltip="'This field is required'" class="mdi mdi-star-outline"></i>
                                    <i v-if="field.is_system_field" v-tooltip="'This field is system field'" class="mdi mdi-shield-lock-outline"></i>
                                    <i v-if="field.is_hidden" v-tooltip="'This field is hidden'" class="mdi mdi-eye-off-outline"></i>

                                    <i v-if="field.validation_condition" v-tooltip="'This field has validation condition'" class="mdi mdi-code-array"></i>
                                    <i v-if="field.dependency" v-tooltip="'This field has dependency condition'" class="mdi mdi-code-array"></i>
                                    <i v-if="field.required_condition" v-tooltip="'This field has required condition'" class="mdi mdi-code-array"></i>
                                    <i v-if="field.read_only_condition" v-tooltip="'This field has read only condition'" class="mdi mdi-code-array"></i>
                                </div> -->

                                <div class="col-auto">
                                    <a href="javascript: void(0);" v-tooltip="'Edit this field'" @click="$parent.editRecord(cron.crons_id_main)"
                                        class="action-icon text-primary"><i class="mdi mdi-pencil"></i></a>

                                    <a href="javascript: void(0);" v-tooltip="'Delete this field'" @click="$parent.deleteRecord(cron.crons_id_main)"
                                        class="action-icon text-danger"><i class="mdi mdi-delete"></i></a>
                                </div>
                            </div>
                            
                            
                        </div>
                    </div>
                
                </div>

            </div>
        </div>



    </div>
</template>

<script>
    Vue.component('result-component', {
        template: '#tpl-result-component',
        props: {
            records: {},
            ctypeId: {},
        },
        data() {
            return {
                groups: [],
            }
        },
        mounted() {
            
        },
        methods: {
            onlyUnique(value, index, self) {
                return self.indexOf(value) === index;
            },
        },
        watch: {
            records() {
                this.groups = this.records.map((item) => item.crons_group_name ?? 'UnCategorized').filter(this.onlyUnique);;
            }
        }

    })
</script>