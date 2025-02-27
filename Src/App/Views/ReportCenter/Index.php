<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    <div>
    <page-title-row-component :title="pageTitle" :bread-crumb="breadCrumb"></page-title-row-component>

    <div class="container-fluid">

        <div class="card shadow-none border">
            <div class=" card-body p-2">
                <div class="row">
                    <h4 class="text-primary mb-2">Quick Filters</h4>
                    <div class="col-6 mb-2">
                        <label for="dashboard_name" class="form-label">Report</label>
                        <input type="text" id="dashboard_name" class="form-control" autocomplete="off" placeholder="Enter Report Name or Keywords" v-model="titleInputValue" @input="filterItems">
                    </div>
        
                    <div class="col-6 mb-2">
                        <label class="form-label">Module</label>
                        <multiselect v-model="moduleSelectedValue" :multiple="false" selected-label="Selected" track-by="module_id" label="module_id_display" placeholder="Choose a Module" :options="modules" :searchable="true" @input="filterItems"> </multiselect>
                        
                    </div>
                </div>

                <div class="mb-3 p-2 pt-0 rounded shadow-none" v-for="group in filteredItems.map((e) => e.module_id_display).filter((value, index, self) => self.indexOf(value) === index)">
                    <h5 class="mb-1 p-1 text-white bg-secondary rounded">{{ group }} ({{ filteredItems.filter((e) => e.module_id_display == group).length }})</h5>
        
                    <div class="row mx-n1 g-0">
                        <div v-for="(item, index) in filteredItems.filter((e) => e.module_id_display == group)" :key="index" class="col-md-6 col-lg-4 col-xl-3 col-xxl-3 ">
                            <div class="card m-1 shadow-none border">
                                <div class="p-2">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar-sm">
                                                <span class="avatar-title bg-light rounded">
                                                    <i v-if="item.is_custom_url" class="mdi mdi-file-powerpoint-box font-24 text-warning"></i>
                                                    <i v-else class="mdi mdi-chart-areaspline font-24 text-primary"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col ps-0">
                                            <a :href="'/dashboards/show/' + item.id" target="_blank" class="text-secondary fw-bold">{{ item.name }}</a>
                                            
                                            <p class="mb-0 font-12">{{ item.last_update_humanify }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <p class="mb-0 font-12" v-if="item.description"> {{ item.description }}</p>
                                        </div>
        
                                    </div> <!-- end row -->
                                </div> <!-- end .p-2-->
                            </div> <!-- end col -->
                        </div> <!-- end col-->
        
                    </div> <!-- end row-->
                </div>
            </div>
        </div>
        
    </div>
</div>

</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',

        components: {
            Multiselect: window.VueMultiselect.default
        },

        data: {
            pageTitle: "Report Center",
            breadCrumb: [],

            items: [],
            filteredItems: [],

            titleInputValue: '',
            
            modules: [],
            moduleSelectedValue: ''
        },

        mounted(){
            this.items = <?= json_encode((array)$data["items"]) ?>;
            this.filteredItems = this.items;

            this.modules = this.items.map(({ module_id, module_id_display }) => ({
                                            module_id, module_id_display
                                        })).filter((value, index, self) => {
                                            return self.findIndex((item) => item.module_id === value.module_id) === index});
        },

        methods:{
            filterItems() {
                const inputValue = this.titleInputValue.toLowerCase();
                const selectedValue = this.moduleSelectedValue?.module_id;

                if (selectedValue != null && inputValue != null) {
                    this.filteredItems = this.items.filter(item => item.module_id === selectedValue && item.name.toLowerCase().includes(inputValue));
                } else if (selectedValue != null) {
                    this.filteredItems = this.items.filter(item => item.module_id === selectedValue);
                } else if (inputValue != null) {
                    this.filteredItems = this.items.filter(item => item.name.toLowerCase().includes(inputValue));
                } else {
                    this.filteredItems = this.items;
                }

            }
        }
    });
</script>