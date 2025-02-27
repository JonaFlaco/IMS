$('#run_business_summary').click(function () {
            var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.edf_eoi_full_application_id_main);
            var formData = new FormData();
            formData.append('id', id.toString());
            axios({
                 method: 'post',
                 url: '/Actions/BusinessSummary/?response_format=json',
                 headers: {
                     responseType: 'blob',
                     'Content-Type': 'form-data',
                 },
                 data: formData,
            })
            .then(function(response){
                       
                if (response.data.status == 'success') {
                $.toast({
                    heading: 'Success',
                    text: response.data.message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'success',
                });
                topBarVm.show_bg_tasks_modal();

            } else {
                $.toast({
                    heading: 'Error',
                    text: 'Something went wrong',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error',
					hideAfter: 100000
                });
            }
 
            })
        })