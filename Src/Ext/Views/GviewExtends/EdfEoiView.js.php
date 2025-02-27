$('#run_edf_profile').click(function () {
    if (vm.records.filter(itm => itm.is_selected == true).length >= 1) {
        var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.edf_eoi_id_main);
        var formData = new FormData();
        formData.append('id', id.toString());

        axios({
            method: 'post',
            url: '/Actions/EdfProfile/?response_format=json',
            headers: {
                responseType: 'blob',
                'Content-Type': 'form-data',
            },
            data: formData,
        })
        .then(function(response) {
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
        .catch(function(error) {
            if (error.response != undefined && error.response.data.status == "failed") {
                $.toast({
                    heading: 'Error',
                    text: error.response.data.message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error',
					hideAfter: 10000
                });
            } else {
                $.toast({
                    heading: 'Error',
                    text: 'Something went wrong',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error',
					hideAfter: 10000
                });
            }
        });
    } else {
        $.toast({
            heading: 'Error',
            text: 'Please select at least a record',
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error',
			hideAfter: 10000
        });
    }
});
