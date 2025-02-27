<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<style scoped>

    .hover-highlight:hover {
        background-color: #f1f3fa;
    }

</style>

<template id="tpl-main">
    
    <div>
        
        <page-title-row-component 
            :title="pageTitle"
        ></page-title-row-component>

        <div class="row pt-3 d-flex justify-content-center">

            <searchbar-component
                :page-title="pageTitle"
                @search="search"
                v-model="keyword"
                :loading="loadingSearch"
                ></searchbar-component>

            <search-result-summary-component
                v-if="searchForTags && !loadingSearch" 
                :posts-count="posts.length"
                :keyword="searchForTags"
                @clear-search="clearSearch()"
                ></search-result-summary-component>

            <search-result-component 
                v-if="searchForTags"
                :selected-post="selectedPost"
                :posts="posts"
                :loading="loadingPosts"
                @open-post="openPost"
            ></search-result-component>
            
            <category-list-component
                v-if="!selectedPost && !loadingSearch && !searchForTags"
                :loading="loadingSearch"
                :selected-category="selectedCategory"
                @open-category="openCategory"
            ></category-list-component>


            <post-list-component
                v-if="selectedCategory"
                :loading="loadingPosts"
                :posts="posts"
                :selected-post="selectedPost"
                :selected-category="selectedCategory"
                @open-post="openPost"
            ></post-list-component>


            <post-component
                v-if="selectedPost"
                :selected-post="selectedPost"
                :help-posts-ctype-id="helpPostsCtypeId"
                @search="search"
                @close="selectedPost = null"
            ></post-component>

        </div>
        <!-- end row -->

        </div> <!-- End Content -->
    </div>
</template>



<?= Application::getInstance()->view->renderView('Components/LogComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('help/Components/SearchbarComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('help/Components/SearchResultSummaryComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('help/Components/SearchResultComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('help/Components/CategoryListComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('help/Components/PostListComponent', (array)$data) ?>
<?= Application::getInstance()->view->renderView('help/Components/PostComponent', (array)$data) ?>

<?= Application::getInstance()->view->renderView('help/index.js', (array)$data) ?>
