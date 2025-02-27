<template id="tpl-options-component">
    <div>

        <div class="col-sm-4">                            
            <h4 class="mb-3 header-title">
                {{ item.name }}
            </h4>
        </div>

        <div class="col-sm-8">
            <div class="text-sm-end">
                
            </div>
        </div>

        <div class="col-md-12 table-responsive-sm">
            <table class="table table-hover table-centered mb-0">
                <thead>
                    <tr>
                        <th style="width: 20px;">
                            <div class="form-check"> 
                                <input type="checkbox" @change="toggleSelection" v-model="item.selected" class="form-check-input" id="customChecktoggle"> 
                                <label class="form-check-label" for="customChecktoggle"></label>
                            </div>
                        </th>
                        <th> Name </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="obj in item.records" :key="obj.id">
                        <td>
                            <div class="form-check">
                                <input type="checkbox" v-model="obj.selected" class="form-check-input" :id="'customCheck' + obj.id">
                                <label class="form-check-label" :for="'customCheck' + obj.id">&nbsp;</label>
                            </div>
                        </td>
                        <td>{{ obj.name }}</td>
                    </tr>
                </tbody>
            </table>
        </div> <!-- end table-responsive-->

    
    </div>
</template>

<script>
    Vue.component('options-component', {
        template: '#tpl-options-component',
        props: {
            item: {
                required: true,
            },
        },
        data() {
            return {
                
            }
        },
        mounted() {
            
        },
        methods: {
            toggleSelection() {
                this.item.records.forEach((x) => {
                    x.selected = this.item.selected;
                });
            },
        }
        
    })
</script>