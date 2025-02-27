
var num = 0 ;
var id = [];
$('#run_duplication_check').click(function () {	
	if(vm.records.filter(itm => itm.is_selected == true).length >= 1){		
		id = vm.records.filter(itm => itm.is_selected == true).map(itm => ({ id : itm.beneficiaries_id_main, code : itm.beneficiaries_code}));
		id_counter = id.length;
		var table =  "<table class='table' id='table_data'><tr></tr><tbody>";
		for (let i=0 ; i<id.length ;  i++){
            table += "<tr><td class='"+id[i].id+"id'><a href='https://ecuadorims.iom.int/beneficiaries/show/"+id[i].id+"' target='_blank'>"+id[i].code+"</a></td>" + 
            "<td class='"+id[i].id+"'><div id='"+id[i].id+"div'><span class='badge bg-secondary p-2'><i class='mdi mdi-timer'></i></span></div></td></tr>";
		}
		
		table += "<tbody></table>" ;
		
		$('#span_counter').text("0 /" +id_counter )
		document.getElementById('tbl_dublication').innerHTML = table;
		
		var modal = new bootstrap.Modal(document.getElementById('modal'), {
            backdrop: 'static',
            keyboard: false,
        })
		modal.show();				
	} else {
		$.toast({
			heading: 'Error',
			text: 'Please select one beneficiary at least',
			showHideTransition: 'slide',
			position: 'top-right',
			icon: 'error'
		});
		
	}

});

$('#check-btn').click(function () {
	$('#span_counter').text("0 /" +id_counter )
	$(this).prop('disabled', true);
	$("#btn_close").prop('disabled', true);
	$("#sbtn_close").prop('disabled', true);
	$('#btn_spiner').append("<span aria-hidden='true' class='spinner-border spinner-border-sm'>");

	var last_id = id[id.length - 1];

	for(let i = 0 ; i<id.length ; i ++)
	{
		send_data(id[i].id , i ,id_counter , last_id)   

	}

});

function send_data(id , counter , id_counter , last_id) {
	$('.'+ id ).empty();
	var spiner = "<div id ="+id + "spiner><span class='badge bg-info p-2'><span aria-hidden='true' class='spinner-border spinner-border-sm'></span></span>" ;
	$('.'+ id).append(spiner);
	
	
	var formData = new FormData();
	formData.append('id', id.toString());
	axios({
		method: 'post',
		url: '/InternalApi/BeneficiariesDuplicationCheck/?response_format=json',
		headers: {
		responseType: 'blob',
		'Content-Type': 'form-data',
	},
	data: formData,
	}).then(function(response){
			
		if(response.data.status == "success"){
			
			$('#'+ id +'spiner').remove();
         if(response.data.duplication && response.data.duplication.length > 0) {
            // If there are duplications, append warning messages
            var html = "<div class='row'><div class='col-auto'><span class='badge bg-warning p-2'><i class='dripicons-warning'></i></span></div><div class='col'>";
            response.data.duplication.forEach(function(duplication) {
               html += "<div>" + duplication.error + "</div>";
            });
            html += "</div></div>";
            $('.'+ id).append(html);
         } else{
			   $('.'+ id).append("<div class='row'><div class='col-auto'><span class='badge bg-success p-2'><i class='mdi mdi-account-check'></i></span></div><div class='col'><div>No se encontró duplicados</div></div></div>");
         }
         is_done(id_counter);
		} 
      else if (response.data.status == "failed") {
			let message = response.data.message ;
			$('#'+ id +'spiner').remove();
			$('.'+ id).append("<div><span class='badge bg-danger p-2'><i class='dripicons-warning'></i></span>"+ message +" </div>");
			is_done(id_counter);
		}
		else {
			
			$('#'+ id +'spiner').remove();
			$('.'+ id).append("<div><span class='badge bg-danger p-2'><i class='dripicons-warning'></i></span> Failed An Error happened </div>");
			is_done(id_counter);
			
		}

		
	}).catch(function(error){
        		
        if (error.response != undefined && error.response.data.status == "failed") {
            let message = error.response.data.message ;
			$('#'+ id +'spiner').remove();
			$('.'+ id).append("<div><span class='badge bg-danger p-2'><i class='dripicons-warning'></i></span>"+ message +" </div>");
        } else {
        
            $.toast({
                heading: 'Error',
                text: 'Something went wrong',
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'error'
            });
        }
        is_done(id_counter);

    });


} // end of send_data function 


