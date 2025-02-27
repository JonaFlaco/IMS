<script>
    var vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            selectedCategory: 0,
            pageTitle: '<?= $data['title'] ?>',
            breadCrumb: [{
                title: 'Admin Panel',
                link: '/admin'
            }]
        },
        methods: {
            async load(group_name) { //Retrives all settings related to the passed group name

                let self = this;
                self.loading = true;

                var response = await axios.get('/InternalApi/SystemConfig/?cmd=get&group_name=' + group_name + '&response_format=json', ).catch(function(error) {
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

                    return false;
                });

                if (response.status == 200) {
                    return response;
                }

                return false;
            },
            async save(item) { //Saves all the passed settings

                let formData = new FormData();
                formData.append('data', JSON.stringify(item));

                var response = await axios({
                    method: 'POST',
                    url: '/InternalApi/SystemConfig/?cmd=save&response_format=json',
                    data: formData,
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("system_config") ?>',
                    }
                }).catch(function(error) {
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

                    return false;
                });

                if (response.status == 200) {

                    if (response.status == 200) {
                        $.toast({
                            heading: 'Success',
                            text: 'Settings saved successfuly',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });
                    }

                    return response;

                }

                return false;
            }
        }
    })
</script>