<template id="tpl-receive-information-component">
    <div id="div_receive_information" class="mb-3 col-md-12">
        <div id="receive_information" class="mb-3 col-md-12">
            <label for="receive_information" class="form-label">
                ¿Por qué medio desea recibir la información?<span class="ml-1 text-danger">&nbsp;*</span>
            </label>
            <select v-model="receive_information" @change="updateValue" name="receive_information" id="receive_information" class="form-select" required="required">
                <option value="1">Llamada por teléfono móvil</option>
                <option v-if="this.whatsapp" value="2">Llamada por WhatsApp</option>
                <option v-if="this.whatsapp" value="3">Mensaje de WhatsApp</option>
                <option value="4">Mensaje de teléfono móvil</option>
                <option value="5">Correo Electrónico</option>
            </select>
            <div class="invalid-feedback"> Ingrese un dato válido </div>
        </div>
    </div>
</template>

<script>
    Vue.component('receive-information-component', {
        template: '#tpl-receive-information-component',
        data() {
            return {
                receive_information: null,
                whatsapp: false,
            };
        },
        watch: {
            '$parent.whatsapp_service': {
                immediate: true,
                handler(newStatusWhatsapp) {
                    this.filterOptions(newStatusWhatsapp);
                }
            }
        },
        mounted() {
            if (this.$parent.id) {
                this.receive_information = this.$parent.receive_information.id;

                if (this.$parent.id) {
                    this.receive_information = this.$parent.receive_information.id;

                    const updateProperty = (index) => {
                        const property = this.$parent.user_form_property[index];
                        if (property.property_same_area == 1) {
                            if (!property.province) {
                                property.province = {
                                    id: null,
                                    name: ''
                                };
                            }
                            property.province.id = this.$parent.province_rent.id || null;
                            property.province.name = this.$parent.province_rent.name || '';

                            if (!property.city) {
                                property.city = {
                                    id: null,
                                    name: ''
                                };
                            }
                            property.city.id = this.$parent.canton_rent.id || null;
                            property.city.name = this.$parent.canton_rent.name || '';
                            property.addres = this.$parent.adress || '';
                            property.locate_gps_lat = this.$parent.locate_gps_lat || '';
                            property.locate_gps_lng = this.$parent.locate_gps_lng || '';
                        } else {

                        }
                    };

                    for (let i = 0; i < 15; i++) {
                        updateProperty(i);
                    }
                }
            }
        },
        methods: {
            updateValue() {
                if (!this.$parent.receive_information) {
                    this.$parent.receive_information = {};
                }
                this.$parent.receive_information.id = this.receive_information;
            },
            filterOptions(newStatusWhatsapp) {
                if (newStatusWhatsapp == 0) {
                    this.whatsapp = false;
                }
                if (newStatusWhatsapp == 1) {
                    this.whatsapp = true;
                }
            },
        }
    });
</script>

<template id="tpl-badge-component">
    <div class="fixed-badge">
        <button type="button" class="btn btn-primary">
        <a class="text-white" 
            href="mailto:rmorillo@iom.int,pmejia@iom.int
            ?subject=Inconvenientes%20con%20el%20Beneficiario
            &body=Estimado/a%20Mejia%20Pamela%20y%20Morillo%20Rodney:%0A%0AEspero%20que%20este%20mensaje%20los%20encuentre%20bien.%0A%0A
            Me%20dirijo%20a%20ustedes%20para%20poner%20en%20su%20conocimiento%20una%20situaci%C3%B3n%20que%20ha%20surgido%20con%20respecto%20al%20beneficiario%20de%20la%20propiedad%20ubicada%20en%20[direcci%C3%B3n%20de%20la%20propiedad],%20de%20la%20cual%20soy%20due%C3%B1o.%0A%0ALamentablemente,%20he%20tenido%20que%20enfrentar%20varias%20molestias%20con%20el%20beneficiario.%0A%0AMe%20gustar%C3%ADa%20que%20se%20tomaran%20las%20medidas%20correspondientes%20para%20solucionar%20este%20problema.%0A%0AAgradezco%20de%20antemano%20su%20pronta%20atenci%C3%B3n%20a%20este%20asunto%20y%20quedo%20a%20su%20disposici%C3%B3n%20para%20cualquier%20informaci%C3%B3n%20adicional%20que%20pueda%20necesitar%20para%20gestionar%20esta%20situaci%C3%B3n%20de%20manera%20efectiva.%0A%0ASin%20otro%20particular,%20reciba%20un%20cordial%20saludo,%0A" target="_blank">
            ¿Tiene inconvenientes<br> con el Beneficiario?
            </a>

        </button>

    </div>
</template>


<script>
    Vue.component('badge-component', {
        template: '#tpl-badge-component',
        data() {
            return {}
        },

        methods: {

        },
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
        width: 400px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        background-color: #fff;
    }

    .list-group-item {
        border: none;
    }

    .large-text {
        font-size: 14px;
    }
</style>