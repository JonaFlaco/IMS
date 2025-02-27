<template id="tpl-codes-component">
    <div id="div_beneficiary" class="mb-3 col-md-12 highlight">
        <label for="beneficiary" class="form-label">
            Seleccione el Código de la persona<span class="ml-1 text-danger">&nbsp;*</span>
        </label>
        <div>
            <div
                tabindex="-1"
                class="multiselect"
                :class="{ 'multiselect--active': isDropdownOpen }"
                @click.self="toggleDropdown"
                required="required">
                <div class="multiselect__tags">
                    <span
                        v-if="selectedCode"
                        class="multiselect__single"
                        @click.stop="deselectCode">
                        {{ selectedCode.code }}
                    </span>

                    <input
                        v-show="!selectedCode"
                        v-model="searchQuery"
                        @input="filterCodes"
                        @focus="isDropdownOpen = true"
                        type="text"
                        placeholder="Buscar código"
                        class="multiselect__input"
                        autocomplete="off" />
                </div>

                <div
                    class="multiselect__content-wrapper"
                    v-show="isDropdownOpen"
                    style="max-height: 300px; overflow-y: auto;">
                    <ul class="multiselect__content">
                        <li v-for="code in limitedCodes"
                            :key="code.id"
                            :class="['multiselect__element', isSelected(code) ? 'selected' : '']"
                            @click="selectCode(code)">
                            <span
                                data-select="Press enter to select"
                                data-selected="Selected"
                                data-deselect="Press enter to remove"
                                :class="[
                'multiselect__option',
                isSelected(code) ? 'bg-green-500 hover:bg-red-500' : 'hover:bg-green-500'
            ]">
                                <span>{{ code.code }}</span>
                            </span>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('codes-component', {
        template: '#tpl-codes-component',
        data() {
            return {
                codes: [],
                filteredCodes: [],
                searchQuery: '',
                selectedCode: null,
                isDropdownOpen: false
            };
        },
        computed: {
            limitedCodes() {
                return this.filteredCodes.slice(0, 100);
            }
        },
        mounted() {
            this.getCodes();
            if (this.$parent.family_code) {
                this.selectedCode = {
                    code: this.$parent.family_code.name,
                    id: this.$parent.family_code.id,
                    sett_ctype_id: "beneficiaries_family_information"
                }
            }
            if (this.$parent.beneficiary) {
                this.selectedCode = {
                    code: this.$parent.beneficiary.name,
                    id: this.$parent.beneficiary.id,
                    sett_ctype_id: "beneficiaries"
                }
            }
            document.addEventListener('click', this.handleClickOutside);

        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleClickOutside);
        },
        methods: {
            getCodes() {
                axios.get(`/InternalApi/RetrievePersonCodes`)
                    .then(response => {
                        this.codes = response.data;
                        this.filteredCodes = this.codes;
                    })
                    .catch(error => {
                        console.error('Error al obtener los códigos:', error);
                    });
            },
            filterCodes() {
                const query = this.searchQuery.toLowerCase();
                this.filteredCodes = this.codes.filter(code =>
                    code.code && code.code.toLowerCase().includes(query)
                );
            },
            toggleDropdown() {
                this.isDropdownOpen = !this.isDropdownOpen;
            },
            selectCode(code) {
                // Asegurar que el código exista
                if (!code || !code.code) return;

                // Verificar si es el mismo código ya seleccionado para deseleccionarlo
                if (this.selectedCode === code) {
                    this.deselectCode();
                } else {
                    this.selectedCode = code;
                    this.searchQuery = ''; // Limpiar la búsqueda
                    this.isDropdownOpen = false; // Cerrar menú

                    if (code.code.startsWith('IOM-FAM-')) {
                        this.$parent.family_code = {
                            name: code.code,
                            id: code.id
                        };
                        this.$parent.beneficiary = {
                            name: null,
                            id: null
                        };

                    } else if (code.code.startsWith('IOM-REG-')) {
                        this.$parent.beneficiary = {
                            name: code.code,
                            id: code.id
                        };
                        this.$parent.family_code = {
                            name: null,
                            id: null
                        }
                    }
                }
            },
            deselectCode() {
                this.selectedCode = null; // Deseleccionar
                this.isDropdownOpen = true; // Reabrir menú
            },
            isSelected(code) {
                return this.selectedCode && this.selectedCode.code === code.code;
            },
            handleClickOutside(event) {
                const dropdown = this.$el.querySelector('.multiselect');
                if (dropdown && !dropdown.contains(event.target)) {
                    this.isDropdownOpen = false;
                }
            }
        }
    });
</script>

<style>
    .multiselect__element {
        background-color: white;
        /* Color inicial */
        transition: background-color 0.3s;
    }

    .multiselect__element:hover {
        background-color: #4caf50;
        /* Hover verde */
        color: white;
    }

    /* Código seleccionado (verde fijo) */
    .selected {
        background-color: #4caf50;
        color: white;
    }

    /* Hover sobre código seleccionado (rojo) */
    .selected:hover {
        background-color: #f44336;
    }
