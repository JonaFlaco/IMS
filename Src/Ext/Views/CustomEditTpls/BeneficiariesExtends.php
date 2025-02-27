<template id="tpl-national-id-no-component">
    <div>
        <div id="div_national_id_no" class="mb-3 col-md-12 highlight">
            <label for="national_id_no" class="form-label">{{ title }}<span v-if="isRequired" class="ml-1 text-danger">&nbsp;*</span></label>
            <input @change="updateValue" :pattern="pattern" type="text" v-model="national_id_no" name="national_id_no" id="national_id_no" :maxlength="maxlength" data-toggle="maxlength" :required="isRequired" class="form-control">
            <div class="invalid-feedback"> Ingrese un dato válido</div>
            <div class="pb-2"><i class="mdi mdi-information">Ingrese el numero de identidad tal como se encuentra en su documento, sin espacios ni caracteres especiales. Debe ser la misma de su nacionalidad.</i></div>
        </div>
    </div>
</template>
<script>
    Vue.component('national-id-no-component', {
        template: '#tpl-national-id-no-component',
        props: ['title', 'value', 'name', 'isRequired'],
        data() {
            return {
                national_id_no: null,
                pattern: null,
                maxlength: 255,
                userId: [], // Inicializamos como array vacío
            }
        },
        mounted() {
            this.national_id_no = this.$parent.national_id_no;
            this.$watch(
                "$parent.nationality_id",
                (new_val, old_val) => {
                    if (new_val && new_val.id) {
                        this.get(new_val.id);
                    }
                }
            );
        },
        methods: {
            updateValue(event) {
                this.$parent.national_id_no = this.national_id_no;
            },
            async get(nationality_id) {
                try {
                    const response = await axios.get(`/InternalApi/RetrieveUserId`);

                    if (typeof response.data === 'string' && response.data.includes('location.href')) {
                        // Si la respuesta contiene la redirección, lo igualamos a un array vacío
                        this.userId = [];
                    } else {
                        this.userId = response.data.userId || [];
                    }

                } catch (error) {
                    console.error('Error al obtener datos del usuario:', error);
                    this.userId = [];
                }

                // Configurar el patrón y maxlength basado en nationality_id si no hay userId
                if (!this.userId.length) {
                    if (nationality_id == 1) { // Ecuador
                        this.pattern = '^\\d{10}$';
                        this.maxlength = 10;
                    } else if (nationality_id == 3) { // Venezuela
                        this.pattern = '^\\d{7,8}$';
                        this.maxlength = 8;
                    } else if (nationality_id == 2) { // Colombia
                        this.pattern = '^\\d{7,10}$';
                        this.maxlength = 10;
                    } else {
                        this.pattern = null;
                        this.maxlength = 25;
                    }
                }
            }
        }
    });
</script>
<template id="tpl-note-cellphone-component">
    <div id="div_note_cellphone" class="col-md-12 pb-1 mb-3 highlight alert alert-warning">
        <label for="note_cellphone"><span class="mdi mdi-phone-incoming"></span> Ingrese un número de celular real para poder ser contactado.</label>
    </div>
</template>
<script>
    Vue.component('note-cellphone-component', {
        template: '#tpl-note-cellphone-component',
        data() {
            return {

            }
        },
        mounted() {

        },
        methods: {},

    });
</script>

<template id="tpl-justify-upload-component">
    <div id="div_justify_upload" class="col-md-12 mb-1 highlight alert alert-warning">
        <label for="justify_upload"> <span class="mdi mdi-account-check"></span> Suba una foto de sus documentos personales. Esto ayudará a validar su información personal</label>
    </div>
</template>
<script>
    Vue.component('justify-upload-component', {
        template: '#tpl-justify-upload-component',
        data() {
            return {

            }
        },
        mounted() {

        },
        methods: {},

    });
</script>

<template id="tpl-note-family-information-component">
    <div id="div_note_family_information" class="col-md-12 pb-1 mb-3 highlight alert alert-warning">
        <label for="note_family_information"><span class="mdi mdi-alert-decagram"></span> Ingrese la información solo de su núcleo familiar que se encuentra en el Ecuador
            <p></p>
            <ul>
                <p>Ingrese sus familiares uno por uno con el boton +Agregar registro</p>
                <p>La relación del miembro familiar debe ser con el aplicante principal que está completando este formulario.</p>
            </ul>
        </label>
    </div>
</template>
<script>
    Vue.component('note-family-information-component', {
        template: '#tpl-note-family-information-component',
        data() {
            return {

            }
        },
        mounted() {

        },
        methods: {},

    });
</script>

<template id="tpl-protection-note-component">
    <div id="div_protection_note" class="alert alert-danger col-md-12 pb-1 mb-3 highlight" role="alert">
        <label for="protection_note">
            SI USTED SE ENCUENTRA EN RIESGO POR FAVOR LLAMAR AL 911. En caso de que requiera orientación, contáctenos al teléfono de orientación +593 994 112 633 de lunes a viernes de 08:00 a 13:30
        </label>
    </div>
</template>
<script>
    Vue.component('protection-note-component', {
        template: '#tpl-protection-note-component',
        data() {
            return {

            }
        },
        mounted() {

        },
        methods: {},

    });
</script>

<template id="tpl-oim-escucha-component">
    <div id="div_oim_escucha" class="alert alert-info col-md-12 pb-1 mb-3 highlight" role="alert">
        <label for="oim_escucha">
            Si tiene inconvenientes para realizar el registro o
            quiere saber el estado de su caso, puede
            comunicarse al número telefónico +593 994 112 633 de
            lunes a viernes de 8h30 a 13h00. </label>
    </div>
</template>
<script>
    Vue.component('oim-escucha-component', {
        template: '#tpl-oim-escucha-component',
        data() {
            return {

            }
        },
        mounted() {

        },
        methods: {},

    });
</script>

<template id="tpl-minor-alert-component">
    <div v-if="edadBeneficiario < 18" class="alert alert-warning" role="alert">
        Si usted tiene menos de 18 años, por favor
        comuníquese enviando un mensaje al correo
        electrónico oimecasistencia@iom.int o
        llamando al número telefónico 0994112633
        de lunes a viernes de 8h30 a 13h00.
    </div>
</template>

<script>
    Vue.component('minor-alert-component', {
        template: '#tpl-minor-alert-component',
        data() {
            return {
                edadBeneficiario: 0 // Inicializar la edad del beneficiario
            }
        },
        mounted() {
            // Calcular la edad del beneficiario inicialmente
            this.calcularEdadBeneficiario();

            // Observar cambios en this.$parent.birth_date
            this.$watch('$parent.birth_date', () => {
                this.calcularEdadBeneficiario();
            });
        },
        methods: {
            calcularEdadBeneficiario() {
                // Suponiendo que this.$parent.birth_date es la fecha de nacimiento del beneficiario
                const birthDate = new Date(this.$parent.birth_date);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const month = today.getMonth() - birthDate.getMonth();
                if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                this.edadBeneficiario = age;
            }
        }
    });
</script>

<template id="tpl-badge-component">
    <div class="fixed-badge">
        <button type="button" class="btn btn-primary">
            <a class="text-white" href="/assets/ext/assistance/manual.pdf" target="_blank">
                <img src="/assets/app/images/icons/pdf.png" width="24px" height="24px">
                ¿Necesitas ayuda?
            </a>
        </button>

    </div>
</template>


<script>
    Vue.component('badge-component', {
        template: '#tpl-badge-component',
        data() {
            return {
            }
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