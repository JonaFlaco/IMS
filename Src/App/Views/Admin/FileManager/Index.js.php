<script>
    var vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            pageTitle: '<?= $data['title'] ?>',
            breadCrumb: [{
                title: 'Admin Panel',
                link: '/admin'
            }],
            rootLevelList: [],
            loadingRootLevelList: false,
            selectedItem: null,
            filterType: 0,
            orderBy: 0,
        },
        async mounted() {
            await this.loadRootLevel();
        },
        methods: {
            async loadRootLevel() {

                let self = this;
                self.loadingRootLevelList = true;
                self.rootLevelList = [];
                
                var response = await axios.get('/InternalApi/FileManagerLoadDirectories/?response_format=json').catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    $.toast({
                        heading: 'Error',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    self.loadingRootLevelList = false;
                });

                if (response.status == 200) {
                    self.rootLevelList = response.data.result;
                }

                self.loadingRootLevelList = false;

            },
            
            async loadStats(item) {

                let self = this;
                item.stats.loading = true;

                var response = await axios.get('/InternalApi/FileManagerLoadDetail/' + item.name + '?response_format=json').catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    $.toast({
                        heading: 'Error',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    item.stats.loading = false;
                });

                if (response.status == 200) {
                    item.stats = response.data.result;
                }

                item.stats.loading = false;

            },
            async cleanup(item) {

                if(confirm("Are you sure you want to cleanup " + item.name + "?") != true)
                    return;
                let self = this;
                item.stats.loading = true;

                formData = new FormData();
                formData.append('directory', item.name);
                
                var response = await axios.post(
                    '/InternalApi/FileManagerCleanup/?response_format=json',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    }
                ).catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    $.toast({
                        heading: 'Error',
                        text: message,
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });

                    item.stats.loading = false;
                });

                if (response.status == 200) {
                    if(item.isDir && response.data.status == "success")
                        item.stats = response.data.result;
                    else
                        self.rootLevelList = self.rootLevelList.filter((x) => x != item);
                }
                
                item.stats.loading = false;

            },
        }
    })
</script>