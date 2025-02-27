$('#run_cbi_atm_export').click(function() {
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
        url: '/Actions/CbiAtmExport/?response_format=json',
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

$('#run_favorita_export').click(function() {
    var modal_filters_fav = new bootstrap.Modal(document.getElementById('modal_filters_fav'), {
        backdrop: 'static',
        keyboard: false,
    });
    modal_filters_fav.show();
});
$('#run_health_cbi_export').click(function() {
    var modal_filters_health = new bootstrap.Modal(document.getElementById('modal_filters_health'), {
        backdrop: 'static',
        keyboard: false,
    });
    modal_filters_health.show();
});

$('#generate-favorita').click(function () {
    var id = vm.records.filter(itm => itm).map(itm => itm.b_services_id_main);
    var formData = new FormData();
    formData.append('id', id.toString());
    axios({
        method: 'post',
        url: '/Actions/FavoritaExport/?response_format=json',
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
            var modal_filters_fav = bootstrap.Modal.getInstance(document.getElementById('modal_filters_fav'));
            modal_filters_fav.hide();
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
$('#generate-health').click(function () {
    var id = vm.records.filter(itm => itm).map(itm => itm.b_services_id_main);
    var formData = new FormData();
    formData.append('id', id.toString());
    axios({
        method: 'post',
        url: '/Actions/HealthCbiExport/?response_format=json',
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
            var modal_filters_health = bootstrap.Modal.getInstance(document.getElementById('modal_filters_health'));
            modal_filters_health.hide();
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

