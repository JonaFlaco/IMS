<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .carousel-inner img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border: 3px solid black;
            border-radius: 15px;
        }
    </style>
</head>

<!-- ESCRITURAS VIVIENDA -->
<template id="tpl-show-writings-component">
    <div v-for="(vivienda, index) in rentInfoHome" :key="vivienda.id" class="col-md-4">
        <div v-if="vivienda.writings_house_name">
            <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=writings_house&size=orginal&file_name=${vivienda.writings_house_name}`"
                target="_blank">
                <img height="250" width="400"
                    :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=writings_house&size=orginal&file_name=${vivienda.writings_house_name}`"
                    alt="Escrituras de la vivienda" class="d-block w-100">
            </a>
        </div>
</template>

<script>
    Vue.component('show-writings-component', {
        template: '#tpl-show-writings-component',
        data() {
            return {
                rentInfoHome: null,
                activeIndex: 0
            };
        },
        mounted() {
            this.fetchRentInfo();
        },
        methods: {
            fetchRentInfo() {
                const serviceId = this.$parent.nodeData.code;
                console.log(serviceId);
                axios.get(`/InternalApi/RetrieveRentInfo/index?id=${serviceId}`)
                    .then(response => {
                        if (response.data && response.data) {
                            this.rentInfoHome = response.data;
                            console.log("Datos cargados:", this.rentInfoHome[0]);
                        } else {
                            console.error("No funciona tu huevada:", response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos:', error);
                    });
            }
        }
    });
</script>
<!-- FIN ESCRITURAS VIVIENDA -->

<!-- INICIO JUSTIFICACION DE PERTINENCIA -->
<template id="tpl-show-relevance-component">
    <div v-for="(vivienda, index) in rentInfoHome" :key="vivienda.id" class="col-md-4">
        <div v-if="vivienda.justification_relevance_name">
            <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=justification_relevance&size=orginal&file_name=${vivienda.justification_relevance_name}`"
                target="_blank">
                <img height="250" width="400"
                    :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=justification_relevance&size=orginal&file_name=${vivienda.justification_relevance_name}`"
                    alt="Justificacion de pertinencia" class="d-block w-100">
            </a>
        </div>
</template>

<script>
    Vue.component('show-relevance-component', {
        template: '#tpl-show-relevance-component',
        data() {
            return {
                rentInfoHome: null,
                activeIndex: 0
            };
        },
        mounted() {
            this.fetchRentInfo();
        },
        methods: {
            fetchRentInfo() {
                const serviceId = this.$parent.nodeData.code;
                console.log(serviceId);
                axios.get(`/InternalApi/RetrieveRentInfo/index?id=${serviceId}`)
                    .then(response => {
                        if (response.data && response.data) {
                            this.rentInfoHome = response.data;
                            console.log("Datos cargados:", this.rentInfoHome[0]);
                        } else {
                            console.error("No funciona tu huevada:", response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos:', error);
                    });
            }
        }
    });
</script>
<!-- FIN JUSTIFICACION DE PERTINENCIA -->

<!-- INICIO PLANTILLA DE LUZ -->
<template id="tpl-show-light-component">
    <div v-for="(vivienda, index) in rentInfoHome" :key="vivienda.id" class="col-md-4">
        <div v-if="vivienda.letter_light_name">
            <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=letter_light&size=orginal&file_name=${vivienda.letter_light_name}`"
                target="_blank">
                <img height="250" width="400"
                    :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=letter_light&size=orginal&file_name=${vivienda.letter_light_name}`"
                    alt="Justificacion de pertinencia" class="d-block w-100">
            </a>
        </div>
</template>

<script>
    Vue.component('show-light-component', {
        template: '#tpl-show-light-component',
        data() {
            return {
                rentInfoHome: null,
                activeIndex: 0
            };
        },
        mounted() {
            this.fetchRentInfo();
        },
        methods: {
            fetchRentInfo() {
                const serviceId = this.$parent.nodeData.code;
                console.log(serviceId);
                axios.get(`/InternalApi/RetrieveRentInfo/index?id=${serviceId}`)
                    .then(response => {
                        if (response.data && response.data) {
                            this.rentInfoHome = response.data;
                            console.log("Datos cargados:", this.rentInfoHome[0]);
                        } else {
                            console.error("No funciona tu huevada:", response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos:', error);
                    });
            }
        }
    });
</script>
<!-- FIN PLANTILLA DE LUZ -->

<!-- INICIO PLANTILLA DE AGUA -->
<template id="tpl-show-water-component">
    <div v-for="(vivienda, index) in rentInfoHome" :key="vivienda.id" class="col-md-4">
        <div v-if="vivienda.letter_water_name">
            <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=letter_water&size=orginal&file_name=${vivienda.letter_water_name}`"
                target="_blank">
                <img height="250" width="400"
                    :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=letter_water&size=orginal&file_name=${vivienda.letter_water_name}`"
                    alt="Justificacion de pertinencia" class="d-block w-100">
            </a>
        </div>
</template>

<script>
    Vue.component('show-water-component', {
        template: '#tpl-show-water-component',
        data() {
            return {
                rentInfoHome: null,
                activeIndex: 0
            };
        },
        mounted() {
            this.fetchRentInfo();
        },
        methods: {
            fetchRentInfo() {
                const serviceId = this.$parent.nodeData.code;
                console.log(serviceId);
                axios.get(`/InternalApi/RetrieveRentInfo/index?id=${serviceId}`)
                    .then(response => {
                        if (response.data && response.data) {
                            this.rentInfoHome = response.data;
                            console.log("Datos cargados:", this.rentInfoHome[0]);
                        } else {
                            console.error("No funciona tu huevada:", response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos:', error);
                    });
            }
        }
    });
</script>
<!-- FIN PLANTILLA DE AGUA -->

<!-- INICIO PLANTILLA TELEFONICA -->
<template id="tpl-show-phone-component">
    <div v-for="(vivienda, index) in rentInfoHome" :key="vivienda.id" class="col-md-4">
        <div v-if="vivienda.letter_phone_name">
            <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=letter_phone&size=orginal&file_name=${vivienda.letter_phone_name}`"
                target="_blank">
                <img height="250" width="400"
                    :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=letter_phone&size=orginal&file_name=${vivienda.letter_phone_name}`"
                    alt="Justificacion de pertinencia" class="d-block w-100">
            </a>
        </div>
</template>

<script>
    Vue.component('show-phone-component', {
        template: '#tpl-show-phone-component',
        data() {
            return {
                rentInfoHome: null,
                activeIndex: 0
            };
        },
        mounted() {
            this.fetchRentInfo();
        },
        methods: {
            fetchRentInfo() {
                const serviceId = this.$parent.nodeData.code;
                console.log(serviceId);
                axios.get(`/InternalApi/RetrieveRentInfo/index?id=${serviceId}`)
                    .then(response => {
                        if (response.data && response.data) {
                            this.rentInfoHome = response.data;
                            console.log("Datos cargados:", this.rentInfoHome[0]);
                        } else {
                            console.error("No funciona tu huevada:", response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos:', error);
                    });
            }
        }
    });
</script>
<!-- FIN PLANTILLA TELEFONICA -->

<!-- INICIO IMPUESTOS PREDIAL -->
<template id="tpl-show-tax-component">
    <div v-for="(vivienda, index) in rentInfoHome" :key="vivienda.id" class="col-md-4">
        <div v-if="vivienda.property_tax">
            <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=property_tax&size=orginal&file_name=${vivienda.property_tax_name}`"
                target="_blank">
                <img height="250" width="400"
                    :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=property_tax&size=orginal&file_name=${vivienda.property_tax_name}`"
                    alt="Justificacion de pertinencia" class="d-block w-100">
            </a>
        </div>
    </div>
</template>

<script>
    Vue.component('show-tax-component', {
        template: '#tpl-show-tax-component',
        data() {
            return {
                rentInfoHome: null,
                activeIndex: 0
            };
        },
        mounted() {
            this.fetchRentInfo();
        },
        methods: {
            fetchRentInfo() {
                const serviceId = this.$parent.nodeData.code;
                console.log(serviceId);
                axios.get(`/InternalApi/RetrieveRentInfo/index?id=${serviceId}`)
                    .then(response => {
                        if (response.data && response.data) {
                            this.rentInfoHome = response.data;
                            console.log("Datos cargados:", this.rentInfoHome[0]);
                        } else {
                            console.error("No funciona tu huevada:", response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos:', error);
                    });
            }
        }
    });
</script>
<!-- FIN IMPUESTOS PREDIAL -->

<!-- INICIO CARRUSEL DE IMAGENES -->
<template id="tpl-show-home-component">
    <div v-for="(vivienda, index) in rentInfoHome" :key="vivienda.id" class="col-md-4">
        <div :id="'carouselExampleControls-' + vivienda.id" class="carousel slide">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_house&size=original&file_name=${vivienda.picture_house_name}`"
                        target="_blank">
                        <img height="250" width="400"
                            :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_house&size=original&file_name=${vivienda.picture_house_name}`"
                            alt="Foto Vivienda" class="d-block w-100">
                    </a>
                </div>
                <div v-if="vivienda.picture_bathroom_name" class="carousel-item">
                    <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_bathroom&size=original&file_name=${vivienda.picture_bathroom_name}`"
                        target="_blank">
                        <img height="250" width="400"
                            :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_bathroom&size=original&file_name=${vivienda.picture_bathroom_name}`"
                            alt="Foto Baño" class="d-block w-100">
                    </a>
                </div>
                <div v-if="vivienda.picture_kitchen_name" class="carousel-item">
                    <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_kitchen&size=original&file_name=${vivienda.picture_kitchen_name}`"
                        target="_blank">
                        <img height="250" width="400"
                            :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_kitchen&size=original&file_name=${vivienda.picture_kitchen_name}`"
                            alt="Foto Cocina" class="d-block w-100">
                    </a>
                </div>
                <div v-if="vivienda.picture_hall_name" class="carousel-item">
                    <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_hall&size=original&file_name=${vivienda.picture_hall_name}`"
                        target="_blank">
                        <img height="250" width="400"
                            :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_hall&size=original&file_name=${vivienda.picture_hall_name}`"
                            alt="Foto Hall" class="d-block w-100">
                    </a>
                </div>
                <div v-for="habitacion in vivienda.picture_room" :key="habitacion.id" class="carousel-item">
                    <a :href="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_room&size=original&file_name=${habitacion.name}`"
                        target="_blank">
                        <img height="250" width="400"
                            :src="`/filedownload?ctype_id=rent_info_user_form_property&field_name=picture_room&size=original&file_name=${habitacion.name}`"
                            alt="Foto Habitación" class="d-block w-100">
                    </a>
                </div>
            </div>
            <a class="carousel-control-prev" :href="'#carouselExampleControls-' + vivienda.id" role="button" data-slide="prev" aria-label="Anterior">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only"></span>
            </a>
            <a class="carousel-control-next" :href="'#carouselExampleControls-' + vivienda.id" role="button" data-slide="next" aria-label="Siguiente">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only"></span>
            </a>
        </div>
    </div>
</template>

<script>
    Vue.component('show-home-component', {
        template: '#tpl-show-home-component',
        data() {
            return {
                rentInfoHome: null,
                activeIndex: 0
            };
        },
        mounted() {
            this.fetchRentInfo();
            $(this.$el).find('.carousel').carousel();
        },
        methods: {
            fetchRentInfo() {
                const serviceId = this.$parent.nodeData.code;
                console.log(serviceId);
                axios.get(`/InternalApi/RetrieveRentInfo/index?id=${serviceId}`)
                    .then(response => {
                        if (response.data && response.data) {
                            this.rentInfoHome = response.data;
                            console.log("Datos cargados:", this.rentInfoHome[0]);
                        } else {
                            console.error("No funciona tu huevada:", response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos:', error);
                    });
            }
        }
    });
</script>
<!-- FIN CARRUSEL DE IMAGENES -->