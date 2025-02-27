<template id="tpl-dependencies-component">
    <div>
            
        <div class="col-md-12 p-0 m-0 mb-3">
            <label class="form-label">
                {{ title }} 
                <a data-bs-toggle="collapse" :href="'#dependencyExplanation' + name" role="button" aria-expanded="false" :aria-controls="'#dependencyExplanation'">
                    <i class="text-info mdi mdi-information cursor-pointer" v-tooltip="'Help'"></i> 
                </a>
                <i class="text-success mdi mdi-play cursor-pointer" v-tooltip="'Check synctax'" @click="check(true)"></i>
            </label>
            <textarea rows="5" class="form-control" 
                row="4"
                v-bind:value="value"
                v-on:input="updateValue"
                :required="isRequired"
                ref="inputArea"
                ></textarea>
        </div>

        <div class="card mt-1">
            <div class="card-body p-0">
                
                <div :id="'dependencyExplanation' + name" class="collapse p-2">
                    <h5 class="card-title mb-0">Explanation</h5>
                    <pre><code>
    
    function selected(data_source, field_name, value, operator = '=') { }

    Paramters:
        data_source
            Specify the source of the field
            Is required
            Example: 
                ''  //To access the Content-Type no matter if inside Field-Collection or Content-Type
                'items' //To access 'items' Field-Collection
                self //To get data from the same context that the field is
        field_name
            Specify field name    
            Is required
            Example:
                'status_id',
                'is_completed',
                'full_name'
        value
            Specify the value field to be filtered based on
            Is required
            Example
                '',
                '2',
                'Male'
        operator
            Specify the operator type to be filtered based on
            Default value is '='
            Is not required but if you don't provide it will set it as the default value
            Example
                '=',
                '!=',
                '>='
                '<='

    Examples:
        //If status_id = 2 and staus_id is in the ctype
        selected('', 'status_id', '2', '=')
        //If status_id = 2 and status_id is in the same context as the field is
        selected(self, 'status_id', '2', '=')
        //If status_id = 2 and status_id is in 'items' Field-Collection
        selected('items', 'status_id', '2', '=')
                    </code></pre>

                    <a data-bs-toggle="collapse" :href="'#dependencyExplanation' + name" role="button" aria-expanded="false" :aria-controls="'#dependencyExplanation' + name">
                        <i class="mdi mdi-close"></i>
                        Hide Explanation
                    </a>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    
    var selected = function(source, field = required("field"), value = null, operator = '=') { return true; } 
    function required(name) {   throw new Error('Parameter ' + name +' is missing'); }

    Vue.component('dependencies-component', {
        template: '#tpl-dependencies-component',
        props: ['title','value','name','isRequired'],
        methods: {
            updateValue: function (event) {
                this.$emit('update', event.target.value);
            },
            check(showMsgOnSuccess = false) {
                
                if (!this.value) 
                    return true;

                try {
                    eval(this.value.replace('$dependencies', 'true'));
                } catch(exc) {
                    $.toast({
                        heading: 'Error',
                        text: exc.message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    return false;
                }

                if(showMsgOnSuccess)
                    $.toast({
                        heading: 'Success',
                        text: 'No error found',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'success'
                    });

                return true;
            },
            beforeSave() {
                return this.check();
            }
        },
    });
</script>
