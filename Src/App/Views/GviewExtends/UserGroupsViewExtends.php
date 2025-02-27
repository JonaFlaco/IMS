<link rel="stylesheet" href="/assets/app/js/dhtmlx/diagram/diagram.css?v=3.0.2">
<link rel="stylesheet" href="/assets/app/js/dhtmlx/diagram/menu/menu.css">
<link href="/assets/app/js/dhtmlx/diagram/diagram.css?v=3.0.2" rel="stylesheet" type="text/css" />
<script src="/assets/app/js/dhtmlx/diagram/diagram.js?v=3.0.2"></script>
<script src="/assets/app/js/dhtmlx/diagram/menu/menu.js"></script>

<template id="tpl-result-component">

    <div>
            
        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                
                </button>
            </div>
            <div class="modal-body">
                <form v-on:submit.prevent id="detailForm" class="needs-validation was-validated-not" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label" for="detailName">Name</label>
                        <input required type="text" id="detailName" class="form-control">
                        <div class="invalid-feedback"> Please enter a valid data </div>
                    </div>
                    

                    <div class="mb-3">
                        <label class="form-label" for="detailParent">Parent</label>
                        <select class="form-select" required id="detailParent">
                            <?php
                            $items = \App\Core\Application::getInstance()->coreModel->getPreloadList("user_groups", "id", "name");
                            foreach($items as $item) {
                                echo sprintf('<option value="%s">%s</option>', $item->id, $item->name);
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="detailColor">Color</label>
                        <input class="form-control" required id="detailColor" type="color" name="color" value="#727cf5">
                    </div>

                    <div id="divJustification" class="mb-3">
                        <label class="form-label" for="justification">What is the reason of this edit? Please write breif justification</label>
                        <input required type="text" id="justification" class="form-control">
                        <div class="invalid-feedback"> Please enter a valid data </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveDetail" class="btn btn-primary" >Save changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
        </div>


        <div class="row">
            <div class="col-12">
            
                <section id="container" class="dhx_sample-container__without-editor">
                    <div class="dhx_sample-widget" id="diagram"></div>
                </section>

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
        }
    })
</script>