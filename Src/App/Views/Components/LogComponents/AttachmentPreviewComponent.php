<template id="tpl-attachment-preview-component">


    <div class="card-body pb-0 ps-0 pe-0 pt-1">

        <div class="row col-md-12 m-0 p-0">
        
            <div v-for="im in attachments" class="col-md-12 p-0">
            
                <div class="card mb-1 mt-1 shadow-none border ps-2 pe-2">
                    <div class="p-2">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img @click="previewImage('ctypes_logs','attachments',im.name)" style="cursor:pointer;" v-if="im.extension == 'png' || im.extension == 'jpg' || im.extension == 'jpeg' || im.extension == 'gif'" 
                                    :src="'/filedownload?ctype_id=ctypes_logs&field_name=attachments&size=small&file_name=' + im.name" class="avatar-sm rounded" alt="file-image">
                                <img v-else-if="im.extension == 'doc' || im.extension == 'docx'" src="/assets/app/images/icons/doc.svg" class="avatar-sm rounded" alt="file-image">
                                <img v-else-if="im.extension == 'txt'" src="/assets/app/images/icons/doc.svg" class="avatar-sm rounded" alt="file-image">
                                <img v-else-if="im.extension == 'xls' || im.extension == 'xlsx'" src="/assets/app/images/icons/xls.svg" class="avatar-sm rounded" alt="file-image">
                                <img v-else-if="im.extension == 'pdf'" src="/assets/app/images/icons/pdf.svg" class="avatar-sm rounded" alt="file-image">

                                <div v-else class="avatar-sm">
                                    <span class="avatar-title bg-primary-lighten text-primary rounded">
                                        .{{im.extension ? im.extension.toUpperCase() : 'N/A'}}
                                    </span>
                                </div>

                            </div>

                            <div class="col ps-0">
                                <a v-if="im.extension == 'png' || im.extension == 'jpg' || im.extension == 'jpeg' || im.extension == 'gif'" @click="previewImage('ctypes_logs','attachments',im.name)" href="javascript: void(0);" class="text-muted font-weight-bold">{{im.original_name}}</a>
                                <a v-else :href="'/filedownload?ctype_id=ctypes_logs&field_name=attachments&file_name=' + im.name" class="text-muted font-weight-bold">{{im.original_name}}</a>
                                <p class="mb-0">{{Number(im.size / 1024).toFixed(2)}} KB</p>
                            </div>
                            <div class="col-auto">
                                
                                <a target="_blank" :href="'/filedownload?ctype_id=ctypes_logs&field_name=attachments&file_name=' + im.name" class="btn btn-link btn-lg text-muted">
                                    <i class="dripicons-download"></i>
                                </a>
                                <button class="btn btn-link btn-lg text-muted" 
                                    :disabled="false" 
                                    v-on:click="removeAttachments(im.name)">
                                    <i class="dripicons-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>



</template>


<script type="text/javascript">
    var attachmentPreviewComponent = {

        template: '#tpl-attachment-preview-component',
        data() {
            return {
                title: 'Cache',
                
            }
        },
        props: ['attachments'],
        mounted() {
            
        },
        methods: {
            removeAttachments(name){
                this.$parent.removeAttachments(name);
            }
        },
        computed: {

        },
    }

    Vue.component('attachment-preview-component', attachmentPreviewComponent)
</script>