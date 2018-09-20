var map;
var autocomplete;
var marker;
var originMarker = marker;
var restMarkers = [];
var latLngs = [];
var validAddresses = [];
var addressCount = 0;
var addressFilled = false;

function reset(){
	clearMarkers(restMarkers);
	addressCount = 0;
	addressFilled = false;
	latLngs = [];
	validAddresses = [];	  
	originMarker.setMap(null);
}

function initMap(){
	map = new google.maps.Map(document.getElementById('map'), {
		center: {lat:42.355442, lng:-71.065505},
		zoom:15
	});

	originMarker = new google.maps.Marker({
		position: {lat:42.355442, lng:-71.065505},
		map:map
	});

	var input = document.getElementById('addr');
	autocomplete = new google.maps.places.Autocomplete(input);
	autocomplete.bindTo('bounds',map);
	// When the user selects an address from the dropdown, populate the address
    // fields in the form.
    autocomplete.addListener('place_changed', callback);

	
	var geocoder = new google.maps.Geocoder;
	map.addListener('click', function(e) {
	  reset();
	  geocodeLatLng(geocoder, map, e);
	});
}

function showAllMarker(restMarkers){
	for (i=0;i<restMarkers.length;i++){
		restMarkers[i].setMap(map);
	}

	var latlngbounds = new google.maps.LatLngBounds();
	for (var i = 0; i < latLngs.length; i++) {
	    latlngbounds.extend(latLngs[i]);
	}		
	map.fitBounds(latlngbounds);
}

function clearMarkers(restMarkers){
		for(i=0;i<restMarkers.length;i++){
			restMarkers[i].setMap(null);
		}
		restMarkers.length = 0;
}

function callback(){
	reset();
	var place = autocomplete.getPlace();
	if (!place.geometry) {
      // User entered the name of a Place that was not suggested and
      // pressed the Enter key, or the Place Details request failed.
      window.alert("No details available for input: '" + place.name + "'");
      return;
    }
    originMarker = new google.maps.Marker({
		position: place.geometry.location,
		map:map
	});
    originMarker.setMap(map);
    latLngs.push(place.geometry.location);

	for (i=0;i<place.address_components.length;i++){
		if (place.address_components[i].types[0]=="postal_code"){
			$('#zipcode').val(place.address_components[i].long_name);
		}
	}
	addressFilled = true;
}

function geocodeLatLng(geocoder, map, e){

	var latLng = {lat:e.latLng.lat(), lng:e.latLng.lng()};
	geocoder.geocode({'location':latLng},function(results, status){
		if (status == 'OK') {
			if (results[1]) {
					originMarker = new google.maps.Marker({
						position: latLng,
						map:map
					});
				
				originMarker.setMap(map);
				latLngs.push(new google.maps.LatLng(e.latLng.lat(),e.latLng.lng()));
				$('#addr').val(results[1].formatted_address);
				for(i=0;i<results.length;i++){
					if (results[i].types[0]=='postal_code'){
						$('#zipcode').val(results[i].address_components[0].long_name);
						addressFilled = true;
					}
				}
			} else{
				window.alert('No results Found');
			}
		} else{
			window.alert('Geocoder failed due to: '+status)
		}
	});
}

function addMarkerByAddr(address){
	var request={
		query: address,
	};
	service = new google.maps.places.PlacesService(map);
	service.textSearch(request, callbackPlace);
}

function callbackPlace(results, status){
	if (status == google.maps.places.PlacesServiceStatus.OK){
		for (var i = 0; i < results.length; i++) {
            addMarker(results[i]);
          }
	}
}
function addMarker(place){
	var marker = new google.maps.Marker({
      map: map,
      position: place.geometry.location
    });
    restMarkers.push(marker);
    latLngs.push(place.geometry.location);
	if (addressCount == restMarkers.length){
		showAllMarker(restMarkers);
	}
}



function validate(){
	if (!addressFilled){
		return false;
	}
	$.ajax({
		cache:true,
		type:'POST',
		url:'findByZip',
		data:$('#dataForm').serialize(),
		async:true,
		error:function(request){
			alert('Connection error:'+request.error);
		},
		success:function(data){
			start = $('#addr').val();
			end = [];
			resultId = [];
			resultName = [];
			if(data!=""){
				for(i=0;i<data.length;i++){
					end.push(data[i].rest_addr);
				}
				var service = new google.maps.DistanceMatrixService();
				service.getDistanceMatrix({
					origins:[start],
					destinations:end,
					travelMode:'DRIVING'
				},function(response,status){
					if (status!= 'OK') {
						alert('Error was: '+status);
					} else{
						var distances = response.rows[0].elements;
						for (i=0;i<distances.length;i++){
							if (distances[i].status=='OK'){
								if (distances[i].distance.value < 3218.69){
									addMarkerByAddr(data[i].rest_addr);
									addressCount++;
									resultId.push(data[i].rest_id);
									resultName.push(data[i].rest_name);
								}
							} else if(distances[i].status=='NOT_FOUND'){
								alert("Invalid address.");
								return;
							}
						}
						$("#resultForm").html('zipcode: '+$('#zipcode').val()+'<br><br>');
						if(resultName.length!=0){
							for (i=0;i<resultName.length;i=i+2){
								$("#resultForm").html($("#resultForm").html()+'<a href="restPage?id='
								+resultId[i]+'">'+resultName[i]+'</a><br>');
							}
						}else{
							$("#resultForm").html($("#resultForm").html()+'No restuarant near 2 miles.<br>');
						}	
					}							
				});
			}else{
				$("#resultForm").html('No restuarant found in this area.<br>');
			}
		}
	});	
	return false;
}