<?php use App\Core\Application; 

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
        
        <?= Application::getInstance()->session->flash() ?>

        <div class="row">

            <div class="col-sm-12">
                <div class="card card-body">
                    <h5 class="card-title">Changelog</h5>
                    <p v-if="changelog" v-html="changelog"> </p>
                    <i v-else>Changelog is empty</i>
                </div>
            </div>
                

            <div class="col-sm-12">
                <div class="card card-body">
                    <h5 class="card-title">Process</h5>


                    <div class="chart-widget-list mb-3">
                        
                        <p v-for="task in tasks" :key="task.id">
                            <i v-if="task.status_id == 1" class="mdi mdi-18px mdi-clock-time-eight text-primary"></i>
                            <i v-else-if="task.status_id == 2" class="mdi mdi-18px mdi-checkbox-marked text-success"></i>
                            <i v-else-if="task.status_id == 3" class="mdi mdi-18px mdi-close-box text-danger"></i>
                            <i v-else class="mdi mdi-18px mdi-checkbox-blank text-secondary"></i>
                            {{ task.title}}

                            <span class="float-end">{{ task.elapsed_time}}</span>
                            <br />
                            <small v-if="task.error" class="text-danger ms-3 me-2 bg-light p-1"> {{ task.error }} </small>
                        </p>
                        
                    </div>

                    <button @click="run" class="btn btn-primary">
                        Update {{ progress}}
                    </button>
                </div>
            </div>

        </div>
    </div>    

</template>

<script>

new Vue({
    el: '#vue-cont',
    template: '#tpl-main',
    data: {
        loading: false,
        title: '<?= $data['title'] ?>',
        filename: '<?= $data['filename'] ?>',
        changelog: '<?= \App\Helpers\MiscHelper::eJson($data['changelog']) ?>',
        currentTask: null,
        progress: '',
        status_list: [
            {id: 0, name: "Waiting"},
            {id: 1, name: "Executing"},
            {id: 2, name: "Success"},
            {id: 3, name: "Failed"},
        ],
        tasks: [
            {
                id: 0,
                name: "update_files",
                title: "Update files",
                status_id: 0,
                elapsed_time: null,
            },
            {
                id: 1,
                name: "update_db",
                title: "Update database",
                status_id: 0,
                elapsed_time: null,
            },
            {
                id: 3,
                name: "update_version_no",
                title: "Update version no",
                status_id: 0,
                elapsed_time: null,
            },
            {
                id: 4,
                name: "clean_up",
                title: "Clean Up",
                status_id: 0,
                elapsed_time: null,
            },    
        ]
    },
    methods: {
        async run() {

            for(var itm of this.tasks){

                if(this.currentTask == null || (itm.status_id != 1 && itm.status_id != 2 && itm.id >= this.currentTask.id) || (itm.id > this.currentTask.id)) {
                    
                    
                    this.progress = (this.tasks.filter((x) => x.id <= itm.id).length - 1) + '/' + this.tasks.length

                    this.currentTask = itm;
                    await this.execute(itm);

                    this.progress = (this.tasks.filter((x) => x.id <= itm.id).length) + '/' + this.tasks.length

                    if(itm.status_id == 3) {
                        return;
                    }

                } else {
                    continue;
                }
                
            };
            
        },
        async execute(itm) {

            let self = this;
            itm.status_id = 1;
            itm.error = null;
            start_date = new Date();
            
            var formData = new FormData();
            formData.append('cmd', itm.name);
            formData.append('filename', this.filename);

            var response = await axios({
                method: 'post', 
                url: '/core/update&response_format=json', 
                data: formData,
                }).catch(function(error){
                    itm.status_id = 3;
                    itm.elapsed_time = self.elapsedTime(start_date);

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
                    
                    itm.error = message;
                });

            if(!response) {
                return;
            }

            if(response.status == 200 && response.data.status == "success") {
                itm.status_id = 2;
                itm.elapsed_time = self.elapsedTime(start_date);
                
            } else {
                itm.status_id = 3;
                itm.elapsed_time = self.elapsedTime(start_date);
                error = "Something went wrong, while loading data";
                
                if(response && response.data && response.data.message) {
                    itm.error = response.data.message;
                } else if (response && response.response && response.response.data && response.response.data.message) {
                    itm.error = response.response.data.message;
                }

                itm.error = error;
            }

            return response;
        },
        elapsedTime(start) {
            let value = ((new Date()) - start) / 1000;
            if(value <= 60)
                return value + ' seconds';
            else if (value / 60 <= 60)
                return (value / 60) + ' minutes';
        }
    },
});

</script>