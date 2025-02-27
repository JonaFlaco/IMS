$('#run_curriculum').click(function () {
    if (vm.records.filter(itm => itm.is_selected == true).length >= 1) {
        var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.labour_matching_id_main);
        var formData = new FormData();
        formData.append('id', id.toString());

        var modal_cv = new bootstrap.Modal(document.getElementById('modal_cv'), {
            backdrop: 'static',
            keyboard: false,
        })
		modal_cv.show()

        
    } else {
        $.toast({
            heading: 'Error',
            text: 'Por favor seleccione al menos un registro',
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error',
			hideAfter: 10000
        });
    }
});

$('#one-file').click(function () {
            var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.labour_matching_id_main);
            var formData = new FormData();
            formData.append('id', id.toString());
            formData.append('download_mode', '1');
            axios({
                 method: 'post',
                 url: '/Actions/Curriculum/?response_format=json',
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
                modal_cv = bootstrap.Modal.getInstance(document.getElementById('modal_cv'))
                modal_cv.hide();
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
            var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.labour_matching_id_main);
            var formData = new FormData();
            formData.append('id', id.toString());
            formData.append('download_mode', '2');
            axios({
                 method: 'post',
                 url: '/Actions/Curriculum/?response_format=json',
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
                modal_cv = bootstrap.Modal.getInstance(document.getElementById('modal_cv'))
                modal_cv.hide();
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