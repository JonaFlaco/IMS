<template id="tpl-gps-map-component">
    
    <div>

        <div v-if="lat && lng">
            <span class="ml-2"> {{ lat }}, {{ lng }} </span>
            <div 
                load="gps_initMap()" 
                :id="name + '-map-div'" 
                style="height: 350px !important; position:relative !important; width:100% !important;"
            ></div>
        </div>
        <div v-else>
            <span class="ml-2"> N/A</span>
        </div>

    </div>
                   
</template>

<script>

    Vue.component('gps-map-component', {
        template: '#tpl-gps-map-component',
        props: ['lat', 'lng', 'name'],
        async mounted() {
            this.initMap();
        },
        methods: {
            initMap() {

                if(this.lat > 0 && this.lng > 0){

                    var map = new google.maps.Map(document.getElementById(this.name + '-map-div'), {
                        center: new google.maps.LatLng(this.lat,this.lng),
                        zoom: 12
                    });
                    
                    var marker = new google.maps.Marker({
                        map: map,
                        position: new google.maps.LatLng(this.lat, this.lng),
                        animation: google.maps.Animation.DROP   
                    });

                }

            }
        },
    })

</script>
