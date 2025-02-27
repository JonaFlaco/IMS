<?php 

use App\Core\Application;
use App\Core\Gctypes\Ctype;
use App\Exceptions\ForbiddenException;
use App\Helpers\DateHelper;

$category = Application::getInstance()->coreModel->nodeModel("help_categories")
    ->id($data['nodeData']->category_id)
    ->loadFirstOrFail();


if(!empty($category->roles)) {
        
    $has_access = false;
    
    foreach($category->roles as $role) {
        if(in_array($role->value, explode(",", $this->app->user->getRoles())))
            $has_access = true;
        
    }

    if(!$has_access){
        throw new ForbiddenException();
    }

}


$data['title'] = $data['nodeData']->title;
$data['helpPostsCtype'] = (new Ctype)->load("help_posts");

$data['nodeData']->body = \App\Core\MarkDown::parse($data['nodeData']->body);

$data['nodeData']->tagList = _explode(" ", $data['nodeData']->tags);
$data['nodeData']->created_date = DateHelper::humanify(strtotime($data['nodeData']->created_date));

if(isset($data['nodeData']->last_update_date)){
    $data['nodeData']->last_update_date = DateHelper::humanify(strtotime($data['nodeData']->last_update_date));
}

$data['sett_load_rich_text_editor'] = true;

?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<style scoped>

    .hover-highlight:hover {
        background-color: #f1f3fa;
    }

</style>

<template id="tpl-main">
    
    <div>
        <page-title-row-component :title="pageTitle"></page-title-row-component>

        <div class="row">
            <div class="col-sm-12">
                <div class="text-center">
                    <h3><i class="mdi mdi-help-circle me-1"></i>{{ pageTitle }}</h3>
                    <p class="text-muted mt-3">Write your question in textbox below and click Search to see the result</p>

                    <div class="d-flex justify-content-center">
                        <div class="app-search col-md-6">
                            <form>
                                <div class="input-group">
                                    <input type="text" v-model="keyword" 
                                        @keyup.enter="search()" class="form-control" 
                                        placeholder="Search..." id="top-search">
                                    <span class="mdi mdi-magnify search-icon"></span>
                                    <button class="input-group-text btn-primary" @click="search()" type="button">
                                        Search
                                    </button>
                                </div>
                            </form>    
                        </div>

                    </div>
                </div>
            </div><!-- end col -->
        </div><!-- end row -->


        <div class="row pt-3 d-flex justify-content-center">

            <div class="col-lg-12 card" v-if="selectedPost">
                <div class="card-body">

                    <div class="btn-group">
                        <a href="/help" type="button" class="btn btn-secondary">
                            <i class="mdi mdi-keyboard-backspace font-16"></i>
                        </button>
                        <a :href="'/help_posts/edit/' + selectedPost.id" target="_blank" class="btn btn-secondary">
                            <i class="mdi mdi-file-edit-outline font-16"></i>
                        </a>
                    </div>
                    
                    <div class="mt-3">
                        <h5 class="font-18">{{ selectedPost.title }}</h5>
                        <hr/>
                        <div class="d-flex mb-3 mt-1">
                            <img class="d-flex me-2 rounded-circle" :src="'/filedownload?ctype_id=users&field_name=profile_picture&size=small&file_name=' + selectedPost.created_user_id_profile_picture" alt="placeholder image" height="32">
                            <div class="w-100 overflow-hidden">
                                <small class="float-end"><i class="mdi mdi-clock-outline"></i>{{ selectedPost.created_date }}</small>
                                <h6 class="m-0 font-14">{{ selectedPost.created_user_id_display }}</h6>
                                <small class="text-muted">
                                    {{selectedPost.created_user_id_position}}
                                </small>
                            </div>
                        </div>


                        <div v-html="selectedPost.body"></div>
                        
                        <br>
                        <span class="me-2" v-if=" selectedPost.last_update_date">
                            <i class="mdi mdi-clock-outline me-1"></i>Last update: {{ selectedPost.last_update_date }} &nbsp;&nbsp; |
                        </span>
                        <span v-if="selectedPost.tagList.length > 0">
                            Tags: <a :href="'/help?keyword=' + tag" v-for="(tag, index) in selectedPost.tagList" :key="index"> 
                                <i class="mdi mdi-tag"></i>
                                {{tag}} 
                            </a>
                        </span>
                        <br><br>
                        <hr/>
                        <log-component 
                            :ctype-name="helpPostsCtype.id" 
                            :content-id="selectedPost.id"
                            csrf-token="<?= Application::getInstance()->csrfProtection->create("ctypes_logs") ?>">
                            </log-component>
                    </div>
  
                </div>
            </div>

        </div>
        <!-- end row -->

        </div> <!-- End Content -->
    </div>
</template>


<?= Application::getInstance()->view->renderView('Components/LogComponent', (array)$data) ?>


<script>

Vue.filter('formatNoOfPosts', function (value) {
    if (!value || value == 0) 
        return 'Empty'
    else
        return value;
});


var vm = new Vue({
    el: '#vue-cont',
    template: '#tpl-main',
    data: {
        pageTitle: 'Help Center',
        selectedPost: <?= json_encode($data['nodeData']) ?>,
        helpPostsCtype: <?= json_encode($data['helpPostsCtype']) ?>,
        keyword: '',
    },
    methods: {
        search() {
            window.location.href = "/help?keyword=" + this.keyword;
        }
    }
})
</script>
