<template id="tpl-search-result-summary-component">
    
    <div class="col-lg-12 mb-2">
        Showing {{ postsCount }} result(s) for "{{keyword}}" 
        <a href="javascript: void(0);" class="text-danger" @click="clearSearch()">
            Clear Search
        </a>
    </div>
</template>

<script>

Vue.component('search-result-summary-component', {
    template: '#tpl-search-result-summary-component',
    props: ['postsCount','keyword'],
    data() {
        return {
            
        }

    },
    methods: {
        clearSearch() {
            this.$emit('clear-search')
        }
    }
});

</script>