</style>

<template id="tpl-show-data-component">
    <div>
        <div v-if="caseInfo" class="card">
            <div class="bg-dark p-1">
                <h5 class="card-title fw-bold text-white">Informacion Personal</h5>
            </div>
            <div class="d-flex justify-content-center">
                <a :href="`/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=${caseInfo.national_id_photo_front_name}`" target="_blank" class="text-dark">
                    <img width="400" height="250" :src="`/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=${caseInfo.national_id_photo_front_name}`" alt="No subió el Frente de la cédula de identidad del beneficiario">
                </a>
            </div>
            <ul class="list-group">
                <li class="list-group-item list-group-item-action">
                    <strong class="fw-bold text-primary">Nombres y Apellidos:</strong> {{ caseInfo.full_name }}
                </li>
            </ul>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Cedula:</strong> {{ caseInfo.national_id_no }}
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Fecha de nacimiento:</strong> {{ caseInfo.birth_date }}
                        </li>
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Edad:</strong> {{ calculateAge(caseInfo.birth_date) }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div v-if="familyData" class="card mt-4">
            <div class="bg-dark p-1">
                <h5 class="card-title fw-bold text-white">Informacion del familiar</h5>
            </div>
            <div class="d-flex justify-content-center">
                <a :href="`/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=orginal&file_name=${familyData.id_photo_family_name}`" target="_blank" class="text-dark">
                    <img width="400" height="250" :src="`/filedownload?ctype_id=beneficiaries_family_information&field_name=id_photo_family&size=orginal&file_name=${familyData.id_photo_family_name}`" alt="No subió el Frente de la cédula de identidad del beneficiario">
                </a>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Nombres y Apellidos:</strong> {{ familyData.full_name }}
                        </li>
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Cedula:</strong> {{ familyData.family_national_id }}
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Fecha de nacimiento:</strong> {{ familyData.birthdate }}
                        </li>
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Edad:</strong> {{ calculateAge(familyData.birthdate) }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Salud -->
            <div class="row mt-3">
                <div v-for="familyHealth in evaluationData" :key="familyHealth.id" class="col-md-6">
                    <div v-for="health in familyHealth.family_health" :key="health.id" v-if="familySelected == health.family_member">
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">¿Tiene usted alguna necesidad médica o de salud que requiera atención?:</strong> {{ health.family_medical_need_display }}
                            </li>
                            <li class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">¿Se encuentra en estado de gestación?:</strong> {{ health.family_is_pregnant_display }}
                            </li>
                            <li class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Tiene alguna discapacidad:</strong> {{ health.family_disability_display }}
                            </li>
                            <template v-if="health.family_disability_display !== 'No'">
                                <li class="list-group-item list-group-item-action">
                                    <strong class="fw-bold text-primary">Tipo de Discapacidad:</strong> {{ health.family_disability_type_display }}
                                </li>
                                <li class="list-group-item list-group-item-action">
                                    <strong class="fw-bold text-primary">Posee algún documento que evidencie la discapacidad:</strong> {{ health.family_has_disability_doc_display }}
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Sexo del miembro de la familia -->
                <div v-for="familySex in evaluationData" :key="familySex.id" class="col-md-6">
                    <div v-for="sex in familySex.family_sex" :key="sex.id" v-if="familySelected == sex.family_member">
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Sexo del miembro de la familia:</strong> {{ sex.family_member_sex_display }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Estudio -->
                <div v-for="familyStudy in evaluationData" :key="familyStudy.id" class="col-md-6">
                    <div v-for="study in familyStudy.family_education" :key="study.id" v-if="familySelected == study.family_member">
                        <ul class="list-group">
                            <li v-if="study.has_study_level_display !== null" class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Cuenta con un nivel de estudio terminado?:</strong> {{ study.has_study_level_display }}
                            </li>
                            <li v-if="study.family_last_level_display !== null" class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">¿Cuál es su último nivel de estudio?:</strong> {{ study.family_last_level_display }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- signos vitales -->
        <div v-if="vitalSigns" class="card">
            <div class="bg-dark p-1">
                <h5 class="card-title fw-bold text-white">Signos Vitales</h5>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Presión Arterial:</strong> {{ vitalSigns.blood_pressure }}
                        </li>
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Peso:</strong> {{ vitalSigns.weight }}
                        </li>
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Pulso:</strong> {{ vitalSigns.pulse }}
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Estatura:</strong> {{ vitalSigns.height }}
                        </li>
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Saturación de Oxígeno:</strong> {{ vitalSigns.oxygen_saturation }}
                        </li>
                        <li class="list-group-item list-group-item-action">
                            <strong class="fw-bold text-primary">Temperatura:</strong> {{ vitalSigns.temperature }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Historial Clínico -->
        <div v-if="historyClinic && historyClinic.length > 0" class="card">
            <div class="bg-dark p-1">
                <h5 class="card-title fw-bold text-white">Historial de Consultas Médicas</h5>
            </div>
            <div v-for="(consulta, index) in historyClinic" :key="consulta.id" class="mb-3" v-if="consulta.first || consulta.text_pathological || consulta.disease_health || consulta.presun_diagnostic">
                <div class="bg-light p-2">
                    <h6 class="fw-bold text-primary">Consulta {{ index + 1 }}: {{ consulta.created_date || 'Fecha no disponible' }}</h6>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li v-if="consulta.first" class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Motivo de Consulta:</strong> {{ consulta.first }}
                            </li>
                            <li v-if="consulta.text_pathological" class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Datos Clínicos:</strong> {{ consulta.text_pathological }}
                            </li>
                            <li v-if="consulta.disease_health" class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Enfermedad Actual:</strong> {{ consulta.disease_health }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li v-if="consulta.presun_diagnostic" class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Diagnóstico:</strong> {{ consulta.presun_diagnostic_display }}
                            </li>
                            <li v-if="consulta.type_provider" class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Exámenes de Laboratorio:</strong> {{ consulta.type_provider_display }}
                            </li>
                            <li v-if="consulta.provider" class="list-group-item list-group-item-action">
                                <strong class="fw-bold text-primary">Pedidos de Imágen:</strong> {{ consulta.provider_display }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    Vue.component('show-data-component', {
        template: '#tpl-show-data-component',
        data() {
            return {
                caseInfo: null,
                familyData: null,
                evaluationData: null,
                aplicantPrincipal: null,
                familySelected: null,
                vitalSigns: null,
                historyClinic: null
            };
        },
        computed: {
            formattedBirthDate() {
                if (this.beneficiaryData && this.beneficiaryData.birth_date) {
                    // Formatea la fecha utilizando moment.js
                    return moment(this.beneficiaryData.birth_date).format('DD/MM/YYYY');
                }
                return ''; // O retorna una cadena vacía si no hay fecha de nacimiento
            },
        },
        mounted() {
            this.$watch(
                () => this.$parent.beneficiary?.id,
                (newBnfId) => {
                    if (newBnfId) {
                        this.getCaseInfo(newBnfId);
                        this.fetchVitalSigns(newBnfId);
                    }
                }, {
                    immediate: true
                }
            );
            this.$watch(
                () => this.$parent.family_code?.id,
                (newFamId) => {
                    if (newFamId) {
                        this.fetchFamilyMembers(newFamId);
                        this.fetchVitalSigns(newFamId);
                    }
                }, {
                    immediate: true
                }
            );
            this.$watch(
                () => this.$parent.id,
                (newHistoryClinicId) => {
                    if (newHistoryClinicId) {
                        this.fetchHistoryClinic(newHistoryClinicId);
                    }
                }, {
                    immediate: true
                }
            );
        },
        methods: {
            getCaseInfo(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveCaseData/index?id=${BnfId}`)
                        .then(response => {
                            this.caseInfo = response.data;
                            this.familyData = null;
                        })
                        .catch(error => {
                            console.error('Error al obtener información del caso:', error);
                            this.caseInfo = null;
                        });
                } else {
                    this.caseInfo = null;
                }
            },
            fetchFamilyMembers(newFamId) {
                if (newFamId) {
                    axios.get(`/InternalApi/RetrieveFamMember/index?id=${newFamId}`)
                        .then(response => {
                            this.familyData = response.data;
                            this.aplicantPrincipal = this.familyData.parent_id;
                            this.caseInfo = null;

                            if (this.aplicantPrincipal) {
                                this.fetchFamilydata(this.aplicantPrincipal);
                            }
                        })

                        .catch(error => {
                            console.error('Error al obtener miembros de la familia:', error);
                            this.familyData = null;
                        });
                } else {
                    this.familyData = null;
                }
            },
            fetchFamilydata(aplicantPrincipal) {
                if (aplicantPrincipal) {
                    axios.get('/InternalApi/IlaGetEvaluation/' + aplicantPrincipal + '&response_format=json')
                        .then(response => {
                            this.evaluationData = response.data.result.evaluation;
                            this.familySelected = this.$parent.family_code.id;
                        })
                        .catch(error => {
                            console.error('Error al obtener miembros de la familia:', error);
                            this.evaluationData = null;
                        });
                } else {
                    this.evaluationData = null;
                }
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
            },
            fetchVitalSigns(id) {
                axios.get(`/InternalApi/RetrieveVitalSigns/index?id=${id}`)
                    .then(response => {
                        this.vitalSigns = response.data;
                    })
                    .catch(error => {
                        console.error('Error al obtener los signos vitales:', error);
                        this.vitalSigns = null;
                    });
            },
            fetchHistoryClinic(id) {
                axios.get(`/InternalApi/RetrieveHistoryClinic/index?id=${id}`)
                    .then(response => {
                        let data = response.data;

                        if (!Array.isArray(data)) {}

                        const uniqueRecords = data.filter((record, index, self) =>
                            index === self.findIndex((t) => t.id === record.id)
                        );

                        this.historyClinic = uniqueRecords;
                    })
                    .catch(error => {
                        console.error('Error al obtener el historial clínico:', error);
                        this.historyClinic = [];
                    });
            }
        }
    });
</script>

<template id="tpl-specialist-component">
    <div id="div_specialist" class="mb-3 col-md-12 highlight">
        <label for="specialist" class="form-label">Especialidades</label>
        <div class="">
            <div tabindex="-1" class="multiselect">
                <div class="multiselect__select" @click="toggleDropdown"></div>
                <div class="multiselect__tags">
                    <div class="multiselect__tags-wrap">
                        <span v-for="especialidad in selectedEspecialidades" :key="especialidad.id" class="multiselect__tag">
                            {{ especialidad.name }}
                            <span class="multiselect__tag-icon" @click.stop="removeEspecialidad(especialidad)">&times;</span>
                        </span>
                    </div>
                    <div class="multiselect__spinner" style="display: none;"></div>
                    <input
                        v-if="dropdownOpen"
                        v-model="searchQuery"
                        @input="filterEspecialidades"
                        name="specialist"
                        id="specialist"
                        type="text"
                        autocomplete="off"
                        placeholder="Buscar Especialidad"
                        tabindex="0"
                        class="multiselect__input"
                    >
                    <span v-if="!selectedEspecialidades.length && !dropdownOpen && !searchQuery">
                        <span class="multiselect__single">Seleccionar Opción</span>
                    </span>
                </div>
                <div class="multiselect__content-wrapper" v-if="dropdownOpen" style="max-height: 300px;">
                    <ul class="multiselect__content">
                        <span>
                            <div class="p-1 row">
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="selectAll">
                                        <i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                        Select All
                                    </button>
                                </div>
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="unselectAll">
                                        <i class="mdi mdi-checkbox-multiple-blank-outline"></i>
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </span>
                        <li v-for="especialidad in filteredEspecialidades" :key="especialidad.id" class="multiselect__element" @mousedown.prevent="toggleEspecialidad(especialidad)">
                            <span class="multiselect__option" :class="{ 'multiselect__option--highlight': isEspecialidadSelected(especialidad) }">
                                {{ especialidad.name }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('specialist-component', {
        template: '#tpl-specialist-component',
        props: {
            value: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                especialidades: [], // Todas las especialidades obtenidas del endpoint
                selectedEspecialidades: this.value, // Especialidades seleccionadas, inicializadas con las opciones existentes
                dropdownOpen: false, // Control para abrir/cerrar el dropdown
                searchQuery: '', // Para la búsqueda en el dropdown
                filteredEspecialidades: [] // Especialidades filtradas por la búsqueda
            };
        },
        mounted() {
            this.getEspecialidades();
            document.addEventListener('click', this.handleClickOutside);
        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleClickOutside);
        },
        watch: {
            selectedEspecialidades: {
                handler(newValue) {
                    this.$emit('input', newValue);
                },
                deep: true
            }
        },
        methods: {
            getEspecialidades() {
                axios
                    .get(`/InternalApi/RetrieveSpeciality`)
                    .then(response => {
                        if (response && response.data && Array.isArray(response.data)) {
                            this.especialidades = response.data.map(especialidad => ({
                                id: especialidad.id,
                                name: especialidad.name,
                            }));
                            this.filteredEspecialidades = this.especialidades;
                        } else {
                            console.error("La respuesta no tiene el formato esperado:", response);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener especialidades:', error);
                    });
            },
            toggleEspecialidad(especialidad) {
                const index = this.selectedEspecialidades.findIndex(item => item.id === especialidad.id);
                if (index === -1) {
                    this.selectedEspecialidades.push(especialidad);
                } else {
                    this.selectedEspecialidades.splice(index, 1);
                }
                this.searchQuery = '';
                this.filterEspecialidades();
            },
            isEspecialidadSelected(especialidad) {
                return this.selectedEspecialidades.some(item => item.id === especialidad.id);
            },
            removeEspecialidad(especialidad) {
                const index = this.selectedEspecialidades.findIndex(item => item.id === especialidad.id);
                if (index !== -1) {
                    this.selectedEspecialidades.splice(index, 1);
                }
            },
            toggleDropdown() {
                this.dropdownOpen = !this.dropdownOpen;
            },
            filterEspecialidades() {
                if (this.searchQuery.trim() === '') {
                    this.filteredEspecialidades = this.especialidades;
                } else {
                    const query = this.searchQuery.toLowerCase();
                    this.filteredEspecialidades = this.especialidades.filter(especialidad =>
                        especialidad.name.toLowerCase().includes(query)
                    );
                }
            },
            selectAll() {
                this.selectedEspecialidades = [...this.especialidades];
            },
            unselectAll() {
                this.selectedEspecialidades = [];
            },
            handleClickOutside(event) {
                const multiselectElement = this.$el.querySelector('.multiselect');
                if (multiselectElement && !multiselectElement.contains(event.target)) {
                    this.dropdownOpen = false;
                }
            }
        }
    });
</script>

<style>
    .multiselect__element {
        background-color: white;
        transition: background-color 0.3s;
        cursor: pointer;
    }

    .multiselect__element:hover {
        background-color: #4caf50; /* Hover verde */
        color: white;
    }

    .multiselect__option--highlight {
        background-color: #4caf50; /* Seleccionado */
        color: white;
    }

    .multiselect__tags {
        display: flex;
        flex-wrap: wrap;
    }

    .multiselect__tag {
        background-color: #e2e6ea;
        color: #495057;
        padding: 0.3rem;
        border-radius: 4px;
        margin: 0.2rem;
        display: flex;
        align-items: center;
    }

    .multiselect__tag-icon {
        margin-left: 0.5rem;
        cursor: pointer;
        color: #6c757d;
    }

    .multiselect__tag-icon:hover {
        color: #dc3545;
    }
</style>

<!-- Ecografía -->

<template id="tpl-ecogra-component">
    <div id="div_ecogra" class="mb-3 col-md-12 highlight">
        <label for="ecogra" class="form-label">Ecografía</label>
        <div class="">
            <div tabindex="-1" class="multiselect">
                <div class="multiselect__select" @click="toggleDropdown"></div>
                <div class="multiselect__tags">
                    <div class="multiselect__tags-wrap">
                        <span v-for="procedimiento in selectedProcedimientos" :key="procedimiento.id" class="multiselect__tag">
                            {{ procedimiento.name }}
                            <span class="multiselect__tag-icon" @click.stop="removeProcedimiento(procedimiento)">&times;</span>
                        </span>
                    </div>
                    <div class="multiselect__spinner" style="display: none;"></div>
                    <input
                        v-if="dropdownOpen"
                        v-model="searchQuery"
                        @input="filterProcedimientos"
                        name="ecogra"
                        id="ecogra"
                        type="text"
                        autocomplete="off"
                        placeholder="Buscar Ecografía"
                        tabindex="0"
                        class="multiselect__input"
                    >
                    <span v-if="!selectedProcedimientos.length && !dropdownOpen && !searchQuery">
                        <span class="multiselect__single">Seleccionar Opción</span>
                    </span>
                </div>
                <div class="multiselect__content-wrapper" v-if="dropdownOpen" style="max-height: 300px;">
                    <ul class="multiselect__content">
                        <span>
                            <div class="p-1 row">
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="selectAll">
                                        <i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                        Select All
                                    </button>
                                </div>
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="unselectAll">
                                        <i class="mdi mdi-checkbox-multiple-blank-outline"></i>
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </span>
                        <li v-for="procedimiento in filteredProcedimientos" :key="procedimiento.id" class="multiselect__element" @mousedown.prevent="toggleProcedimiento(procedimiento)">
                            <span class="multiselect__option" :class="{ 'multiselect__option--highlight': isProcedimientoSelected(procedimiento) }">
                                {{ procedimiento.name }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('ecogra-component', {
        template: '#tpl-ecogra-component',
        props: {
            value: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                procedimientos: [], // Todos los procedimientos obtenidos del endpoint
                selectedProcedimientos: this.value, // Procedimientos seleccionados, inicializados con las opciones existentes
                dropdownOpen: false, // Control para abrir/cerrar el dropdown
                searchQuery: '', // Para la búsqueda en el dropdown
                filteredProcedimientos: [] // Procedimientos filtrados por la búsqueda
            };
        },
        mounted() {
            this.getProcedimientos();
            document.addEventListener('click', this.handleClickOutside);
        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleClickOutside);
        },
        watch: {
            selectedProcedimientos: {
                handler(newValue) {
                    this.$emit('input', newValue);
                },
                deep: true
            }
        },
        methods: {
            getProcedimientos() {
                axios
                    .get(`/InternalApi/RetrieveUltrasound`)
                    .then(response => {
                        if (response && response.data && Array.isArray(response.data)) {
                            this.procedimientos = response.data.map(procedimiento => ({
                                id: procedimiento.id,
                                name: procedimiento.name,
                            }));
                            this.filteredProcedimientos = this.procedimientos;
                        } else {
                            console.error("La respuesta no tiene el formato esperado:", response);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los procedimientos:', error);
                    });
            },
            toggleProcedimiento(procedimiento) {
                const index = this.selectedProcedimientos.findIndex(item => item.id === procedimiento.id);
                if (index === -1) {
                    this.selectedProcedimientos.push(procedimiento);
                } else {
                    this.selectedProcedimientos.splice(index, 1);
                }
                this.searchQuery = '';
                this.filterProcedimientos();
            },
            isProcedimientoSelected(procedimiento) {
                return this.selectedProcedimientos.some(item => item.id === procedimiento.id);
            },
            removeProcedimiento(procedimiento) {
                const index = this.selectedProcedimientos.findIndex(item => item.id === procedimiento.id);
                if (index !== -1) {
                    this.selectedProcedimientos.splice(index, 1);
                }
            },
            toggleDropdown() {
                this.dropdownOpen = !this.dropdownOpen;
            },
            filterProcedimientos() {
                if (this.searchQuery.trim() === '') {
                    this.filteredProcedimientos = this.procedimientos;
                } else {
                    const query = this.searchQuery.toLowerCase();
                    this.filteredProcedimientos = this.procedimientos.filter(procedimiento =>
                        procedimiento.name.toLowerCase().includes(query)
                    );
                }
            },
            selectAll() {
                this.selectedProcedimientos = [...this.procedimientos];
            },
            unselectAll() {
                this.selectedProcedimientos = [];
            },
            handleClickOutside(event) {
                const multiselectElement = this.$el.querySelector('.multiselect');
                if (multiselectElement && !multiselectElement.contains(event.target)) {
                    this.dropdownOpen = false;
                }
            }
        }
    });
</script>

<style>
    .multiselect__element {
        background-color: white;
        transition: background-color 0.3s;
        cursor: pointer;
    }

    .multiselect__element:hover {
        background-color: #4caf50; /* Hover verde */
        color: white;
    }

    .multiselect__option--highlight {
        background-color: #4caf50; /* Seleccionado */
        color: white;
    }

    .multiselect__tags {
        display: flex;
        flex-wrap: wrap;
    }

    .multiselect__tag {
        background-color: #e2e6ea;
        color: #495057;
        padding: 0.3rem;
        border-radius: 4px;
        margin: 0.2rem;
        display: flex;
        align-items: center;
    }

    .multiselect__tag-icon {
        margin-left: 0.5rem;
        cursor: pointer;
        color: #6c757d;
    }

    .multiselect__tag-icon:hover {
        color: #dc3545;
    }
</style>

<!-- Radiografia -->
<template id="tpl-radiogra-component">
    <div id="div_radiogra" class="mb-3 col-md-12 highlight">
        <label for="radiogra" class="form-label">Radiografía</label>
        <div class="">
            <div tabindex="-1" class="multiselect">
                <div class="multiselect__select" @click="toggleDropdown"></div>
                <div class="multiselect__tags">
                    <div class="multiselect__tags-wrap">
                        <span v-for="procedimiento in selectedProcedimientos" :key="procedimiento.id" class="multiselect__tag">
                            {{ procedimiento.name }}
                            <span class="multiselect__tag-icon" @click.stop="removeProcedimiento(procedimiento)">&times;</span>
                        </span>
                    </div>
                    <div class="multiselect__spinner" style="display: none;"></div>
                    <input
                        v-if="dropdownOpen"
                        v-model="searchQuery"
                        @input="filterProcedimientos"
                        name="radiogra"
                        id="radiogra"
                        type="text"
                        autocomplete="off"
                        placeholder="Buscar Radiografía"
                        tabindex="0"
                        class="multiselect__input"
                    >
                    <span v-if="!selectedProcedimientos.length && !dropdownOpen && !searchQuery">
                        <span class="multiselect__single">Seleccionar Opción</span>
                    </span>
                </div>
                <div class="multiselect__content-wrapper" v-if="dropdownOpen" style="max-height: 300px;">
                    <ul class="multiselect__content">
                        <span>
                            <div class="p-1 row">
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="selectAll">
                                        <i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                        Select All
                                    </button>
                                </div>
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="unselectAll">
                                        <i class="mdi mdi-checkbox-multiple-blank-outline"></i>
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </span>
                        <li v-for="procedimiento in filteredProcedimientos" :key="procedimiento.id" class="multiselect__element" @mousedown.prevent="toggleProcedimiento(procedimiento)">
                            <span class="multiselect__option" :class="{ 'multiselect__option--highlight': isProcedimientoSelected(procedimiento) }">
                                {{ procedimiento.name }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('radiogra-component', {
        template: '#tpl-radiogra-component',
        props: {
            value: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                procedimientos: [],
                selectedProcedimientos: this.value,
                dropdownOpen: false,
                searchQuery: '',
                filteredProcedimientos: []
            };
        },
        mounted() {
            this.getProcedimientos();
            document.addEventListener('click', this.handleClickOutside);
        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleClickOutside);
        },
        watch: {
            selectedProcedimientos: {
                handler(newValue) {
                    this.$emit('input', newValue);
                },
                deep: true
            }
        },
        methods: {
            getProcedimientos() {
                axios
                    .get(`/InternalApi/RetrieveRadiography`)
                    .then(response => {
                        if (response && response.data && Array.isArray(response.data)) {
                            this.procedimientos = response.data.map(procedimiento => ({
                                id: procedimiento.id,
                                name: procedimiento.name,
                            }));
                            this.filteredProcedimientos = this.procedimientos;
                        } else {
                            console.error("La respuesta no tiene el formato esperado:", response);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los procedimientos:', error);
                    });
            },
            toggleProcedimiento(procedimiento) {
                const index = this.selectedProcedimientos.findIndex(item => item.id === procedimiento.id);
                if (index === -1) {
                    this.selectedProcedimientos.push(procedimiento);
                } else {
                    this.selectedProcedimientos.splice(index, 1);
                }
                this.searchQuery = '';
                this.filterProcedimientos();
            },
            removeProcedimiento(procedimiento) {
                const index = this.selectedProcedimientos.findIndex(item => item.id === procedimiento.id);
                if (index !== -1) {
                    this.selectedProcedimientos.splice(index, 1);
                }
            },
            filterProcedimientos() {
                const query = this.searchQuery.toLowerCase();
                this.filteredProcedimientos = this.procedimientos.filter(procedimiento =>
                    procedimiento.name.toLowerCase().includes(query)
                );
            },
            toggleDropdown() {
                this.dropdownOpen = !this.dropdownOpen;
            },
            handleClickOutside(event) {
                if (!this.$el.contains(event.target)) {
                    this.dropdownOpen = false;
                }
            },
            selectAll() {
                this.selectedProcedimientos = [...this.procedimientos];
            },
            unselectAll() {
                this.selectedProcedimientos = [];
            },
            isProcedimientoSelected(procedimiento) {
                return this.selectedProcedimientos.some(item => item.id === procedimiento.id);
            }
        }
    });
</script>

<!-- Laboratorio -->

<template id="tpl-laboratory-component">
    <div id="div_laboratory" class="mb-3 col-md-12 highlight">
        <label for="laboratory" class="form-label">Laboratorio</label>
        <div class="">
            <div tabindex="-1" class="multiselect">
                <div class="multiselect__select" @click="toggleDropdown"></div>
                <div class="multiselect__tags">
                    <div class="multiselect__tags-wrap">
                        <span v-for="procedimiento in selectedProcedimientos" :key="procedimiento.id" class="multiselect__tag">
                            {{ procedimiento.name }}
                            <span class="multiselect__tag-icon" @click.stop="removeProcedimiento(procedimiento)">&times;</span>
                        </span>
                    </div>
                    <div class="multiselect__spinner" style="display: none;"></div>
                    <input
                        v-if="dropdownOpen"
                        v-model="searchQuery"
                        @input="filterProcedimientos"
                        name="laboratory"
                        id="laboratory"
                        type="text"
                        autocomplete="off"
                        placeholder="Buscar Laboratorio"
                        tabindex="0"
                        class="multiselect__input"
                    >
                    <span v-if="!selectedProcedimientos.length && !dropdownOpen && !searchQuery">
                        <span class="multiselect__single">Seleccionar Opción</span>
                    </span>
                </div>
                <div class="multiselect__content-wrapper" v-if="dropdownOpen" style="max-height: 300px;">
                    <ul class="multiselect__content">
                        <span>
                            <div class="p-1 row">
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="selectAll">
                                        <i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                        Select All
                                    </button>
                                </div>
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="unselectAll">
                                        <i class="mdi mdi-checkbox-multiple-blank-outline"></i>
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </span>
                        <li v-for="procedimiento in filteredProcedimientos" :key="procedimiento.id" class="multiselect__element" @mousedown.prevent="toggleProcedimiento(procedimiento)">
                            <span class="multiselect__option" :class="{ 'multiselect__option--highlight': isProcedimientoSelected(procedimiento) }">
                                {{ procedimiento.name }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('laboratory-component', {
        template: '#tpl-laboratory-component',
        props: {
            value: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                procedimientos: [],
                selectedProcedimientos: this.value,
                dropdownOpen: false,
                searchQuery: '',
                filteredProcedimientos: []
            };
        },
        mounted() {
            this.getProcedimientos();
            document.addEventListener('click', this.handleClickOutside);
        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleClickOutside);
        },
        watch: {
            selectedProcedimientos: {
                handler(newValue) {
                    this.$emit('input', newValue);
                },
                deep: true
            }
        },
        methods: {
            getProcedimientos() {
                axios
                    .get(`/InternalApi/RetrieveLaboratory`)
                    .then(response => {
                        if (response && response.data && Array.isArray(response.data)) {
                            this.procedimientos = response.data.map(procedimiento => ({
                                id: procedimiento.id,
                                name: procedimiento.name,
                            }));
                            this.filteredProcedimientos = this.procedimientos;
                        } else {
                            console.error("La respuesta no tiene el formato esperado:", response);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los procedimientos:', error);
                    });
            },
            toggleProcedimiento(procedimiento) {
                const index = this.selectedProcedimientos.findIndex(item => item.id === procedimiento.id);
                if (index === -1) {
                    this.selectedProcedimientos.push(procedimiento);
                } else {
                    this.selectedProcedimientos.splice(index, 1);
                }
                this.searchQuery = '';
                this.filterProcedimientos();
            },
            removeProcedimiento(procedimiento) {
                const index = this.selectedProcedimientos.findIndex(item => item.id === procedimiento.id);
                if (index !== -1) {
                    this.selectedProcedimientos.splice(index, 1);
                }
            },
            filterProcedimientos() {
                const query = this.searchQuery.toLowerCase();
                this.filteredProcedimientos = this.procedimientos.filter(procedimiento =>
                    procedimiento.name.toLowerCase().includes(query)
                );
            },
            toggleDropdown() {
                this.dropdownOpen = !this.dropdownOpen;
            },
            handleClickOutside(event) {
                if (!this.$el.contains(event.target)) {
                    this.dropdownOpen = false;
                }
            },
            selectAll() {
                this.selectedProcedimientos = [...this.procedimientos];
            },
            unselectAll() {
                this.selectedProcedimientos = [];
            },
            isProcedimientoSelected(procedimiento) {
                return this.selectedProcedimientos.some(item => item.id === procedimiento.id);
            }
        }
    });
</script>

<!-- Procedimiento -->
<template id="tpl-procedures-component">
    <div id="div_procedures" class="mb-3 col-md-12 highlight">
        <label for="procedures" class="form-label">Procedimientos</label>
        <div class="">
            <div tabindex="-1" class="multiselect">
                <div class="multiselect__select" @click="toggleDropdown"></div>
                <div class="multiselect__tags">
                    <div class="multiselect__tags-wrap">
                        <span v-for="procedimiento in selectedProcedimientos" :key="procedimiento.id" class="multiselect__tag">
                            {{ procedimiento.name }}
                            <span class="multiselect__tag-icon" @click.stop="removeProcedimiento(procedimiento)">&times;</span>
                        </span>
                    </div>
                    <div class="multiselect__spinner" style="display: none;"></div>
                    <input
                        v-if="dropdownOpen"
                        v-model="searchQuery"
                        @input="filterProcedimientos"
                        name="procedures"
                        id="procedures"
                        type="text"
                        autocomplete="off"
                        placeholder="Buscar Procedimiento"
                        tabindex="0"
                        class="multiselect__input"
                    >
                    <span v-if="!selectedProcedimientos.length && !dropdownOpen && !searchQuery">
                        <span class="multiselect__single">Seleccionar Opción</span>
                    </span>
                </div>
                <div class="multiselect__content-wrapper" v-if="dropdownOpen" style="max-height: 300px;">
                    <ul class="multiselect__content">
                        <span>
                            <div class="p-1 row">
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="selectAll">
                                        <i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                        Select All
                                    </button>
                                </div>
                                <div class="col-md-6 d-grid">
                                    <button type="button" class="btn btn-sm btn-outline-success" @click="unselectAll">
                                        <i class="mdi mdi-checkbox-multiple-blank-outline"></i>
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </span>
                        <li v-for="procedimiento in filteredProcedimientos" :key="procedimiento.id" class="multiselect__element" @mousedown.prevent="toggleProcedimiento(procedimiento)">
                            <span class="multiselect__option" :class="{ 'multiselect__option--highlight': isProcedimientoSelected(procedimiento) }">
                                {{ procedimiento.name }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('procedures-component', {
        template: '#tpl-procedures-component',
        props: {
            value: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                procedimientos: [],
                selectedProcedimientos: this.value,
                dropdownOpen: false,
                searchQuery: '',
                filteredProcedimientos: []
            };
        },
        mounted() {
            this.getProcedimientos();
            document.addEventListener('click', this.handleClickOutside);
        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleClickOutside);
        },
        watch: {
            selectedProcedimientos: {
                handler(newValue) {
                    this.$emit('input', newValue);
                },
                deep: true
            }
        },
        methods: {
            getProcedimientos() {
                axios
                    .get(`/InternalApi/RetrieveProcedures`)
                    .then(response => {
                        if (response && response.data && Array.isArray(response.data)) {
                            this.procedimientos = response.data.map(procedimiento => ({
                                id: procedimiento.id,
                                name: procedimiento.name,
                            }));
                            this.filteredProcedimientos = this.procedimientos;
                        } else {
                            console.error("La respuesta no tiene el formato esperado:", response);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los procedimientos:', error);
                    });
            },
            toggleProcedimiento(procedimiento) {
                const index = this.selectedProcedimientos.findIndex(item => item.id === procedimiento.id);
                if (index === -1) {
                    this.selectedProcedimientos.push(procedimiento);
                } else {
                    this.selectedProcedimientos.splice(index, 1);
                }
                this.searchQuery = '';
                this.filterProcedimientos();
            },
            removeProcedimiento(procedimiento) {
                const index = this.selectedProcedimientos.findIndex(item => item.id === procedimiento.id);
                if (index !== -1) {
                    this.selectedProcedimientos.splice(index, 1);
                }
            },
            filterProcedimientos() {
                const query = this.searchQuery.toLowerCase();
                this.filteredProcedimientos = this.procedimientos.filter(procedimiento =>
                    procedimiento.name.toLowerCase().includes(query)
                );
            },
            toggleDropdown() {
                this.dropdownOpen = !this.dropdownOpen;
            },
            handleClickOutside(event) {
                if (!this.$el.contains(event.target)) {
                    this.dropdownOpen = false;
                }
            },
            selectAll() {
                this.selectedProcedimientos = [...this.procedimientos];
            },
            unselectAll() {
                this.selectedProcedimientos = [];
            },
            isProcedimientoSelected(procedimiento) {
                return this.selectedProcedimientos.some(item => item.id === procedimiento.id);
            }
        }
    });
</script>
