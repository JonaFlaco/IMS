<?php use \App\Core\Application; ?>

<?= (new \App\views\Components\WidgetComponent())->generate() ?>

<template id="tpl-homepage-widgets-component">
    
    <div class="col-lg-12">

        <!-- Column Settings Modal -->
        <div id="customizeHomePageModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <h4 class="mt-0"><i class="mdi mdi-table-column-plus-after"></i> <?= t("Añadir Widgets") ?></h4>
                        </div>
                        
                        <h4><?= t("Widgets añadidos") ?></h4>
                        <div class="alert alert-info" v-if="widgetsTemp.length == 0">
                            <?= t("No se ha añadido ningún widget") ?>
                        </div>
                        <div v-else>
                            <div class="row p-1">

                                    
                                <draggable 
                                    tag="div" 
                                    :list="widgetsTemp" 
                                    :group="{ name: 'fields' }"
                                    ghost-class="vuedraggable-ghost" 
                                    handle=".field-handle"
                                    class="row m-0"
                                    :class="dragged && widgetsTemp.length == 0 ? 'p-2 border border-danger dashed-border' : ''"
                                    @start="dragged = true"
                                    @end="dragged = false"
                                    >
                                    
                    
                    
                                    <div v-for="wid in widgetsTemp" :key="'layout_' + wid.id" :class="'col-md-' + wid.size">
                                        
                                        <div class="card widget-flat">
                                            <div class="card-body">
                                                <div class="float-end">
                                                    <img :src="wid.icon" height="32" width="32"/>
                                                </div>
                                                
                                                <h5 class="mt-1 mb-3"> 
                                                        <i class="text-dark mdi mdi-pan field-handle cursor-grab"></i> {{ wid.name }}
                                                </h5>
                                                <p class="mb-0 text-muted">
                                                    {{ wid.description }}
                                                </p>
                                                
                                                <div class="row">
                                                    <div class="col-md-3 d-grid ps-0 pe-0">
                                                        <button type="button" @click="resize(wid, -1)" class="btn btn-primary m-0 btn-sm">
                                                            <i class="mdi mdi-arrow-collapse-left"></i>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-3 d-grid">
                                                        <button type="button" @click="resize(wid, 1)" class="btn btn-primary m-0 btn-sm">
                                                            <i @click="resize(wid, 1)" class="mdi mdi-arrow-collapse-right"></i>
                                                        </button>
                                                    </div>
                                                    <div class="ps-0 pe-0 col-md-6 d-grid">
                                                        <button @click="remove(wid)" type="button" class="btn btn-danger m-0 btn-sm">
                                                            <i class="mdi mdi-trash-can"></i>
                                                            <?= t("Eliminar") ?>
                                                        </button>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </draggable>
                            </div>
                        </div>

                        <div v-if="availableWidgetsLoaded">
                            <h4><?= t("Widgets disponibles") ?></h4>
                            <div class="alert alert-info" v-if="allWidgets.filter((x) => x.is_added != true && (x.name.toLowerCase().trim().includes(widgetSearch.toLowerCase().trim())) || x.tags.includes(widgetSearch.toLowerCase().trim())).length == 0">
                                <?= t("No hay ningún widget disponible") ?>
                            </div>
                            <div data-simplebar style="max-height: 500px;">
                                <div class="row p-1">

                                    <div v-for="wid in allWidgets.filter((x) => x.is_added != true && (x.name.toLowerCase().trim().includes(widgetSearch.toLowerCase().trim())) || x.tags.includes(widgetSearch.toLowerCase().trim()) )" :key="wid.id" class="col-md-4">
                                        <div class="card widget-flat">
                                            <div class="card-body">
                                                <div class="float-end">
                                                    <img :src="wid.icon" height="32" width="32"/>
                                                </div>
                                                <h5 class="mt-1 mb-3"> 
                                                    {{ wid.name }}
                                                </h5>
                                                <p class="mb-0 text-muted">
                                                    {{ wid.description }}
                                                </p>

                                                <p>
                                                    <span v-for="tag in wid.tags">
                                                        <span class="badge text-secondary bg-light me-1"> {{ tag }} </span>
                                                    </span>
                                                <p>

                                                <div class="d-grid">
                                                    <button v-if="wid.is_added" @click="remove(wid)" type="button" class="btn btn-danger m-0 btn-sm">
                                                        <i class="mdi mdi-trash-can"></i>
                                                        <?= t("Eliminar") ?>
                                                    </button>
                                                    <button v-else type="button" @click="add(wid)" class="btn btn-primary m-0 btn-sm">
                                                        <i class="mdi mdi-check"></i>
                                                        <?= t("Añadir") ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            

                            </div>
                            
                            
                            <div v-if="allWidgets.length > 0" class="form-floating mt-2 mb-2">
                                <input type="text" v-model="widgetSearch" class="form-control" id="widgetSearch" placeholder="Search"/>
                                <label for="widgetSearch"><?= t("Buscar") ?></label>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-4 d-grid">
                                <button type="button" class="btn btn-secondary m-0 btn-sm" data-bs-dismiss="modal">
                                    <i class="mdi mdi-window-close"></i>
                                    <?= t("Cerrar") ?>
                                </button>
                            </div>

                            <div class="col-md-4 d-grid">
                                <button :disabled="loadingWidgetsList || availableWidgetsLoaded" class="btn btn-info" @click="loadListOfWidgets">
                                    <i class="mdi mdi-web"></i>
                                    <span v-if="loadingWidgetsList"><?= t("Cargando") ?>...</span>
                                    <span v-else><?= t("Cargar widgets disponibles") ?></span>
                                </button>
                            </div>

                            <div class="col-md-4 d-grid">
                                <button :disabled="savingFavWidgets" @click="saveFavWidgets" type="button" class="btn btn-success m-0 btn-sm">
                                    <i class="mdi mdi-content-save"></i>
                                    <span v-if="savingFavWidgets"><?= t("Guardando") ?>...</span>
                                    <span v-else><?= t("Guardar") ?></span>
                                </button>
                            </div>


                        </div>


                    </div>
                </div>
            </div>
        </div>

        
        <div class="row">
        
            <div v-for="wid in widgets" :key="wid.id" :class="'col-md-' + wid.size">
                <widget-component 
                        :id="wid.id"
                        :size="wid.size"
                        :type="wid.type"
                    >
                </widget-component>
            </div>
        </div>
    
        <div class="col-md-12">
            <button class="btn btn-secondary" @click="customizePage()"> <?= t("Personalizar Pagina") ?> </button>
            <span v-if="loadingFavWidgetsList"><?= t("Cargando") ?>... </span>
        </div>

    </div>
                   
