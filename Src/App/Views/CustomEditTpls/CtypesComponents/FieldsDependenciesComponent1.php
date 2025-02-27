
<template id="tpl-c1-component">

<div class="p-0 ps-3">
    {{ value.type }} 
    
    <div v-if="value.children.length == 0" style="display: inline !important;">
        <input type="text" v-model="value.source">
        <input type="text" v-model="value.field">
        <input type="text" v-model="value.value">
        <select  v-model="value.operator" class="form-select">
            <option v-for="opt in operatorTypes" :value="opt.name"> {{ opt.title }} </option>
        </select>
    </div>
    <button class="btn btn-link btn-rounded" @click="add('AND')">Add (AND)</button>
    <button class="btn btn-link btn-rounded" @click="add('OR')">Add (OR)</button>

    <c1-component
        class="mt-1 mb-1"
        v-for="item in value.children"
        :value="item"
        ></c1-component>
    
</div>


</template>

<script>
Vue.component('c1-component', {
template: '#tpl-c1-component',
props: {
    value: {},
},
data() {
    return {
        operatorTypes: [
            {name: '=', title: 'Equals'},
            {name: '!=', title: 'Not equals'},
            {name: '<', title: 'Less than'},
            {name: '>', title: 'Greater than'},
            {name: '=<', title: 'Less than or equals'},
            {name: '>+', title: 'Greater than or equals'},
        ],
    }
}, 
methods: {
    add(type) {
        this.value.children.push(
            {
                type: type,
                source: '',
                field: '',
                value: '',
                operator: '',
                children: [
                    
                ],
            },
        );
    }
},
});
</script>

<template id="tpl-fields-dependencies-component">
<div class="col-md-12 p-0">
        
    <div class="col-md-12 pb-1 mb-3"
        id="div_description">
        <label class="form-label" for="description">{{ title }}</label>
        <textarea rows="5" class="form-control" 
            ref="description" 
            name="description"
            id="description"
            v-bind:value="value"
            v-on:input="updateValue"
            :required="true"
            ></textarea>
    </div>
    
    <!-- <button class="btn btn-secondary" @click="checkDep(value)">Check</button> -->
    <div class="alert alert-info">
        <button class="btn btn-link btn-rounded me-2" @click="getResult">Get</button>{{ result }}
    </div>

    <c1-component
        class="mt-1 mb-1"
        v-for="item in list"
        :value="item"
        ></c1-component>

    <button class="btn btn-link btn-rounded" @click="add('AND')">Add (AND)</button>
    <button class="btn btn-link btn-rounded" @click="add('OR')">Add (OR)</button>
</div>
</template>

<script>
Vue.component('fields-dependencies-component', {
    template: '#tpl-fields-dependencies-component',
    props: ['title','value'],
    data() {
        return {

            list: [
                {
                    type: 'OR',
                    source: '',
                    field: '',
                    value: '',
                    operator: '',
                    children: [
                        {
                            type: 'AND',
                            source: 'self',
                            field: 'name',
                            value: 'blnd',
                            operator: '=',
                            children: [
                                
                            ],
                        },
                        {
                            type: 'AND',
                            source: 'self',
                            field: 'no',
                            value: '50',
                            operator: '<',
                            children: [
                                
                            ],
                        },
                        {
                            type: 'OR',
                            source: 'items',
                            field: 'salary',
                            value: '1000',
                            operator: '!=',
                            children: [
                                
                            ],
                        },
                    ],
                },
                {
                    type: 'AND',
                    source: 'self',
                    field: 'code',
                    value: '',
                    operator: '!=',
                    children: [
                        
                    ],
                },
            ],

        }
    },
    methods: {
        updateValue: function (event) {
            this.$emit('input', event.target.value);
        },
        checkDep(value) {
            alert(value);
        },
        add(type) {
            this.list.push(
                {
                    type: type,
                    source: '',
                    field: '',
                    value: '',
                    operator: '',
                    children: [
                        
                    ],
                },
            );
        },
        getResult() {
            
            let result = "";

            let i = 1;
            this.list.forEach((item) => {
                result += this.processTree(item, i++ == this.list.length);
                
            });

            this.$emit('input', result);
        },
        processTree(item, isLastItem = false) {
            
            let value = ''; 
            
            value += item.children.length > 0 ? '' : "selected('" + item.source + "','" + item.field + "','" + item.value + "','" + item.operator + "') ";

            children = '';
            let i = 1;
            item.children.forEach((obj) => {
                children += this.processTree(obj, i++ == item.children.length);
            })

            if(children.length > 0 ) {
                value += '( ' + children + ') ';
            }

            if(!isLastItem)
                value += item.type + ' ';

            return value;
        },
    },
});
</script>
