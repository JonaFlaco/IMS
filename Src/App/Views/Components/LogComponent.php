<?php use \App\Core\Application; ?>

<template id="tpl-log-component">
    
    <div class="col-lg-12">

        <div class="tab-content">
            <div class="tab-pane show active" id="home-b1">
                
                <div v-if="loadingLog">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?= t("Loading") ?>...
                </div>

                <div id="log_section">
                
                    <div v-if="!loadingLog">
                        
                        <div class="mt-2 mb-2" style="border-color: silver; border-bottom-width: 1px; border-bottom-style: solid;">

                            <textarea v-model="add_log_justification" class="form-control form-control-light mb-1" placeholder="<?= t("Escribe un comentario") ?>" rows="2"></textarea>
                            <div class="row">
                                <attachment-preview-component
                                    v-if="!attachmentIsForReply"
                                    :attachments="attachments"
                                    class="col-md-12 pt-1 pb-1"
                                    ></attachment-preview-component>
                                
                                <div class="text-end mb-1">
                                
                                    <input type="file" class="form-control"  
                                        ref="log_attachments" 
                                        id="log_attachments" 
                                        style="display:none"
                                        name="log_attachments" 
                                        v-on:change="prepareToUploadAttachments()" 
                                        multiple>

                                    <label :disabled="uploading_attachments == 1" for="log_attachments" class="form-label">
                                        <button @click="triggerUploadAttachment(null)" type="button" class="btn btn-link btn-sm text-muted font-18">
                                            <i class="dripicons-paperclip"></i>
                                        </button>
                                    </label>

                                    <button :disabled="loading_add_log == 1" type="button" @click="AddLog(null)" class="btn btn-primary btn-sm">
                                        <span v-if="loading_add_log == 1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <?= t("Enviar") ?>
                                    </button>
                                </div>
                            </div>
                        
                        </div>
                        


                        <div v-for="lg in log" class="d-flex mb-2 mt-1">
                            <img class="me-2 avatar-sm rounded-circle" :src="'/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=' + lg.profile_picture_name">
                            <div class="w-100">
                                
                                <h5 class="mt-0">
                                    <img v-if="lg.is_comment != 1" height="24" width="24" src="/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=<?= SYSTEM_USER_PROFILE_PICTURE ?>">
                                    {{lg.user_full_name}}: {{lg.title}} 
                                    <small class="text-muted float-end">{{ lg.date_humanify }}</small>
                                </h5>
                                
                                <span v-if="lg.justification != null && lg.justification.length > 0"> 
                                    <span v-html="lg.justification"></span> <br />

                                    <ul>
                                        <li v-for="(x, index) in lg.reasons_list"> {{ x.name }} </li>
                                    </ul>
                                </span>
                                
                                <attachment-preview-simple-component
                                    class="ms-1 me-2 mt-1"
                                    :attachments="lg.attachments"
                                    ></attachment-preview-simple-component>
                                
                                <a href="javascript: void(0);" @click="ShowReplyBox(lg.id)" class="text-muted font-13 d-inline-block mt-2"><i
                                                class="mdi mdi-reply"></i> <?= t("Reply") ?></a>

                                <?= (Application::getInstance()->user->isAdmin() == true ? '<a v-if="(lg.sub_items == null || lg.sub_items.length == 0) && lg.is_comment == 1" href="javascript: void(0);" class="text-danger" @click="DeleteLog(lg.id)"><i class="mdi mdi-trash-can-outline"></i> Delete</a>' : '') ?>
                        

                                <div v-for="xg in lg.sub_items" class="d-flex mt-2">
                                    <img class="me-3 avatar-sm rounded-circle" :src="'/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=' + xg.profile_picture_name">
                                    <div class="w-100">
                                        <h5 class="mt-0">
                                            {{xg.user_full_name}}: {{xg.title}}
                                            <small class="text-muted float-end">{{xg.date_humanify}}</small>
                                        </h5>
                                        
                                        <span v-if="xg.justification != null && xg.justification.length > 0">
                                            <span v-html="xg.justification"></span> <br />
                                            <ul>
                                                <li v-for="(x, index) in lg.reasons_list"> {{ x.name }} </li>
                                            </ul>
                                        </span>

                                        <attachment-preview-simple-component
                                            class="ms-1 me-2 mt-1"
                                            :attachments="xg.attachments"
                                            ></attachment-preview-simple-component>
                                    </div>
                                </div>

                                <div v-if="log_reply_to_log_id != null && log_reply_to_log_id.length > 0 && lg.show_reply_box != undefined && lg.show_reply_box == 1" class="inbox-item-text mt-2" style="border-color: silver; border-bottom-width: 1px; border-bottom-style: solid;">

                                    <textarea v-model="add_log_sub_justification" class="form-control form-control-light mb-1" placeholder="Write a reply" rows="2"></textarea>

                                    <attachment-preview-component
                                        v-if="attachmentIsForReply == lg.id"
                                        :attachments="attachments"
                                        class="col-md-12 pt-1 pb-1"
                                        ></attachment-preview-component>
                                        
                                    <div class="text-end">
                                        <div class="btn-group mb-2 ms-2 d-none d-sm-inline-block">

                                            <label :disabled="uploading_attachments == 1" for="log_attachments" class="form-label">
                                                <button @click="triggerUploadAttachment(lg.id)" type="button" class="btn btn-link btn-sm text-muted font-18">
                                                    <i class="dripicons-paperclip"></i>
                                                </button>
                                            </label>

                                            <button :disabled="loading_add_log == 1" type="button" @click="AddLog(lg.id)" class="btn btn-primary btn-sm">
                                                <span v-if="loading_add_log == 1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                <?= t("Reply") ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div v-if="load_all_log_records != true && pending_log_records > 0" class="text-center mt-2">
                            <a href="javascript:void(0);" @click="showLog(1)" class="text-danger"><?= t("Cargar más") ?> ({{pending_log_records}})</a>
                        </div>
                    </div>
                            
                </div>

            </div>
            
        </div>


    </div>
                   
