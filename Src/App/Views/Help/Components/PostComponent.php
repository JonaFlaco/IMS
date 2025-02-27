<template id="tpl-post-component">
    <div class="col-lg-8 card">
        <div class="card-body">

            <div class="btn-group">
                <button type="button" class="btn btn-secondary" @click="closePost">
                    <i class="mdi mdi-keyboard-backspace font-16"></i>
                </button>
                <a :href="'/help_posts/edit/' + selectedPost.id" target="_blank" class="btn btn-secondary">
                    <i class="mdi mdi-file-edit-outline font-16"></i>
                </a>
                <a :href="'/help_posts/show/' + selectedPost.id" target="_blank" class="btn btn-secondary">
                    <i class="mdi mdi-open-in-new font-16"></i>
                </a>
            </div>
            
            <div class="mt-3">
                <h5 class="font-18">{{ selectedPost.title }}</h5>
                <hr/>
                <div class="d-flex mb-3 mt-1">
                    <img class="d-flex me-2 rounded-circle" :src="'/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=' + selectedPost.created_user_id_profile_picture" alt="placeholder image" height="32">
                    <div class="w-100 overflow-hidden">
                        <small class="float-end"><i class="mdi mdi-clock-outline"></i>{{ selectedPost.created_date_humanify }}</small>
                        <h6 class="m-0 font-14">{{ selectedPost.created_user_id_display }}</h6>
                        <small class="text-muted">
                            {{selectedPost.created_user_id_position}}
                        </small>
                    </div>
                </div>


                <div v-html="selectedPost.body"></div>
                
                <br>
                <span class="me-2" v-if=" selectedPost.last_update_date_humanify">
                    <i class="mdi mdi-clock-outline me-1"></i>Last update: {{ selectedPost.last_update_date_humanify }} &nbsp;&nbsp; |
                </span>
                <span v-if="selectedPost.tagList.length > 0">
                    Tags: <a href="javascript: void(0);" @click="search(tag)" v-for="(tag, index) in selectedPost.tagList" :key="index"> 
                        <i class="mdi mdi-tag"></i>
                        {{tag}} 
                    </a>
                </span>
                
                <br><br>
                
                <hr/>
                
                <log-component 
                    v-if="selectedPost"
                    :ctype-id="helpPostsCtypeId" 
                    :content-id="selectedPost.id"
                    csrf-token="<?= \App\Core\Application::getInstance()->csrfProtection->create("ctypes_logs") ?>"
                ></log-component>

            </div>

        </div>
    </div>
</template>

<script>

Vue.component('post-component', {
    template: '#tpl-post-component',
    props: ['selectedPost','helpPostsCtypeId'],
    data() {
        return {
            
        }

    },
    methods: {
        search(keyword) {
            this.$emit('search', keyword);
        },
        closePost() {
            this.$emit('close');
        },
    }
});

</script>
