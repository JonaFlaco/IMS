<template id="tpl-show-data-component">
    <div>
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
        mounted() {
            this.getAllData();
        },
        methods: {
            getAllData() {
                this.benefiaciaryPoolData();
            },
            benefiaciaryPoolData() {
                const beneficiaryId = this.$parent.nodeData.beneficiary_id;
                axios.get(`/InternalApi/RetrieveCaseData/index?id=${beneficiaryId}`)
                    .then(response => {
                        this.beneficiaryData = response.data;
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos del beneficiario:', error);
                    });
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