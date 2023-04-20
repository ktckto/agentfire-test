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

        async function getMarkers(tagIdArray){

            var ajax = await $.ajax(settings.endpointURL+'/markers',{
                data:{
                  "tags":tagIdArray
                },
                beforeSend: function(xhr){
                    xhr.setRequestHeader ("X-WP-Nonce", settings.nonce);
                }
            });

            return ajax;
        }
        let createdMarkers=[];
        function renderMarkers(data){
            if (createdMarkers!==null) {
                for (var i = createdMarkers.length - 1; i >= 0; i--) {
                    createdMarkers[i].remove();
                }
            }
            const markers = data.data;
            const isOnlyOwn=$('#filter-own').prop('checked');
            markers.map(marker =>{
                let color="blue";
                if ((typeof data?.user_id !== 'undefined') && (marker?.user_id===data?.user_id)){
                    color="red";
                }
                else if(isOnlyOwn){
                    return;
                }
                createdMarkers.push(new mapboxgl.Marker({ color: color})
                    .setLngLat([marker.longitude,marker.latitude])
                    .onClick((e) => {
                        //e.stopPropagation();
                        showPopup([marker.longitude,marker.latitude],marker.id);
                    })
                    .addTo(map));
            })
        }

        async function fetchAndRenderWithFilters(){
            let filters=[];
            $('#tags-list input[type=checkbox]:checked').map(function(a,b){filters.push(b.name)});
            const data=await getMarkers(filters);
            if (data.status==="ok"){
                renderMarkers(data);
            }

        }



        //Click on marker event
       async function showPopup(longLat,id){
            const data=await getMarkerDateTitle(id);
            new mapboxgl.Popup({ offset: [0, -15] })
                .setLngLat(longLat)
                .setHTML(
                    `<h3>${data.title}</h3><p>Date: ${data.date}, ID: ${data.id}</p>`
                )
                .addTo(map);
        }
        async function getMarkerDateTitle(id){
            var ajax = await $.ajax(settings.endpointURL+'/getMarkerDateTitle?id='+id,{
                beforeSend: function(xhr){
                    xhr.setRequestHeader ("X-WP-Nonce", settings.nonce);
                }
            });
            return ajax;
        }

    await fetchAndRenderWithFilters();
    $('input#filter-own').on('change',async function(){
        await fetchAndRenderWithFilters();
        });

    $('input.filter-checkbox').on('change',async function(){
        await fetchAndRenderWithFilters();

    });


    map.on('click',function (e){
        console.log(e.lngLat);
        //show modal with lngLat
        const $latitude=$('#modal-latitude-input');
        const $longitude=$('#modal-longitude-input');
        $latitude.val(e.lngLat.lat);
        $longitude.val(e.lngLat.lng);
        $('#modal-addMarker').modal('toggle');
    });

    //activate modal
        $('#modal-addMarker').modal({
            keyboard: false
        });
        //activate select2
        $('.select2-modal-input').select2({
            dropdownParent: $('#modal-addMarker'),
            width:"100%"
        });
        $('#modal-btn-add').on('click',async function (e){
            const $latitude=$('#modal-latitude-input');
            const $longitude=$('#modal-longitude-input');
            const $name=$('#modal-name-input');
            const $tags=$('#modal-tags');
            const data={
                'latitude':$latitude.val(),
                'longitude':$longitude.val(),
                'name':$name.val(),
                'tags':$tags.val()
            };
            await $.ajax(settings.endpointURL+'/addMarker',{
                type:"POST",
                data:data,
                beforeSend: function(xhr){
                    xhr.setRequestHeader ("X-WP-Nonce", settings.nonce);
                },
                success:async function (){
                    alert("Added marker");
                    await fetchAndRenderWithFilters();
                    $('#modal-addMarker').modal('toggle');
                },
                error: function() {
                    alert("Error");
                }
            });
        });
    })
})(jQuery)

