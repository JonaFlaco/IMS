<?php use App\Core\Application; ?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>

<style scoped>

.sourceInput {
    width: 50px;
}
.fieldInput {
    width: 80px;
}
.valueInput {
    width: 50px;
}
.operatorInput {
    width: 50px;
}

.inline {
    display: inline;
}

.entity:hover {
    background-color:darkgreen !important;
}

</style>

<template id="tpl-c1-component">

    <div class="p-1 ps-3 bg-info entity">
        <select  v-model="value.type" class="form-select">
            <option v-for="opt in andOrTypes" :value="opt"> {{ opt }} </option>
        </select>
        
        <span v-if="value.children && value.children.length > 0" class="text-white">(<br></span>

        <div class="p-1 m-1 inline" v-if="value.children.length == 0" >
            <input type="text" v-model="value.source" class="sourceInput">
            <input type="text" v-model="value.field" class="fieldInput">
            <select  v-model="value.operator" class="valueInput form-select">
                <option v-for="opt in operatorTypes" :value="opt.name"> {{ opt.title }} </option>
            </select>
            <input type="text" v-model="value.value" class="operatorInput" v-if="getOperatorData(value.operator)?.hasParam">
            
        </div>
        
        <button class="btn btn-link text-white" @click="add()"><i class="mdi mdi-plus"></i></button>

            <c1-component
                class="mt-1 mb-1 inline entity"
                v-for="item in value.children"
                :value="item"
                ></c1-component>
    
        <span v-if="value.children && value.children.length > 0" class="text-white"><br>)</span>
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
                    {name: '=', title: 'Equals', hasParam: true},
                    {name: '!=', title: 'Not equals', hasParam: true},
                    {name: '<', title: 'Less than', hasParam: true},
                    {name: '>', title: 'Greater than', hasParam: true},
                    {name: '=<', title: 'Less than or equals', hasParam: true},
                    {name: '>=', title: 'Greater than or equals', hasParam: true},
                    {name: 'null', title: 'Is empty', hasParam: false},
                    {name: 'not null', title: 'Is not empty', hasParam: false},
                ],
                andOrTypes: ['AND', 'OR'],
            }
        }, 
        methods: {
            add(type = 'AND') {
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
            },
            getOperatorData(name) {
                return this.operatorTypes.find((item) => item.name == name);
            },
        },
    });
</script>

<template id="tpl-main">
    
    <div>
        <page-title-row-component :title="pageTitle"></page-title-row-component>

        <div class="alert alert-info">
            <button class="btn btn-primary me-2" @click="getResult">Get</button>{{ result }}
        </div>

        <div class="row">
            <c1-component
                class="mt-1 mb-1"
                v-for="item in list"
                :value="item"
                ></c1-component>
        </div>

        <button class="btn btn-secondary" @click="add()">Add</button>
       
    </div>
</template>

<script>

    var vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            pageTitle: 'Home',
            result: 'Pending',
            
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
                    value: '1',
                    operator: '!=',
                    children: [
                        
                    ],
                },
                {
                    type: 'AND',
                    source: 'self',
                    field: 'salary',
                    value: '2',
                    operator: '!=',
                    children: [
                        
                    ],
                },
                {
                    type: 'OR',
                    source: 'items',
                    field: 'status_id',
                    value: '3',
                    operator: '!=',
                    children: [
                        
                    ],
                },
            ],

        },
        methods: {
            add(type = 'AND') {
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
                
                this.result = "";

                let i = 1;
                this.list.forEach((item) => {
                    this.result += this.processTree(item, i++ == this.list.length);
                    
                });
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
    })
</script>