<template id="tpl-field-style-component">
    <div class="mb-3 col-md-6" id="style-select">

        <label class="form-label" for="style-cbx">
            Style
            <span v-if="isRequired" class="ml-1 text-danger">&nbsp*</span>
        </label>

        <select class="form-select" 
            :class="value" 
            v-model="value" 
            v-on:input="updateValue"
            ref="style-cbx" 
            name="style-cbx" 
            id="style-cbx">
            <option v-for="(item, index) in items" :class="item.id" :key="index" :value="item.id">
                {{ item.name}}
            </option>
        </select>

    </div>

</template>

<script>
    Vue.component('field-style-component', {
        template: '#tpl-field-style-component',
        props: ['title', 'value', 'name', 'isRequired'],
        data() {
            return {
                items: [{
                        id: 'red-01',
                        name: 'Red 01'
                    },
                    {
                        id: 'red-02',
                        name: 'Red 02'
                    },
                    {
                        id: 'green-01',
                        name: 'Green 01'
                    },
                    {
                        id: 'green-02',
                        name: 'Green 02'
                    },
                ],
            };
        },
        methods: {
            updateValue: function (event) {
                this.$emit('update', event.target.value);
            },
        },
    });
</script>