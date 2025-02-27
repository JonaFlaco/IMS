<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<template id="tpl-main">

    <div class="container-fluid">

        <div class="mt-3 mb-2" v-for="group in items.map((e) => e.module_id_display).filter((value, index, self) => self.indexOf(value) === index)">
            <h5 class="mb-2">{{ group ?? "Uncategorized" }}</h5>

            <div  class="row mx-n1 g-0">
                <div v-for="(item, index) in items.filter((e) => e.module_id_display == group)" :key="index" class="col-xxl-3 col-lg-6">
                    <div class="card m-1 shadow-none border">
                        <div class="p-2">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-secondary rounded">
                                            <i class="mdi mdi-folder-zip font-16"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col ps-0">
                                    <a href="javascript:void(0);" class="text-muted fw-bold"> {{ item.name }} </a>
                                    <p class="mb-0 font-13">{{ item.module_id_display }}</p>
                                </div>
                                <div class="mt-1">
                                    <p class="mb-0"> {{ item.description ?? "No description" }}</p>
                                </div>
                            </div> <!-- end row -->
                        </div> <!-- end .p-2-->
                    </div> <!-- end col -->
                </div> <!-- end col-->

            </div> <!-- end row-->
        </div>

        
    </div>

</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            items: <?= json_encode((array)$data["items"]) ?>,
        }
    });
</script>