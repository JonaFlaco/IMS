<?= App\Core\Application::getInstance()->view->renderView('CustomTpls/EdfRelatedFormsComponent',[]) ?>
<template id="tpl-business-gps-lat-component">
    <p class="card-p">
        <span class="me-1"><strong>Ubicación en el mapa:</strong></span>
        <span>
            {{this.$parent.nodeData.business_gps_lat}}, {{this.$parent.nodeData.business_gps_lng}}
            <a :href="googleMapsLink" target="_blank">
                <i class="text-primary mdi mdi-google-maps has-tooltip" data-original-title="null" style="font-size: 36px;"></i>
            </a>
        </span>
    </p>
</template>

<script>
    Vue.component('business-gps-lat-component', {
        template: '#tpl-business-gps-lat-component',
        props: {
            business_gps_lat: {
                type: Number,
                required: true
            },
            business_gps_lng: {
                type: Number,
                required: true
            }
        },
        computed: {
            googleMapsLink() {
                const latDMS = this.convertToDMS(this.$parent.nodeData.business_gps_lat, true);
                const lngDMS = this.convertToDMS(this.$parent.nodeData.business_gps_lng, false);
                return `https://www.google.com/maps/place/${latDMS}+${lngDMS}/@${this.$parent.nodeData.business_gps_lat},${this.$parent.nodeData.business_gps_lng},17z`;
            }
        },
        methods: {
            convertToDMS(decimal, isLat) {
                const absDecimal = Math.abs(decimal);
                const degrees = Math.floor(absDecimal);
                const minutes = Math.floor((absDecimal - degrees) * 60);
                const seconds = ((absDecimal - degrees - (minutes / 60)) * 3600).toFixed(2);
                const direction = isLat ?
                    decimal < 0 ? 'S' : 'N' :
                    decimal < 0 ? 'W' : 'E';

                return `${degrees}°${minutes}'${seconds}"${direction}`;
            }
        }
    });
</script>

