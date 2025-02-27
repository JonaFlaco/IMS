<template id="tpl-show-data-component">
    <div>
        <button type="button" class="btn btn-secondary" @click="getAllData">Obtener Datos del Solicitante</button>
        <div v-if="evaluationInfo">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-primary mb-3">Este caso ya tiene registrada una Evaluación</h4>
                    <div v-for="eva in evaluationInfo" class="alert alert-info" role="alert">
                        <a target="_blank" :href="'/evaluation/show/' + eva.id" class="fw-bold">Click aquí para ver detalle de evaluación {{eva.code}}</a>
                        <ul>
                            <li>Gestor evaluador: <strong class="text-primary">{{eva.created_user_id_display}}</strong> </li>
                            <li>Puntaje: <strong class="text-primary">{{ formatScore(eva.score) }}</strong></li>
                            <li>Fecha de evaluación: <strong class="text-primary">{{ formatDate(eva.created_date) }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="beneficiaryData">
            <div class="card">
                <div class="card-body">
                    <ul class="list-group">
                        <div class="row">
                            <div class="bg-dark p-1">
                                <h5 class="card-title fw-bold text-white">{{ beneficiaryData.code}}</h5>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a :href="`/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=${beneficiaryData.national_id_photo_front_name}`" target="_blank" class="text-dark">
                                    <img width="400" height="250" :src="`/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=${beneficiaryData.national_id_photo_front_name}`" alt="No subió el Frente de la cédula de identidad del beneficiario">
                                </a>
                            </div>
                            <li class="list-group-item list-group-item-action"><strong class="fw-bold text-primary">Nombres y Apellidos: </strong>{{ beneficiaryData.full_name}}</li>
                            <div class="col-md-6 ">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Cedula:</strong>
                                        {{beneficiaryData.national_id_no}}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 ">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Nacionalidad: </strong>{{beneficiaryData.nationality_id_display}}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 ">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Fecha de nacimiento:</strong>
                                        {{formattedBirthDate}}
                                    </li>
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Edad:</strong>
                                        {{ calculateAge(beneficiaryData.birth_date) }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 ">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Correo electronico: </strong>{{beneficiaryData.email_id}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Genero: </strong>{{beneficiaryData.gender_id_display}}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 ">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Telefono: </strong>{{beneficiaryData.phone_number}}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 ">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Telefono alternativo: </strong>{{beneficiaryData.inter_phone_number}}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 ">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-action">
                                        <strong class="fw-bold text-primary">Medio de contacto preferido: </strong>{{beneficiaryData.medio_contacto_display}}
                                    </li>
                                </ul>
                            </div>

                            <li class="list-group-item list-group-item-action"><strong class="fw-bold text-primary">Motivo principal de contacto: </strong>{{ beneficiaryData.recommended_service_id_display}}</li>
                            <ul class="list-group">
                                <li class="list-group-item list-group-item-action"><strong class="fw-bold text-primary">Mas motivos de contacto: </strong>
                                    <ul>
                                        <li v-for="service in beneficiaryData.recommended_services" :key="service.value">
                                            {{ service.name }}
                                        </li>
                                    </ul>
                                </li>
                            </ul>


                        </div>
                        <br>

                        <div v-if="beneficiaryData.family_information.length == 0" class="alert alert-danger fw-bold" role="alert">
                            <strong v-if="beneficiaryData.gender_id == 2">Mujer </strong>Sin Familiares registrados
                        </div>
                        <div v-if="calculateAge(beneficiaryData.birth_date) < 18 " class="alert alert-danger" role="alert">
                            <strong class="fw-bold"> NNA no acompañado </strong>
                        </div>
                        <div class="row">
                            <table>
                                <div>
                                    <tr v-if="hasDependentsUnder18">
                                        <th colspan="3" class="bg-secondary p-1">
                                            <h5 class="card-title fw-bold text-white">
                                                Dependientes NNA del grupo familiar
                                            </h5>
                                        </th>
                                    </tr>
                                </div>

                                <tbody>
                                    <tr v-for="familyMember in beneficiaryData.family_information" :key="familyMember.id" v-if="calculateAge(familyMember.birthdate) < 18">
                                        <!-- Fila para la foto -->
                                        <td class="col-md-4 ">
                                            <ul class="list-group">
                                                <div class="d-flex justify-content-center">
                                                    <a :href="`/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=orginal&file_name=${familyMember.id_photo_family_name}`" target="_blank" class="text-dark">
                                                        <img width="400" height="250" :src="`/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=orginal&file_name=${familyMember.id_photo_family_name}`" alt="No subió el Frente de la cédula de identidad del beneficiario">
                                                    </a>
                                                </div>

                                            </ul>
                                        </td>

                                        <td class="col-md-4 ">
                                            <ul class="list-group">
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Nombre: </strong>{{ familyMember.full_name }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Numero de identificación: </strong>{{ familyMember.family_national_id }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Parentezco: </strong>{{ familyMember.relationship_display }}
                                                </li>
                                            </ul>
                                        </td>

                                        <td class="col-md-4 ">
                                            <ul class="list-group">

                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Fecha de nacimiento: </strong>{{ formatDate(familyMember.birthdate) }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Edad: </strong>{{ calculateAge(familyMember.birthdate) }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Genero: </strong>{{familyMember.gender_id_display}}
                                                </li>
                                            </ul>
                                        </td>
                                        <br>
                                    </tr>
                                </tbody>
                                <div v-if="calculateAge(beneficiaryData.family_information.birthdate) > 60">
                                    <tr>
                                        <th v-if="hasOldDependents" colspan="3" class="bg-secondary p-1">
                                            <h5 class="card-title fw-bold text-white">
                                                Miembros del Grupo Familiar mayor de 60 años
                                            </h5>
                                        </th>
                                    </tr>
                                </div>
                                <tbody>
                                    <tr v-for="familyMember in beneficiaryData.family_information" :key="familyMember.id" v-if="calculateAge(familyMember.birthdate) > 60">
                                        <!-- Fila para la foto -->
                                        <td class="col-md-4 ">
                                            <ul class="list-group">
                                                <div class="d-flex justify-content-center">
                                                    <a :href="`/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=orginal&file_name=${familyMember.id_photo_family_name}`" target="_blank" class="text-dark">
                                                        <img width="400" height="250" :src="`/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=orginal&file_name=${familyMember.id_photo_family_name}`" alt="No subió el Frente de la cédula de identidad del beneficiario">
                                                    </a>
                                                </div>

                                            </ul>
                                        </td>

                                        <td class="col-md-4 ">
                                            <ul class="list-group">
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Nombre: </strong>{{ familyMember.full_name }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Numero de identificación: </strong>{{ familyMember.family_national_id }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Parentezco: </strong>{{ familyMember.relationship_display }}
                                                </li>
                                            </ul>
                                        </td>

                                        <td class="col-md-4 ">
                                            <ul class="list-group">

                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Fecha de nacimiento: </strong>{{ formatDate(familyMember.birthdate) }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Edad: </strong>{{ calculateAge(familyMember.birthdate) }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Genero: </strong>{{familyMember.gender_id_display}}
                                                </li>
                                            </ul>
                                        </td>
                                        <br>
                                    </tr>
                                </tbody>
                                <div v-if="calculateAge(beneficiaryData.family_information.birthdate) >= 18 && calculateAge(beneficiaryData.family_information.birthdate) < 60">
                                    <tr>
                                        <th v-if="hasOtherDependents" colspan="3" class="bg-secondary p-1">
                                            <h5 class="card-title fw-bold text-white">
                                                Otros miembros del grupo familiar
                                            </h5>
                                        </th>
                                    </tr>
                                </div>

                                <tbody>
                                    <tr v-for="familyMember in beneficiaryData.family_information" :key="familyMember.id" v-if="calculateAge(familyMember.birthdate) >= 18 && calculateAge(familyMember.birthdate) < 60 ">
                                        <!-- Fila para la foto -->
                                        <td class="col-md-4 ">
                                            <ul class="list-group">
                                                <div class="d-flex justify-content-center">
                                                    <a :href="`/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=orginal&file_name=${familyMember.id_photo_family_name}`" target="_blank" class="text-dark">
                                                        <img width="400" height="250" :src="`/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=orginal&file_name=${familyMember.id_photo_family_name}`" alt="No subió el Frente de la cédula de identidad del beneficiario">
                                                    </a>
                                                </div>

                                            </ul>
                                        </td>

                                        <td class="col-md-4 ">
                                            <ul class="list-group">
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Nombre: </strong>{{ familyMember.full_name }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Numero de identificación: </strong>{{ familyMember.family_national_id }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Parentezco: </strong>{{ familyMember.relationship_display }}
                                                </li>
                                            </ul>
                                        </td>

                                        <td class="col-md-4 ">
                                            <ul class="list-group">

                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Fecha de nacimiento: </strong>{{ formatDate(familyMember.birthdate) }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Edad: </strong>{{ calculateAge(familyMember.birthdate) }}
                                                </li>
                                                <li class="list-group-item list-group-item-action">
                                                    <strong class="fw-bold text-primary">Genero: </strong>{{familyMember.gender_id_display}}
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                    </ul>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Para actualizar la información vuelva a presionar "Obtener Datos del Solicitante"</small>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-primary mb-3">Servicios previos registrados</h4>
                        <table class="table table-striped" style="table-layout: fixed;" v-if='this.services'>
                            <thead>
                                <th style="text-align:center;">Código</th>
                                <th style="text-align:center;">Unidad</th>
                                <th style="text-align:center;">Tipo</th>
                                <th style="text-align:center;">Sub servicio / Modo de servicio</th>
                                <th style="text-align:center;">Estado del servicio</th>
                                <th style="text-align:center;">Creado por</th>
                                <th style="text-align:center;">Fecha del registro del servicio</th>
                            </thead>

                            <tr v-for="service in this.services">
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
                            <strong>Sin datos</strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div v-else>
            <p>Seleccione un código de solicitante</p>
        </div>
    </div>

</template>

<script>
    Vue.component('show-data-component', {
        template: '#tpl-show-data-component',
        data() {
            return {
                beneficiaryData: null,
                errorMessage: null,
                services: null,
                evaluationInfo: null,

            }
        },
        computed: {
            formattedBirthDate() {
                if (this.beneficiaryData && this.beneficiaryData.birth_date) {
                    // Formatea la fecha utilizando moment.js
                    return moment(this.beneficiaryData.birth_date).format('DD/MM/YYYY');
                }
                return ''; // O retorna una cadena vacía si no hay fecha de nacimiento
            },
            hasDependentsUnder18() {
                if (!this.beneficiaryData || !this.beneficiaryData.family_information) {
                    return false;
                }
                // Verifica si hay al menos un miembro menor de 18 años
                return this.beneficiaryData.family_information.some(member => {
                    return this.calculateAge(member.birthdate) < 18;
                });
            },
            hasOldDependents() {
                if (!this.beneficiaryData || !this.beneficiaryData.family_information) {
                    return false;
                }
                // Verifica si hay al menos un miembro menor de 18 años
                return this.beneficiaryData.family_information.some(member => {
                    return this.calculateAge(member.birthdate) > 60;
                });
            },
            hasOtherDependents() {
                if (!this.beneficiaryData || !this.beneficiaryData.family_information) {
                    return false;
                }
                // Verifica si hay al menos un miembro menor de 18 años
                return this.beneficiaryData.family_information.some(member => {
                    return this.calculateAge(member.birthdate) >= 18 && this.calculateAge(member.birthdate) < 60;
                });
            },
        },
        methods: {
            getAllData() {
                this.benefiaciaryPoolData();
                this.benefiaciaryServicesData();
                this.getEvaluationInfo(this.$parent.beneficiary_id.id);
            },
            benefiaciaryPoolData() {
                const beneficiaryId = this.$parent.beneficiary_id.id;
                axios.get(`/InternalApi/RetrieveCaseData/index?id=${beneficiaryId}`)
                    .then(response => {
                        this.beneficiaryData = response.data;
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos del beneficiario:', error);
                    });
            },
            async benefiaciaryServicesData() {
                var self = this;
                var id = this.$parent.beneficiary_id.id;
                if (id == null || id == undefined) {
                    alert('Id not found');
                    return;
                }
                this.services = null;


                var response = await axios.get('/InternalApi/IlaGetServcies/' + id + '&response_format=json', ).catch(function(error) {
                    message = error;

                    if (error.response != undefined && error.response.data.status == "failed") {
                        message = error.response.data.message;
                    }

                    self.data.errorMessage = message;
                });

                if (response) {
                    if (response.status == 200 && response.data && response.data.status == "success") {
                        this.services = response.data.result.services;
                    } else {
                        self.data.errorMessage = "Something went wrong";
                    }

                }
            },
            getEvaluationInfo(BnfId) {
                if (BnfId) {
                    axios.get('/InternalApi/IlaGetEvaluation/' + BnfId + '&response_format=json')
                        .then(response => {
                            this.evaluationInfo = response.data.result.evaluation;
                        })
                        .catch(error => {
                            console.error('Error al obtener información de la evaluacion:', error);
                            this.evaluationInfo = null;
                        });
                } else {
                    this.evaluationInfo = null;
                }
            },
            formatScore(score) {
                return parseFloat(score).toFixed(2);
            },
            formatDate(date) {
                return moment(date).format('DD/MM/YYYY');
            },
            formatDate(dateString) {
                const date = new Date(dateString);
                return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
            },
            calculateAge(birthDate) {
                const today = new Date();
                const dob = new Date(birthDate);
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                return age;
            }
        }
    });
</script>

<template id="tpl-current-migratory-situation-component">
    <div v-if="showQuestion" id="div_current_migratory_situation" class="mb-3 col-md-12">
        <div id="div_current_migratory_situation" class="mb-3 col-md-12"><label for="current_migratory_situation" class="form-label ">
                ¿Cuál es su situación migratoria actual?<span class="ml-1 text-danger">&nbsp;*</span></label>
            <select v-model="current_migratory_situation" @change="updateValue" name="current_migratory_situation" id="current_migratory_situation" class="form-select" required="required"><!---->
                <option value="1">Solicitante de Protección internacional</option>
                <option value="2">Visa de Protección internacional</option>
                <option value="3">Situación migratoria irregular</option>
                <option value="4">Apátrida</option>
                <option value="5">Situación migratoria regular</option>
                <option value="6">Prefiere no responder</option>
            </select>
            <div class="invalid-feedback"> Ingrese un dato válido </div>
        </div>
    </div>
</template>

<script>
    Vue.component('current-migratory-situation-component', {
        template: '#tpl-current-migratory-situation-component',
        data() {
            return {
                showQuestion: false,
                current_migratory_situation: null,
                caseInfo: null,

            }
        },
        watch: {
            '$parent.beneficiary_id.id': {
                immediate: true,
                handler(newBnfId) {
                    this.getCaseInfo(newBnfId);
                }
            },
        },
        mounted() {

            if (this.$parent.id && this.$parent.current_migratory_situation) {
                this.current_migratory_situation = this.$parent.current_migratory_situation;

            }
        },
        methods: {
            updateValue() {
                this.$parent.current_migratory_situation = this.current_migratory_situation
            },
            getCaseInfo(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveCaseDataBasic/index?id=${BnfId}`)
                        .then(response => {
                            this.caseInfo = response.data;

                            if (this.caseInfo.nationality_id != 1)
                                this.showQuestion = true;
                        })
                        .catch(error => {
                            console.error('Error al obtener información del caso:', error);
                            this.caseInfo = null;
                        });
                } else {
                    this.caseInfo = null;
                }
            },
            getMigratoryDisplay(value) {
                switch (value) {
                    case "1":
                        return "Solicitante de Protección internacional";
                    case "2":
                        return "Visa de Protección internacional";
                    case "3":
                        return "Situación migratoria irregular";
                    case "4":
                        return "Apátrida";
                    case "5":
                        return "Situación migratoria regular";
                    case "6":
                        return "Prefiere no responder";
                    default:
                        return "";
                }
            }
        },
    });
</script>

<template id="tpl-first-arrival-component">
    <div v-show="showQuestion" id="div_first_arrival" class="mb-3 col-md-12">
        <label for="first_arrival" class="form-label">
            ¿En qué fecha ingresó al país por primera vez?<span class="ml-1 text-danger">&nbsp;*</span>
        </label>
        <input type="text" name="first_arrival" id="first_arrival" class="form-control" v-model="first_arrival" @change="updateValue">
        <div class="invalid-feedback"> Ingrese un dato válido </div>
    </div>
</template>

<script>
    Vue.component('first-arrival-component', {
        template: '#tpl-first-arrival-component',
        data() {
            return {
                showQuestion: false,
                first_arrival: null,
                caseInfo: null,
            }
        },
        watch: {
            '$parent.beneficiary_id.id': {
                immediate: true,
                handler(newBnfId) {
                    this.getCaseInfo(newBnfId);
                }
            },
        },
        mounted() {
            if (this.$parent.id && this.$parent.first_arrival) {
                this.first_arrival = this.formatDate(this.$parent.first_arrival);
            }
        },
        methods: {
            updateValue() {
                const formattedDate = this.formatDate(this.first_arrival);
                this.$parent.first_arrival = formattedDate;
            },
            formatDate(dateString) {
                const date = new Date(dateString);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const seconds = String(date.getSeconds()).padStart(2, '0');

                return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}.000`;
            },
            getCaseInfo(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveCaseDataBasic/index?id=${BnfId}`)
                        .then(response => {
                            this.caseInfo = response.data;
                            if (this.caseInfo.nationality_id != 1) {
                                this.showQuestion = true;
                            }
                        })
                        .catch(error => {
                            console.error('Error al obtener información del caso:', error);
                            this.caseInfo = null;
                        });
                } else {
                    this.caseInfo = null;
                }
            },
        },
    });
</script>


<template id="tpl-family-sex-list-component">
    <div v-if="this.familyData && this.familyData.length > 0" id="div_family_sex_multiselect" class="mb-3 col-md-12">
        <label for="family_sex_multiselect" class="form-label fs-3 fw-bold">
            Perfil de miembros familiares
        </label>
        <span class="ml-1 text-danger">&nbsp;*</span>
        <div>
            <div data-simplebar="init">
                <div>
                    <div>
                        <div>
                            <div v-for="(member, index) in familyData" :key="member.id" class="card bg-light px-2 py-2 custom-control custom-checkbox ms-3">
                                <div class="d-flex align-items-center mb-3">
                                    <input type="checkbox" :name="'family_sex_multiselect'" :value="member.id" v-model="selectedMembers" @change="updateValue" class="form-check-input" required="required">
                                    <label class="form-check-label ms-2"><strong>{{ member.full_name }}</strong></label>
                                </div>

                                <div v-if="selectedMembers.includes(member.id)" id="div_family_member_sex" class="mb-3 col-md-12">
                                    <label for="family_member_sex" class="form-label">
                                        Sexo<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="memberHealth[member.id]" @change="updateValue" name="family_member_sex" id="family_member_sex" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="1">Hombre</option>
                                        <option value="2">Mujer</option>
                                        <option value="3">Intersex</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 75px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('family-sex-list-component', {
        template: '#tpl-family-sex-list-component',
        data() {
            return {
                familyData: [],
                selectedMembers: [], // Array para almacenar los IDs seleccionados
                memberHealth: {} // Objeto para almacenar el estado de salud de cada miembro seleccionado
            };
        },
        watch: {
            '$parent.beneficiary_id.id': {
                immediate: true,
                handler(newBnfId) {
                    this.fetchFamilyMembers(newBnfId);
                }
            },
        },
        mounted() {
            if (this.$parent.id && this.$parent.family_sex) {
                this.selectedMembers = this.$parent.family_sex.map(member => member.family_member || member.beneficiaries_id);
                this.$parent.family_sex.forEach(member => {
                    this.memberHealth[member.family_member || member.beneficiaries_id] = member.family_member_sex;
                });
            }
        },
        methods: {
            fetchFamilyMembers(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveFamilyMembers/index?id=${BnfId}`)
                        .then(response => {
                            this.familyData = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener miembros de la familia:', error);
                            this.familyData = [];
                        });
                } else {
                    this.familyData = [];
                }
            },

            updateValue() {
                this.$parent.family_sex = this.selectedMembers.map((id, index) => {
                    const isBeneficiary = id === this.$parent.beneficiary_id.id;
                    return {
                        sys_is_edit_mode: false,
                        sort: 99999,
                        id: null,
                        token: null,
                        parent_id: null,
                        family_member: isBeneficiary ? null : id,
                        family_member_display: isBeneficiary ? null : this.familyData.find(member => member.id === id).full_name,
                        family_member_sex: this.memberHealth[id] || null,
                        family_member_sex_display: this.memberHealth[id] !== undefined ? this.getHealthDisplay(this.memberHealth[id]) : "",
                        sett_index: index
                    };
                });
            },
            getHealthDisplay(value) {
                switch (value) {
                    case "1":
                        return "Hombre";
                    case "2":
                        return "Mujer";
                    case "3":
                        return "Intersex";
                    default:
                        return "";
                }
            }
        }
    });
</script>


<template id="tpl-family-health-list-component">
    <div v-if="this.familyData && this.familyData.length > 0" id="div_family_health_multiselect" class="mb-3 col-md-12">
        <label for="family_health_multiselect" class="form-label fs-3 fw-bold">
            Evaluación de salud de miembros familiares {{getSexMembers()}}
        </label>
        <span class="ml-1 text-danger">&nbsp;*</span>
        <div>
            <div data-simplebar="init">
                <div>
                    <div>
                        <div>
                            <div v-for="(member, index) in familyData" :key="member.id" class="card bg-light px-2 py-2 custom-control custom-checkbox ms-3">
                                <div class="d-flex align-items-center mb-3">
                                    <input type="checkbox" :name="'family_health_multiselect'" :value="member.id" v-model="selectedMembers" @change="updateValue" class="form-check-input" required="required">
                                    <label class="form-check-label ms-2"><strong>{{ member.full_name }}</strong></label>
                                </div>

                                <div v-if="selectedMembers.includes(member.id) && calculateAge(member.birthdate) >= 10 && getSexMembers(member.id) === '2'" id="div_family_is_pregnant" class="mb-3 col-md-12">
                                    <label for="family_is_pregnant" class="form-label">
                                        ¿Usted se encuentra en período de embarazo o lactancia?<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="family_is_pregnant[member.id]" @change="updateValue" name="family_is_pregnant" id="family_is_pregnant" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                        <option value="2">Prefiere no responder</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                    <div class="pb-2"><i class="mdi mdi-information">Si la respuesta fue "Sí", se debe llenar la evaluación de valoración de riesgo de Salud</i></div>
                                </div>

                                <div v-if="selectedMembers.includes(member.id)" id="div_family_medical_need" class="mb-3 col-md-12">
                                    <label for="family_medical_need" class="form-label">
                                        ¿Tiene usted alguna necesidad médica o de salud que requiera atención?<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="family_medical_need[member.id]" @change="updateValue" name="family_medical_need" id="family_medical_need" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                        <option value="2">Prefiere no responder</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                    <div class="pb-2"><i class="mdi mdi-information">Si la respuesta fue "Sí", se debe llenar la evaluación de valoración de riesgo de Salud</i></div>
                                </div>

                                <div v-if="selectedMembers.includes(member.id)" id="div_family_disability" class="mb-3 col-md-12">
                                    <label for="family_disability" class="form-label">
                                        {{$parent.family_health.token}} ¿Tiene alguna discapacidad?<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="family_disability[member.id]" @change="updateValue" name="family_disability" id="family_disability" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                        <option value="2">Prefiere no responder</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                    <div class="pb-2"><i class="mdi mdi-information">Si la respuesta fue "Sí", se debe llenar la evaluación de valoración de riesgo de Salud</i></div>
                                </div>

                                <div v-if="family_disability[member.id] == '1'" id="div_family_disability_type" class="mb-3 col-md-12">
                                    <label for="family_disability_type" class="form-label">
                                        Tipo de Discapacidad<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="family_disability_type[member.id]" @change="updateValue" name="family_disability_type" id="family_disability_type" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="1">Discapacidad auditiva</option>
                                        <option value="2">Discapacidad física</option>
                                        <option value="3">Discapacidad de lenguaje </option>
                                        <option value="4">Discapacidad psicosocial </option>
                                        <option value="5">Discapacidad visual</option>
                                        <option value="6">Discapacidad intelectual</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                </div>

                                <div v-if="family_disability[member.id] == '1'" id="div_family_has_disability_doc" class="mb-3 col-md-12">
                                    <label for="family_has_disability_doc" class="form-label">
                                        Posee algún documento que evidencie la discapacidad.<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="family_has_disability_doc[member.id]" @change="updateValue" name="family_has_disability_doc" id="family_has_disability_doc" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                        <option value="2">Prefiere no responder</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                    <div class="pb-2"><i class="mdi mdi-information">Si la respuesta fue "Sí", se debe llenar la evaluación de valoración de riesgo de Salud</i></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 75px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('family-health-list-component', {
        template: '#tpl-family-health-list-component',
        data() {
            return {
                familyData: [],
                selectedMembers: [], // Array para almacenar los IDs seleccionados
                previousSelectedMembers: [], // Variable temporal para almacenar la selección anterior
                // Objeto para almacenar el estado de salud de cada miembro seleccionado
                family_is_pregnant: {},
                family_medical_need: {},
                family_disability: {},
                family_has_disability_doc: {},
                family_disability_type: {},
                sexMembers: {}
            };
        },
        watch: {
            '$parent.beneficiary_id.id': {
                immediate: true,
                handler(newBnfId) {
                    // Guardar la selección anterior
                    this.previousSelectedMembers = [...this.selectedMembers];
                    this.fetchFamilyMembers(newBnfId);
                }
            },
        },
        mounted() {
            if (this.$parent.id && this.$parent.family_health) {
                this.selectedMembers = this.$parent.family_health.map(member => member.family_member || member.beneficiaries_id);
                this.$parent.family_health.forEach(member => {
                    this.family_is_pregnant[member.family_member || member.beneficiaries_id] = member.family_is_pregnant;
                    this.family_medical_need[member.family_member || member.beneficiaries_id] = member.family_medical_need;
                    this.family_disability[member.family_member || member.beneficiaries_id] = member.family_disability;
                    this.family_has_disability_doc[member.family_member || member.beneficiaries_id] = member.family_has_disability_doc;
                    this.family_disability_type[member.family_member || member.beneficiaries_id] = member.family_disability_type;
                });
            }
            this.sexMembers = this.getSexMembers(); // Cargar la información del sexo

        },
        methods: {
            fetchFamilyMembers(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveFamilyMembers/index?id=${BnfId}`)
                        .then(response => {
                            this.familyData = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener miembros de la familia:', error);
                            this.familyData = [];
                        });
                } else {
                    this.familyData = [];
                }
            },
            getSexMembers(id) {
                if (Array.isArray(this.$parent.family_sex)) {
                    const member = this.$parent.family_sex.find(member => member.family_member === id);
                    return member ? member.family_member_sex : null;
                }
                return null;
            },
            restorePreviousSelection() {
                // Restaurar la selección anterior si los miembros son válidos
                this.selectedMembers = this.previousSelectedMembers.filter(id =>
                    this.familyData.some(member => member.id === id)
                );
                this.updateValue();
                // Limpiar la selección anterior
                this.previousSelectedMembers = [];
            },
            calculateAge(birthdate) {
                const birthDate = new Date(birthdate);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDifference = today.getMonth() - birthDate.getMonth();
                if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age;
            },
            updateValue() {
                this.$parent.family_health = this.selectedMembers.map((id, index) => {
                    const isBeneficiary = id === this.$parent.beneficiary_id.id;
                    return {
                        sys_is_edit_mode: false,
                        sort: 99999,
                        id: null,
                        token: null,
                        parent_id: null,
                        family_member: isBeneficiary ? null : id,
                        family_member_display: isBeneficiary ? null : this.familyData.find(member => member.id === id).full_name,
                        family_is_pregnant: this.family_is_pregnant[id] || null,
                        family_is_pregnant_display: this.family_is_pregnant[id] !== undefined ? this.getHealthDisplay(this.family_is_pregnant[id]) : "",
                        family_medical_need: this.family_medical_need[id] || null,
                        family_medical_need_display: this.family_medical_need[id] !== undefined ? this.getHealthDisplay(this.family_medical_need[id]) : "",
                        family_disability: this.family_disability[id] || null,
                        family_disability_display: this.family_disability[id] !== undefined ? this.getHealthDisplay(this.family_disability[id]) : "",
                        family_has_disability_doc: this.family_has_disability_doc[id] || null,
                        family_has_disability_doc_display: this.family_has_disability_doc[id] !== undefined ? this.getHealthDisplay(this.family_has_disability_doc[id]) : "",
                        family_disability_type: this.family_disability_type[id] || null,
                        family_disability_type_display: this.family_disability_type[id] !== undefined ? this.getDisabilityDisplay(this.family_disability_type[id]) : "",
                        sett_index: index
                    };
                });
            },
            getHealthDisplay(value) {
                switch (value) {
                    case "0":
                        return "No";
                    case "1":
                        return "Sí";
                    case "2":
                        return "Prefiere no responder";
                    default:
                        return "";
                }
            },
            getDisabilityDisplay(value) {
                switch (value) {
                    case "1":
                        return "Discapacidad auditiva";
                    case "2":
                        return "Discapacidad física";
                    case "3":
                        return "Discapacidad de lenguaje";
                    case "4":
                        return "Discapacidad psicosocial";
                    case "5":
                        return "Discapacidad visual";
                    case "6":
                        return "Discapacidad intelectual";
                    default:
                        return "";
                }
            }
        }
    });
</script>



<template id="tpl-family-migrant-status-list-component">
    <div v-if="this.familyData && this.familyData.length > 0" id="div_family_migrant_status_multiselect" class="mb-3 col-md-12">
        <label for="family_migrant_status_multiselect" class="form-label fs-3 fw-bold">
            Situación migratoria de miembros familiares
        </label>
        <span class="ml-1 text-danger">&nbsp;*</span>
        <div>
            <div data-simplebar="init">
                <div>
                    <div>
                        <div>
                            <div v-for="(member, index) in familyData" :key="member.id" class="card bg-light px-2 py-2 custom-control custom-checkbox ms-3">
                                <div class="d-flex align-items-center mb-3">
                                    <input type="checkbox" :name="'family_migrant_status_multiselect'" :value="member.id" v-model="selectedMembers" @change="updateValue" class="form-check-input" required="required">
                                    <label class="form-check-label ms-2"><strong>{{ member.full_name }}</strong></label>
                                </div>

                                <div v-if="selectedMembers.includes(member.id) && member.nationality != 1" id="div_migratory_situation" class="mb-3 col-md-12">
                                    <label for="migratory_situation" class="form-label">
                                        ¿Cuál es su situación migratoria actual?<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="memberHealth[member.id]" @change="updateValue" name="migratory_situation" id="migratory_situation" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="1">Solicitante de Protección internacional</option>
                                        <option value="2">Visa de Protección internacional</option>
                                        <option value="3">Situación migratoria irregular</option>
                                        <option value="4">Apátrida</option>
                                        <option value="5">Situación migratoria regular</option>
                                        <option value="6">Prefiere no responder</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 75px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('family-migrant-status-list-component', {
        template: '#tpl-family-migrant-status-list-component',
        data() {
            return {
                familyData: [],
                selectedMembers: [], // Array para almacenar los IDs seleccionados
                previousSelectedMembers: [], // Variable temporal para almacenar la selección anterior
                memberHealth: {} // Objeto para almacenar el estado de salud de cada miembro seleccionado
            };
        },
        watch: {
            '$parent.beneficiary_id.id': {
                immediate: true,
                handler(newBnfId) {
                    // Guardar la selección anterior
                    this.previousSelectedMembers = [...this.selectedMembers];
                    this.fetchFamilyMembers(newBnfId);
                }
            },
        },
        mounted() {
            if (this.$parent.id && this.$parent.family_migrant_status) {
                this.selectedMembers = this.$parent.family_migrant_status.map(member => member.family_member || member.beneficiaries_id);
                this.$parent.family_migrant_status.forEach(member => {
                    this.memberHealth[member.family_member || member.beneficiaries_id] = member.family_migratory_situation;
                });
            }
        },
        methods: {
            fetchFamilyMembers(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveFamilyMembers/index?id=${BnfId}`)
                        .then(response => {
                            this.familyData = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener miembros de la familia:', error);
                            this.familyData = [];
                        });
                } else {
                    this.familyData = [];
                }
            },
            restorePreviousSelection() {
                // Restaurar la selección anterior si los miembros son válidos
                this.selectedMembers = this.previousSelectedMembers.filter(id =>
                    this.familyData.some(member => member.id === id)
                );
                this.updateValue();
                // Limpiar la selección anterior
                this.previousSelectedMembers = [];
            },
            updateValue() {
                this.$parent.family_migrant_status = this.selectedMembers.map((id, index) => {
                    const isBeneficiary = id === this.$parent.beneficiary_id.id;
                    return {
                        sys_is_edit_mode: false,
                        sort: 99999,
                        id: null,
                        token: null,
                        parent_id: null,
                        family_member: isBeneficiary ? null : id,
                        family_member_display: isBeneficiary ? null : this.familyData.find(member => member.id === id).full_name,
                        family_migratory_situation: this.memberHealth[id] || null,
                        family_migratory_situation_display: this.memberHealth[id] !== undefined ? this.getHealthDisplay(this.memberHealth[id]) : "",
                        sett_index: index
                    };
                });
            },

            getHealthDisplay(value) {
                switch (value) {
                    case "1":
                        return "Solicitante de Protección internacional";
                    case "2":
                        return "Visa de Protección internacional";
                    case "3":
                        return "Situación migratoria irregular";
                    case "4":
                        return "Apátrida";
                    case "5":
                        return "Situación migratoria regular";
                    case "6":
                        return "Prefiere no responder";
                    default:
                        return "";
                }
            }
        }
    });
</script>

<template id="tpl-family-education-list-component">
    <div v-if="this.familyData && this.familyData.length > 0" id="div_family_education_multiselect" class="mb-3 col-md-12">
        <label for="family_education_multiselect" class="form-label fs-3 fw-bold">
            Educación de miembros familiares
        </label>
        <span class="ml-1 text-danger">&nbsp;*</span>
        <div>
            <div data-simplebar="init">
                <div>
                    <div>
                        <div>
                            <div v-for="(member, index) in familyData" :key="member.id" class="card bg-light px-2 py-2 custom-control custom-checkbox ms-3">
                                <div class="d-flex align-items-center mb-3">
                                    <input type="checkbox" :name="'family_education_multiselect'" :value="member.id" v-model="selectedMembers" @change="updateValue" class="form-check-input" required="required">
                                    <label class="form-check-label ms-2"><strong>{{ member.full_name }}</strong></label>
                                </div>

                                <div v-if="selectedMembers.includes(member.id)  && calculateAge(member.birthdate) >= 18" id="div_has_study_level" class="mb-3 col-md-12">
                                    <label for="has_study_level" class="form-label">
                                        ¿Cuenta con un nivel de estudio terminado?<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="has_study_level[member.id]" @change="updateValue" name="has_study_level" id="has_study_level" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                        <option value="2">Prefiere no responder</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                </div>

                                <div v-if="selectedMembers.includes(member.id) && has_study_level[member.id] == '1'" id="div_family_last_level" class="mb-3 col-md-12">
                                    <label for="family_last_level" class="form-label">
                                        ¿Cuál es su último nivel de estudio terminado?<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="family_last_level[member.id]" @change="updateValue" name="family_last_level" id="family_last_level" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="1">Primaria</option>
                                        <option value="2">Secundaria</option>
                                        <option value="3">Tecnico</option>
                                        <option value="4">Universitario</option>
                                        <option value="5">Posgrado</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                </div>

                                <div v-if="selectedMembers.includes(member.id)  && calculateAge(member.birthdate) >= 4 && calculateAge(member.birthdate) < 18" id="div_is_assisting_school" class="mb-3 col-md-12">
                                    <label for="is_assisting_school" class="form-label">
                                        {{$parent.family_education.token}} ¿Está asistiendo a la escuela?<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="is_assisting_school[member.id]" @change="updateValue" name="is_assisting_school" id="is_assisting_school" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                        <option value="2">Prefiere no responder</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                </div>

                                <div v-if="selectedMembers.includes(member.id) && is_assisting_school[member.id] == '0'" id="div_why_not_school" class="mb-3 col-md-12">
                                    <label for="why_not_school" class="form-label">
                                        ¿Por qué no asiste a la escuela?<span class="ml-1 text-danger">&nbsp;*</span>
                                    </label>
                                    <select v-model="why_not_school[member.id]" @change="updateValue" name="why_not_school" id="why_not_school" required class="form-select">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="1">Falta de información</option>
                                        <option value="2">Falta de recursos</option>
                                        <option value="3">Discriminación</option>
                                        <option value="4">Decisión familiar</option>
                                        <option value="5">Falta de gestión por parte del sistema de educación</option>
                                    </select>
                                    <div class="invalid-feedback">Ingrese un dato válido</div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 75px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('family-education-list-component', {
        template: '#tpl-family-education-list-component',
        data() {
            return {
                familyData: [],
                selectedMembers: [], // Array para almacenar los IDs seleccionados
                previousSelectedMembers: [], // Variable temporal para almacenar la selección anterior
                has_study_level: {},
                is_assisting_school: {},
                family_last_level: {},
                why_not_school: {}
            };
        },
        watch: {
            '$parent.beneficiary_id.id': {
                immediate: true,
                handler(newBnfId) {
                    // Guardar la selección anterior
                    this.previousSelectedMembers = [...this.selectedMembers];
                    this.fetchFamilyMembers(newBnfId);
                }
            },
        },
        mounted() {
            if (this.$parent.id && this.$parent.family_education) {
                this.selectedMembers = this.$parent.family_education.map(member => member.family_member || member.beneficiaries_id);
                this.$parent.family_education.forEach(member => {
                    this.has_study_level[member.family_member || member.beneficiaries_id] = member.has_study_level;
                    this.is_assisting_school[member.family_member || member.beneficiaries_id] = member.is_assisting_school;
                    this.family_last_level[member.family_member || member.beneficiaries_id] = member.family_last_level;
                    this.why_not_school[member.family_member || member.beneficiaries_id] = member.why_not_school;
                });
            }
        },
        methods: {
            fetchFamilyMembers(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveFamilyMembers/index?id=${BnfId}`)
                        .then(response => {
                            this.familyData = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener miembros de la familia:', error);
                            this.familyData = [];
                        });
                } else {
                    this.familyData = [];
                }
            },
            restorePreviousSelection() {
                // Restaurar la selección anterior si los miembros son válidos
                this.selectedMembers = this.previousSelectedMembers.filter(id =>
                    this.familyData.some(member => member.id === id)
                );
                this.updateValue();
                // Limpiar la selección anterior
                this.previousSelectedMembers = [];
            },
            calculateAge(birthdate) {
                const birthDate = new Date(birthdate);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDifference = today.getMonth() - birthDate.getMonth();
                if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age;
            },
            updateValue() {
                this.$parent.family_education = this.selectedMembers.map((id, index) => {
                    const isBeneficiary = id === this.$parent.beneficiary_id.id;
                    return {
                        sys_is_edit_mode: false,
                        sort: 99999,
                        id: null,
                        token: null,
                        parent_id: null,
                        family_member: isBeneficiary ? null : id,
                        family_member_display: isBeneficiary ? null : this.familyData.find(member => member.id === id).full_name,
                        has_study_level: this.has_study_level[id] || null,
                        has_study_level_display: this.has_study_level[id] !== undefined ? this.getYesNoDisplay(this.has_study_level[id]) : "",
                        is_assisting_school: this.is_assisting_school[id] || null,
                        is_assisting_school_display: this.is_assisting_school[id] !== undefined ? this.getYesNoDisplay(this.is_assisting_school[id]) : "",
                        family_last_level: this.family_last_level[id] || null,
                        family_last_level_display: this.family_last_level[id] !== undefined ? this.getStudyLevelsDisplay(this.family_last_level[id]) : "",
                        why_not_school: this.why_not_school[id] || null,
                        why_not_school_display: this.why_not_school[id] !== undefined ? this.getStudyReasonsDisplay(this.why_not_school[id]) : "",
                        sett_index: index
                    };
                });
            },
            getYesNoDisplay(value) {
                switch (value) {
                    case "0":
                        return "No";
                    case "1":
                        return "Sí";
                    case "2":
                        return "Prefiere no responder";
                    default:
                        return "";
                }
            },
            getStudyLevelsDisplay(value) {
                switch (value) {
                    case "1":
                        return "Primaria";
                    case "2":
                        return "Secundaria";
                    case "3":
                        return "Tecnico";
                    case "4":
                        return "Universitario";
                    case "5":
                        return "Posgrado";
                    default:
                        return "";
                }
            },
            getStudyReasonsDisplay(value) {
                switch (value) {
                    case "1":
                        return "Falta de información";
                    case "2":
                        return "Falta de recursos";
                    case "3":
                        return "Decisión familiar";
                    case "4":
                        return "Decisión familiar";
                    case "5":
                        return "Falta de gestión por parte del sistema de educación";
                    default:
                        return "";
                }
            }
        }
    });
</script>

<template id="tpl-protection-confirm-component">
    <div v-if="showQuestion" id="div_protection_confirm" class="mb-3 col-md-12">
        <label for="protection_confirm" class="form-label">
            ¿El caso se encuentra habilitado para que un gestor pueda agregar un servicio?<span class="ml-1 text-danger">&nbsp;*</span>
        </label>
        <select v-model="protection_confirm" @change="updateValue" name="protection_confirm" id="protection_confirm" required class="form-select">
            <option value="" disabled selected>Seleccione una opción</option>
            <option value="0">No</option>
            <option value="1">Sí</option>
        </select>
        <div class="invalid-feedback">Ingrese un dato válido</div>
    </div>
</template>

<script>
    Vue.component('protection-confirm-component', {
        template: '#tpl-protection-confirm-component',
        data() {
            return {
                showQuestion: false,
                protection_confirm: null,
            }
        },
        mounted() {
            fetch('/internalApi/RetrieveUserRoles/index')
                .then(response => response.json())
                .then(data => {
                    // Si el valor es true, muestra la pregunta
                    this.showQuestion = data.hasProtectionFocalPointRole;
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
            if (this.$parent.id && this.$parent.protection_confirm) {
                this.protection_confirm = this.$parent.protection_confirm;

            }
        },
        methods: {
            updateValue() {
                this.$parent.protection_confirm = this.protection_confirm
            },
            getYesNoDisplay(value) {
                switch (value) {
                    case "0":
                        return "No";
                    case "1":
                        return "Sí";
                    case "2":
                        return "Prefiere no responder";
                    default:
                        return "";
                }
            },
        },
    });
</script>

<template id="tpl-observations-component">
    <div id="div_observations" class="mb-3 col-md-12">
        <label for="observations" class="form-label ">
            Observaciones </label>
        <textarea
            rows="5"
            name="observations"
            id="observations" 
            maxlength="2000" 
            class="form-control"
            v-model="observations"
            @change="updateValue">
        </textarea>
        <div class="invalid-feedback"> Ingrese un dato válido </div>
    </div>
</template>

<script>
    Vue.component('observations-component', {
        template: '#tpl-observations-component',
        data() {
            return {
                observations: null
            }
        },
        mounted() {
            this.observations = this.$parent.observations;
            
        },
        methods: {
            updateValue() {
                this.$parent.observations = this.observations
            },

        },
    });
</script>
<template id="tpl-badge-component">
    <div class="fixed-badge">
        <button type="button" class="btn btn-primary" @click="toggleCollapse">
            Puntaje <span class="badge bg-secondary large-text">{{this.dynamicScore}}</span>
        </button>

        <div v-if="isCollapsed" class="card card-custom">
            <div class="card-body p-2 text-white bg-secondary">
                <h5>Resumen de puntaje</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Sexo: <a v-if="this.$parent.sex_id == 2"> Mujer <span class="badge bg-primary large-text"> 2pts</span></a> <a v-if="this.$parent.sex_id == 1"> Hombre</a> <a v-if="this.$parent.sex_id == 3"> Intersex</a></li>
                <li v-if="this.$parent.is_pregnant == 1" class="list-group-item">Aplicante principal en periodo de lactancia <span class="badge bg-primary large-text"> 2pts</span></li>
            </ul>
        </div>
    </div>
</template>


<script>
    Vue.component('badge-component', {
        template: '#tpl-badge-component',
        data() {
            return {
                familyData: [],
                caseInfo: null,
                selectedMembers: [], // Array para almacenar los IDs seleccionados
                previousSelectedMembers: [],
                isCollapsed: false,
                dynamicScore: 0,
            }
        },
        watch: {
            '$parent.beneficiary_id.id': {
                immediate: true,
                handler(newBnfId) {
                    // Guardar la selección anterior
                    this.previousSelectedMembers = [...this.selectedMembers];
                    this.fetchFamilyMembers(newBnfId);
                    this.getCaseInfo(newBnfId);
                }
            },
            '$parent.sex_id': {
                immediate: true,
                handler(newSexId) {
                    this.updateScore();
                }
            },
            '$parent.is_pregnant': {
                immediate: true,
                handler(newPregnantStatus) {
                    this.updateScore();
                }
            }

        },
        methods: {
            toggleCollapse() {
                this.isCollapsed = !this.isCollapsed;
            },
            getCaseInfo(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveCaseData/index?id=${BnfId}`)
                        .then(response => {
                            this.caseInfo = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener información del caso:', error);
                            this.caseInfo = null;
                        });
                } else {
                    this.caseInfo = null;
                }
            },
            fetchFamilyMembers(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveFamilyMembers/index?id=${BnfId}`)
                        .then(response => {
                            this.familyData = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener miembros de la familia:', error);
                            this.familyData = [];
                        });
                } else {
                    this.familyData = [];
                }
            },
            restorePreviousSelection() {
                // Restaurar la selección anterior si los miembros son válidos
                this.selectedMembers = this.previousSelectedMembers.filter(id =>
                    this.familyData.some(member => member.id === id)
                );
                this.updateValue();
                // Limpiar la selección anterior
                this.previousSelectedMembers = [];
            },
            updateScore() {
                const sexId = this.$parent.sex_id;
                const isPregnant = this.$parent.is_pregnant;
                this.dynamicScore = this.calculateScore({
                    sexId,
                    isPregnant,
                    // Agrega más variables aquí en el futuro
                });
            },
            calculateScore({
                sexId,
                isPregnant
            }) {
                let score = 0;

                const scoreRules = {
                    sexId: {
                        2: 2,
                        // Agrega más reglas según sea necesario
                    },
                    isPregnant: {
                        1: 2,
                        // Agrega más reglas según sea necesario
                    }
                };

                // Aplicar las reglas de puntaje
                score += scoreRules.sexId[sexId] || 0;
                score += scoreRules.isPregnant[isPregnant] || 0;

                return score;
            }
        },
    });
</script>

<!-- 
<style>
    .fixed-badge {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .card-custom {
        position: absolute;
        bottom: 17px;
        right: 0;
        width: 400px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        background-color: #fff;
    }

    .list-group-item {
        border: none;
    }

    .large-text {
        font-size: 14px;
    }
</style> -->