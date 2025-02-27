<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Setup - Information Management System - IMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="desc" name="description" />
        
        <!-- App favicon -->
        <link rel="shortcut icon" href="/assets/ext/images/favicon.ico">

        <link href="/assets/theme/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/theme/css/app-modern.min.css" rel="stylesheet" type="text/css" id="light-style" />

        <link href="/assets/app/css/style.css" rel="stylesheet" type="text/css" />

        <link href="/assets/app/css/vue-multiselect.min.css" rel="stylesheet" type="text/css" />

        <script src="/assets/app/js/vue-multiselect.min.js"></script>
        <link href="/assets/app/js/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css" />

        <script src="/assets/app/js/vue.min.js"></script>
        <script src="/assets/app/js/axios.min.js"></script>
        
    </head>


    <body class="loading">
          
        <!-- Begin page -->
        <div class="wrapper">
            
            <div class="account-pages mt-5 mb-5" >
                <div class="container">
                    <div class="row justify-content-center">
                        
                        <div class="col-lg-8">
                            <div class="card">

                                <!-- Logo -->
                                <div class="card-header pt-4 pb-4 text-center bg-primary">
                                    <span><img src="/assets/ext/images/logo_white.png" alt="" height="18"></span>
                                    <h4 class="text-white p-0">Information Management System - IMS<h4>
                                </div>

                                <div id="vue-cont">

                                </div>
                            </div>

                            <!-- Footer Note -->
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                </div>
                            </div>
                            <!-- End of Footer Note -->

                        </div>
                        
                    </div>
                    
                </div>
            </div>


                <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        <div aria-live="polite" aria-atomic="true"> <div style="position: absolute; top: 80px; right: 20px;" id="notification_panel"></div>
    </div>
    <!-- END wrapper -->

    <script src="/assets/theme/js/vendor.min.js"></script>
    <script src="/assets/theme/js/app.min.js"></script>

    <script src="/assets/app/js/v-tooltip.min.js"></script>
    
    
    <script src="/assets/app/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>

    <script src="/assets/app/js/jquery_date_picker_dropdown.js"></script>

    <script src="/assets/app/js/Sortable.min.js"></script>
    <script src="/assets/app/js/vuedraggable.umd.min.js"></script>

    
        
    
    
    </body>
</html>