function is_done(counter)
{
	num++;
	$('#span_counter').text( num  +' / ' +id_counter )
		if(num == counter)
		{
			$('#btn_spiner').remove();
			$("#check-btn").attr("disabled", false);
			$("#btn_close").attr("disabled", false);
			$("#sbtn_close").attr("disabled", false);
			$('#modal').modal({backdrop: 'static', keyboard: false})  
			num = 0 ;
		}
}

$('#run_bnf_services_profile').click(function () {
    if (vm.records.filter(itm => itm.is_selected == true).length >= 1) {
        var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.beneficiaries_id_main);
        var formData = new FormData();
        formData.append('id', id.toString());

        var modal_profile = new bootstrap.Modal(document.getElementById('modal_profile'), {
            backdrop: 'static',
            keyboard: false,
        })
		modal_profile.show()

        
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
            var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.beneficiaries_id_main);
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
                modal_profile = bootstrap.Modal.getInstance(document.getElementById('modal_profile'))
                modal_profile.hide();
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
            var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.beneficiaries_id_main);
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
                modal_profile = bootstrap.Modal.getInstance(document.getElementById('modal_profile'))
                modal_profile.hide();
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



$('#run_case_worker_assignment').click(function () {
    if (vm.records.filter(itm => itm.is_selected == true).length >= 1) {
        var id = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.beneficiaries_id_main);
        var formData = new FormData();
        formData.append('id', id.toString());
        console.log(id);
        var modal_cw_assignment = new bootstrap.Modal(document.getElementById('modal_cw_assignment'))
		modal_cw_assignment.show()

        
    } else {
        $.toast({
            heading: 'Error',
            text: 'Seleccione al menos un caso',
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error',
            hideAfter: 10000
        });
    } 
});

$('#modal_cw_assignment').on('show.bs.modal', function () {
    var bnfId = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.beneficiaries_id_main);
    var firstId = bnfId.length > 0 ? bnfId[0] : null;
    var selectedUserId = $('#case_worker_select').val();

    // Realizar una solicitud AJAX para obtener los datos de los usuarios
    $.ajax({
        url: '/InternalApi/CaseWorkerAssignment/',
        type: 'GET',
        data: {
            id: firstId,
        },
        success: function (data) {
            // Llenar el dropdown con los datos de los usuarios
            var select = $('#case_worker_select');
            select.empty();
            select.append($('<option value="">Seleccione un case worker</option>')); // Agregar una opción vacía como primera opción
            $.each(data, function (index, user) {
                select.append($('<option></option>').attr('value', user.id).text(user.full_name));
            });
        },

    });
    // Realizar una solicitud AJAX para obtener el código del caso
    $.ajax({
        url: '/InternalApi/CaseWorkerAssignmentCodeTitle',
        type: 'GET',
        data: {
            id: bnfId.toString(),
            selectedUserId: selectedUserId
        },
        success: function (response) {
            // Limpiar el cuerpo de la tabla
            $('#code_table_body').empty();
            
            // Iterar sobre los datos recibidos y agregar filas a la tabla
            response.codes.forEach((code, index) => {                
                var row = `<tr>
                              <td class="rounded p-1 bg-secondary text-white fw-bold">${code}</td>
                           </tr>`;
                
                $('#code_table_body').append(row);
            });
            $('#case_code').text(response.codes);
        },

    });
});

$('#cw_already_assigned').on('show.bs.modal', function () {
    var bnfIds = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.beneficiaries_id_main);
    var selectedUserId = $('#case_worker_select').val();
    $.ajax({
        url: '/InternalApi/CaseWorkerAssignmentCodeTitle',
        type: 'GET',
        data: {
            id: bnfIds.toString(),
            selectedUserId: selectedUserId
        },
        success: function (response) {
            // Limpiar el cuerpo de la tabla
            $('#case_worker_table_body').empty();
            
            // Iterar sobre los datos recibidos y agregar filas a la tabla
            response.assignedcodes.forEach((code, index) => {
                var prevCw = response.assignedcaseworkers[index];
                
                var row = `<tr>
                              <td class="bg-secondary text-white fw-bold">${code}</td>
                              <td class="bg-success text-white fw-bold">${prevCw}</td>
                           </tr>`;
                
                $('#case_worker_table_body').append(row);
            });

            // Actualizar los textos en la alerta
            $('#new_cw').text(response.new_cw);
        },
        error: function () {
            alert('Hubo un error al obtener los datos.');
        }
    });
});

