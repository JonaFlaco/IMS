$('#run_bnf_services_profile').click(function () {
    if (vm.records.filter(itm => itm.is_selected == true).length >= 1) {
        var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.b_services_id_main);
        var formData = new FormData();
        formData.append('id', id.toString());

        var modal = new bootstrap.Modal(document.getElementById('modal'), {
            backdrop: 'static',
            keyboard: false,
        })
		modal.show()

        
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

$('#one-file').click(function () {
            var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.b_services_id_main);
            var formData = new FormData();
            formData.append('id', id.toString());
            formData.append('download_mode', '1');
            axios({
                 method: 'post',
                 url: '/Actions/BnfServicesProfile/?response_format=json',
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
                modal = bootstrap.Modal.getInstance(document.getElementById('modal'))
                modal.hide();
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

$('#multiple-files').click(function () {
            var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.b_services_id_main);
            var formData = new FormData();
            formData.append('id', id.toString());
            formData.append('download_mode', '2');
            axios({
                 method: 'post',
                 url: '/Actions/BnfServicesProfile/?response_format=json',
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
                modal = bootstrap.Modal.getInstance(document.getElementById('modal'))
                modal.hide();
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


$('#run_me_export').click(function() {
    var modal_filters = new bootstrap.Modal(document.getElementById('modal_filters'), {
        backdrop: 'static',
        keyboard: false,
    });
    modal_filters.show();
});

$('#generate-file').click(function () {
    var id = vm.records.filter(itm => itm).map(itm => itm.b_services_id_main);
    var formData = new FormData();
    formData.append('id', id.toString());
    axios({
        method: 'post',
        url: '/Actions/MeExport/?response_format=json',
        headers: {
            'Content-Type': 'application/json',
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
            var modal_filters = bootstrap.Modal.getInstance(document.getElementById('modal_filters'));
            modal_filters.hide();
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
        $.toast({
            heading: 'Error',
            text: 'An error occurred: ' + error.message,
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error',
            hideAfter: 100000
        });
    });
});