<template id="tpl-main">
    
    <div class="row p-1">
        <div class="col-sm-3 mb-2 mb-sm-0">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link" :class="currentStep == 0 ? 'active show' : ''">
                    <i class="mdi mdi-information"></i>
                    <span>Welcome</span>
                </a>

                <a class="nav-link" :class="currentStep == 1 ? 'active show' : ''">
                <i class="mdi mdi-server"></i>
                    <span>Db Server</span>
                </a>

                <a class="nav-link" :class="currentStep == 2 ? 'active show' : '' ">
                    <i class="mdi mdi-database"></i>
                    <span>Database</span>
                </a>

                <a class="nav-link" :class="currentStep == 3 ? 'active show' : ''">
                    <i class="mdi mdi-account-circle"></i>
                    <span>Admin User</span>
                </a>

                <a class="nav-link" :class="currentStep == 4 ? 'active show' : ''">
                    <i class="mdi mdi-account-circle"></i>
                    <span>Configure</span>
                </a>

                <a class="nav-link" :class="currentStep == 5 ? 'active show' : ''">
                    <i class="mdi mdi-check-all"></i>
                    <span>Finish</span>
                </a>
            </div>
        </div>

        <div class="col-sm-9 ">
            <div class="tab-content p-2" id="v-pills-tabContent">
            
                <!-- Welcome tab -->
                <div 
                    class="tab-pane p-1 fade" 
                    :class="currentStep == 0 ? 'active show' : ''" 
                    role="tabpanel" 
                    data-simplebar style="height: 350px !important;"
                    >
                    Welcome!
                </div>
                
                <!-- Db Server Connection Tab -->
                <div 
                    class="tab-pane p-1 fade" 
                    :class="currentStep == 1 ? 'active show' : ''" 
                    role="tabpanel" 
                    data-simplebar style="height: 350px !important;"
                    >
                    <form id="serverConnectionForm" ref="serverConnectionForm" method="post" action="#" class="form-horizontal">
                        <div class="row">
                            <div class="col-12">
                                
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="db-host"> Server</label>
                                    <div class="col-md-9">
                                        <input type="text" required id="db-host" v-model="db_host" name="db-host" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="db-port"> Port</label>
                                    <div class="col-md-9">
                                        <input type="text" required id="db-port" v-model="db_port" name="db-port" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="db-username"> Username</label>
                                    <div class="col-md-9">
                                        <input type="text" required id="db-username" v-model="db_username" name="db-username" class="form-control" required>
                                    </div>
                                </div>
                    
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="db-password">Password</label>
                                    <div class="col-md-9">
                                        <input type="password" required id="db-password" v-model="db_password" name="db-password" class="form-control" required>
                                    </div>
                                </div>

                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </form>
                </div>
                
                <!-- Database Tab -->
                <div 
                    class="tab-pane p-1 fade" 
                    :class="currentStep == 2 ? 'active show' : ''" 
                    role="tabpanel" 
                    data-simplebar style="height: 350px !important;"
                    >
                
                    <div class="row">
                        <div class="col-12">
                            
                            <div class="row mb-3">
                                <label class="col-md-3 col-form-label" for="db-name">Databases</label>
                                <div class="col-md-9">
                                    <select id="db-select" v-model="db_name" name="db-select" class="form-select" required>
                                        <option value=""> -- Select -- </option>
                                        <option v-for="(itm, index) in db_list" :key="index"> {{itm}} </option>
                                    </select>
                                </div>

                            </div>

                            <div class="row mb-3">
                                <button @click="getListOfDatabases()" class="btn btn-secondary col-md-6">
                                    <span v-if="loading_db_list">Refreshing...</span>
                                    <span v-else>Refresh</span>
                                </button>
                                <button @click="show_create_db_panel = true" class="btn btn-secondary col-md-6">Create New Database</button>
                            </div>
                            

                            <div v-if="show_create_db_panel" class="row bg-light p-2">

                                <div class="row">
                                    <label class="col-md-3 col-form-label" for="db-name">New db name</label>
                                    <div class="col-md-9">
                                        <input type="text" id="db-name" v-model="db_name_new" name="db-name" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row mt-1 mb-2">
                                    <button :disabled="loading_creating_db" class="btn btn-success col-md-6" @click="createDb()">
                                        <i class="mdi mdi-plus"></i>
                                        <span v-if="loading_creating_db">Creating...</span>
                                        <span v-else>Create</span>
                                    </button>
                                    <button :disabled="loading_creating_db" class="btn btn-secondary col-md-6" @click="show_create_db_panel = false">
                                        <i class="mdi mdi-exit"></i>
                                        Cancel
                                    </button>
                                </div>
                            </div>

                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>

                <!-- Admin user Tab -->
                <div 
                    class="tab-pane p-1 fade" 
                    :class="currentStep == 3 ? 'active show' : ''" 
                    role="tabpanel" 
                    data-simplebar style="height: 350px !important;"
                    >
                    <form id="adminUserForm" ref="adminUserForm" method="post" action="#" class="form-horizontal">
                        <div class="row">
                            <div class="col-12">
                                
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="admin_name"> Admin username</label>
                                    <div class="col-md-9">
                                        <input type="text" required id="admin_name" v-model="admin_name" name="admin_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="admin_full_name"> Admin Full Name</label>
                                    <div class="col-md-9">
                                        <input type="text" required id="admin_full_name" v-model="admin_full_name" name="admin_full_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="admin_email"> Admin Email</label>
                                    <div class="col-md-9">
                                        <input type="email" required id="admin_email" v-model="admin_email" name="admin_email" class="form-control" required>
                                    </div>
                                </div>
                    
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="admin_password">Password</label>
                                    <div class="col-md-9">
                                        <input type="password" required id="admin_password" v-model="admin_password" name="admin_password" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="admin_password2">Retype Password</label>
                                    <div class="col-md-9">
                                        <input type="password" required id="admin_password2" v-model="admin_password2" name="admin_password2" class="form-control" required>
                                    </div>
                                </div>

                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </form>
                </div>


                <!-- Configure Tab -->
                <div 
                    class="tab-pane p-1 fade" 
                    :class="currentStep == 4 ? 'active show' : ''" 
                    role="tabpanel" 
                    data-simplebar style="height: 350px !important;"
                    >
                    <form id="configureForm" ref="configureForm" method="post" action="#" class="form-horizontal">
                        <div class="row">
                            <div class="col-12">
                                
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="app_title"> Site Title</label>
                                    <div class="col-md-9">
                                        <input type="text" required id="app_title" v-model="app_title" name="app_title" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="app_email"> Site Email</label>
                                    <div class="col-md-9">
                                        <input type="text" required id="app_email" v-model="app_email" name="app_email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="app_url"> Site URL</label>
                                    <div class="col-md-9">
                                        <input type="text" required id="app_url" v-model="app_url" name="app_url" class="form-control" required>
                                    </div>
                                </div>
                    
                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="app_desc">Site Description</label>
                                    <div class="col-md-9">
                                        <textarea type="text" required id="app_desc" v-model="app_desc" name="app_desc" class="form-control" required></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label" for="app_timezone">Timezone</label>
                                    <div class="col-md-9">
                                        <select 
                                            :disabled="loading"
                                            v-model="app_timezone" 
                                            class="form-select" 
                                            id="app_timezone">
                                            <option v-for="x in app_timezoneOptions"> {{ x }}</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </form>
                </div>


                <!-- Finish Tab -->
                <div 
                    class="tab-pane p-1 fade" 
                    :class="currentStep == 5 ? 'active show' : ''" 
                    role="tabpanel" 
                    data-simplebar style="height: 350px;"
                    >
                    <form id="otherForm" method="post" action="#" class="form-horizontal"></form>
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <h2 class="mt-0">
                                        <i class="mdi mdi-check-all"></i>
                                    </h2>
                                    <h3 class="mt-0">IMS Ready!</h3>
                    
                                    <p class="w-75 mb-2 mx-auto">Now your system is up and running, click finish to login to the system.</p>

                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </form>
                </div>

            </div> <!-- end tab-content-->
        </div> <!-- end col-->

        <div class="mt-2">
            <button @click="prev()" v-if="currentStep > 0" :disabled="loading" class="btn btn-info float-start">
                Previous
            </button>

            <button @click="next()" v-if="currentStep >= 0 && currentStep < totalSteps" :disabled="loading" class="btn btn-info float-end">
                <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Next
            </button>
            <a class="btn btn-success float-end" v-if="currentStep == totalSteps" href="/">Finish</a>
        </div>
            
    </div> <!-- tab-content -->
    
