<template id="tpl-sub-service-component">
    <div id="div_sub_service" class="mb-3 col-md-12" v-if=" this.$parent.unit_id != 2">
        <label for="sub_service" class="form-label">Sub servicio</label>
        <select name="sub_service" id="sub_service" required="required" class="form-select" @change="updateValue" v-model="selectedSubService">
            <option v-for="subService in subServicesData" :key="subService.id" :value="subService.id">{{ subService.name }}</option>
        </select>
        <div class="invalid-feedback">Please enter a valid data</div>
    </div>
</template>

<script>
    Vue.component('sub-service-component', {
        template: '#tpl-sub-service-component',
        data() {
            return {
                subServicesData: [],
                selectedSubService: null
            };
        },
        watch: {
            '$parent.service_id': {
                immediate: true,
                handler(newServiceId) {
                    this.fetchSubServices(newServiceId);
                }
            },
            subServicesData: {
                immediate: true,
                handler(newSubServicesData) {
                    if (this.$parent.sub_service && this.$parent.id) {
                        this.selectedSubService = this.$parent.sub_service;
                    }
                }
            }
        },
        mounted() {
            if (!this.$parent.id && this.$parent.sub_service) {
                this.selectedSubService = this.$parent.sub_service;
            }
        },
        methods: {
            fetchSubServices(serviceId) {
                if (serviceId && this.$parent.unit_id != 2) {
                    axios.get(`/InternalApi/RetrieveSubServices/index?id=${serviceId}`)
                        .then(response => {
                            this.subServicesData = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener sub servicios o no existen sub servicios asociados al servicio seleccionado:', error);
                            this.subServicesData = [];
                        });
                } else {
                    this.subServicesData = [];
                }
            },
            updateValue(event) {
                if (this.$parent.unit_id != 2) {
                    this.$parent.sub_service = this.selectedSubService;
                }
            }
        }
    });
</script>

<template id="tpl-family-represent-component">
    <div id="div_family_represent" class="mb-3 col-md-12 highlight">
        <label for="family_represent" class="form-label ">
            Encargado de la familia
        </label>
        <span v-if="this.$parent.represent_aplicant == 0" class="ml-1 text-danger">&nbsp;*</span>
        <select name="family_represent" id="family_represent" required="required" class="form-select" @change="updateValue" v-model="selectedFamilyMember">
            <option v-for="member in familyData" :key="member.id" :value="member.id">{{ member.full_name }}</option>
        </select>
        <div class="invalid-feedback"> Please enter a valid data </div>
    </div>
</template>

