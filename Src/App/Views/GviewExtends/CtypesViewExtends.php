<script>
    var mix = {
        data: {

        },
        mounted() {
            this.$refs.run_export_odk_tpl.addEventListener('click', this.exportOdkTpl);
            this.$refs.run_export_qt_tpl.addEventListener('click', this.exportQtTpl);
        },
        methods: {
            async exportOdkTpl() {
                let self = this;
                
                if(this.records.filter((e) => e.is_selected).length != 1) {
                    alert('select only one record');
                    return;
                }

                let ctypeId = this.records.filter((e) => e.is_selected)[0].ctypes_id;

                var formData = new FormData();                
                formData.append('ctype_id', ctypeId);
                
                axios({
                    method: 'post',
                    url: '/Actions/CtypeExportOdkTemplate?response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){
                    topBarVm.show_bg_tasks_modal();

                    if(response.data.status == 'success'){
                        $.toast({
                            heading: 'Success',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });
                        
                    } else {
                        $.toast({
                            heading: 'error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }
                    
                })
                .catch(function(error){

                    if (error.response != undefined && error.response.data.status == "failed") {						
							$.toast({heading: 'Error',text: error.response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});
					} else {						
							$.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
					}			
                });    
            },
            async exportQtTpl() {
                let self = this;
                self.loading = true;

                if(this.records.filter((e) => e.is_selected).length != 1) {
                    alert('select only one record');
                    return;
                }

                let ctypeId = this.records.filter((e) => e.is_selected)[0].ctypes_id;

                var formData = new FormData();                
                formData.append('ctype_id', ctypeId);
                formData.append('filter_hidden_fields', 1);

                axios({
                    method: 'post',
                    url: '/Actions/CtypeExportQuestionnaireTemplate?response_format=json',
                    data:formData,
                    headers: {
                        'Content-Type': 'form-data',
                    }
                })
                .then(function(response){
                    topBarVm.show_bg_tasks_modal();
                    
                    if(response.data.status == 'success'){
                        $.toast({
                            heading: 'Success',
                            text: response.data.message,
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'success'
                        });
                        
                    } else {
                        $.toast({
                            heading: 'error',
                            text: 'Something went wrong',
                            showHideTransition: 'slide',
                            position: 'top-right',
                            icon: 'error'
                        });
                    }
                    
                })
                .catch(function(error){
                    
                    if (error.response != undefined && error.response.data.status == "failed") {						
							$.toast({heading: 'Error',text: error.response.data.message,showHideTransition: 'slide',position: 'top-right',icon: 'error'});
					} else {						
							$.toast({heading: 'Error',text: 'Something went wrong',showHideTransition: 'slide',position: 'top-right',icon: 'error'});
					}			
                });                

            },
        }
    };
</script>