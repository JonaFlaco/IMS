<template id="tpl-general-info-component">
    
    <!-- Loading Panel -->
    <div v-if="loading" class="col-xl-3 col-lg-6 float-center">
        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        {{loadingMessage}}
    </div>
    <!-- End of Loading Panel -->

    <!-- Error in loading Panel -->
    <div v-else-if="errorInLoading">
        Error while loading data.
        <button 
            :disabled="loading"
            @click="refresh"
            type="button" 
            class="btn btn-secondary"
            >
            <i class="mdi mdi-refresh font-16"></i> 
            Retry
        </button>
    </div>
    <!-- End of Error in loading Panel -->
    
    <!-- Main Panel -->
    <div v-else>

        <!-- Top Bar -->
        <div class="row">
            <div class="col-sm-4">                            
                <h4 class="mb-3 header-title">
                    {{ title }}
                </h4>
            </div>

            <div class="col-sm-8">
                <div class="text-sm-end">
                    <button 
                        :disabled="loading"
                        @click="save"
                        type="button" 
                        class="btn btn-primary"
                        >
                        <i class="mdi mdi mdi-content-save font-16"></i> 
                        Save
                    </button>
                </div>
            </div>

        </div>
        <!-- End of Top Bar -->
        

        <!-- Form -->
        <form ref="form">

            <div class="mb-3">
                <label class="form-label" for="siteTitle">Site Title: <span class="ml-1 text-danger">*</span></label>
                <input 
                    :disabled="loading"
                    type="text" 
                    required
                    class="form-control" 
                    id="siteTitle" 
                    v-model="item.app_title"
                    >
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label" for="app_email">Site Email: <span class="ml-1 text-danger">*</span></label>
                <input 
                    :disabled="loading"
                    type="email" 
                    required
                    class="form-control" 
                    id="app_email" 
                    v-model="item.app_email"
                    >
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="siteUrl">Site URL: <span class="ml-1 text-danger">*</span></label>
                <input 
                    :disabled="loading"
                    type="text" 
                    required
                    class="form-control" 
                    id="siteUrl" 
                    v-model="item.app_url"
                    >
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="siteDescription">Site Description: <span class="ml-1 text-danger">*</span></label>
                <input 
                    :disabled="loading"
                    type="text" 
                    required
                    class="form-control" 
                    id="siteDescription" 
                    v-model="item.app_description"
                    >
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="siteAdminsEmail">Site Admins Email: <span class="ml-1 text-danger">*</span></label>
                <input 
                    :disabled="loading"
                    type="text" 
                    required
                    class="form-control" 
                    id="siteAdminsEmail" 
                    v-model="item.sys_admin_group_email"
                    >
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="timezone">Timezone: <span class="ml-1 text-danger">*</span></label>
                <select 
                    :disabled="loading"
                    v-model="item.timezone" 
                    class="form-select" 
                    id="timezone">
                    <option v-for="x in timezoneOptions"> {{ x }}</option>
                </select>
                <div class="invalid-feedback">
                    Enter a valid data
                </div>
            </div>
            
        </form>
        <!-- End of Form -->
            
    </div>
    <!-- End of Main Panel -->
    
</template>


