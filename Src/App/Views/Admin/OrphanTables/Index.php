<?php

use App\Core\Application;

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div>

        <page-title-row-component :title="pageTitle" :bread-crumb="breadCrumb">
        </page-title-row-component>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h4> <i class="mdi mdi-chart-areaspline"></i> Maintenance - Orphan Tables</h4>

                        <div v-if="loading">
                            Loading...
                        </div>
                        <div v-else-if="items.length == 0">
                            No orphan table found
                        </div>
                        <div v-else>
                            <p>System found (<strong class="text-primary">{{items.length}}</strong>) orphan tables</p>

                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-centered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Table</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in items">
                                            <td> {{ item.name }} </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div> <!-- end card-body-->
            </div>
        </div> <!-- end col-->
    </div>
</template>

<script>
    var vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            pageTitle: 'Orphan Tables',
            breadCrumb: [
                {title: 'Admin', link: '/admin'},
            ],
            loading: false,
            errorMessage: null,
            items: [],
        },
        props: ['ctypeId'],
        async mounted() {
            await this.loadData();
        },
        methods: {
            async loadData() {

                var self = this;

                this.errorMessage = null;

                this.loading = true;

                var response = await axios.post('/InternalApi/CtypeOrphanTablesGet?response_format=json', ).catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    self.errorMessage = message;
                });

                if (response) {
                    if (response.status == 200 && response.data && response.data.status == "success") {
                        self.items = response.data.result;
                    } else {
                        self.errorMessage = "Something went wrong";
                    }

                }

                this.loading = false;
            },

        }
    })
</script>