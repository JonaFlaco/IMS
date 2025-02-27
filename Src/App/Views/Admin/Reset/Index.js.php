<script>
    var vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            pageTitle: '<?= $data['title'] ?>',
            breadCrumb: [
                {title: 'Admin', link: '/admin'},
            ],
            hasPendingTask: false,
            data: [],

            menuList: [
                {id: 'introduction', name: 'Introduction', icon: 'dripicons-home', statusId: 0},
                {id: 'options', name: 'Options', icon: 'dripicons-gear', statusId: 0, name: 'options', selected: true,
                    records: [
                        {
                            id: 'update_all_user_references_to_system_user',
                            name: 'Update all user references to system user',
                            selected: true,
                        },
                        {
                            id: 'insert_initial_log_for_each_record_after_reset_log',
                            name: 'Insert initial log for each record after reset log',
                            selected: true,
                        },
                    ]
                },
                {id: 'ctypes', name: 'Content-Types', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'views', name: 'Views', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'crons', name: 'Crons', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'status_workflow_templates', name: 'Status Workflow Templates', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'status_list', name: 'Status List', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'form_types', name: 'Form Types', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'roles', name: 'Roles', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'governorates', name: 'Governorates', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'units', name: 'Units', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'menu', name: 'Menu', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'settings', name: 'Settings', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'dashboards', name: 'Dashboards', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'widgets', name: 'Widgets', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'custom_url', name: 'Custom URL', icon: 'mdi mdi-table', statusId: 0, records: [], tableStructure: [{id: 'id', name: 'Id'}, {id: 'old_url', name: 'Old URL'}, {id: 'new_url', name: 'New URL'}, ]},
                {id: 'documents', name: 'Documents', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'surveys', name: 'Surveys', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'modules', name: 'Modules', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'db_connection_strings', name: 'Db Connection Strings', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'documentations', name: 'Documentations', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'positions', name: 'Positions', icon: 'mdi mdi-table', statusId: 0, records: []},
                {id: 'user_groups', name: 'User Groups', icon: 'dripicons-user', statusId: 0, records: []},
                {id: 'misc', name: 'Miscellaneous', icon: 'mdi mdi-dots-horizontal-circle', statusId: 0, records: [], tableStructure: [{id: 'id', name: 'Id'}, {id: 'name', name: 'Name'}, {id: 'value', name: 'Value'}, ]},
                {id: 'users', name: 'Users', icon: 'dripicons-user', statusId: 0, records: []},
                {id: 'stuck_tables', name: 'Stuck Tables', icon: 'mdi mdi-table-remove', statusId: 0, records: []},
                {id: 'ext_dir', name: 'Reset Ext Directory', icon: 'dripicons-folder-open', statusId: 0, records: []},
            ],
            selectedMenu: null,

        },
        async mounted() {

            this.selectedMenu = this.menuList[0];
            //await this.loadAll();

        },
        methods: {
            async loadAll() {

                for(var index = 2; index <= 28; index++) {
                    await this.load(this.menuList[index]);
                }
            },
            async load(item) {
                
                let self = this;
                
                item.statusId = 1;
                item.errorMessage = null;
                item.loadingMessage = 'Loading, please wait...';

                var response = await axios.get('/InternalApi/SystemReset/?cmd=get_' + item.id + '&response_format=json',   
                    ).catch(function(error){
                        message = error;
                        
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                        return error;
                    });

                if(response.status == 200) {
                    response.data.result.forEach((item) => {
                        item.selected = false;
                    });

                    item.records = response.data.result;
                    item.statusId = 2;
                    return response;
                } else {
                    item.statusId = 3;
                    
                    item.errorMessage = "Something went wrong, while loading data";
                    
                    if(response && response.data && response.data.message) {
                        item.errorMessage = response.data.message;
                    } else if (response && response.response && response.response.data && response.response.data.message) {
                        item.errorMessage = response.response.data.message;
                    }
                }

                return response;
            },
            async run(item) {
                
                this.hasPendingTask = true;
                let self = this;

                var count = item.records.filter((x) => x.selected).length;

                if(!confirm('Are you sure want execute ' + count + ' items in ' + item.name + '?'))
                    return;

                item.loadingMessage = 'Executing ' + count + ' items, please wait...';

                item.statusId = 1;

                formData = new FormData();
                formData.append('ids', item.records.filter(x=> x.selected).map(x=>x.id));                
                formData.append('options', this.menuList.find((x) => x.name == "options").records.filter((x) => x.selected).map(x=>x.name));

                var response = await axios.post('/InternalApi/SystemReset/?cmd=reset_' + item.id + '&response_format=json',
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'Csrf-Token': '<?= \App\Core\Application::getInstance()->csrfProtection->create("system_reset") ?>',
                            }
                        }
                    ).catch(function(error){
                        message = error;
                        item.statusId = 3;
                        if(error.response != undefined && error.response.data.status == "failed") {
                            message = error.response.data.message;
                        }

                        $.toast({
                            heading: 'Error',
                            text: message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                        
                        return error;
                    });

                this.hasPendingTask = false;

                if(response.status == 200) {
                    item.statusId = 2;
                    this.load(item);
                } else {
                    item.statusId = 3;
                }

                return response;

            },
            openMenu(item) {
                if(this.hasPendingTask) {
                    alert('You can not open ' + item.name + ', because you have pending task in ' + this.selectedMenu.name);
                } else {
                    this.selectedMenu = item
                }

            }
        },
    })
</script>