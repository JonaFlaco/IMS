<?php use App\Core\Application; ?>

<template id="tpl-demo-alert-component">

    <div>
        <div v-if="data && data.isActive" class="alert alert-warning mt-2" role="alert">
            <strong>DEMO PLATFORM - </strong> You are using demo platform
            <span v-if="data.isAdmin">
                , connected to <i class='mdi mdi-database'></i> <strong> {{ data.dbName }} </strong> db on <i class='mdi mdi-server'></i> <strong> {{ data.hostName }} </strong> server. git branch <i class='mdi mdi-git'></i> <strong> {{ data.gitBranch }} </strong>
            </span>            
        </div>
    </div>

</template>


<script type="text/javascript">

    var component = {
        template: '#tpl-demo-alert-component',
        props: ['data'],
    }

    Vue.component('demo-alert-component', component)

</script>
