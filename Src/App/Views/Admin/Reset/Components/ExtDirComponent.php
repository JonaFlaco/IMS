<template id="tpl-ext-dir-component">
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


</div>
</template>

<script>
    Vue.component('ext-dir-component', {
        template: '#tpl-ext-dir-component',
        props: {
            item: {
                required: true,
            },
        },
    })
</script>