
<template id="tpl-fields-conditional-formatting-component">
    <div class="bg-light p-1">

        <label for="condValue">{{ title }}</label>

        <form ref="conditionalFormattingForm">

            <div v-for="item in conditions" class="p-1 m-1">

                <div class="row">

                    <div class="form-group">
                        <label for="condOperator">Operator</label>
                        <select class="form-control form-control-sm" required v-model="item.operator" id="condOperator" placeholder="Select an operator">
                            <option v-for="operator in operators" :value="operator.id">{{operator.name}}</option>
                        </select>
                    </div>

                    <div class="form-group" :class="item.operator == 'between' ? 'col-md-6' : 'col-md-12'">
                        <label for="condValue">{{ item.operator == 'between' ? 'From' : 'Value' }}</label>
                        <input type="text" v-model="item.value" required class="form-control form-control-sm" id="condValue" placeholder="Enter Value">
                    </div>

                    <div class="form-group col-md-6" v-if="item.operator == 'between'">
                        <label for="condValue2">To</label>
                        <input type="text" v-model="item.value_2" required class="form-control form-control-sm" id="condValue2" placeholder="Enter Value 2">
                    </div>

                    <div class="form-group">
                        <label for="condStyle">Style</label>
                        <input type="text" v-model="item.style" required class="form-control form-control-sm" id="condStyle" placeholder="Style">
                    </div>
                </div>

                <button type="button" class="btn btn-danger btn-sm mt-1" @click="remove(item)">Remove condition</button>
            </div>

            <button type="button" class="btn btn-primary btn-sm mt-1 ms-1" @click="add">Add Condition</button>

        </form>
    </div>
</template>

<script>
    Vue.component('fields-conditional-formatting-component', {
        template: '#tpl-fields-conditional-formatting-component',
        props: ['title', 'value', 'name', 'isRequired'],
        data() {
            return {
                operators: [{
                        id: 'equal',
                        name: 'Equal'
                    },
                    {
                        id: 'less',
                        name: 'Less than'
                    },
                    {
                        id: 'less_or_equal',
                        name: 'Less than or equal'
                    },
                    {
                        id: 'between',
                        name: 'Between'
                    },
                    {
                        id: 'greater',
                        name: 'Greater than'
                    },
                    {
                        id: 'greater_or_equal',
                        name: 'Greater than or equal'
                    }
                ],
                conditions: [

                ],
                jsonValue: null,
            }
        },
        mounted() {
            this.conditions = JSON.parse(this.value) ?? [];
        },
        methods: {

            add() {

                if (!this.$refs.conditionalFormattingForm.checkValidity()) {
                    $.toast({
                        heading: 'Please fill the form with correct data',
                        text: 'Error in conditional formatting',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    return false;
                }

                this.conditions.push({
                    operator: '',
                    value: null,
                    value_2: null,
                    style: null
                });

            },
            remove(item) {
                this.conditions = this.conditions.filter((e) => e != item);
            },
            
            beforeSave() {
                this.$refs.conditionalFormattingForm.classList.add('was-validated');

                if (!this.$refs.conditionalFormattingForm.checkValidity()) {
                    $.toast({
                        heading: 'Please fill the form with correct data',
                        text: 'Error in conditional formatting',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    return false;
                }

                this.$emit('input', JSON.stringify(this.conditions));

            },
        },
    });
</script>