</template>

<script>
    new Vue({
        el: '#vue-cont',
        template: '#tpl-main',
        data: {
            loading: false, 
            
            db_host: 'localhost',
            db_port: '1433',
            db_username: '',
            db_password: null,
            db_name: '',
            db_list: [],
            db_name_new:'',

            admin_name: 'admin',
            admin_full_name: 'Administrator',
            admin_email: '',
            admin_password: null,
            admin_password2: null,

            app_title: 'Information Management System',
            app_email: '',
            app_url: 'http://localhost',
            app_desc: 'Information Management System',
            app_timezone: 'Asia/Baghdad',
            app_timezoneOptions: [
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
            ],
            
            loading: false,
            loading_creating_db: false,
            loading_db_list: false,

            show_create_db_panel: false,

            currentStep: 0,
            totalSteps: 5,
        },
        mounted() {
            
        },
        methods: {
            async next() {

                //Check Database Connection
                if(this.currentStep == 1) {
                    this.$refs.serverConnectionForm.classList.add('was-validated');

                    if (!this.$refs.serverConnectionForm.checkValidity()) {
                        return this.returnError('Please enter valid values');
                    }
                    
                    var connectionStatus = await this.checkConnection();
                    
                    if(!connectionStatus) {
                        return this.returnError('Connection to server failed');
                    }

                    this.getListOfDatabases();
                
                //Check if database exist and valid
                } else if(this.currentStep == 2) {
                    
                    if(!this.db_name || this.db_name.length == 0) {
                        return this.returnError('Please select a database first');
                    }

                    var dbExists = await this.checkDbExist();
                    if(!dbExists) {
                        return this.returnError('Database not found');
                    }

                //Create admin account
                } else if (this.currentStep == 3) {

                    this.$refs.adminUserForm.classList.add('was-validated');
                    if (!this.$refs.adminUserForm.checkValidity()) {
                        return this.returnError('Please enter valid values');
                    }

                    if(this.admin_password != this.admin_password2) {
                        return this.returnError('Passwords does not match');
                    }

                    if(this.admin_password.length < 8) {
                        return this.returnError('Password must be at least 8 charecters long');
                    }

                    let createUserResult = await this.createAdminUser();
                    if(!createUserResult) {
                        return this.returnError('Something went wrong');
                    }
                } else if (this.currentStep == 4) {

                    this.$refs.configureForm.classList.add('was-validated');
                    if (!this.$refs.configureForm.checkValidity()) {
                        return this.returnError('Please enter valid values');
                    }

                    let createUserResult = await this.saveConfigure();
                    if(!createUserResult) {
                        return this.returnError('Something went wrong');
                    }
                }

                if(this.currentStep < this.totalSteps)
                    this.currentStep++;
            },
            returnError(message) {
                $.toast({
                    heading: 'Error',
                    text: message,
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                });
                return;
            },
            prev() {
                if(this.currentStep > 0)
                    this.currentStep--;
            },
            async createAdminUser() {
                var self = this;
                self.loading = true;

                var formData = new FormData();
                formData.append("db_host", this.db_host);
                formData.append("db_port", this.db_port);
                formData.append("db_username", this.db_username);
                formData.append("db_password", this.db_password);
                formData.append("db_name", this.db_name);

                formData.append("admin_name", this.admin_name);
                formData.append("admin_full_name", this.admin_full_name);
                formData.append("admin_email", this.admin_email);
                formData.append("admin_password", this.admin_password);
                
                var res = await axios.post(
                    '/InitialSetup?cmd=createadminuser', 
                    formData
                ).catch(function (error) {
                    return false;
                });
                
                self.loading = false;

                return res != false;
            },
            async saveConfigure() {
                var self = this;
                self.loading = true;

                var formData = new FormData();
                formData.append("db_host", this.db_host);
                formData.append("db_port", this.db_port);
                formData.append("db_username", this.db_username);
                formData.append("db_password", this.db_password);
                formData.append("db_name", this.db_name);

                formData.append("app_title", this.app_title);
                formData.append("app_email", this.app_email);
                formData.append("app_url", this.app_url);
                formData.append("app_desc", this.app_desc);
                formData.append("app_timezone", this.app_timezone);
                
                var res = await axios.post(
                    '/InitialSetup?cmd=saveConfigure', 
                    formData
                ).catch(function (error) {
                    return false;
                });
                
                self.loading = false;

                return res != false;
            },
            async setUpDb() {

                var formData = new FormData();
                formData.append("db_host", this.db_host);
                formData.append("db_port", this.db_port);
                formData.append("db_username", this.db_username);
                formData.append("db_password", this.db_password);
                formData.append("db_name", this.db_name);
                
                var res = await axios.post(
                    '/InitialSetup?cmd=SetupDb', 
                    formData
                );
            },
            async checkConnection() {
                
                var self = this;
                self.loading = true;

                var formData = new FormData();
                formData.append("db_host", this.db_host);
                formData.append("db_port", this.db_port);
                formData.append("db_username", this.db_username);
                formData.append("db_password", this.db_password);
                
                var res = await axios.post(
                    '/InitialSetup?cmd=checkConnection', 
                    formData
                ).catch(function (error) {
                    return false;
                });
                
                self.loading = false;

                return res != false;
            },
            async createDb() {
                
                if(!this.db_name_new || this.db_name_new.length == 0) {
                    alert('Enter new database name please');
                    return;
                }

                var self = this;
                self.loading = true;
                self.loading_creating_db = true;

                var formData = new FormData();
                formData.append("db_host", this.db_host);
                formData.append("db_port", this.db_port);
                formData.append("db_username", this.db_username);
                formData.append("db_password", this.db_password);
                formData.append("db_name", this.db_name_new);
                
                var res = await axios.post(
                    '/InitialSetup?cmd=createdb', 
                    formData
                ).catch(function (error) {
                    alert("Something went wrong");
                });
                
                self.loading = false;
                self.loading_creating_db = false;

                await this.getListOfDatabases();

                this.db_name = this.db_name_new;

                this.show_create_db_panel = false;

            },
            async getListOfDatabases() {
                
                var self = this;
                self.loading = true;
                self.loading_db_list = true;
                this.db_list = [];

                var formData = new FormData();
                formData.append("db_host", this.db_host);
                formData.append("db_port", this.db_port);
                formData.append("db_username", this.db_username);
                formData.append("db_password", this.db_password);
                
                var res = await axios.post(
                    '/InitialSetup?cmd=getlistofdb', 
                    formData
                ).catch(function (error) {
                    return false;
                });
                
                self.loading = false;
                self.loading_db_list = false;

                if(res && res.data && res.data.length > 0) {
                    this.db_list = res.data.map(x => x.name  );
                }
            },
            async checkDbExist() {
                var formData = new FormData();
                formData.append("db_host", this.db_host);
                formData.append("db_port", this.db_port);
                formData.append("db_username", this.db_username);
                formData.append("db_password", this.db_password);
                formData.append("db_name", this.db_name);
                
                var res = await axios.post(
                    '/InitialSetup?cmd=checkDbExist', 
                    formData
                ).catch(function (error) {
                    return false;
                });

                return (res && res.data && res.data == 1);
                
            },
        }
    })
</script>
