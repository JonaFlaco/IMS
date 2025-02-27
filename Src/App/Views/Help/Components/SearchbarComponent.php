<template id="tpl-searchbar-component">
    
    <div class="col-sm-12 mb-2">
        <div class="text-center">
            <h3><i class="mdi mdi-help-circle me-1"></i>{{ pageTitle }}</h3>
            <p class="text-muted mt-3">Write your question in textbox below and click Search to see the result</p>

            
            <div class="d-flex justify-content-center">
                <div class="app-search col-md-6">
                    <div class="input-group">
                        <input type="text" 
                            v-bind:value="value"
                            v-on:input="updateValue"
                            @keyup.enter="search()" 
                            class="form-control" 
                            placeholder="Search..." id="top-search">
                        
                        <span class="mdi mdi-magnify search-icon"></span>

                        <button class="input-group-text btn-primary" 
                        type="button"
                        @click="search()"
                        :disabled="loading"
                        >
                            <span v-if="loading">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Searching...
                            </span>
                            <span v-else>
                                Search
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div><!-- end col -->

</template>

<script>

Vue.component('searchbar-component', {
    template: '#tpl-searchbar-component',
    props: ['pageTitle', 'loading' ,'value'],
    data() {
        return {
            
        }
    },
    methods: {
        search(event) {
            this.$emit('search', this.value);
        },
        updateValue: function (event) {
            this.$emit('input', event.target.value);
        },
    }
});

</script>
