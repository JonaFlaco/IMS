<template id="tpl-search-result-component">
    <div 
        :class="{'col-lg-4' : selectedPost, 'col-lg-12' : !selectedPost}">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title">
                    <span v-if="loading">Loading results... </span>
                    <span v-else-if="posts.length == 0">No result found to show</span>
                    <span v-else>Search result: </span>
                </h5>

                <div v-for="(post, index) in posts" :key="index" 
                    @click="openPost(post)"
                    class="d-flex align-items-start mb-2 hover-highlight cursor-pointer">
                    <img class="me-3 rounded-circle" :src="'/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=' + post.created_user_id_profile_picture" width="40" alt="Generic placeholder image">
                    <div class="w-100 overflow-hidden">
                        <span class="font-13 float-end"><i class="mdi mdi-clock-outline"></i>{{ post.created_date_humanify }}</span>
                        <h5 class="mt-0 mb-1" :class="{'text-primary' : selectedPost == post}">
                            <i v-tooltip="'This post is pined to top'" v-if="post.pin" class="mdi mdi-pin"></i>
                            {{ post.title }} 
                        </h5>
                        <span class="font-13">
                            <span class="ps-1 border-start border-3 me-3" :style="'border-color: ' + post.category_color + ' !important'">
                                <span class="mt-0 mb-1">{{ post.category_id_display}}</span>
                            </span>
                            By {{ post.created_user_id_display }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>

Vue.component('search-result-component', {
    template: '#tpl-search-result-component',
    props: ['selectedPost','posts','loading'],
    data() {
        return {
            
        }

    },
    methods: {
        openPost(post) {
            this.$emit('open-post', post);
        }
    }
});

</script>
