(function($){
    $(document).ready(async function(){
        console.log(settings);
        mapboxgl.accessToken = settings.MAPBOX_API_KEY;
        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            //center: [12.550343, 55.665957],
            //zoom: 8
        });

        async function getMarkers(){
            var ajax = await $.ajax(settings.endpointURL);
            ajax=ajax[0];
            return ajax;
        }

//get markers via endpoint
        let createdMarkers=[];
        data=await getMarkers();
        if (data.status==="ok"){
            data.data.map(marker =>{
                createdMarkers.push(new mapboxgl.Marker().setLngLat([marker.longitude,marker.latitude]).addTo(map));
            })
        }
    })
})(jQuery)

