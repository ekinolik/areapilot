$(document).ready(function() {
   myAddress = $("div#map_canvas").text();
   geocodeInitialize();
   codeAddress(myAddress);
});

var geocoder;
var map;
function geocodeInitialize() {
   geocoder = new google.maps.Geocoder();
   var latlng = new google.maps.LatLng(-34.397, 150.644);
   var myOptions = {
      zoom: 13,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.HYBRID,
   }
   map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}

function codeAddress(address) {
   //var address = document.getElementById("eafp").value;
   if (geocoder) {
      geocoder.geocode( { 'address': address}, function(results, status) {
	    if (status == google.maps.GeocoderStatus.OK) {
		  map.setCenter(results[0].geometry.location);
		  var marker = new google.maps.Marker({
			map: map, 
			position: results[0].geometry.location
		  });
	    } else {
		  codeAddress("Crabby's Boat House, 200 Main St # 101A, Huntington Beach, CA 92648-8102");
	    }
      });
   }
}
