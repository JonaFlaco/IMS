<?php

use App\Core\Application;

$data = (object)$data;
$nodeData = $data->nodeData;
function calculateAge($birthdate)
{
    $birthDate = new DateTime($birthdate);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
    return $age;
}
?>

<?= Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data) ?>


<template id="tpl-main">
    <div>

        <div class="row">
            <div class="col-lg-12 pt-3">
                <div class="card">
                    <div class="card-body bg-primary text-white">
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <h4 class="header-title"> {{ ctype.name }} </h4>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <div class="text-sm-end">
                                    <a v-if="nodeData.created_user_id_display" target="_blank" class="text-white" :href="'/users/show/' + nodeData.created_user_id">
                                        <i class="mdi mdi-account"></i>
                                        {{ nodeData.created_user_id_display }}
                                    </a>
                                    <span class="mx-1">&#183;</span>
                                    <i v-if="nodeData.created_date" class="mdi mdi-calendar"></i>
                                    {{ nodeData.created_date | formatDate }}
                                </div>
                            </div>
                            <div class="col-sm-6 mb-1">
                                Código de registro: {{ nodeData[ctype.display_field_name] }}
                            </div>

                            <?php if ($data->ctypeObj->use_generic_status) { ?>

                                <!-- Update Status Modal Modal -->
                                <update-status-component v-if="updateStatusItems.length > 0" ctype-id="<?= $data->ctypeObj->id ?>" :records="updateStatusItems" @clean-up="updateStatusItems = []" @after-update="afterUpdateStatus">
                                </update-status-component>

                                <div class="col-sm-6 mb-1">
                                    <div class="text-sm-end">
                                        <span class="p-1" :class="nodeData.status.style">
                                            Status:
                                            {{ nodeData.status.name }}
                                            <a href="javascript: void(0);" class="ms-2 hide_on_print" @click="updateStatus()">
                                                <i class="mdi mdi-format-list-bulleted"> </i> Cambiar status
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title text-primary mb-3">Información importante para gestores</h5>
                    <p class="card-text">Recuerda seguir los siguientes pasos antes de iniciar una evaluacion del caso</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">1. Revisa que la informacion de la solicitud sea correcta, puedes guiarte con la fotografía adjunta para verificar que la informacion ingresada coincida con el documento</li>
                        <li class="list-group-item">2. Puedes <a :href="'/beneficiaries/edit/' + this.nodeData.id" target="_blank" class="btn btn-outline-primary btn-sm">Editar</a> los datos del caso de ser necesario, para ello debe agregar una justificacion</li>
                        <li class="list-group-item">3. Si la informacion es correcta puedes cambiar el status del caso a verified para revisar duplicados automáticamente</li>
                        <li class="list-group-item">4. Antes de realizar una evaluacion asegurate que los datos del aplicante principal y sus familiares sean correctos, recuerda que la informacion en esta solicitud tambien afectará el puntaje de evaluación </li>
                        <li class="list-group-item">5. Aprueba el caso antes de iniciar una evaluación (El status no cambiará si existen duplicados, solo cambiará si presionas en "Lo entiendo y continúo")</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="card">
                    <span class="me-1"><strong>Foto frontal de la cedula:</strong></span>
                    <div class="rounded mx-auto d-block contenedor-imagen">
                        <a v-if='nodeData.national_id_photo_front_name' :href="'/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=' + nodeData.national_id_photo_front_name" target="_blank" class="text-dark">
                            <img alt="National ID Photo" :src="'/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=' + nodeData.national_id_photo_front_name">

                        </a>

                    </div>
                    <span v-if='nodeData.national_id_photo_back_name' class="me-1"><strong>Foto posterior de la cedula:</strong></span>
                    <div v-if='nodeData.national_id_photo_back_name' class="rounded mx-auto d-block contenedor-imagen">
                        <a :href="'/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_back&size=orginal&file_name=' + nodeData.national_id_photo_back_name" target="_blank" class="text-dark">
                            <img alt="National ID Photo" :src="'/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_back&size=orginal&file_name=' + nodeData.national_id_photo_back_name">

                        </a>

                    </div>
                    <!-- <p class=" mt-1">
                        <a v-if='nodeData.national_id_photo_front_name' :href="'/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=' + nodeData.national_id_photo_front_name" target="_blank" class="text-dark">
                            <img width="400" height="250" class="rounded mx-auto d-block" alt="National ID Photo" :src="'/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=' + nodeData.national_id_photo_front_name">
                        </a>
                    </p> -->
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3">Información General</h4>
                        <p class="card-p"><strong>¿Usted reside en el Ecuador?</strong> <span class="ml-2"><?= $nodeData->is_reside_ecuador == true ? 'Si' : 'No' ?></span></p>
                        <p class="card-p"><strong>Unidad:</strong> <span class="ml-2"> {{ nodeData.unit_id_display }} </span></p>
                        <p class="card-p"><strong>Nombres completos:</strong> <span class="ml-2"> {{ nodeData.full_name }} </span></p>
                        <p class="card-p"><strong>Numero de cedula:</strong> <span class="ml-2"> {{ nodeData.national_id_no }} </span></p>
                        <p class="card-p"><strong>Genero:</strong> <span class="ml-2"> {{ nodeData.gender_id_display }} </span></p>
                        <p class="card-p"><strong>Fecha de nacimiento:</strong> <span class="ml-2"> {{moment(nodeData.birth_date).format('YYYY-MM-DD')}} </span></p>
                        <p class="card-p"><strong>Edad:</strong> <span class="ml-2"> <?php echo  calculateAge($nodeData->birth_date); ?> </span></p>
                        <p class="card-p"><strong>Nacionalidad:</strong> <span class="ml-2"> {{ nodeData.nationality_id_display }} </span></p>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3">Contactos y ubicación</h4>
                        <p class="card-p"><strong>Telefono principal:</strong> <span class="ml-2"> {{ nodeData.phone_number }} </span></p>
                        <p class="card-p"><strong>Telefono secundario:</strong> <span class="ml-2"> {{ nodeData.inter_phone_number  }} </span></p>
                        <p class="card-p"><strong>Medio de contacto:</strong> <span class="ml-2"> {{ nodeData.medio_contacto_display }} </span></p>
                        <p class="card-p"><strong>Correo electronico:</strong> <span class="ml-2"> {{ nodeData.bnf_type_id_display }} </span></p>
                        <p class="card-p"><strong>Provincia:</strong> <span class="ml-2"> {{ nodeData.province_display }} </span></p>
                        <p class="card-p"><strong>Canton:</strong> <span class="ml-2"> {{ nodeData.canton_display }} </span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3">Documentos</h4>
                        <div class="row">
                            <div class="col-md-12 mt-1">
                                <span class="me-1"><strong>Documentos adicionales:</strong></span>
                                <?php if (!empty($nodeData->document_type_display)) { ?>
                                    <ul>
                                        <?php foreach (preg_split('/[\r\n]+/', $nodeData->document_type_display) as $cbx_itm) { ?>
                                            <li> <?= e($cbx_itm) ?> </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </div>

                            <div class="col-md-6">
                                <p class="card-p"><strong>Pasaporte:</strong> <span class="ml-2"> {{ nodeData.passport_no }} </span></p>
                            </div>
                            <div class="col-md-6 mt-1">
                                <span class="me-1"><strong>Foto del pasaporte:</strong></span>
                                <div>
                                    <p class="ms-2 mt-1">
                                        <a v-if='nodeData.passport_photo_name' :href="'/filedownload?ctype_id=beneficiaries&field_name=passport_photo&size=orginal&file_name=' + nodeData.passport_photo_name" target="_blank" class="text-dark">
                                            <img height="32" width="32" alt="Passport Photo" :src="'/filedownload?ctype_id=beneficiaries&field_name=passport_photo&size=small&file_name=' + nodeData.passport_photo_name">
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <p class="card-p"><strong>Certificado de nacimiento:</strong> <span class="ml-2"> {{ nodeData.birth_certificate_no }} </span></p>
                            </div>
                            <div class="col-md-6 mt-1">
                                <span class="me-1"><strong>Foto del Certificado de nacimiento:</strong></span>
                                <div>
                                    <p class="ms-2 mt-1">
                                        <a v-if='nodeData.birth_certificate_photo_name' :href="'/filedownload?ctype_id=beneficiaries&field_name=birth_certificate_photo&size=orginal&file_name=' + nodeData.birth_certificate_photo_name" target="_blank" class="text-dark">
                                            <img height="32" width="32" alt="Birth Certificate Photo" :src="'/filedownload?ctype_id=beneficiaries&field_name=birth_certificate_photo&size=small&file_name=' + nodeData.birth_certificate_photo_name">
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <p class="card-p"><strong>Otro documento:</strong> <span class="ml-2"> {{ nodeData.other_id_no }} </span></p>
                            </div>
                            <div class="col-md-6 mt-1">
                                <span class="me-1"><strong>Foto del Otro documento: </strong></span>
                                <div>
                                    <p class="ms-2 mt-1">
                                        <a v-if='nodeData.other_doc_photo_name' :href="'/filedownload?ctype_id=beneficiaries&field_name=other_doc_photo&size=orginal&file_name=' + nodeData.other_doc_photo_name" target="_blank" class="text-dark">
                                            <img height="32" width="32" alt="Birth Certificate Photo" :src="'/filedownload?ctype_id=beneficiaries&field_name=other_doc_photo&size=small&file_name=' + nodeData.other_doc_photo_name">
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <?php if (!empty($nodeData->family_information)) { ?>
                <div class="card-body p-4" style="background-color: #f8f9fa; border-radius: 8px;">
                    <center>
                        <h1 class="card-title mb-3"><strong>Información de los familiares</strong></h1>
                    </center>
                    <div class="row">
                        <div class="cards-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                            <?php foreach ($nodeData->family_information as $itm) { ?>
                                <div class="card" style="margin-bottom: 1rem;">
                                    <center>
                                        <a href="/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=original&file_name=<?php echo e($itm->id_photo_family_name); ?>" target="_blank">
                                            <img style="max-width: 100%; max-height: 250px; object-fit: cover;" alt="Foto documento familiar" src="/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=original&file_name=<?php echo e($itm->id_photo_family_name); ?>">
                                        </a>
                                    </center>
                                    <div class="card-body">
                                        <strong>Código:</strong> <?php echo e($itm->code); ?><br>
                                        <strong>Relación:</strong> <?php echo e($itm->relationship_display); ?><br>
                                        <strong>Nombre completo:</strong> <?php echo e($itm->full_name); ?><br>
                                        <strong>Fecha de nacimiento:</strong> <?php echo date('Y-m-d', strtotime(e($itm->birthdate))); ?><br>
                                        <strong>Edad:</strong> <?php echo calculateAge($itm->birthdate); ?><br>
                                        <strong>Nacionalidad:</strong> <?php echo e($itm->nationality_display); ?><br>
                                        <strong>Género:</strong> <?php echo e($itm->gender_id_display); ?><br>
                                        <strong>Número de identificación:</strong> <?php echo e($itm->family_national_id); ?><br>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>


        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-primary mb-3">Razones de contacto</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="card-p"><strong>Motivo principal de contacto con IOM:</strong> <span class="ml-2"> {{ nodeData.recommended_service_id_display }} </span></p>
                        </div>
                        <div class="col-md-12">
                            <p class="card-p"><strong>Otos motivos de contacto con IOM:</strong> <span class="ml-2"> {{ nodeData.recommended_services_display }} </span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-primary mb-3">Servicios previos registrados</h4>
                    <span v-if="data.loading">Loading ...</span>
                    <span v-else-if="data.errorMessage" class="text-danger"> Error while loading Statistics: {{ data.errorMessage }} </span>
                    <div v-else>
                        <table class="table table-striped" style="table-layout: fixed;" v-if='data.services'>
                            <thead>
                                <th style="text-align:center;">Código</th>
                                <th style="text-align:center;">Unidad</th>
                                <th style="text-align:center;">Tipo</th>
                                <th style="text-align:center;">Sub servicio / Modo de servicio</th>
                                <th style="text-align:center;">Estado del servicio</th>
                                <th style="text-align:center;">Creado por</th>
                                <th style="text-align:center;">Fecha del registro del servicio</th>
                            </thead>

                            <tr v-for="service in data.services">
                                <td align="center">
                                    <strong>
                                        <a :href="'/b_services/show/' + service.id" target="_blank" class="text-normal"><span style="white-space: pre-wrap;"> {{ service.code }} </span><i class="ms-1 mdi mdi-open-in-new"></i></a>
                                    </strong>
                                </td>
                                <td align="center">
                                    {{ service.unit_id_display}}
                                </td>
                                <td align="center">
                                    {{ service.service_id_display }}
                                </td>
                                <td align="center">
                                    {{ service.sub_service_display}}
                                    <a v-if="!service.sub_service_display">{{ service.health_referral_new_display}}</a>
                                </td>
                                <td align="center">
                                    {{ service.status_id_display }}
                                </td>
                                <td align="center">
                                    <a v-if="service.created_user_id_display" :href="'/users/show/' + service.created_user_id" target="_blank" class="text-normal"><span style="white-space: pre-wrap;"> {{ service.created_user_id_display }} </span><i class="ms-1 mdi mdi-open-in-new"></i></a>
                                    <a v-if="!service.created_user_id_display">Importado</a>
                                </td>
                                <td align="center">
                                    {{moment(service.created_date).format('YYYY-MM-DD')}}
                                </td>
                            </tr>
                        </table>
                        <div v-else class="col-md-12 p-0 m-0">
                            <strong>Sin Datos</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-primary mb-3">Estado de Evaluación</h4>
                    <div v-if="this.data.evaluation">
                        <div v-for="eva in this.data.evaluation" class="alert alert-info" role="alert">
                            <a target="_blank" :href="'/evaluation/show/' + eva.id" class="fw-bold">Click aquí para ver detalle de evaluación {{eva.code}}</a>
                            <ul>
                                <li>Gestor evaluador: <strong class="text-primary">{{eva.created_user_id_display}}</strong> </li>
                                <li>Puntaje: <strong class="text-primary">{{ formatScore(eva.score) }}</strong></li>
                                <li>Fecha de evaluación: <strong class="text-primary">{{ formatDate(eva.created_date) }}</strong></li>
                            </ul>
                        </div>
                    </div>
                    <div v-if="!this.data.evaluation" class="alert alert-danger" role="alert">
                        Sin evaluacion registrada
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-primary mb-3">Comentarios adicionales </h4>
                    <p class="card-p"> <span class="ml-2"> {{ nodeData.coments_prev_data }} </span></p>
                </div>
            </div>
        </div>


    </div>
</template>


<?= Application::getInstance()->view->renderView('Components/UpdateStatusComponent', (array)$data) ?>

<script>
    let vm = new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            updateStatusItems: [],
            SaveButtonLoading: false,
            ctype: <?= json_encode($data->ctypeObj);  ?>,
            nodeData: <?= json_encode($nodeData);  ?>,
            status: <?= json_encode($data->ctypeObj->use_generic_status ? $nodeData->status : []) ?>,
            data: {
                loading: false,
                errorMessage: null,
                services: null,
                evaluation: null,

            },
        },
        mounted() {
            this.getServices();
            this.getEvaluation();
        },
        methods: {
            async getServices() {
                var self = this;
                var id = this.nodeData.id;
                if (id == null || id == undefined) {
                    alert('Id not found');
                    return;
                }
                this.data.loading = true;
                this.data.services = null;
                this.data.eoi_profiles = null;
                this.data.hpf_profiles = null;

                var response = await axios.get('/InternalApi/IlaGetServcies/' + id + '&response_format=json', ).catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    self.data.errorMessage = message;
                });

                if (response) {
                    if (response.status == 200 && response.data && response.data.status == "success") {
                        this.data.services = response.data.result.services;
                        this.data.eoi_profiles = response.data.result.eoi_profiles;
                        this.data.hpf_profiles = response.data.result.hpf_profiles;
                    } else {
                        self.data.errorMessage = "Something went wrong";
                    }

                }
                this.data.loading = false;
            },
            async getEvaluation() {
                var self = this;
                var id = this.nodeData.id;
                if (id == null || id == undefined) {
                    alert('Id not found');
                    return;
                }
                this.data.loading = true;
                this.data.evaluation = null;

                var response = await axios.get('/InternalApi/IlaGetEvaluation/' + id + '&response_format=json', ).catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    self.data.errorMessage = message;
                });

                if (response) {
                    if (response.status == 200 && response.data && response.data.status == "success") {
                        this.data.evaluation = response.data.result.evaluation;
                    } else {
                        self.data.errorMessage = "Something went wrong";
                    }

                }
                this.data.loading = false;
            },
            formatScore(score) {
                return parseFloat(score).toFixed(2);
            },
            formatDate(date) {
                return moment(date).format('DD/MM/YYYY');
            },

            <?php if ($data->ctypeObj->use_generic_status) : ?>

                updateStatus() {

                    this.updateStatusItems = [];

                    this.updateStatusItems.push({
                        id: <?= $nodeData->id ?>,
                        title: this.<?= (!empty($data->ctypeObj->display_field_name) ? $data->ctypeObj->display_field_name : "id") ?>,
                    });

                },
                afterUpdateStatus(item) {
                    this.status.id = item.status.id;
                    this.status.name = item.status.name;
                    this.status.style = item.status.style;
                },

            <?php endif; ?>


            run_beneficary_download_individual() {
                <?= get_button_method('dataexport/exportindividual/[ID]?ctype_id=[CTYPEID]', $data->ctypeObj->id, $nodeData->id); ?>
            },

        },

    });
</script>
<style>
    .contenedor-imagen {
        width: 400px;
        height: 250px;
        background-color: #f0f0f0;
        overflow: hidden;
    }

    .contenedor-imagen img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>