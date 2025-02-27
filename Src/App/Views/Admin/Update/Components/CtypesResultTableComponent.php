<template id="tpl-ctypes-result-table-component">

    <div class="col-sm-12 table-responsive">
        <table class="table table-centered table-hover mb-0">
            <tbody>
                <tr 
                    v-for="item in items.filter((e) => !ctypeNameSearch || ctypeNameSearch.length == 0 || e.title.toLowerCase().includes(ctypeNameSearch.toLowerCase().trim()))" 
                    class="cursor-pointer" 
                    @click="openDetail(item.name)">
                    <td>
                        <img class="mr-2 rounded-circle" :src="item.icon" width="40" alt="Generic placeholder image">
                        
                        <p class="m-0 d-inline-block align-middle font-16">
                            <span class="mt-0 mb-1">{{item.title}}<small class="font-weight-normal ms-3">{{item.last_update_date}}</small></span>
                            <br />
                            <span v-if="item.installed" class="font-13 badge bg-success">Installed</span>
                            <span v-if="!item.installed" class="font-13 badge bg-danger">Not Installed</span>
                        </p>
                    </td>
                    <td>
                        <span class="text-muted font-13">Module</span> <br/>
                        <p class="mb-0">{{item.module ?? 'N/A'}}</p>
                    </td>
                    <td>
                        <span class="text-muted font-13">Exported Records</span> <br/>
                        <p class="mb-0">{{item.exportedRecordsCount ?? 0}}</p>
                    </td>
                    <td>
                        <span class="text-muted font-13">Category</span> <br/>
                        <p class="mb-0">{{item.category}}</p>
                    </td>
                    <td>
                        <span class="text-muted font-13">Last Update By</span> <br/>
                        <p class="mb-0">{{item.last_updated_user_name ?? item.created_user_name}}</p>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

</template>


<script type="text/javascript">

    var CtypesResultTableComponent = {

        template: '#tpl-ctypes-result-table-component',
        data() {
            return {
            }
        },
        props: ['items', 'ctypeNameSearch'],
        methods: {
            openDetail(name) {
                this.$emit('open-detail', name);
            },
        },
        computed: {
            
        },
    }

    Vue.component('ctypes-result-table-component', CtypesResultTableComponent)

</script>
