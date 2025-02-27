<script type="text/x-template" id="tpl-page-title-row-component">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                
                <!-- Breadcrumb -->
                <div class="page-title-right mt-0">
                    <ol class="breadcrumb m-0">
                        
                        <li v-if="breadCrumb && breadCrumb.length > 0" class="breadcrumb-item">
                            <a href="/"> Home </a>
                        </li>
                        
                        <li v-for="item in breadCrumb" class="breadcrumb-item" :class="{active: item.active}">
                            <span v-if="item.active">{{ item.title }} </span>
                            <a v-else :href="item.link">{{ item.title }}</a>
                        </li>

                        <li class="breadcrumb-item active">
                            <span> {{ title }} </span>
                        </li>
                    </ol>
                </div> 
                <!-- End of Breadcrumb -->

                <!-- Page Title -->
                <h4 class="page-title">{{ title }}</h4>
            </div>
        </div>
    </div>
    
</script>

<script>

    Vue.component('page-title-row-component', {
        template: '#tpl-page-title-row-component',
        props: {
            title: {
                require: true
            },
            breadCrumb: {

            }
        }
    });

</script>