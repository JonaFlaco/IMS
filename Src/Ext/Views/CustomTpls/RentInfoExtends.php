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
    }
  </style>
</head>

<template id="tpl-show-home-component">
  <div>
    <div class="card-body text-center p-4" style="background-color: #f8f9fa; border-radius: 8px;">
      <h5 class="card-title mb-3">Contacto del arrendatario</h5>
      <a :href="`mailto:${rentInfoHome.rent_email}
                ?subject=Estado%20de%20su%20Perfil%20de%20usuario%0A
                
                ?cc=rmorillo@iom.int, pmejia@iom.int, fcastrillon@iom.int, ecarbo@iom.int, 
                euaraujo@iom.int, sgallardo@iom.int, dchiang@iom.int

                &body=Buen%20día%20estimado%20${rentInfoHome.rent_name}%0A
                Con%20número%20de%20CI:%20${rentInfoHome.rent_id_no}%0A%0A
                Espero%20que%20este%20correo%20lo%20encuentre%20bien.%0A%0A
                Me%20complace%20informarle%20que%20tras%20revisar%20su%20usuario%20con%20los%20siguientes%20datos%3A%0A
                Nombres%20y%20Apellidos%3A%20${rentInfoHome.rent_name}%0A
                CI%3A%20${rentInfoHome.rent_id_no}%0A
                Nacionalidad%3A%20${rentInfoHome.nacionality_display}%0A
                Provincia%20donde%20vive%3A%20${rentInfoHome.province_rent_display}%0A
                Cantón%20donde%20vive%3A%20${rentInfoHome.canton_rent_display}%0A%0A
                Se%20ha%20decidido%20aprobar%20su%20perfil%20para%20entrar%20al%20programa%20de%20arrendadores.%0A%0A
                Gracias%20por%20su%20atención%2C%0A
                Saludos%20cordiales.%0A
                Asistencia%20Humanitaria`"
        class="btn btn-primary me-2">
        Correo de Contacto
      </a>

      <a :href="`https://wa.me/593${rentInfoHome.rent_phone}?
                text=Buen%20día%20estimado%20${rentInfoHome.rent_name}%0A
                Con%20número%20de%20CI:%20${rentInfoHome.rent_id_no}%0A%0A
                Espero%20que%20este%20correo%20lo%20encuentre%20bien.%0A%0A
                Me%20complace%20informarle%20que%20tras%20revisar%20su%20usuario%20con%20los%20siguientes%20datos%3A%0A
                Nombres%20y%20Apellidos%3A%20${rentInfoHome.rent_name}%0A
                CI%3A%20${rentInfoHome.rent_id_no}%0A
                Género%3A%20${rentInfoHome.gender_display}%0A
                Nacionalidad%3A%20${rentInfoHome.nacionality_display}%0A
                Provincia%20donde%20vive%3A%20${rentInfoHome.province_rent_display}%0A
                Cantón%20donde%20vive%3A%20${rentInfoHome.canton_rent_display}%0A%0A
                Se%20ha%20decidido%20aprobar%20su%20perfil%20para%20entrar%20al%20programa%20de%20arrendadores.%0A%0A
                Gracias%20por%20su%20atención%2C%0A
                Saludos%20cordiales.%0A
                Asistencia%20Humanitaria`" 
        class="btn btn-success"
        target="_blank">
        Número de Teléfono
      </a>
    </div>

    <div class="row">
      <div v-for="vivienda in rentInfoHome?.user_form_property" :key="vivienda.id" class="col-md-4">
        <div class="card mb-4">

          <!-- Carrusel de imágenes -->
          <div :id="`carousel-${vivienda.id}`" class="carousel slide" data-interval="false"
            aria-label="Galería de imágenes">
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

            <a class="carousel-control-prev" :href="`#carousel-${vivienda.id}`" role="button" data-slide="prev"
              aria-label="Anterior">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Anterior</span>
            </a>
            <a class="carousel-control-next" :href="`#carousel-${vivienda.id}`" role="button" data-slide="next"
              aria-label="Siguiente">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Siguiente</span>
            </a>
          </div>

          <!-- Información de la vivienda -->
          <div class="card-body">
            <h5 class="card-title"><strong>Tipo de Vivienda:</strong> {{ vivienda.property_type_display }}</h5>
            <!-- <h5 class="card-title"><strong>Puntaje de Vivienda:</strong> {{ vivienda.score }}</h5> -->
          </div>
          <ul class="list-group list-group-flush">

            <h5>Propiedad</h5>
            <button class="btn btn-primary mb-2" @click="toggleVisibility('propiedad', vivienda.id)">
              <i
                :class="{'fas fa-chevron-down': !isVisible('propiedad', vivienda.id), 'fas fa-chevron-up': isVisible('propiedad', vivienda.id)}"></i>
            </button>
            <div :id="`propiedad-${vivienda.id}`" v-if="isVisible('propiedad', vivienda.id)">
              <li class="list-group-item"><strong>La casa está disponible:</strong> {{
                vivienda.availability_rent_display }}</li>
              <li class="list-group-item"><strong>Precio de renta:</strong> ${{ vivienda.rental_price }}</li>
            </div>

            <h5>Ubicación</h5>
            <button class="btn btn-primary mb-2" @click="toggleVisibility('ubicacion', vivienda.id)">
              <i
                :class="{'fas fa-chevron-down': !isVisible('ubicacion', vivienda.id), 'fas fa-chevron-up': isVisible('ubicacion', vivienda.id)}"></i>
            </button>
            <div :id="`ubicacion-${vivienda.id}`" v-if="isVisible('ubicacion', vivienda.id)">
              <li class="list-group-item"><strong>Provincia:</strong> {{ vivienda.province_display }}</li>
              <li class="list-group-item"><strong>Ciudad:</strong> {{ vivienda.city_display }}</li>
              <li class="list-group-item"><strong>Dirección:</strong> {{ vivienda.addres }}</li>
              <!-- <li class="list-group-item"><strong>Ubicación Actual:</strong> {{ vivienda.address_gps_lat }}, {{ vivienda.address_gps_lng }}</li> -->
              <li class="list-group-item"><locate-gps-lat-component v-if="vivienda.address_gps_lat && vivienda.address_gps_lng"
                  :locate_gps_lat="vivienda.address_gps_lat" :locate_gps_lng="vivienda.address_gps_lng">
                </locate-gps-lat-component></li>
            </div>

            <h5>Dormitorios</h5>
            <button class="btn btn-primary mb-2" @click="toggleVisibility('dormitorios', vivienda.id)">
              <i
                :class="{'fas fa-chevron-down': !isVisible('dormitorios', vivienda.id), 'fas fa-chevron-up': isVisible('dormitorios', vivienda.id)}"></i>
            </button>
            <div :id="`dormitorios-${vivienda.id}`" v-if="isVisible('dormitorios', vivienda.id)">
              <li class="list-group-item"><strong>¿El baño esta dentro de la vivienda?(Privado):</strong> {{
                vivienda.private_bathroom_display }}</li>
              <li class="list-group-item"><strong>¿Cuantas personas pueden dormir en una habitacion?</strong> {{
                vivienda.room_occupancy_display }}</li>
            </div>
            <div>
              <center>
                <h5 class="card-title mb-3">Enviar Mensaje/Correo acerca de la vivienda</h5>
              </center>
              <div class="d-flex justify-content-center align-items-center">
                <a :href="`mailto:${rentInfoHome.rent_email}

                ?subject=Estado%20de%20la%20Vivienda%20tipo%20${vivienda.property_type_display}

                &body=Estimado/a%20${rentInfoHome.rent_name}%0A
                Con%20Número%20de%20CI:%20${rentInfoHome.rent_id_no},%0A%0A

                Espero%20que%20este%20mensaje%20le%20encuentre%20bien.%0A%0A
                Me%20complace%20informarle%20que,%20tras%20revisar%20la%20propiedad%20ubicada%20en:%0A

                Provincia:%20${ vivienda.province_display }%0A
                Ciudad:%20${ vivienda.city_display }%0A
                Direccion:%20${ vivienda.addres }%0A%0A

                Con%20el%20precio%20de%20arriendo%20de:%20$${vivienda.rental_price}%0A%0A

                %20Se%20ha%20decidido%20aceptar%20para%20entrar%20al%20programa%20de%20arrendamiento%0A%0A

                Gracias%20nuevamente%20por%20su%20atención,%0A
                Saludos%20cordiales.%0A
                Asistencia%20Humanitaria.%0A`"
                  class=" btn btn-primary me-2">
                  Enviar Correo
                </a>

                <a :href="`https://wa.me/593${rentInfoHome.rent_phone}?
                text=Estimado/a%20${rentInfoHome.rent_name},
                %20Con%20Número%20de%20CI:%20${rentInfoHome.rent_id_no},%0A%0A
                Espero%20que%20este%20mensaje%20le%20encuentre%20bien.%0A%0A
                Me%20complace%20informarle%20que,%20tras%20revisar%20la%20propiedad%20ubicada%20en:%0A%0A
                Provincia:%20${vivienda.province_display}%0A%0A
                Ciudad:%20${vivienda.city_display}%0A%0A
                Dirección:%20${vivienda.addres}%0A%0A
                Con%20el%20precio%20de%20arriendo%20de:%20$${vivienda.rental_price}%0A%0A
                Se%20ha%20decidido%20aceptar%20para%20entrar%20al%20programa%20de%20arrendamiento.%0A%0A
                Gracias%20nuevamente%20por%20su%20atención,%0A%0A
                Saludos%20cordiales.%0A%0A
                Asistencia%20Humanitaria.`"
                  class="btn btn-success"
                  target="_blank">
                  Enviar WhatsApp
                </a>
              </div>
            </div>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  Vue.component('show-home-component', {
    template: '#tpl-show-home-component',
    data() {
      return {
        rentInfoHome: {},
        visibility: {
          propiedad: {},
          ubicacion: {},
          dormitorios: {},
        },
      };
    },
    mounted() {
      this.homeData();
    },
    methods: {
      homeData() {
        const id = this.$parent.nodeData.id;
        axios.get(`/InternalApi/RetrieveHomeInfo/index?id=${id}`)
          .then(response => {
            this.rentInfoHome = response.data;
          })
          .catch(error => {
            console.error('Error al obtener los datos de la vivienda:', error);
          });
      },
      toggleVisibility(section, id) {
        this.$set(this.visibility[section], id, !this.visibility[section][id]);
      },
      isVisible(section, id) {
        return !!this.visibility[section][id];
      },
    }
  });
</script>

<template id="tpl-locate-gps-lat-component">
  <p class="card-p">
    <span class="me-1"><strong>Ubicación en el mapa:</strong></span>
    <span>
      {{ locate_gps_lat }} {{ locate_gps_lng }}
      <a :href="googleMapsLink" target="_blank">
        <i class="text-primary mdi mdi-google-maps has-tooltip" style="font-size: 36px;"></i>
      </a>
    </span>
  </p>
</template>

<script>
  Vue.component('locate-gps-lat-component', {
    template: '#tpl-locate-gps-lat-component',
    props: {
      locate_gps_lat: {
        type: Number,
        required: true
      },
      locate_gps_lng: {
        type: Number,
        required: true
      }
    },
    computed: {
      googleMapsLink() {
        const latDMS = this.convertToDMS(this.locate_gps_lat, true);
        const lngDMS = this.convertToDMS(this.locate_gps_lng, false);
        return `https://www.google.com/maps/place/${latDMS}+${lngDMS}/@${this.locate_gps_lat},${this.locate_gps_lng},17z`;
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

<template id="tpl-user-form-property-list-component">
  <div>
  </div>
</template>

<script>
  Vue.component('user-form-property-list-component', {
    template: '#tpl-user-form-property-list-component',
    data() {
      return {};
    },
    mounted() {},
    methods: {}
  });
</script>

<template id="tpl-count-houses-component">
  <div>
    <p class="card-p"><span class="me-1"><strong>Total de viviendas: {{rentInfoHome.rent_house}}</strong></span> <span> </span></p>
  </div>
</template>

<script>
  Vue.component('count-houses-component', {
    template: '#tpl-count-houses-component',
    data() {
      return {
        rentInfoHome: {}
      };
    },
    mounted() {
      this.homeData();
    },
    methods: {
      homeData() {
        const id = this.$parent.nodeData.id;
        axios.get(`/InternalApi/RetrieveHomeInfo/index?id=${id}`)
          .then(response => {
            this.rentInfoHome = response.data;
          })
          .catch(error => {
            console.error('Error al obtener los datos de la vivienda:', error);
          });
      },
    }
  });
</script>