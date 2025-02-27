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
            };
        },
        mounted() {
            this.$watch(
                () => this.$parent.beneficiary?.id,
                (newBnfId) => {
                    if (newBnfId) {
                        this.getCaseInfo(newBnfId);
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
        }
    });
</script>


<template id="tpl-especialidades-component">
    <div v-if="especialidades.length > 0 || selectedEspecialidades.length > 0">
        <div id="div_especialidades" class="mb-3 col-md-12">
            <label for="especialidades" class="form-label">
                Especialidades solicitadas
            </label>
            <div>
                <div data-simplebar="init" style="max-height: 250px;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <button class="ms-3 btn-select-all btn btn-sm btn-outline-dark" @click="selectAll">Seleccionar todo</button>
                        <button class="btn-unselect-all btn btn-sm btn-outline-dark" @click="unselectAll">Deseleccionar todo</button>
                        <div v-for="especialidad in especialidades" :key="especialidad.value" class="custom-control custom-checkbox ms-3">
                            <input
                                type="checkbox"
                                :id="'especialidades_' + especialidad.value"
                                :value="especialidad.value"
                                class="form-check-input"
                                v-model="selectedEspecialidades">
                            <label :for="'especialidades_' + especialidad.value" class="form-check-label">
                                {{ especialidad.name }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    Vue.component('especialidades-component', {
        template: '#tpl-especialidades-component',
        data() {
            return {
                especialidades: [],
                selectedEspecialidades: [],
            };
        },
        mounted() {
            if (this.$parent.id && this.$parent.especialidades) {
                this.selectedEspecialidades = this.$parent.especialidades;
            }

            this.$watch(
                () => this.$parent.beneficiary?.id,
                (newBnfId) => {
                    if (newBnfId) {
                        this.getEspecialidades(newBnfId);
                    }
                }, {
                    immediate: true
                }
            );

            this.$watch(
                () => this.$parent.family_code?.id,
                (newFamId) => {
                    if (newFamId) {
                        this.getEspecialidadesFam(newFamId);
                    }
                }, {
                    immediate: true
                }
            );
        },

        watch: {
            selectedEspecialidades(newValue) {
                this.$parent.especialidades = newValue;
            },
        },

        methods: {
            getEspecialidadesFam(id) {
                axios.get(`/InternalApi/RetrieveCaseEspecialidadesFam/index?id=${id}`)
                    .then(response => {
                        this.especialidades = response.data;
                    })
                    .catch(error => {
                        console.error('Error al obtener las especialidades familiares:', error);
                        this.especialidades = [];
                    });
            },
            getEspecialidades(id) {
                axios.get(`/InternalApi/RetrieveCaseEspecialidades/index?id=${id}`)
                    .then(response => {
                        this.especialidades = response.data;
                    })
                    .catch(error => {
                        console.error('Error al obtener las especialidades:', error);
                        this.especialidades = [];
                    });
            },
            selectAll() {
                this.selectedEspecialidades = this.especialidades.map(e => e.value);
            },
            unselectAll() {
                this.selectedEspecialidades = [];
            },
            updateValue() {
                this.$parent.especialidades = this.selectedEspecialidades
            },
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

<!-- Ecografías -->
<template id="tpl-ecografias-component">
    <div v-if="ecografias.length > 0 || selectedEcografias.length > 0">
        <div id="div_ecografia" class="mb-3 col-md-12">
            <label for="ecografia" class="form-label">
                Ecografías solicitadas
            </label>
            <div>
                <div data-simplebar="init" style="max-height: 250px;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <button class="ms-3 btn-select-all btn btn-sm btn-outline-dark" @click="selectAll">Seleccionar todo</button>
                        <button class="btn-unselect-all btn btn-sm btn-outline-dark" @click="unselectAll">Deseleccionar todo</button>
                        <div v-for="ecografia in ecografias" :key="ecografia.value" class="custom-control custom-checkbox ms-3">
                            <input
                                type="checkbox"
                                :id="'ecografia_' + ecografia.value"
                                :value="ecografia.value"
                                class="form-check-input"
                                v-model="selectedEcografias">
                            <label :for="'ecografia_' + ecografia.value" class="form-check-label">
                                {{ ecografia.name }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('ecografias-component', {
        template: '#tpl-ecografias-component',
        data() {
            return {
                ecografias: [], // Lista de ecografías
                selectedEcografias: [], // Ecografías seleccionadas
            };
        },
        mounted() {
            if (this.$parent.id && this.$parent.ecografias) {
                this.selectedEcografias = this.$parent.ecografias;
            }

            this.$watch(
                () => this.$parent.beneficiary?.id || this.$parent.family_code?.id,
                (newId) => {
                    if (newId) {
                        this.getEcografias(newId);
                    }
                }, {
                    immediate: true,
                }
            );
        },
        watch: {
            selectedEcografias(newValue) {
                this.$parent.ecografias = newValue;
            },
        },
        methods: {
            getEcografias(id) {
                axios
                    .get(`/InternalApi/RetrieveCaseUlstrasound/index?id=${id}`)
                    .then((response) => {
                        if (response.data && response.data.length) {
                            this.ecografias = response.data.map((item) => ({
                                value: item.value,
                                name: item.name,
                            }));
                        } else {
                            console.warn("No se encontraron ecografías");
                            this.ecografias = [];
                        }
                    })
                    .catch((error) => {
                        console.error("Error al obtener las ecografías:", error);
                        this.ecografias = [];
                    });
            },
            selectAll() {
                this.selectedEcografias = this.ecografias.map((e) => e.value);
            },
            unselectAll() {
                this.selectedEcografias = [];
            },
        },
    });
</script>

<style>
    .multiselect__element {
        background-color: white;
        transition: background-color 0.3s;
    }

    .multiselect__element:hover {
        background-color: #4caf50;
        color: white;
    }

    .selected {
        background-color: #4caf50;
        color: white;
    }

    .selected:hover {
        background-color: #f44336;
    }
</style>

<!-- Radiografías -->
<template id="tpl-radiografias-component">
    <div v-if="radiografias.length > 0 || selectedRadiografias.length > 0">
        <div id="div_radiografias" class="mb-3 col-md-12">
            <label for="radiografia" class="form-label">
                Radiografías solicitadas
            </label>
            <div>
                <div data-simplebar="init" style="max-height: 250px;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <button class="ms-3 btn-select-all btn btn-sm btn-outline-dark" @click="selectAll">Seleccionar todo</button>
                        <button class="btn-unselect-all btn btn-sm btn-outline-dark" @click="unselectAll">Deseleccionar todo</button>
                        <div v-for="radiografia in radiografias" :key="radiografia.value" class="custom-control custom-checkbox ms-3">
                            <input
                                type="checkbox"
                                :id="'radiografia_' + radiografia.value"
                                :value="radiografia.value"
                                class="form-check-input"
                                v-model="selectedRadiografias">
                            <label :for="'radiografia_' + radiografia.value" class="form-check-label">
                                {{ radiografia.name }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('radiografias-component', {
        template: '#tpl-radiografias-component',
        data() {
            return {
                radiografias: [], // Lista de radiografías
                selectedRadiografias: [], // Radiografías seleccionadas
            };
        },
        mounted() {
            if (this.$parent.id && this.$parent.radiografias) {
                this.selectedRadiografias = this.$parent.radiografias;
            }

            this.$watch(
                () => this.$parent.beneficiary?.id || this.$parent.family_code?.id,
                (newId) => {
                    if (newId) {
                        this.getRadiografias(newId);
                    }
                }, {
                    immediate: true,
                }
            );
        },
        watch: {
            selectedRadiografias(newValue) {
                this.$parent.radiografias = newValue;
            },
        },
        methods: {
            getRadiografias(id) {
                axios
                    .get(`/InternalApi/RetrieveCaseRadiography/index?id=${id}`)
                    .then((response) => {
                        if (response.data && response.data.length) {
                            this.radiografias = response.data.map((item) => ({
                                value: item.value,
                                name: item.name,
                            }));
                        } else {
                            console.warn("No se encontraron radiografías");
                            this.radiografias = [];
                        }
                    })
                    .catch((error) => {
                        console.error("Error al obtener las radiografías:", error);
                        this.radiografias = [];
                    });
            },
            selectAll() {
                this.selectedRadiografias = this.radiografias.map((e) => e.value);
            },
            unselectAll() {
                this.selectedRadiografias = [];
            },
        },
    });
</script>

<style>
    .multiselect__element {
        background-color: white;
        transition: background-color 0.3s;
    }

    .multiselect__element:hover {
        background-color: #4caf50;
        color: white;
    }

    .selected {
        background-color: #4caf50;
        color: white;
    }

    .selected:hover {
        background-color: #f44336;
    }
</style>

<!-- Laboratorios -->
<template id="tpl-laboratorios-component">
    <div v-if="laboratorios.length > 0 || selectedLaboratorios.length > 0">
        <div id="div_laboratorios" class="mb-3 col-md-12">
            <label for="laboratorio" class="form-label">
                Exámenes de Laboratorios solicitados
            </label>
            <div>
                <div data-simplebar="init" style="max-height: 250px;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <button class="ms-3 btn-select-all btn btn-sm btn-outline-dark" @click="selectAll">Seleccionar todo</button>
                        <button class="btn-unselect-all btn btn-sm btn-outline-dark" @click="unselectAll">Deseleccionar todo</button>
                        <div v-for="laboratorio in laboratorios" :key="laboratorio.value" class="custom-control custom-checkbox ms-3">
                            <input
                                type="checkbox"
                                :id="'laboratorio_' + laboratorio.value"
                                :value="laboratorio.value"
                                class="form-check-input"
                                v-model="selectedLaboratorios">
                            <label :for="'laboratorio_' + laboratorio.value" class="form-check-label">
                                {{ laboratorio.name }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('laboratorios-component', {
        template: '#tpl-laboratorios-component',
        data() {
            return {
                laboratorios: [], // Lista de laboratorios
                selectedLaboratorios: [], // Laboratorios seleccionados
            };
        },
        mounted() {
            if (this.$parent.id && this.$parent.laboratorios) {
                this.selectedLaboratorios = this.$parent.laboratorios;
            }

            this.$watch(
                () => this.$parent.beneficiary?.id || this.$parent.family_code?.id,
                (newId) => {
                    if (newId) {
                        this.getLaboratorios(newId);
                    }
                }, {
                    immediate: true,
                }
            );
        },
        watch: {
            selectedLaboratorios(newValue) {
                this.$parent.laboratorios = newValue;
            },
        },
        methods: {
            getLaboratorios(id) {
                axios
                    .get(`/InternalApi/RetrieveCaseLaboratory/index?id=${id}`)
                    .then((response) => {
                        if (response.data && response.data.length) {
                            this.laboratorios = response.data.map((item) => ({
                                value: item.value,
                                name: item.name,
                            }));
                        } else {
                            console.warn("No se encontraron laboratorios");
                            this.laboratorios = [];
                        }
                    })
                    .catch((error) => {
                        console.error("Error al obtener los laboratorios:", error);
                        this.laboratorios = [];
                    });
            },
            selectAll() {
                this.selectedLaboratorios = this.laboratorios.map((e) => e.value);
            },
            unselectAll() {
                this.selectedLaboratorios = [];
            },
        },
    });
</script>

<style>
    .multiselect__element {
        background-color: white;
        transition: background-color 0.3s;
    }

    .multiselect__element:hover {
        background-color: #4caf50;
        color: white;
    }

    .selected {
        background-color: #4caf50;
        color: white;
    }

    .selected:hover {
        background-color: #f44336;
    }
</style>

<!-- Procedimientos -->
<template id="tpl-procedures-component">
    <div v-if="procedures.length > 0 || selectedProcedures.length > 0">
        <div id="div_procedures" class="mb-3 col-md-12">
            <label for="procedures" class="form-label">
                Procedimientos solicitados
            </label>
            <div>
                <div data-simplebar="init" style="max-height: 250px;">
                    <div class="simplebar-content" style="padding: 0px;">
                        <button class="ms-3 btn-select-all btn btn-sm btn-outline-dark" @click="selectAll">Seleccionar todo</button>
                        <button class="btn-unselect-all btn btn-sm btn-outline-dark" @click="unselectAll">Deseleccionar todo</button>
                        <div v-for="procedure in procedures" :key="procedure.value" class="custom-control custom-checkbox ms-3">
                            <input
                                type="checkbox"
                                :id="'procedure_' + procedure.value"
                                :value="procedure.value"
                                class="form-check-input"
                                v-model="selectedProcedures">
                            <label :for="'procedure_' + procedure.value" class="form-check-label">
                                {{ procedure.name }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    Vue.component('procedures-component', {
        template: '#tpl-procedures-component',
        data() {
            return {
                procedures: [], // Lista de procedimientos
                selectedProcedures: [], // Procedimientos seleccionados
            };
        },
        mounted() {
            if (this.$parent.id && this.$parent.procedures) {
                this.selectedProcedures = this.$parent.procedures;
            }

            this.$watch(
                () => this.$parent.beneficiary?.id || this.$parent.family_code?.id,
                (newId) => {
                    if (newId) {
                        this.getProcedures(newId);
                    }
                }, {
                    immediate: true,
                }
            );
        },
        watch: {
            selectedProcedures(newValue) {
                this.$parent.procedures = newValue;
            },
        },
        methods: {
            getProcedures(id) {
                axios
                    .get(`/InternalApi/RetrieveCaseProcedures/index?id=${id}`)
                    .then((response) => {
                        if (response.data && response.data.length) {
                            this.procedures = response.data.map((item) => ({
                                value: item.value,
                                name: item.name,
                            }));
                        } else {
                            console.warn("No se encontraron procedimientos");
                            this.procedures = [];
                        }
                    })
                    .catch((error) => {
                        console.error("Error al obtener los procedimientos:", error);
                        this.procedures = [];
                    });
            },
            selectAll() {
                this.selectedProcedures = this.procedures.map((p) => p.value);
            },
            unselectAll() {
                this.selectedProcedures = [];
            },
        },
    });
</script>

<style>
    .multiselect__element {
        background-color: white;
        transition: background-color 0.3s;
    }

    .multiselect__element:hover {
        background-color: #4caf50;
        color: white;
    }

    .selected {
        background-color: #4caf50;
        color: white;
    }

    .selected:hover {
        background-color: #f44336;
    }
</style>