</template>

<script>

    Vue.component('log-component', {
        template: '#tpl-log-component',
        props: {
            ctypeId: {},
            contentId: {},
        },
        data() {
            return {
                loadingLog: false,
                log: [],
                loading_add_log: false,
                log_reply_to_log_id: null,
                add_log_justification: '',
                add_log_sub_justification: '',
                load_all_log_records: false,
                pending_log_records: false,
                
                attachments: [],
                uploading_attachments: false,
                var_attachments: [],
                attachmentIsForReply: null,
            }
        },
        mounted() {
            this.showLog();
            this.$watch(
                "contentId",
                (new_value, old_value) => {
                    this.showLog();
                }
            );
            this.$watch(
                "ctypeId",
                (new_value, old_value) => {
                    this.showLog();
                }
            );

        },
        methods: {
            triggerUploadAttachment(id) {

                if(this.attachmentIsForReply != id){
                    this.clearUpload();
                }

                this.attachmentIsForReply = id;
                
                $("#log_attachments").click();
            },
            prepareToUploadAttachments(){
                let self = this;
            
                self.var_attachments = this.$refs.log_attachments.files;
                self.uploadAttachments(2);
                
            },
            removeAttachments(name){
                let self = this;
                
                self.attachments = self.attachments.filter((e)=>e.name !== name );
                    
                $("#log_attachments").val('');
                
                //self.validate();
            },
            
            uploadAttachments(fileType){
               
                let self = this
                let formData = new FormData();

                this.uploading_attachments = true;
                for( var i = 0; i < self.var_attachments.length; i++ ){
                    let file = self.var_attachments[i];
                    formData.append('attachments[' + i + ']', file);
                }

                axios.post( '/fileupload/upload/ctypes_logs?file_type=' + fileType + '&field_name=attachments&response_format=json',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
                ).then(function(response){
                    if(response.data.status == 'success'){
                    
                        response.data.images.forEach(function(itm){
                            self.attachments.push(itm);
                        });
                        self.uploading_attachments = false;
                        
                        //self.validate();
                    } else {
                        $.toast({
                            heading: 'Error',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }

                    
                    self.uploading_attachments = false;
                    $("#log_attachments").val('');
                    
                }).catch(function(error){

                    if(error.response != undefined && error.response.data.status == 'failed'){
                        error = error.response.data.message;
                    }

                    $.toast({
                        heading: 'Error',
                        text: error,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    
                    self.uploading_attachments = false;
                    $("#log_attachments").val('');
                    
                });
            },


            showLog(show_all = false){
                
                if(show_all == true){
                    this.load_all_log_records = 1;
                }

                let self = this;
                this.log_reply_to_log_id = null;
                self.log = [];
                
                this.loadingLog = true;
        
                axios({
                    method: 'get',
                    url: '/InternalApi/getCtypesLog/' + this.contentId + '&ctype_id=' + this.ctypeId + '&load_all_records=' + (self.load_all_log_records == true ? 1 : 0) + '&response_format=json',
                })
                .then(function(response){
                    if(response.data.status == 'success'){
                                
                        self.log = response.data.result;

                        self.log.forEach((itm) => {
                            itm.attachments = JSON.parse(itm.attachments);
                            itm.reasons_list = JSON.parse(itm.reasons_list);
                            itm.sub_items.forEach((x) => {
                                x.attachments = JSON.parse(x.attachments);
                                x.reasons_list = JSON.parse(x.reasons_list);
                            }); 

                        });

                        if(self.log != null && self.log.length > 0){
                            self.pending_log_records = self.log[0].pending_log_records;
                        }

                        self.loadingLog = false;
        
                    } else {
                        
                        self.loadingLog = false;
        
                        $.toast({
                            heading: 'error',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                    }
                    
                })
                .catch(function(error){
        
                    self.loadingLog = false;
                    
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
        
            },
            ShowReplyBox(id){

                if(this.log_reply_to_log_id == id)
                    return;

                this.log.forEach((e) => {
                    if(e.id == id){
                        e.show_reply_box = 1;
                    } else {
                        e.show_reply_box = 0;
                    }
                });

                this.clearUpload();

                this.add_log_sub_justification = '';
                    
                this.log_reply_to_log_id = id;
                
            },
            clearUpload() {
                $("#log_attachments").val('');
                this.attachments = [];
                this.var_attachments = [];
            },
            DeleteLog(id){
                
                let self = this;
                
                this.loading_add_log = true;
        
                let formData = new FormData();
                formData.append('id_list', id);
                formData.append('ctype_id', "ctypes_logs");


                axios({
                    method: 'post',
                    url: '/InternalApi/deleteRecord/?response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                        'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("delete_ctype_log") ?>',
                    }
                })
                .then(function(response){

                    if(response.data.status == 'success'){
        
                        self.loading_add_log = false;

                        //self.log.push(response.data.log);

                        self.showLog();
        
                    } else {
                        
                        self.loading_add_log = false;
        
                        $.toast({
                            heading: 'error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
        
        
                    }
                    
                })
                .catch(function(error){
        
                    self.loading_add_log = false;
                    
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
        
            },
            AddLog(parentLog){
                
                let self = this;

                if(this.attachmentIsForReply != parentLog) {
                    this.clearUpload();
                }
                
                var justification = this.add_log_justification;
                
                if(parentLog > 0){
                    justification = this.add_log_sub_justification;
                }

                if(justification == null || justification.length == 0){
                    $.toast({
                        heading: 'error',
                        text: 'Comment can not be empty',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    return;
                }

                this.loading_add_log = true;
        
                let formData = new FormData();
                formData.append('justification', justification);
                formData.append('attachments', JSON.stringify(this.attachments));

                axios({
                    method: 'post',
                    url: '/InternalApi/addCtypesLog/' + this.contentId + '&ctype_id=' + this.ctypeId + '&reply_to_id=' + (parentLog > 0 ? self.log_reply_to_log_id : '') + '&response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                        'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("add_ctype_log") ?>',
                    }
                })
                .then(function(response){

                    if(response.data.status == 'success'){
        
                        self.loading_add_log = false;

                        self.add_log_justification = '';
                        self.add_log_sub_justification = '';

                        self.attachments = [];
                        self.var_attachments = [];

                        self.showLog();
        
                    } else {
                        
                        self.loading_add_log = false;
        
                        $.toast({
                            heading: 'error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
        
        
                    }
                    
                })
                .catch(function(error){
        
                    self.loading_add_log = false;
                    
                });
        
            },
        }

    })

</script>

<?= Application::getInstance()->view->renderView('Components/LogComponents/AttachmentPreviewComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('Components/LogComponents/AttachmentPreviewSimpleComponent', (array)$data) ?>