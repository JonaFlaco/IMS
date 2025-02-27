<template id="tpl-category-list-component">
    
    <div class="card "
        :class="{'col-lg-4' : selectedCategory, 'col-lg-12' : !selectedCategory}"
        >

        <div class="card-body">
            <h5 class="card-title">Categories</h5>

            <div v-if="loading">
                Loading...
            </div>
            <div v-else-if="categories.length == 0">
                <i class="mdi mdi-information-outline"></i>
                No search result, please try another keyword.
            </div>
            <div v-else>
                <div class="row" v-for="(cat, index) in categories" :key="index" >
                    
                    <div 
                        class="ps-1 border-start border-3 mb-2 hover-highlight cursor-pointer" 
                        :style="'border-color: ' + cat.color + ' !important'">
        
                        <div
                            @click="openCategory(cat)"
                            class="d-flex align-items-start">
                            <!-- <img class="me-3 rounded-circle" src="/assets/app/images/icons/image.png" width="40" alt="Category Icon"> -->
                            <div class="w-100 overflow-hidden">
                                <span class="badge badge-info-lighten font-13 float-end">{{ cat.no_of_posts}}</span>
                                <h5 class="mt-0 mb-1" :class="{'text-primary' : selectedCategory == cat}">{{ cat.name}}</h5>
                                <span class="font-13">{{ cat.description}}</span>
                            </div>
                        </div>
                
                        <div class="col-d-10 mt-1">
                            <span v-for="(sub, index) in cat.sub_categories" :key="index" 
                                class="ps-1 border-start border-3 me-3 " 
                                :style="'border-color: ' + sub.color + ' !important'"
                                @click="openCategory(sub)">
                                <span class="mt-0 mb-1" :class="{'text-primary' : selectedCategory == sub}">{{ sub.name}}</span>
                            </span>
                        </div>
                    </div>
                </div>
                
            </div>
            
            
        </div>

    </div>

</template>

<script>

Vue.component('category-list-component', {
    template: '#tpl-category-list-component',
    props: ['selectedCategory'],
    data() {
        return {
            categories: [],
            loading: false,
        }

    },
    async mounted() {
        await this.getCategories();
    },
    methods: {
        openCategory(category) {
            this.$emit('open-category', category); 
        },
        async getCategories() {

            let self = this;
            self.loading = true;
            self.categories = [];

            var response = await axios.get('/InternalApi/HelpGetCategories/?response_format=json',   
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
                    
                    self.loading = false;
                });

            if(response.status == 200) {
                this.categories = response.data.result;
                this.categories.forEach((item) => {
                    if(item.sub_categories == null || item.sub_categories.length == 0) {
                        item.sub_categories = [];
                    } else {
                        item.sub_categories = JSON.parse(item.sub_categories);
                    }
                })
                self.loading = false;
            }

        },
    }
});

</script>
