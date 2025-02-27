<?= App\Core\Application::getInstance()->view->renderView('CustomTpls/EdfRelatedFormsComponent', []) ?>

<template id="tpl-score-summary-component">
    <div class="fixed-badge">
        <button type="button" class="btn btn-primary" @click="toggleCollapse">
            Puntaje <span class="badge bg-secondary large-text">{{ Number(this.$parent.nodeData.score).toFixed(2) }}</span>
        </button>

        <div v-if="isCollapsed" class="card card-custom ">
            <div class="card-body p-2 text-white bg-primary">
                <h5>Resumen de puntaje</h5>
            </div>
            <ul class="list-group list-group-flush text-white bg-dark ">
                <li class="list-group-item"><span class="me-1"><strong>Costo por trabajo:[{{aditional_staff_value}}/({{iom_contribution}}/2000)]*25</strong></span> <span> </span> <span class="badge bg-primary"> Puntaje: {{scorePerJob}}</span> </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿La cantidad que el propietario de la empresa propone contribuir es comparable a la cantidad que solicita?
                            ({{business_contr}} / ({{iom_contribution}})) * 10 </strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{(compScore).toFixed(2)}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Años de operación del negocio: ({{Number(years).toFixed(1)}} / 7) * 5</strong></span>
                    <span> </span>
                    <span class="badge bg-primary"> Puntaje: {{(yearsScore).toFixed(2)}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Genero del representante legal: {{repGender}} </strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreGender}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿Hay actualmente más trabajadoras mujeres que trabajadores hombres?
                            ({{femaleNum}} / {{employeesNum}}) * 5
                        </strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{(scoreFemaleEmp).toFixed(2)}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Número de empleados de grupos considerados prioritarios: ({{employeesPrio}} / {{employeesNum}}) * 5</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{(scorePrio).toFixed(2)}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿La ubicación es buena para el negocio?: {{this.$parent.nodeData.business_location_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreLocation}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿Es el entorno empresarial seguro e inclusivo para todos los empleados? (incluyendo baños, áreas sociales, condiciones de trabajo, uso seguro de equipos, EPP, medidas ignífugas, medidas de seguridad, y otros dependiendo de cada negocio): {{this.$parent.nodeData.business_safe_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreEnv}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿Cumple la empresa con las normas laborales y las buenas prácticas empresariales?: {{this.$parent.nodeData.standards_practices_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreStand}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>Durante su visita, ¿observó las actividades comerciales o pudieron demostrar actividades recientes relacionadas con el negocio (incluidos los clientes, el personal que trabaja, la producción, el equipo en uso, otros)?: {{this.$parent.nodeData.observe_business_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreObserve}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿Recomienda este negocio para la subvención del EDF?: {{this.$parent.nodeData.edf_recommend_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreRecom}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1 text-warning"><strong>Salario trabajador 1: {{this.$parent.nodeData.employee_ver[0].salary}}</strong></span>
                    <span class="me-1 text-info"><strong>Salario trabajador 2: {{this.$parent.nodeData.employee_ver[1].salary}}</strong></span>
                    <span class="me-1"><strong>Promedio de salarios de los 2 trabajadores: {{averageSalary}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreSalary}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿La empresa paga su salario a tiempo?</strong></span>
                    <span class="me-1 text-warning"><strong>Trabajador 1: {{this.$parent.nodeData.employee_ver[0].time_pay_display}}</strong></span>
                    <span class="me-1 text-info"><strong>Trabajador 2: {{this.$parent.nodeData.employee_ver[1].time_pay_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scorePayTime}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿Recibes algún incentivo (por ejemplo, bonificación, horas extras, cuidado de niños, transporte, formación)?</strong></span>
                    <span class="me-1 text-warning"><strong>Trabajador 1: {{this.$parent.nodeData.employee_ver[0].incentives_display}}</strong></span>
                    <span class="me-1 text-info"><strong>Trabajador 2: {{this.$parent.nodeData.employee_ver[1].incentives_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreIncentives}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿Crees que tu trabajo es sostenible?</strong></span>
                    <span class="me-1 text-warning"><strong>Trabajador 1: {{this.$parent.nodeData.employee_ver[0].sustainable_job_display}}</strong></span>
                    <span class="me-1 text-info"><strong>Trabajador 2: {{this.$parent.nodeData.employee_ver[1].sustainable_job_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreSustainableJob}}</span>
                </li>
                <hr class="mt-1 mb-1" />
                <li class="list-group-item">
                    <span class="me-1"><strong>¿Es este un buen ambiente de trabajo?</strong></span>
                    <span class="me-1 text-warning"><strong>Trabajador 1: {{this.$parent.nodeData.employee_ver[0].environment_work_display}}</strong></span>
                    <span class="me-1 text-info"><strong>Trabajador 2: {{this.$parent.nodeData.employee_ver[1].environment_work_display}}</strong></span>
                    <span>
                    </span>
                    <span class="badge bg-primary"> Puntaje: {{scoreEnvironment}}</span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    Vue.component('score-summary-component', {
        template: '#tpl-score-summary-component',
        data() {
            return {
                score: this.$parent.nodeData.score,
                eoiData: null,
                business_contr: 0,
                iom_contribution: 0,
                aditional_staff_value: 0,
                scorePerJob: 0,
                compScore: 0,
                years: 0,
                yearsScore: 0,
                scoreGender: 0,
                scoreFemaleEmp: 0,
                repGender: null,
                femaleNum: 0,
                employeesNum: 0,
                scorePrio: 0,
                employeesPrio: 0,
                scoreLocation: 0,
                scoreEnv: 0,
                scoreStand: 0,
                scoreObserve: 0,
                scoreRecom: 0,
                averageSalary: 0,
                scoreSalary: 0,
                scorePayTime: 0,
                scoreIncentives: 0,
                scoreSustainableJob: 0,
                scoreEnvironment: 0,
                isCollapsed: false,
            };
        },
        mounted() {
            this.edfEoiData();
        },
        methods: {
            toggleCollapse() {
                this.isCollapsed = !this.isCollapsed;
            },
            edfEoiData() {
                const eoiId = this.$parent.nodeData.business_id;
                axios.get(`/InternalApi/RetrieveEdfEoiData/index?id=${eoiId}`)
                    .then(response => {
                        this.eoiData = response.data;
                        this.calculateScore();
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos:', error);
                    });
            },
            calculateScore() {
                let business_contr = this.eoiData.contribution_value;
                let iom_contribution = this.eoiData.total_amount__iom;
                let aditional_staff_value = this.eoiData.additional_staff_need;

                if (this.$parent.nodeData.correct_contribution_bsns && this.$parent.nodeData.correct_grant_iom) {
                    // Both values are set
                    business_contr = this.$parent.nodeData.correct_contribution_bsns;
                    iom_contribution = this.$parent.nodeData.correct_grant_iom;
                } else if (this.$parent.nodeData.correct_contribution_bsns) {
                    // Only correct_contribution_bsns is set
                    business_contr = this.$parent.nodeData.correct_contribution_bsns;
                } else if (this.$parent.nodeData.correct_grant_iom) {
                    // Only correct_grant_iom is set
                    iom_contribution = this.$parent.nodeData.correct_grant_iom;
                }
                if (this.$parent.nodeData.correct_aditional_staff)
                    aditional_staff_value = this.$parent.nodeData.correct_aditional_staff;

                this.business_contr = business_contr;
                this.iom_contribution = iom_contribution;
                this.aditional_staff_value = aditional_staff_value;
                const investment = (aditional_staff_value / (iom_contribution / 2000) * 25);
                const scorePerJob = investment > 25 ? 25 : investment;
                this.scorePerJob = scorePerJob;

                // Is the amount the business owner is proposing to contribute comparable to the amount they are requesting?
                const comp = (business_contr / (iom_contribution)) * 10;
                const compScore = comp > 10 ? 10 : comp;
                this.compScore = compScore;

                //Years of operation
                if (this.$parent.nodeData.correct_years_operation) {
                    this.years = this.$parent.nodeData.correct_years_operation;
                    if (this.$parent.nodeData.correct_years_operation > 7)
                        this.yearsScore = 5;

                    if (this.$parent.nodeData.correct_years_operation > 1 && this.$parent.nodeData.correct_years_operation <= 7)
                        this.yearsScore = (this.$parent.nodeData.correct_years_operation / 7) * 5;
                }
                if (!this.$parent.nodeData.correct_years_operation) {
                    this.years = this.eoiData.operation_years_company;

                    if (this.eoiData.operation_years_company > 7)
                        this.yearsScore = 5;

                    if (this.eoiData.operation_years_company > 1 && this.eoiData.operation_years_company <= 7)
                        this.yearsScore = (this.eoiData.operation_years_company / 7) * 5;
                }

                //Is the legal representative a woman?
                if (this.$parent.nodeData.correct_gender_rep == 2)
                    this.scoreGender = 5;

                if (this.$parent.nodeData.correct_gender_rep)
                    this.repGender = this.$parent.nodeData.correct_gender_rep_display;

                if (!this.$parent.nodeData.correct_gender_rep && this.eoiData.gender_legal_rep == 2)
                    this.scoreGender = 5;

                if (!this.$parent.nodeData.correct_gender_rep)
                    this.repGender = this.eoiData.gender_legal_rep_display;

                //Is there more female workers than male workers currently?
                if (this.$parent.nodeData.correct_current_employees) {
                    this.scoreFemaleEmp = (this.$parent.nodeData.correct_number_female_employees / this.$parent.nodeData.correct_current_employees) * 5;
                    this.femaleNum = this.$parent.nodeData.correct_number_female_employees;
                    this.employeesNum = this.$parent.nodeData.correct_current_employees;
                }

                if (!this.$parent.nodeData.correct_current_employees) {
                    this.scoreFemaleEmp = (this.eoiData.female_employees_number / this.eoiData.employees_number) * 5;
                    this.femaleNum = this.eoiData.female_employees_number;
                    this.employeesNum = this.eoiData.employees_number;
                }

                //Number of employees from groups considered priority
                if (this.$parent.nodeData.confirm_priority_employees == 0) {
                    const migrant_employees = Number(this.$parent.nodeData.correct_migrant_employees) || 0;
                    const refugee_employees = Number(this.$parent.nodeData.correct_refugee_employees) || 0;
                    const returned_employees = Number(this.$parent.nodeData.correct_returned_employees) || 0;
                    const disability_employees = Number(this.$parent.nodeData.correct_disability_employees) || 0;
                    const lgbt_employees = Number(this.$parent.nodeData.correct_lgbt_employees) || 0;

                    const priorityNum = migrant_employees + refugee_employees + returned_employees + disability_employees + lgbt_employees;
                    this.scorePrio = (priorityNum / this.$parent.nodeData.correct_current_employees) * 5;
                    this.employeesPrio = priorityNum;
                } else {
                    const migrant_employees = Number(this.eoiData.migrant_employees) || 0;
                    const refugee_employees = Number(this.eoiData.refugee_employees) || 0;
                    const returned_employees = Number(this.eoiData.returned_employees) || 0;
                    const disability_employees = Number(this.eoiData.disability_employees) || 0;
                    const lgbt_employees = Number(this.eoiData.lgbt_employees) || 0;

                    const priority_employees_number = migrant_employees + refugee_employees + returned_employees + disability_employees + lgbt_employees;
                    if (priority_employees_number) {
                        const priorityPercent = (priority_employees_number / this.eoiData.employees_number) * 5;
                        this.scorePrio = priorityPercent;
                        this.employeesPrio = priority_employees_number;

                    }
                }

                //  Is the location good for business
                if (this.$parent.nodeData.business_location == 1)
                    this.scoreLocation = 2.5;

                //  Is the business environment safe and inclusive for all employees? (including bathrooms, social areas, working conditions, safe use of equipment, PPE, fireproof measures, security measures, and others depending on each business)
                if (this.$parent.nodeData.business_safe == 1)
                    this.scoreEnv = 2.5;

                //Is the business compliant with labour standards and good buisness practices?
                if (this.$parent.nodeData.standards_practices == 1)
                    this.scoreStand = 2.5;

                //During your visit, did you observe business activities or were they able to demonstrate  recent activity related to the business (including customers, staff working, production, equipment in use, other)?
                if (this.$parent.nodeData.observe_business == 1)
                    this.scoreObserve = 2.5;

                //Do you recommend this business for EDF grant?
                if (this.$parent.nodeData.edf_recommend == 1)
                    this.scoreRecom = 2.5;

                if (this.$parent.nodeData.employee_ver) {
                    //What is your average monthly salary?
                    const salaryOne = Number(this.$parent.nodeData.employee_ver[0].salary) || 0;
                    const salaryTwo = Number(this.$parent.nodeData.employee_ver[1].salary) || 0;
                    this.averageSalary = (salaryOne + salaryTwo) / 2;

                    if (this.averageSalary >= 460)
                        this.scoreSalary = 6;

                    //Does the business pay your salary on time?
                    if (this.$parent.nodeData.employee_ver[0].time_pay == 1 && this.$parent.nodeData.employee_ver[1].time_pay == 1)
                        this.scorePayTime = 6;

                    if (this.$parent.nodeData.employee_ver[0].time_pay == 1 && this.$parent.nodeData.employee_ver[1].time_pay == 0)
                        this.scorePayTime = 3;

                    if (this.$parent.nodeData.employee_ver[0].time_pay == 0 && this.$parent.nodeData.employee_ver[1].time_pay == 1)
                        this.scorePayTime = 3;

                    //Do you receive any incentives (e.g. bonus, overtime, childcare, transport, trainings)?
                    if (this.$parent.nodeData.employee_ver[0].incentives == 1 && this.$parent.nodeData.employee_ver[1].incentives == 1)
                        this.scoreIncentives = 6;

                    if (this.$parent.nodeData.employee_ver[0].incentives == 1 && this.$parent.nodeData.employee_ver[1].incentives == 0)
                        this.scoreIncentives = 3;

                    if (this.$parent.nodeData.employee_ver[0].incentives == 0 && this.$parent.nodeData.employee_ver[1].incentives == 1)
                        this.scoreIncentives = 3;

                    //Do you think your job is sustainable?
                    if (this.$parent.nodeData.employee_ver[0].sustainable_job == 1 && this.$parent.nodeData.employee_ver[1].sustainable_job == 1)
                        this.scoreSustainableJob = 6;

                    if (this.$parent.nodeData.employee_ver[0].sustainable_job == 1 && this.$parent.nodeData.employee_ver[1].sustainable_job == 0)
                        this.scoreSustainableJob = 3;

                    if (this.$parent.nodeData.employee_ver[0].sustainable_job == 0 && this.$parent.nodeData.employee_ver[1].sustainable_job == 1)
                        this.scoreSustainableJob = 3;

                    //Is this a good working environment?
                    if (this.$parent.nodeData.employee_ver[0].environment_work == 1 && this.$parent.nodeData.employee_ver[1].environment_work == 1)
                        this.scoreEnvironment = 6;

                    if (this.$parent.nodeData.employee_ver[0].environment_work == 1 && this.$parent.nodeData.employee_ver[1].environment_work == 0)
                        this.scoreEnvironment = 3;

                    if (this.$parent.nodeData.employee_ver[0].environment_work == 0 && this.$parent.nodeData.employee_ver[1].environment_work == 1)
                        this.scoreEnvironment = 3;
                }

            }

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