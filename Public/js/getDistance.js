var count = 1;
var markers = [];
function initMap(){
	var map;
	map = new google.maps.Map(document.getElementById('map'), {
		center: {lat:42.355442, lng:-71.065505},
		zoom:15
	});
	map.addListener('click', function(e) {
	  var lat = e.latLng.lat();
	  var lng = e.latLng.lng();
	  var marker = null;
	  if (count<=3){
	  	if (count==1){
	  		clearMarker(markers);
	  	}
	  	$('#lat'+count).val(lat);
	  	$('#lng'+count).val(lng);
	  	marker = new google.maps.Marker({
	  		position:{lat:e.latLng.lat(),lng:e.latLng.lng()},
	  		map:map
	  	});
	  	marker.setMap(map);
	  	markers.push(marker);
	  	count++;
	  }
	});
}

function clearMarker(markers){
	while(markers.length!=0){
		var marker = markers.pop();
		marker.setMap(null);
	}
}

function validate(){
	var start = new google.maps.LatLng($('#lat1').val(),$('#lng1').val());
	var end = [];
	end.push(new google.maps.LatLng($('#lat2').val(),$('#lng2').val()));
	end.push(new google.maps.LatLng($('#lat3').val(),$('#lng3').val()))
	var service = new google.maps.DistanceMatrixService();
	service.getDistanceMatrix({
		origins: [start],
		destinations: end,
		travelMode: 'DRIVING'
	},function(response,status){
		var result = '';
		if (status!= 'OK') {
			alert('Error was: '+status);
		} else{
			var status = response.rows[0].elements[0].status;
			if (status=='OK'){
				var kekka = response.rows[0].elements;
				for (i=0;i<kekka.length;i++){
					result = result + kekka[i].distance.text+ ' ';
				}
			} else{
				if (status=='ZERO_RESULTS'){
					result = 'No valid route.';
				} else{
					result = 'Invalid position.';
				}
			}
			$('#result').html('Distance is: '+result);
		}
	});
	count = 1;
	return false;
}