</template>

<script>

    Vue.component('homepage-widgets-component', {
        template: '#tpl-homepage-widgets-component',
        data() { return {
                loadingWidgetsList: false,
                loadingFavWidgetsList: false,
                dragged: false,
                allWidgets: [],
                widgets: [],
                widgetsTemp: [],
                widgetSearch: '',
                availableWidgetsLoaded: false,
                savingFavWidgets: false,
            }
        },
        async mounted() {
            await this.loadFavWidgets();
        },
        methods: {
            customizePage() {

                let self = this;

                self.widgetSearch = '';
                self.allWidgets = [];
                self.availableWidgetsLoaded = false;
                self.widgetsTemp = JSON.parse(JSON.stringify(self.widgets));

                var myModal = new bootstrap.Modal(document.getElementById('customizeHomePageModal'), {
                })
                myModal.show();

            },
            async loadListOfWidgets() {
                
                let self = this;

                self.widgetSearch = '';
                self.allWidgets = [];
                self.loadingWidgetsList = true;

                var response = await axios({
                    method: 'GET',
                    url: '/InternalApi/GetListOfWidgets/0?response_format=json',
                }).catch(function(error){
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

                    return false;
                });

                if(response.status == 200) {

                    self.allWidgets = response.data.result;
                    self.allWidgets.forEach((itm) => {
                        itm.is_added = self.widgetsTemp.filter((e) => e.id == itm.id).length > 0;
                    })

                    self.availableWidgetsLoaded = true;

                }

                self.loadingWidgetsList = false;

            },
            async loadFavWidgets() {
                
                let self = this;

                self.widgets = [];
                self.loadingFavWidgetsList = true;
                
                var response = await axios({
                    method: 'GET',
                    url: '/InternalApi/GetListOfWidgets/1?response_format=json',
                }).catch(function(error){
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

                    return false;
                });

                if(response.status == 200) {

                    self.widgets = response.data.result;
                    
                }

                self.loadingFavWidgetsList = false;

            },
            async saveFavWidgets() {
                
                let self = this;

                self.savingFavWidgets = true;

                var formData = new FormData();
                formData.append('widgets', JSON.stringify(self.widgetsTemp));
                
                var response = await axios({
                    method: 'POST',
                    url: '/InternalApi/SaveHomeFavWidgets/?response_format=json',
                    data: formData,
                }).catch(function(error){
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

                    return false;
                });

                if(response.status == 200) {
                    $.toast({
                        heading: 'Success',
                        text: 'Settings saved successfuly',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'success'
                    });

                    self.widgets = JSON.parse(JSON.stringify(self.widgetsTemp));

                    myModal = bootstrap.Modal.getInstance(document.getElementById('customizeHomePageModal'))
                    myModal.hide(); 
                }

                self.savingFavWidgets = false;

            },
            add(widget) {
                this.widgetsTemp.push(widget);
                this.allWidgets.find((e) => e.id == widget.id).is_added = true;
            },
            remove(widget) {
                this.widgetsTemp = this.widgetsTemp.filter((e) => e.id != widget.id);
                let obj = this.allWidgets.find((e) => e.id == widget.id);
                if(obj)
                    obj.is_added = false;
            },
            resize(widget, operator) {
                if(operator == 1 && widget.size < 12) {
                    widget.size++;
                }

                if(operator == -1 && widget.size > 0) {
                    widget.size--;
                }

                this.widgetsTemp.find((e) => e.id == widget.id).size = widget.size;
            },
        },
        
    })

</script>