<script>
    Vue.component('family-represent-component', {
        template: '#tpl-family-represent-component',
        data() {
            return {
                familyData: [],
                selectedFamilyMember: null
            };
        },
        watch: {
            '$parent.bnf_id.id': {
                immediate: true,
                handler(newBnfId) {
                    this.fetchFamilyMembers(newBnfId);
                }
            },
            familyData: {
                immediate: true,
                handler(newFamilyData) {
                    if (this.$parent.family_represent) {
                        this.selectedFamilyMember = this.$parent.family_represent;
                    }
                }
            }
        },
        mounted() {
            if (this.$parent.id && this.$parent.family_represent) {
                this.selectedFamilyMember = this.$parent.family_represent;
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
            updateValue(event) {
                this.$parent.family_represent = this.selectedFamilyMember;
            }
        }
    });
</script>


<template id="tpl-members-assisted-multiselect-component">
    <div v-if="this.$parent.bnf_id.id" id="div_members_assisted_multiselect" class="mb-3 col-md-12">
        <label for="members_assisted_multiselect" class="form-label">
            Seleccione los miembros familiares que van a recibir la asistencia
        </label>
        <span class="ml-1 text-danger">&nbsp;*</span>
        <div>
            <div data-simplebar="init">
                <div>
                    <div>
                        <div>
                            <div v-for="(member, index) in combinedData" :key="member.id" class="custom-control custom-checkbox ms-3">
                                <input
                                    type="checkbox"
                                    :name="'members_assisted_multiselect'"
                                    :value="member.id"
                                    v-model="selectedMembers"
                                    @change="updateValue"
                                    class="form-check-input">
                                <label class="form-check-label">{{ member.full_name }}</label>
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
    Vue.component('members-assisted-multiselect-component', {
        template: '#tpl-members-assisted-multiselect-component',
        data() {
            return {
                familyData: [],
                caseInfo: null,
                combinedData: [],
                selectedMembers: [], // Array para almacenar los IDs seleccionados
                previousSelectedMembers: [] // Variable temporal para almacenar la selección anterior
            };
        },
        watch: {
            '$parent.bnf_id.id': {
                immediate: true,
                handler(newBnfId) {
                    // Guardar la selección anterior
                    this.previousSelectedMembers = [...this.selectedMembers];
                    this.fetchFamilyMembers(newBnfId);
                    this.getCaseInfo(newBnfId);
                }
            },
            combinedData: {
                immediate: true,
                handler(newCombinedData) {
                    if (this.previousSelectedMembers.length > 0) {
                        this.restorePreviousSelection();
                    } else if (this.$parent.members_assisted) {
                        this.selectedMembers = this.$parent.members_assisted.map(member => member.family_member || member.beneficiaries_id);
                    }
                }
            }
        },
        mounted() {
            if (this.$parent.id && this.$parent.members_assisted) {
                this.selectedMembers = this.$parent.members_assisted.map(member => member.family_member || member.beneficiaries_id);
            }
        },
        methods: {
            fetchFamilyMembers(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveFamilyMembers/index?id=${BnfId}`)
                        .then(response => {
                            this.familyData = response.data;
                            this.combineData();
                        })
                        .catch(error => {
                            console.error('Error al obtener miembros de la familia:', error);
                            this.familyData = [];
                            this.combineData();
                        });
                } else {
                    this.familyData = [];
                    this.combineData();
                }
            },
            getCaseInfo(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveCaseData/index?id=${BnfId}`)
                        .then(response => {
                            this.caseInfo = response.data;
                            this.combineData();
                        })
                        .catch(error => {
                            console.error('Error al obtener información del caso:', error);
                            this.caseInfo = null;
                            this.combineData();
                        });
                } else {
                    this.caseInfo = null;
                    this.combineData();
                }
            },
            combineData() {
                let combined = [];

                if (this.caseInfo) {
                    combined.push({
                        id: this.caseInfo.id,
                        full_name: this.caseInfo.full_name
                    });
                }

                if (this.familyData.length > 0) {
                    combined = combined.concat(this.familyData.map(member => ({
                        id: member.id,
                        full_name: member.full_name
                    })));
                }

                const uniqueCombined = combined.reduce((acc, current) => {
                    const x = acc.find(item => item.id === current.id);
                    if (!x) {
                        return acc.concat([current]);
                    } else {
                        return acc;
                    }
                }, []);

                this.combinedData = [];
                this.$nextTick(() => {
                    this.combinedData = uniqueCombined;
                });
            },
            restorePreviousSelection() {
                // Restaurar la selección anterior si los miembros son válidos
                this.selectedMembers = this.previousSelectedMembers.filter(id =>
                    this.combinedData.some(member => member.id === id)
                );
                this.updateValue();
                // Limpiar la selección anterior
                this.previousSelectedMembers = [];
            },
            updateValue() {
                this.$parent.members_assisted = this.selectedMembers.map((id, index) => {
                    const isBeneficiary = id === this.$parent.bnf_id.id;
                    return {
                        sys_is_edit_mode: false,
                        sort: 99999,
                        id: null,
                        token: null,
                        parent_id: null,
                        beneficiaries_id: isBeneficiary ? id : null,
                        beneficiaries_id_display: isBeneficiary ? this.combinedData.find(member => member.id === id).full_name : null,
                        family_member: isBeneficiary ? null : id,
                        family_member_display: isBeneficiary ? null : this.combinedData.find(member => member.id === id).full_name,
                        sett_index: index
                    };
                });
            }
        }
    });
</script>



