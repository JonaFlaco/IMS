<template id="tpl-attachment-preview-simple-component">

    <div class="ms-1 me-2 mt-1">
        <span v-for="afile in attachments">
            <a 
                class="text-dark"
                :href="'/filedownload?ctype_id=ctypes_logs&field_name=attachments&size=orginal&file_name=' + afile.name" 
                target="_blank">

                <img height=32 width=32 
                    :alt="afile.original_name" 
                    :src="getFileThumbnail('ctypes_logs', 'attachments', afile.name)">
                {{ afile.original_name }} ({{Number(afile.size / 1024).toFixed(2)}} KB)
            </a>
        </span>
    </div>

</template>


<script type="text/javascript">
    var attachmentPreviewSimpleComponent = {

        template: '#tpl-attachment-preview-simple-component',
        data() {
            return {
                title: 'Cache',
                
            }
        },
        props: ['attachments'],
        mounted() {
            
        },
        methods: {
           
        },
        computed: {

        },
    }

    Vue.component('attachment-preview-simple-component', attachmentPreviewSimpleComponent)
</script>