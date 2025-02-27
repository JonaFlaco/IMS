<template id="tpl-intro-component">
    <div>
        <!-- Top Bar -->
        <div class="row">
            <div class="col-sm-4">                            
                <h4 class="mb-3 header-title">
                    {{ title }}
                </h4>
            </div>

        </div>
        <!-- End of Top Bar -->

        <p>
            This page is critical as it allows you to change some really dangerious settings, please close this tab if you opened here by mistake.
        </p>
    </div>
</template>

<script type="text/javascript">

    var introComponent = {
        template: '#tpl-intro-component',
        data() {
            return {
                title: 'Introduction',
            }
        }
    }

    Vue.component('intro-component', introComponent);
</script>