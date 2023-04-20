(function($){
    $(document).ready(async function(){
        console.log(settings);
        mapboxgl.accessToken = settings.MAPBOX_API_KEY;
        // Override internal functionality
        mapboxgl.Marker.prototype.onClick = function(handleClick) {
            this._handleClick = handleClick;
            return this;
        };
        mapboxgl.Marker.prototype._onMapClick = function(t) {
            const targetElement = t.originalEvent.target;
            const element = this._element;
            if (this._handleClick && (targetElement === element || element.contains((targetElement)))) {
                this.togglePopup();
                this._handleClick();
            }
        };
        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            //center: [12.550343, 55.665957],
            //zoom: 8
        });

        async function getMarkers(){
            var ajax = await $.ajax(settings.endpointURL,{
                beforeSend: function(xhr){
                    xhr.setRequestHeader ("X-WP-Nonce", settings.nonce);
                }
            });
            ajax=ajax[0];
            return ajax;
        }

//get markers via endpoint
        let createdMarkers=[];
        data=await getMarkers();
        if (data.status==="ok"){
            data.data.map(marker =>{
                let color="blue";
                if ((typeof data?.user_id !== 'undefined') && (marker?.user_id===data?.user_id)){
                    color="red";
                }
                createdMarkers.push(new mapboxgl.Marker({ color: color})
                    .setLngLat([marker.longitude,marker.latitude])
                    .onClick(() => {
                        showPopup([marker.longitude,marker.latitude],marker.id);
                    })
                    .addTo(map));
            })
        }



        //Click on marker event
        function showPopup(longLat,id){
            new mapboxgl.Popup({ offset: [0, -15] })
                .setLngLat(longLat)
                .setHTML(
                    `<h3>${id}</h3><p>${id}</p>`
                )
                .addTo(map);
        }



    })
})(jQuery)

