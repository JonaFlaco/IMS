<?php use App\Core\Application; 

$flashContent = Application::getInstance()->session->flash();

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ title }}</h4>
                </div>
            </div>
        </div>     
        
        <?= $flashContent ?>

        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    
                    <h4 class="header-title mb-2">Releases</h4>

                    <div>
                        <div class="timeline-alt pb-0">
                            <div class="timeline-item" v-for="item in items" :key="item.id">
                                <i class="mdi mdi-upload bg-info-lighten text-info timeline-icon"></i>
                                <div class="timeline-item-info">
                                    <a href="javascript: void(0);" class="text-info fw-bold mb-1 d-block">{{ item.file_name }}</a>

                                    <p class="mb-0 pb-2">
                                        
                                        <i class="mdi mdi-timetable"></i>
                                        {{ item.date }}

                                    </p>

                                    <p v-html="item.changelog" class="p-1 bg-light"> </p>
                                    
                                    <a v-if="item.ready" :href="'/core/download/' + item.file_name" target="_BLANK"><i class="mdi mdi-arrow-down-circle-outline"></i> Download</a>

                                    <button v-if="item.ready" class="btn btn-link pull-right" @click="prepare(item, true)">
                                        <span v-if="item.preparing">
                                            <i class="mdi mdi-database-arrow-up-outline"></i> 
                                            Regenerating...
                                        </span>
                                        <span v-else>
                                            <i class="mdi mdi-database-arrow-up-outline"></i> 
                                            Regenerate
                                        </span>
                                    </button>
                                    <button v-else class="btn btn-link pull-right" @click="prepare(item)">
                                        <span v-if="item.preparing">
                                            <i class="mdi mdi-database-arrow-up-outline"></i> 
                                            Preparing...
                                        </span>
                                        <span v-else>
                                            <i class="mdi mdi-database-arrow-up-outline"></i> 
                                            Prepare
                                        </span>
                                    </button>
                                    <br />
                                    <br />
                                </div>
                            </div>
                        <!-- end timeline -->
                    </div> <!-- end slimscroll -->
                </div>
                <!-- end card-body -->
            </div>
            <!-- end card-->
        </div>
        <!-- end col -->  
        
    </div>    

</template>

<script>

new Vue({
    el: '#vue-cont',
    template: '#tpl-main',
    data: {
        loading: false,
        take_dbsnapshot_loading: false,
        title: '<?= $data['title'] ?>',
        items: <?= json_encode($data['items']) ?>,
    },
    methods: {
        async prepare(item, overwrite = false) {

            let self = this;
            item.preparing = true;
        
            var response = await axios({
                method: 'GET', 
                url: '/core/prepare/' + item.file_name + '&overwrite=' + overwrite + '&response_format=json', 
                }).catch(function(error){
                    item.preparing = false;
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

            if(!response) {
                return;
            }
            
            if(response.status == 200 && response.data.status == "success") {
                $.toast({
                    heading: 'Success',
                    text: "Release package prepared successfuly",
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'success'
                });
                item.ready = true;
                item.preparing = false;
            } else {
                item.preparing = false;
                error = "Something went wrong, while loading data";
                
                if(response && response.data && response.data.message) {
                    error = response.data.message;
                } else if (response && response.response && response.response.data && response.response.data.message) {
                    error = response.response.data.message;
                }

                $.toast({
                    heading: 'Failed',
                    text: error,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'failed'
                });
                
            }

            self.take_dbsnapshot_loading = false;

        },
    }
});

</script>