<script type="text/javascript">

    var generalInfoComponent = {

        template: '#tpl-general-info-component',
        data() {
            return {
                title: 'General Info',
                group_name: 'General Info',
                loading: false,
                loadingMessage: 'Loading, please wait...',
                errorInLoading: false,
                item: {
                    app_title: '',
                    app_url: '',
                    app_email: '',
                    app_description: '',
                    sys_admin_group_email: '',
                    timezone: 'Asia/Baghdad',
                },
                timezoneOptions: [
                    'Africa/Abidjan',
                    'Africa/Accra',
                    'Africa/Addis_Ababa',
                    'Africa/Algiers',
                    'Africa/Asmara',
                    'Africa/Asmera',
                    'Africa/Bamako',
                    'Africa/Bangui',
                    'Africa/Banjul',
                    'Africa/Bissau',
                    'Africa/Blantyre',
                    'Africa/Brazzaville',
                    'Africa/Bujumbura',
                    'Africa/Cairo',
                    'Africa/Casablanca',
                    'Africa/Ceuta',
                    'Africa/Conakry',
                    'Africa/Dakar',
                    'Africa/Dar_es_Salaam',
                    'Africa/Djibouti',
                    'Africa/Douala',
                    'Africa/El_Aaiun',
                    'Africa/Freetown',
                    'Africa/Gaborone',
                    'Africa/Harare',
                    'Africa/Johannesburg',
                    'Africa/Juba',
                    'Africa/Kampala',
                    'Africa/Khartoum',
                    'Africa/Kigali',
                    'Africa/Kinshasa',
                    'Africa/Lagos',
                    'Africa/Libreville',
                    'Africa/Lome',
                    'Africa/Luanda',
                    'Africa/Lubumbashi',
                    'Africa/Lusaka',
                    'Africa/Malabo',
                    'Africa/Maputo',
                    'Africa/Maseru',
                    'Africa/Mbabane',
                    'Africa/Mogadishu',
                    'Africa/Monrovia',
                    'Africa/Nairobi',
                    'Africa/Ndjamena',
                    'Africa/Niamey',
                    'Africa/Nouakchott',
                    'Africa/Ouagadougou',
                    'Africa/Porto-Novo',
                    'Africa/Sao_Tome',
                    'Africa/Timbuktu',
                    'Africa/Tripoli',
                    'Africa/Tunis',
                    'Africa/Windhoek',
                    'America/Adak',
                    'America/Anchorage',
                    'America/Anguilla',
                    'America/Antigua',
                    'America/Araguaina',
                    'America/Argentina/Buenos_Aires',
                    'America/Argentina/Catamarca',
                    'America/Argentina/ComodRivadavia',
                    'America/Argentina/Cordoba',
                    'America/Argentina/Jujuy',
                    'America/Argentina/La_Rioja',
                    'America/Argentina/Mendoza',
                    'America/Argentina/Rio_Gallegos',
                    'America/Argentina/Salta',
                    'America/Argentina/San_Juan',
                    'America/Argentina/San_Luis',
                    'America/Argentina/Tucuman',
                    'America/Argentina/Ushuaia',
                    'America/Aruba',
                    'America/Asuncion',
                    'America/Atikokan',
                    'America/Atka',
                    'America/Bahia',
                    'America/Bahia_Banderas',
                    'America/Barbados',
                    'America/Belem',
                    'America/Belize',
                    'America/Blanc-Sablon',
                    'America/Boa_Vista',
                    'America/Bogota',
                    'America/Boise',
                    'America/Buenos_Aires',
                    'America/Cambridge_Bay',
                    'America/Campo_Grande',
                    'America/Cancun',
                    'America/Caracas',
                    'America/Catamarca',
                    'America/Cayenne',
                    'America/Cayman',
                    'America/Chicago',
                    'America/Chihuahua',
                    'America/Coral_Harbour',
                    'America/Cordoba',
                    'America/Costa_Rica',
                    'America/Creston',
                    'America/Cuiaba',
                    'America/Curacao',
                    'America/Danmarkshavn',
                    'America/Dawson',
                    'America/Dawson_Creek',
                    'America/Denver',
                    'America/Detroit',
                    'America/Dominica',
                    'America/Edmonton',
                    'America/Eirunepe',
                    'America/El_Salvador',
                    'America/Ensenada',
                    'America/Fort_Wayne',
                    'America/Fortaleza',
                    'America/Glace_Bay',
                    'America/Godthab',
                    'America/Goose_Bay',
                    'America/Grand_Turk',
                    'America/Grenada',
                    'America/Guadeloupe',
                    'America/Guatemala',
                    'America/Guayaquil',
                    'America/Guyana',
                    'America/Halifax',
                    'America/Havana',
                    'America/Hermosillo',
                    'America/Indiana/Indianapolis',
                    'America/Indiana/Knox',
                    'America/Indiana/Marengo',
                    'America/Indiana/Petersburg',
                    'America/Indiana/Tell_City',
                    'America/Indiana/Vevay',
                    'America/Indiana/Vincennes',
                    'America/Indiana/Winamac',
                    'America/Indianapolis',
                    'America/Inuvik',
                    'America/Iqaluit',
                    'America/Jamaica',
                    'America/Jujuy',
                    'America/Juneau',
                    'America/Kentucky/Louisville',
                    'America/Kentucky/Monticello',
                    'America/Knox_IN',
                    'America/Kralendijk',
                    'America/La_Paz',
                    'America/Lima',
                    'America/Los_Angeles',
                    'America/Louisville',
                    'America/Lower_Princes',
                    'America/Maceio',
                    'America/Managua',
                    'America/Manaus',
                    'America/Marigot',
                    'America/Martinique',
                    'America/Matamoros',
                    'America/Mazatlan',
                    'America/Mendoza',
                    'America/Menominee',
                    'America/Merida',
                    'America/Metlakatla',
                    'America/Mexico_City',
                    'America/Miquelon',
                    'America/Moncton',
                    'America/Monterrey',
                    'America/Montevideo',
                    'America/Montreal',
                    'America/Montserrat',
                    'America/Nassau',
                    'America/New_York',
                    'America/Nipigon',
                    'America/Nome',
                    'America/Noronha',
                    'America/North_Dakota/Beulah',
                    'America/North_Dakota/Center',
                    'America/North_Dakota/New_Salem',
                    'America/Ojinaga',
                    'America/Panama',
                    'America/Pangnirtung',
                    'America/Paramaribo',
                    'America/Phoenix',
                    'America/Port_of_Spain',
                    'America/Port-au-Prince',
                    'America/Porto_Acre',
                    'America/Porto_Velho',
                    'America/Puerto_Rico',
                    'America/Rainy_River',
                    'America/Rankin_Inlet',
                    'America/Recife',
                    'America/Regina',
                    'America/Resolute',
                    'America/Rio_Branco',
                    'America/Rosario',
                    'America/Santa_Isabel',
                    'America/Santarem',
                    'America/Santiago',
                    'America/Santo_Domingo',
                    'America/Sao_Paulo',
                    'America/Scoresbysund',
                    'America/Shiprock',
                    'America/Sitka',
                    'America/St_Barthelemy',
                    'America/St_Johns',
                    'America/St_Kitts',
                    'America/St_Lucia',
                    'America/St_Thomas',
                    'America/St_Vincent',
                    'America/Swift_Current',
                    'America/Tegucigalpa',
                    'America/Thule',
                    'America/Thunder_Bay',
                    'America/Tijuana',
                    'America/Toronto',
                    'America/Tortola',
                    'America/Vancouver',
                    'America/Virgin',
                    'America/Whitehorse',
                    'America/Winnipeg',
                    'America/Yakutat',
                    'America/Yellowknife',
                    'Antarctica/Casey',
                    'Antarctica/Davis',
                    'Antarctica/DumontDUrville',
                    'Antarctica/Macquarie',
                    'Antarctica/Mawson',
                    'Antarctica/McMurdo',
                    'Antarctica/Palmer',
                    'Antarctica/Rothera',
                    'Antarctica/South_Pole',
                    'Antarctica/Syowa',
                    'Antarctica/Vostok',
                    'Arctic/Longyearbyen',
                    'Asia/Aden',
                    'Asia/Amman',
                    'Asia/Anadyr',
                    'Asia/Aqtau',
                    'Asia/Aqtobe',
                    'Asia/Ashkhabad',
                    'Asia/Baghdad',
                    'Asia/Bahrain',
                    'Asia/Baku',
                    'Asia/Beirut',
                    'Asia/Bishkek',
                    'Asia/Brunei',
                    'Asia/Calcutta',
                    'Asia/Chongqing',
                    'Asia/Chungking',
                    'Asia/Colombo',
                    'Asia/Dacca',
                    'Asia/Dhaka',
                    'Asia/Dili',
                    'Asia/Dubai',
                    'Asia/Dushanbe',
                    'Asia/Harbin',
                    'Asia/Hebron',
                    'Asia/Ho_Chi_Minh',
                    'Asia/Hong_Kong',
                    'Asia/Irkutsk',
                    'Asia/Istanbul',
                    'Asia/Jakarta',
                    'Asia/Jayapura',
                    'Asia/Kabul',
                    'Asia/Kamchatka',
                    'Asia/Karachi',
                    'Asia/Kashgar',
                    'Asia/Katmandu',
                    'Asia/Khandyga',
                    'Asia/Kolkata',
                    'Asia/Krasnoyarsk',
                    'Asia/Kuching',
                    'Asia/Kuwait',
                    'Asia/Macao',
                    'Asia/Macau',
                    'Asia/Makassar',
                    'Asia/Manila',
                    'Asia/Muscat',
                    'Asia/Nicosia',
                    'Asia/Novosibirsk',
                    'Asia/Omsk',
                    'Asia/Oral',
                    'Asia/Phnom_Penh',
                    'Asia/Pyongyang',
                    'Asia/Qatar',
                    'Asia/Qyzylorda',
                    'Asia/Rangoon',
                    'Asia/Saigon',
                    'Asia/Sakhalin',
                    'Asia/Samarkand',
                    'Asia/Seoul',
                    'Asia/Singapore',
                    'Asia/Taipei',
                    'Asia/Tashkent',
                    'Asia/Tbilisi',
                    'Asia/Tel_Aviv',
                    'Asia/Thimbu',
                    'Asia/Thimphu',
                    'Asia/Tokyo',
                    'Asia/Ulaanbaatar',
                    'Asia/Ulan_Bator',
                    'Asia/Urumqi',
                    'Asia/Ust-Nera',
                    'Asia/Vladivostok',
                    'Asia/Yakutsk',
                    'Asia/Yekaterinburg',
                    'Asia/Yerevan',
                    'Atlantic/Azores',
                    'Atlantic/Canary',
                    'Atlantic/Cape_Verde',
                    'Atlantic/Faroe',
                    'Atlantic/Madeira',
                    'Atlantic/Reykjavik',
                    'Atlantic/St_Helena',
                    'Australia/ACT',
                    'Australia/Brisbane',
                    'Australia/Broken_Hill',
                    'Australia/Currie',
                    'Australia/Eucla',
                    'Australia/Hobart',
                    'Australia/Lindeman',
                    'Australia/Melbourne',
                    'Australia/North',
                    'Australia/Perth',
                    'Australia/South',
                    'Australia/Sydney',
                    'Australia/Victoria',
                    'Australia/Yancowinna',
                    'Europe/Amsterdam',
                    'Europe/Athens',
                    'Europe/Belfast',
                    'Europe/Berlin',
                    'Europe/Brussels',
                    'Europe/Bucharest',
                    'Europe/Busingen',
                    'Europe/Copenhagen',
                    'Europe/Dublin',
                    'Europe/Guernsey',
                    'Europe/Isle_of_Man',
                    'Europe/Istanbul',
                    'Europe/Kaliningrad',
                    'Europe/Lisbon',
                    'Europe/Ljubljana',
                    'Europe/Luxembourg',
                    'Europe/Malta',
                    'Europe/Mariehamn',
                    'Europe/Monaco',
                    'Europe/Nicosia',
                    'Europe/Oslo',
                    'Europe/Podgorica',
                    'Europe/Riga',
                    'Europe/Rome',
                    'Europe/San_Marino',
                    'Europe/Simferopol',
                    'Europe/Skopje',
                    'Europe/Stockholm',
                    'Europe/Tallinn',
                    'Europe/Tirane',
                    'Europe/Tiraspol',
                    'Europe/Vaduz',
                    'Europe/Vatican',
                    'Europe/Vienna',
                    'Europe/Vilnius',
                    'Europe/Warsaw',
                    'Europe/Zagreb',
                    'Europe/Zaporozhye',
                    'Europe/Zurich',
                    'Indian/Antananarivo',
                    'Indian/Chagos',
                    'Indian/Christmas',
                    'Indian/Cocos',
                    'Indian/Kerguelen',
                    'Indian/Mahe',
                    'Indian/Maldives',
                    'Indian/Mauritius',
                    'Indian/Reunion',
                    'Pacific/Apia',
                    'Pacific/Chatham',
                    'Pacific/Chuuk',
                    'Pacific/Efate',
                    'Pacific/Fakaofo',
                    'Pacific/Fiji',
                    'Pacific/Galapagos',
                    'Pacific/Guadalcanal',
                    'Pacific/Guam',
                    'Pacific/Johnston',
                    'Pacific/Kosrae',
                    'Pacific/Kwajalein',
                    'Pacific/Marquesas',
                    'Pacific/Nauru',
                    'Pacific/Niue',
                    'Pacific/Noumea',
                    'Pacific/Pago_Pago',
                    'Pacific/Palau',
                    'Pacific/Pitcairn',
                    'Pacific/Ponape',
                    'Pacific/Port_Moresby',
                    'Pacific/Rarotonga',
                    'Pacific/Saipan',
                    'Pacific/Tahiti',
                    'Pacific/Tarawa',
                    'Pacific/Tongatapu',
                    'Pacific/Truk',
                    'Pacific/Wallis',
                    'Pacific/Yap'
                ]
            }
        },
        props: [],
        mounted() {
            this.refresh();
        },
        methods: {
            async refresh() {
                
                let self = this;
                self.loading = true;
                
                var response = await this.$parent.load(this.group_name);
                
                self.loading = false;

                if(response != false && response.status == 200) {
                    self.item = response.data.result;
                } else {
                    this.errorInLoading = true;
                }
            },
            async save() {
                
                if (!this.$refs.form.checkValidity()) {

                    this.$refs.form.classList.add('was-validated');

                    $.toast({
                        heading: 'Error',
                        text: 'Please enter valid values',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'error'
                    });
                    
                    return;
                } else {
                    this.$refs.form.classList.remove('was-validated');
                }

                let self = this;
                self.loading = true;
                
                let formData = new FormData();
                formData.append('data', JSON.stringify(this.item));
                
                var response = await this.$parent.save(this.item);
                self.loading = false;

            }
        },
        computed: {
            
        },
    }

    Vue.component('general-info-component', generalInfoComponent)

</script>
