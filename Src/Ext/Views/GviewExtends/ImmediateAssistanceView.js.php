$('#run_immediate_assistance_export').click(function() {
    var modal_filters = new bootstrap.Modal(document.getElementById('modal_filters'), {
        backdrop: 'static',
        keyboard: false,
    });
    modal_filters.show();
});

$('#generate-file').click(function () {
    var id = vm.records.filter(itm => itm).map(itm => itm.immediate_assistance_id_main);
    var formData = new FormData();
    formData.append('id', id.toString());
    axios({
        method: 'post',
        url: '/Actions/ImmediateAssistanceExport/?response_format=json',
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