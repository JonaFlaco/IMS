<?php

use App\Core\Application;

// Application::getInstance()->view->renderView('components/mapComponent', (array)$data)
?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKJJhpyQ0jjnRWi2hnS7C7BIRddUSVUQM&libraries=visualization,drawing"></script> -->

<template id="tpl-main">

    <div v-if="loading">
        <?= t("Loading") ?>...
    </div>

    <div v-else-if="error">
        {{ error }}
    </div>
    <div v-else-if="nodeData && ctype">

        //%%HTML_CONTENT%%

    </div>
    <div v-else>
        <?= t("No data to show") ?>
    </div>

</template>


<?= Application::getInstance()->view->renderView('Components/UpdateStatusComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('Components/LogComponent', (array)$data) ?>

<script>
    let vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            updateStatusItems: [],
            SaveButtonLoading: false,
            recordId: null,
            nodeData: null,
            ctype: null,
            loading: false,
            error: null,
            loadLog: false,
        },
        async mounted() {

            this.recordId = window.location.pathname.split('/')[3];
            this.ctypeId = window.location.pathname.split('/')[1];

            this.loadData();
        },
        methods: {
            //%%METHODS%%
            showLog() {
                this.loadLog = true;
                var myModal = new bootstrap.Modal(document.getElementById('logModal'), {
                })
                myModal.show();
            },
            async loadData() {
                let self = this;

                self.loading = true;
                self.error = null;

                var response = await axios({
                    method: 'GET',
                    url: '/InternalApi/getCtypeShowDetail/' + this.recordId + '?ctype_id=' + this.ctypeId + '&response_format=json',
                }).catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    self.error = message;

                    return false;
                });

                if (response.status == 200) {
                    self.nodeData = response.data.result.nodeData;
                    self.ctype = response.data.result.ctype;
                }

                self.loading = false;
            }
        },
    });
</script>

//%%SCRIPTS%%