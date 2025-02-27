<template id="tpl-show-hito-component">
    <div class="card text-white bg-dark">
        <div v-for="cases in caseAbp" class="card-body">
            <div class="card-header bg-primary">
                <h5 class="card-title">{{cases.code}}</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-dark text-white"><strong>Costo total del hito: </strong>{{cases.milestones[0].total_milestone_amount}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Subvención OIM: </strong>{{cases.milestones[0].milestone_amount_one}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Contribución de la empresa: </strong>{{cases.milestones[0].contribution_amount}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Número de empleados: </strong>{{cases.milestones[0].new_workers}}</li>
            </ul>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-dark text-white"><strong>Actividad: </strong>{{cases.milestones[0].activity}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Medios de verificación: </strong>{{cases.milestones[0].mean_verification}}</li>
            </ul>
        </div>
    </div>
</template>

<script>
    Vue.component('show-hito-component', {
        template: '#tpl-show-hito-component',
        data() {
            return {
                caseAbp: [],
            };
        },
        watch: {
            '$parent.enterprise.id': {
                immediate: true,
                handler(newBsnsId) {
                    this.getApprovedBusinessPlan(newBsnsId);
                }
            }
        },
        mounted() {

        },
        methods: {
            getApprovedBusinessPlan(BsnsId) {
                if (BsnsId) {
                    axios.get(`/InternalApi/RetrieveApprovedBusinessPlan/index?id=${BsnsId}`)
                        .then(response => {
                            this.caseAbp = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener información del negocio:', error);
                            this.caseAbp = [];
                        });
                } else {
                    this.caseAbp = [];
                }
            },

        }

    });
</script>

<template id="tpl-show-hito-two-component">
    <div class="card text-white bg-dark">
        <div v-for="cases in caseAbp" class="card-body">
            <div class="card-header bg-primary">
                <h5 class="card-title">{{cases.code}}</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-dark text-white"><strong>Costo total del hito: </strong>{{cases.milestones[1].total_milestone_amount}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Subvención OIM: </strong>{{cases.milestones[1].milestone_amount_one}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Contribución de la empresa: </strong>{{cases.milestones[1].contribution_amount}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Número de empleados: </strong>{{cases.milestones[1].new_workers}}</li>
            </ul>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-dark text-white"><strong>Actividad: </strong>{{cases.milestones[1].activity}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Medios de verificación: </strong>{{cases.milestones[1].mean_verification}}</li>
            </ul>
        </div>
    </div>
</template>

<script>
    Vue.component('show-hito-two-component', {
        template: '#tpl-show-hito-two-component',
        data() {
            return {
                caseAbp: [],
            };
        },
        watch: {
            '$parent.enterprise.id': {
                immediate: true,
                handler(newBsnsId) {
                    this.getApprovedBusinessPlan(newBsnsId);
                }
            }
        },
        mounted() {

        },
        methods: {
            getApprovedBusinessPlan(BsnsId) {
                if (BsnsId) {
                    axios.get(`/InternalApi/RetrieveApprovedBusinessPlan/index?id=${BsnsId}`)
                        .then(response => {
                            this.caseAbp = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener información del negocio:', error);
                            this.caseAbp = [];
                        });
                } else {
                    this.caseAbp = [];
                }
            },

        }

    });
</script>

<template id="tpl-show-hito-three-component">
    <div class="card text-white bg-dark">
        <div v-for="cases in caseAbp" class="card-body">
            <div class="card-header bg-primary">
                <h5 class="card-title">{{cases.code}}</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-dark text-white"><strong>Costo total del hito: </strong>{{cases.milestones[2].total_milestone_amount}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Subvención OIM: </strong>{{cases.milestones[2].milestone_amount_one}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Contribución de la empresa: </strong>{{cases.milestones[2].contribution_amount}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Número de empleados: </strong>{{cases.milestones[2].new_workers}}</li>
            </ul>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-dark text-white"><strong>Actividad: </strong>{{cases.milestones[2].activity}}</li>
                <li class="list-group-item bg-dark text-white"><strong>Medios de verificación: </strong>{{cases.milestones[2].mean_verification}}</li>
            </ul>
        </div>
    </div>
</template>

<script>
    Vue.component('show-hito-three-component', {
        template: '#tpl-show-hito-three-component',
        data() {
            return {
                caseAbp: [],
            };
        },
        watch: {
            '$parent.enterprise.id': {
                immediate: true,
                handler(newBsnsId) {
                    this.getApprovedBusinessPlan(newBsnsId);
                }
            }
        },
        mounted() {

        },
        methods: {
            getApprovedBusinessPlan(BsnsId) {
                if (BsnsId) {
                    axios.get(`/InternalApi/RetrieveApprovedBusinessPlan/index?id=${BsnsId}`)
                        .then(response => {
                            this.caseAbp = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener información del negocio:', error);
                            this.caseAbp = [];
                        });
                } else {
                    this.caseAbp = [];
                }
            },

        }

    });
</script>

<template id="tpl-show-employes-component">
    <div v-for="cases in caseAbp" class="text-white bg-secondary card pt-1 px-1">
        <h5 class="card-title"><strong>Número total de empleados actuales: </strong>{{cases.current_employees}}</h5>
    </div>
</template>

<script>
    Vue.component('show-employes-component', {
        template: '#tpl-show-employes-component',
        data() {
            return {
                caseAbp: [],
            };
        },
        watch: {
            '$parent.enterprise.id': {
                immediate: true,
                handler(newBsnsId) {
                    this.getApprovedBusinessPlan(newBsnsId);
                }
            }
        },
        mounted() {

        },
        methods: {
            getApprovedBusinessPlan(BsnsId) {
                if (BsnsId) {
                    axios.get(`/InternalApi/RetrieveApprovedBusinessPlan/index?id=${BsnsId}`)
                        .then(response => {
                            this.caseAbp = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener información del negocio:', error);
                            this.caseAbp = [];
                        });
                } else {
                    this.caseAbp = [];
                }
            },

        }

    });
</script>

<template id="tpl-new-employes-component">
    <div v-for="cases in caseAbp" class="text-white bg-secondary card pt-1 px-1">
        <h5 class="card-title"><strong>Número total de nuevos empleados: </strong>{{cases.new_employees}}</h5>
    </div>
</template>

<script>
    Vue.component('new-employes-component', {
        template: '#tpl-new-employes-component',
        data() {
            return {
                caseAbp: [],
            };
        },
        watch: {
            '$parent.enterprise.id': {
                immediate: true,
                handler(newBsnsId) {
                    this.getApprovedBusinessPlan(newBsnsId);
                }
            }
        },
        mounted() {

        },
        methods: {
            getApprovedBusinessPlan(BsnsId) {
                if (BsnsId) {
                    axios.get(`/InternalApi/RetrieveApprovedBusinessPlan/index?id=${BsnsId}`)
                        .then(response => {
                            this.caseAbp = response.data;
                        })
                        .catch(error => {
                            console.error('Error al obtener información del negocio:', error);
                            this.caseAbp = [];
                        });
                } else {
                    this.caseAbp = [];
                }
            },

        }

    });
</script>