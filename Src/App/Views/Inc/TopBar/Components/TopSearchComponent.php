<?php use App\Core\Application; ?>

<template id="tpl-top-search-component">

    <div>
        
        <div class="app-search dropdown d-none d-lg-block">
            <form>
                <div class="input-group">
                    <input type="text" class="form-control dropdown-toggle" placeholder="Search..." id="top-search">
                    <span class="mdi mdi-magnify search-icon"></span>
                    <button class="input-group-text btn-primary" type="submit">Search</button>
                </div>

            </form>

            <!-- <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                <div class="dropdown-header noti-title">
                    <h5 class="text-overflow mb-2">Coming Soon</h5>
                </div>
            <div> -->
        </div>
    </div>
</template>


<script type="text/javascript">

    var component = {
        template: '#tpl-top-search-component',
    }

    Vue.component('top-search-component', component)

</script>
