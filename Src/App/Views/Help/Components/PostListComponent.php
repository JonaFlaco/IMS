<template id="tpl-post-list-component">
    
    <div :class="{'col-lg-4' : selectedPost, 'col-lg-8' : !selectedPost}">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title">
                    <span v-if="selectedCategory == null">Select a category in the left side </span>
                    <span v-else-if="loading">Loading posts from {{ selectedCategory.name }}... </span>
                    <span v-else-if="posts.length == 0">No post in {{ selectedCategory.name }} to show</span>
                    <span v-else="selectedCategory">Posts from {{ selectedCategory.name }} </span>
                </h5>

                <div v-if="selectedCategory">
                
                    <div v-for="(post, index) in posts" :key="index" 
                        @click="openPost(post)"
                        class="d-flex align-items-start mb-2 hover-highlight cursor-pointer">
                        <img class="me-3 rounded-circle" :src="'/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=' + post.created_user_id_profile_picture" width="40" alt="User Image">
                        <div class="w-100 overflow-hidden">
                            <span class="font-13 float-end"><i class="mdi mdi-clock-outline"></i>{{ post.created_date_humanify }}</span>
                            <h5 class="mt-0 mb-1" :class="{'text-primary' : selectedPost == post}">
                                <i v-tooltip="'This post is pined to top'" v-if="post.pin" class="mdi mdi-pin"></i>
                                {{ post.title }} 
                            </h5>
                            <span class="font-13">By {{ post.created_user_id_display }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</template>

<script>

Vue.component('post-list-component', {
    template: '#tpl-post-list-component',
    props: ['selectedPost','posts','selectedCategory','loading'],
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