<template id="tpl-display-info-component">
    <div class="card">
        <div class="bg-dark p-1">
            <h5 class="card-title fw-bold text-white">Informacion del aplicante principal</h5>
        </div>
        <div class="d-flex justify-content-center">
            <a :href="`/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=${caseInfo.national_id_photo_front_name}`" target="_blank" class="text-dark">
                <img width="400" height="250" :src="`/filedownload?ctype_id=beneficiaries&field_name=national_id_photo_front&size=orginal&file_name=${caseInfo.national_id_photo_front_name}`" alt="No subió el Frente de la cédula de identidad del beneficiario">
            </a>
        </div>
        <li class="list-group-item list-group-item-action"><strong class="fw-bold text-primary">Nombres y Apellidos: </strong>{{ caseInfo.full_name}}</li>
        <div class="col-md-6 p-0">
            <ul class="list-group">
                <li class="list-group-item list-group-item-action">
                    <strong class="fw-bold text-primary">Cedula:</strong>
                    {{caseInfo.national_id_no}}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    Vue.component('display-info-component', {
        template: '#tpl-display-info-component',
        data() {
            return {
                caseInfo: null,
            };
        },
        watch: {
            '$parent.bnf_id.id': {
                immediate: true,
                handler(newBnfId) {
                    this.getCaseInfo(newBnfId);
                }
            }
        },
        methods: {
            getCaseInfo(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveCaseData/index?id=${BnfId}`)
                        .then(response => {
                            this.caseInfo = response.data;
                            this.combineData();
                        })
                        .catch(error => {
                            console.error('Error al obtener información del caso:', error);
                            this.caseInfo = null;
                            this.combineData();
                        });
                } else {
                    this.caseInfo = null;
                    this.combineData();
                }
            },

        }
    });
</script>


<template id="tpl-physical-document-component">
    <div id="div_physical_document" class="mb-3 col-md-12 highlight">
        <label for="physical_document" class="form-label ">Documento que el beneficiario tiene físicamente <span class="ml-1 text-danger">&nbsp;*</span></label>
        <select name="physical_document" id="physical_document" required="required" class="form-select" @change="updateValue" v-model="selectedDocument">
            <option v-if="caseInfo.passport_no" value="1">Pasaporte</option>
            <option v-if="caseInfo.national_id_no" value="5">Cédula de identidad</option>
        </select>
        <div class="invalid-feedback"> Please enter a valid data </div>
    </div>
</template>

<script>
    Vue.component('physical-document-component', {
        template: '#tpl-physical-document-component',
        data() {
            return {
                caseInfo: null,
                selectedDocument: null
            };
        },
        watch: {
            '$parent.bnf_id.id': {
                immediate: true,
                handler(newBnfId) {
                    this.getCaseInfo(newBnfId);
                }
            },
            caseInfo: {
                immediate: true,
                handler(newCaseInfo) {
                    if (newCaseInfo && this.$parent.physical_document) {
                        this.selectedDocument = this.$parent.physical_document;
                    }
                }
            }
        },
        mounted() {
            if (this.$parent.id) {
                this.selectedDocument = this.$parent.physical_document;
            }
        },
        methods: {
            getCaseInfo(BnfId) {
                if (BnfId) {
                    axios.get(`/InternalApi/RetrieveCaseData/index?id=${BnfId}`)
                        .then(response => {
                            this.caseInfo = response.data;
                            this.combineData();
                        })
                        .catch(error => {
                            console.error('Error al obtener información del caso:', error);
                            this.caseInfo = null;
                            this.combineData();
                        });
                } else {
                    this.caseInfo = null;
                    this.combineData();
                }
            },
            updateValue(event) {
                this.$parent.physical_document = this.selectedDocument;
            },

        }
    });
</script>

<template id="tpl-ips-protection-component"> 
    <div id="div_ips_protection" class="mb-3 col-md-12 highlight">
        <label for="ips_protection" class="form-label">
            Implementing Partner de Protección<span class="ml-1 text-danger">&nbsp;*</span>
        </label>
        <select name="ips_protection" id="ips_protection" required="required" class="form-select" @change="updateValue" v-model="selectedIpProtection">
            <option v-for="option in filteredOptions" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
        <div class="invalid-feedback"> Please enter a valid data </div>
    </div>
</template>

<script>
    Vue.component('ips-protection-component', {
        template: '#tpl-ips-protection-component',
        data() {
            return {
                selectedIpProtection: null,
                userProvinces: null,
                options: [
                    { value: "2", label: "HIAS" },
                    { value: "4", label: "Fundación Diálogo Diverso" },
                    { value: "5", label: "Fundación Alas de Colibrí" },
                    { value: "6", label: "Fundación Lunita Lunera" },
                    { value: "7", label: "Asociación Mujer & Mujer" },
                    { value: "8", label: "Fundación Akuanuna" }
                ],
                filteredOptions: []
            };
        },
        mounted() {
            this.getUserProvinces();
            if (this.$parent.id) {
                this.selectedIpProtection = this.$parent.ips_protection;
            }
        },
        methods: {
            getUserProvinces() {
                axios
                    .get(`/InternalApi/RetrieveUserProvinces`)
                    .then(response => {
                        this.userProvinces = response.data;
                        this.filterOptions();
                    })
                    .catch(error => {
                        console.error('Error al obtener información del caso:', error);
                        this.userProvinces = [];
                    });
            },
            filterOptions() {
                const provinceOptionMapping = {
                    "24": ["4", "8"], // Pichincha
                    "5": ["4", "5", "6"], // Carchi
                    "4": ["2", "6"], // El Oro
                    "16": ["5"], // Sucumbios
                    "10": ["2", "7"], // Guayas
                    "19": ["6"] // Manabi
                };

                const allowedOptions = new Set();
                this.userProvinces.forEach(province => {
                    const optionsForProvince = provinceOptionMapping[province];
                    if (optionsForProvince) {
                        optionsForProvince.forEach(option => allowedOptions.add(option));
                    }
                });

                this.filteredOptions = this.options.filter(option => allowedOptions.has(option.value));
            },
            updateValue(event) {
                this.$parent.ips_protection = this.selectedIpProtection;
            }
        }
    });
</script>
