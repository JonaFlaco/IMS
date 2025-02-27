<?php 
    
use App\Core\Application;

$data = (object)$data; 
$nodeData = $data->nodeData;

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">
    <div>
        <?php if($data->ctypeObj->use_generic_status){ ?>

        <!-- Update Status Modal Modal -->
        <update-status-component 
                v-if="updateStatusItems.length > 0" 
                ctype-id="<?= $data->ctypeObj->id ?>"
                :records="updateStatusItems"
                @clean-up="updateStatusItems = []"
                @after-update="afterUpdateStatus"
                >
            </update-status-component>
        
        <?php } ?>

        <div class="row">
            <div class="col-lg-12 pt-3">
                <div class="card">
                    <div class="card-body bg-primary text-white">
                        <h4 class="header-title mb-3"><?= e($data->title);?></h4>
                        Record Id: <?= e($nodeData->id); ?>

                        <?php if($data->ctypeObj->use_generic_status){ ?>

                            <button 
                                @click="updateStatus()" 
                                class="btn btn-sm btn-link" 
                                :class="status.style"
                                role="button" 
                                type="button">
                                {{ status.name }}
                            </button>
                            
                        <?php } ?>
                        
                    </div>
                </div>
            </div>
        </div> 
        
        %%generateUITplButtons%%

        <div class="row">
            %%generateUITpl%%
        </div>
    </div>
</template>    


<?= Application::getInstance()->view->renderView('Components/UpdateStatusComponent', (array)$data) ?>

<script>

    let vm = new Vue({
        el:'#vue-cont',
        template: '#tpl-main',
        data:{
            updateStatusItems: [],
            SaveButtonLoading: false,

            status: <?= json_encode($data->ctypeObj->use_generic_status ? $nodeData->status : []) ?>,
        },
        methods:{

            <?php if($data->ctypeObj->use_generic_status): ?>

                updateStatus(){

                    this.updateStatusItems = [];

                    this.updateStatusItems.push({
                            id: <?= $nodeData->id ?>, 
                            title: this.<?= (!empty($data->ctypeObj->display_field_name) ? $data->ctypeObj->display_field_name : "id") ?>, 
                        });

                },
                afterUpdateStatus(item) {
                    this.status.id = item.status.id;
                    this.status.name = item.status.name;
                    this.status.style = item.status.style;
                },
            
            <?php endif; ?>

            %%buttons_actions%%
        },

    });
        
</script>

%%gpsPanelInitialization%%