<template id="tpl-dscore-component">
    <div class="fixed-badge">
        <button type="button" class="btn btn-primary" @click="toggleCollapse">
            Puntaje <span class="badge bg-secondary large-text">{{ Number(this.$parent.nodeData.score).toFixed(2) }}</span>
        </button>

        <div v-if="isCollapsed" class="card card-custom ">
            <div class="card-body p-2 text-white bg-primary">
                <h5>Resumen de puntaje</h5>
            </div>
            <ul class="list-group list-group-flush text-white bg-dark ">
                <li class="list-group-item"><span class="me-1"><strong>Género del representante legal:</strong></span> <span> {{this.$parent.nodeData.gender_legal_rep_display}} </span> <span class="badge bg-primary"> Puntaje: {{ genderScore }}</span> </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Pyme está liderada por una mujer: </strong></span>
                    <span>
                        {{ Number(this.$parent.nodeData.has_female_leader) === 1 ? 'Sí' : 'No' }}
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{ scoreLeader }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Número de empleadas mujeres:</strong></span>
                    <span> {{this.$parent.nodeData.female_employees_number}} </span>
                    <span class="badge bg-primary"> Puntaje por porcentaje de empleadas mujeres: {{ scoreFemalePer }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Años de funcionamiento de la empresa: </strong></span>
                    <span>
                        {{Number(this.$parent.nodeData.operation_years_company).toFixed(2)}}
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{ scoreYears }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Total ingresos (según campo 6999 form 101 SRI) año 2023:</strong></span>
                    <span>
                        {{ Number(this.$parent.nodeData.total_income_tres).toFixed(2) }}
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{ scoreIncome }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Numero de empleados que tiene la empresa: </strong></span>
                    <span>
                        {{this.$parent.nodeData.employees_number}}
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{ scoreEmployees }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Numero de empleados prioritarios: </strong></span>
                    <span>
                        {{priority_employees_number}}
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{ scorePriEmployees }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Tiene prácticas de responsabilidad social empresarial: </strong></span>
                    <span>
                        {{Number(this.$parent.nodeData.has_social_responsability) === 1 ? 'Sí' : 'No'}}
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{ scoreSocial }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Existe un beneficio adicional que la empresa entrega a sus colaboradores además de los beneficios de ley: </strong></span>
                    <span>
                        {{Number(this.$parent.nodeData.has_aditional_benefits) === 1 ? 'Sí' : 'No'}}
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{ scoreBenef }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Han implementado medidas para fomentar la diversidad e inclusión en el lugar de trabajo: </strong></span>
                    <span>
                        {{Number(this.$parent.nodeData.has_diversity_politics) === 1 ? 'Sí' : 'No'}}
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{ scoreDiversity }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span>Formula: (Owner’s contribution/(Amount requested))*15</span>
                    <span>({{this.$parent.nodeData.contribution_value}}/(1*{{this.$parent.nodeData.total_amount__iom}}))*15 = {{comparative}}</span>
                    <span class="badge bg-primary"> Puntaje por el Valor de la contribución dividido para el monto solicitado: {{ scoreCompare }}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Empleos adicionales que generaría el plan de expansión: </strong></span>
                    <span>
                        {{ this.$parent.nodeData.additional_staff_need }}
                    </span>
                    <span>Formula: (2000/*(Amount requested/Jobs created))*25</span>
                    <span>(2000/({{this.$parent.nodeData.total_amount__iom}}/{{ this.$parent.nodeData.additional_staff_need }}))*25 </span>
                    <span class="badge bg-primary"> Puntaje: {{ scoreStaff }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    Vue.component('dscore-component', {
        template: '#tpl-dscore-component',
        data() {
            return {
                score: this.$parent.nodeData.score,
                genderScore: 0,
                scoreLeader: 0,
                scoreFemalePer: 0,
                scoreYears: 0,
                scoreIncome: 0,
                scoreEmployees: 0,
                scorePriEmployees: 0,
                priority_employees_number: 0,
                scoreSocial: 0,
                scoreBenef: 0,
                scoreDiversity: 0,
                scoreCompare: 0,
                comparative: 0,
                newStaffPercent: 0,
                scoreStaff: 0,
                scoreTotal: 0,
                isCollapsed: false,
            };
        },
        methods: {
            toggleCollapse() {
                this.isCollapsed = !this.isCollapsed;
            },
            calculateScore() {
                if (this.$parent.nodeData.gender_legal_rep == 2) {
                    this.genderScore += 5;
                }

                if (this.$parent.nodeData.has_female_leader == 1) {
                    this.scoreLeader += 5;
                }

                if (this.$parent.nodeData.female_employees_number) {
                    const femalePercent = (this.$parent.nodeData.female_employees_number / this.$parent.nodeData.employees_number) * 15;

                    this.scoreFemalePer += femalePercent;
                }

                if (this.$parent.nodeData.operation_years_company < 8) {
                    if (this.$parent.nodeData.operation_years_company >= 1 && this.$parent.nodeData.operation_years_company <= 3)
                        this.scoreYears += 2;

                    if (this.$parent.nodeData.operation_years_company >= 4 && this.$parent.nodeData.operation_years_company <= 5)
                        this.scoreYears += 3;

                    if (this.$parent.nodeData.operation_years_company >= 6 && this.$parent.nodeData.operation_years_company <= 7)
                        this.scoreYears += 4;
                }

                if (this.$parent.nodeData.operation_years_company > 7)
                    this.scoreYears += 5;

                if (this.$parent.nodeData.total_income_tres >= 50000 && this.$parent.nodeData.total_income_tres <= 100000)
                    this.scoreIncome += 5;

                if (this.$parent.nodeData.total_income_tres > 100000 && this.$parent.nodeData.total_income_tres <= 1000000)
                    this.scoreIncome += 10;

                if (this.$parent.nodeData.total_income_tres > 1000000 && this.$parent.nodeData.total_income_tres <= 2000000)
                    this.scoreIncome += 15;

                if (this.$parent.nodeData.employees_number > 0 && this.$parent.nodeData.employees_number < 10)
                    this.scoreEmployees += 1;

                if (this.$parent.nodeData.employees_number > 9 && this.$parent.nodeData.employees_number < 50)
                    this.scoreEmployees += 3;

                if (this.$parent.nodeData.employees_number > 49 && this.$parent.nodeData.employees_number < 200)
                    this.scoreEmployees += 4;

                if (this.$parent.nodeData.employees_number > 199)
                    this.scoreEmployees += 5;

                const migrant_employees = Number(this.$parent.nodeData.migrant_employees) || 0;
                const refugee_employees = Number(this.$parent.nodeData.refugee_employees) || 0;
                const returned_employees = Number(this.$parent.nodeData.returned_employees) || 0;
                const disability_employees = Number(this.$parent.nodeData.disability_employees) || 0;
                const lgbt_employees = Number(this.$parent.nodeData.lgbt_employees) || 0;

                const priority_employees_number = migrant_employees + refugee_employees + returned_employees + disability_employees + lgbt_employees;
                this.priority_employees_number = priority_employees_number;

                if (priority_employees_number) {
                    const priorityPercent = (priority_employees_number / this.$parent.nodeData.employees_number) * 5;

                    this.scorePriEmployees += priorityPercent;

                }

                if (this.$parent.nodeData.has_social_responsability == 1)
                    this.scoreSocial += 2;

                if (this.$parent.nodeData.has_aditional_benefits == 1)
                    this.scoreBenef += 1;

                if (this.$parent.nodeData.has_diversity_politics == 1)
                    this.scoreDiversity += 2;

                const contribution_value = Number(this.$parent.nodeData.contribution_value) || 0;
                const total_amount__iom = Number(this.$parent.nodeData.total_amount__iom) || 0;
                const comparative = (contribution_value / (total_amount__iom * 1)) * 15;
                this.comparative = comparative
                if (comparative <= 15)
                    this.scoreCompare += comparative;

                if (comparative > 15)
                    this.scoreCompare += 15;

                const newStaffPercent = (2000 / (this.$parent.nodeData.total_amount__iom / this.$parent.nodeData.additional_staff_need)) * 25;
                this.newStaffPercent = newStaffPercent

                if (newStaffPercent <= 25)
                    this.scoreStaff += newStaffPercent;

                if (newStaffPercent > 25)
                    this.scoreStaff += 25;

                const scoreTotal = this.genderScore + this.scoreLeader + this.scoreFemalePer + this.scoreYears + this.scoreIncome + this.scoreEmployees + this.scorePriEmployees + this.scoreSocial + this.scoreBenef + this.scoreDiversity + this.scoreCompare + this.scoreStaff
                this.scoreTotal = scoreTotal
            }

        },
        mounted() {
            this.calculateScore();
        }
    });
</script>
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
        width: 450px;
        max-height: 400px;
        overflow-y: auto;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        background-color: #343a40 !important;
        color: #ffffff !important;
    }

    .list-group-item {
        border: none;
        background-color: #343a40 !important;
        color: #ffffff !important;
    }

    .large-text {
        font-size: 14px;
    }
</style>