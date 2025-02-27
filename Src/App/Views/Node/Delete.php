<?php use App\Core\Application;

$data = (object)$data;
?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    <div>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Eliminar de {{ ctypeName }}</h4>
                </div>
            </div>
        </div>     


        <div class="card text-center">
            <div class="card-header alert-danger">
                <strong>ELIMINAR!</strong>
            </div>
            <div class="card-body">
                <p class="card-text">¿Estás seguro de que quieres eliminar el registro #{{ id }} de {{ ctypeName }}?</p>
                <a :href="'/' + ctypeId" class="btn btn-primary">Regresar</a>
                <button @click="submit()" :disabled="loading == 1" class="btn btn-danger">
                    <span v-if="loading == 1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Eliminar
                </button>
            </div>
            
        </div>

    </div>
</template>

<script>
    let vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            id: '<?= $data->id ?>',
            ctypeName: '<?= $data->ctype_obj->name ?>',
            ctypeId: '<?= $data->ctype_obj->id ?>',
            loading: false,
        },
        methods: {
            submit: function(){

                if(!confirm('Are you sure want to delete this record?')){
                    return;
                }

                let self = this;
                self.loading = true;
                var formData = new FormData();
                formData.append('id_list', this.id);
                formData.append('ctype_id', this.ctypeId);
                axios({
                    method: 'post',
                    url: '/InternalApi/deleteRecord/?response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                        'Csrf-Token': '<?= Application::getInstance()->csrfProtection->create("delete_" . $data->ctype_obj->id); ?>',
                    }
                })
                .then(function(response){
                    
                    self.loading = false;
        
                    if(response.data.status == 'success'){
                        self.loading = false;
                        $.toast({heading: 'success',text: 'Finished',showHideTransition: 'slide',position: 'top-right',icon: 'success'});
                        window.location.href = '/' + self.ctypeId;
                    } else {
                        
                        if(response.data.message != null && response.data.message.length > 0){

                            $.toast({heading: 'error',text: response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});
        
                        } else {
                            $.toast({heading: 'error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
                        }
                    }
                    
                })
                .catch(function(error){
                    self.loading = false;
                    
                    if(error.response != undefined && error.response.data.status == 'failed') {
                        $.toast({
                            heading: 'error',
                            text: error.response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                    } else {
                        
                        $.toast({
                            heading: 'error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                    }
                    
                });
            }
        }
    });
</script>
    