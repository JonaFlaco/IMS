<?php
    $keyword = \App\Core\Application::getInstance()->request->getParam("keyword"); 
?>

<script>

var vm = new Vue({
    el: '#vue-cont',
    template: '#tpl-main',
    data: {
        pageTitle: '<?= $data['title'] ?>',
        keyword: '<?= $keyword ?>',
        searchForTags: '',
        selectedCategory: null,
        posts: [],
        loadingPosts: false,
        selectedPost: null,

        helpPostsCtypeId: <?= json_encode($data['helpPostsCtype']->id) ?>
    },
    computed: {
        keywordList: function() {
            return new Set(this.searchForTags.split(" "));
        },
        loadingSearch: function() {
            return (this.searchForTags && this.loadingPosts) || this.loadingCategories;
        },
    },
    mounted() {
        if(this.keyword != null && this.keyword.length > 0) {
            this.search();
        }
    },
    methods: {
        async clearSearch() {
            this.keyword = null;
            this.selectedPost = null;
            this.selectedCategory = null;
            this.searchForTags = null;
            this.updateKeyworkParamInUrl();
        },
        updateKeyworkParamInUrl(value = null) {
            key = "keyword";
            var url = new URL(window.location.href);
            if(value == null || value.length == 0 || value == "null") {
                url.searchParams.delete(key);
            } else {
                url.searchParams.set(key, value);
            }
            window.history.replaceState(null, null, url);
        },
        async openPost(post) {
            this.selectedPost = post;
        },
        async openCategory(category) {
            this.selectedCategory = category;
            this.selectedPost = null;
            await this.getPosts();
        },
       
        async getPosts() {
            
            let self = this;
            self.loadingPosts = true;
            self.posts = [];
            var response = await axios.get('/InternalApi/HelpGetPosts/' + (this.selectedCategory ? this.selectedCategory.id : '') + '?keyword=' + (this.keyword == null ? '' : this.keyword) + '&response_format=json',   
                ).catch(function(error){
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

                    self.loadingPosts = false;
                });

            if(response.status == 200) {
                this.posts = response.data.result;
                self.loadingPosts = false;
            }

        },

        async search(keyword) {

            if(keyword) {
                this.keyword = keyword;
            }

            if(this.keyword == null) {
                this.keyword = "";
            }

            this.selectedCategory = null;
            this.posts = [];
            this.selectedPost = null;

            this.searchForTags = this.keyword.trim();

            this.updateKeyworkParamInUrl(this.keyword);
            await this.getPosts();
        },
    }
})
</script>