$('#btn_assignament').on('click', function () {
    $("#btn_assignament").prop('disabled', true);
    var bnfId = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.beneficiaries_id_main);
    var selectedUserId = $('#case_worker_select').val();
    if (!selectedUserId) {
        $.toast({
            heading: 'Error',
            text: 'Seleccione un case worker válido',
            showHideTransition: 'slide',
            position: 'top-right',
            icon: 'error',
            hideAfter: 10000
        });
        $("#btn_assignament").prop('disabled', false);
        return; 
    }
    $.ajax({
        url: '/InternalApi/CaseWorkerAssignmentSave/',
        type: 'POST',
        data: {
            id: bnfId.toString(),
            selectedUserId: selectedUserId
        },
        success: function(response) {
            if (response.status == 'missed') {
                modal_cw_assignment = bootstrap.Modal.getInstance(document.getElementById('modal_cw_assignment'))
                modal_cw_assignment.hide();
                var cw_already_assigned = new bootstrap.Modal(document.getElementById('cw_already_assigned'))
                cw_already_assigned.show();
                $("#btn_assignament").prop('disabled', false);
            } else {
                $.toast({
                    heading: 'Case worker asignado exitosamente',
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'success',
                    afterHidden: function () {
                        $("#btn_assignament").prop('disabled', false);
                    }
                });
                modal_cw_assignment = bootstrap.Modal.getInstance(document.getElementById('modal_cw_assignment'))
                modal_cw_assignment.hide();
            }
            if (response.status != 'missed') {
            // Refresh after update case worker
            vm.beneficiaries_status_id = [{
                id: "95",
                name: "Pendiente"
            }];

            Vue.nextTick(function () {
                window.location.reload(); 
            });
        }
        },
        error: function(xhr, status, error) {
            console.error(error);
            $("#btn_assignament").prop('disabled', false);
        }
    });
});


$('#btn_re_assignament').on('click', function () {
    $("#btn_re_assignament").prop('disabled', true); 
    var bnfId = vm.records.filter(itm => itm.is_selected == true).map(itm => itm.beneficiaries_id_main);
    var selectedUserId = $('#case_worker_select').val();

    $.ajax({
        url: '/InternalApi/CaseWorkerAssignmentUpdate/',
        type: 'POST',
        data: {
            id: bnfId.toString(),
            selectedUserId: selectedUserId
        },
        success: function(response) {
            $.toast({
                heading: 'Case worker actualizado exitosamente',
                showHideTransition: 'slide',
                position: 'top-right',
                icon: 'success',
                afterHidden: function () {
                    $("#btn_re_assignament").prop('disabled', false); 
                }
            });
            var cw_already_assigned = bootstrap.Modal.getInstance(document.getElementById('cw_already_assigned'));
            cw_already_assigned.hide();
                        
            // Refrescar la página después de la actualización
            Vue.nextTick(function () {
                window.location.reload(); 
            });
        },
        error: function(xhr, status, error) {
            console.error(error);
            $("#btn_re_assignament").prop('disabled', false); 
        }
    });
});


$('#run_service_summary').click(function() {
    var modal_summary = new bootstrap.Modal(document.getElementById('modal_summary'), {
        backdrop: 'static',
        keyboard: false,
    });
    modal_summary.show();
});

$('#generate-summary').click(function () {
    var id = vm.records.filter(itm => itm).map(itm => itm.beneficiaries_id_main);
    var formData = new FormData();
    formData.append('id', id.toString());
    axios({
        method: 'post',
        url: '/Actions/ServiceSummary/?response_format=json',
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
            var modal_summary = bootstrap.Modal.getInstance(document.getElementById('modal_summary'));
            modal_summary.hide();
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