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
                        <li class="list-group-item list-group-item-action">
                            <div v-if="!familySelected">
                                <div v-for="familyHealth in evaluationData" :key="familyHealth.id" class="col-md-6">
                                    <strong class="fw-bold text-primary">Sexo del Aplicante Principal:</strong> {{ familyHealth.sex_id_display }}
                                </div>

                            </div>
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



        <div v-if="familyData" class="card ">
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
                        <li class="list-group-item list-group-item-action">
                            <div v-for="familySex in evaluationData" :key="familySex.id">
                                <div v-for="sex in familySex.family_sex" :key="sex.id" v-if="familySelected == sex.family_member">
                                    <strong class="fw-bold text-primary">Sexo del miembro de la familia:</strong> {{ sex.family_member_sex_display }}
                                </div>
                            </div>
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


                <!-- Sexo del miembro de la familia -->


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
                            this.fetchFamilydata(BnfId);
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