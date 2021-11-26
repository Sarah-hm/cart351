<!DOCTYPE html>
<html>
<head>
  <title> Google Map API example </title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTBA5GXAPNgRhVIvAxsgsuZpn2ezBGeCY"></script>
    <script>

    // we listen for the window load event ...
    $(document).ready(function(){

      let map = null;
      let latitude = null;
      let longitude = null;

      $('#show-map').on('click',loadAndRun);

      function loadAndRun(){
        console.log("clicked");
      //Get longitude/latitude based if the user enabled geolocation or not
        if(navigator.geolocation) { //If user enable geolocation, set lat/long to their current position
          navigator.geolocation.getCurrentPosition(function(position) {
          latitude = position.coords.latitude;
          longitude = position.coords.longitude;

          //Create the map using the latitude and longitude previously calculated
          let mapProp= {
          //center on montreal
          center:new google.maps.LatLng(latitude, longitude),
          zoom:13,
          };
          map = new google.maps.Map(document.getElementById("map"),mapProp);
          //only adds marker if geolocation was enabled, hence new user and new location
          addMarker(latitude, longitude);
        }); // IF GEO


        //If geolocation is not enabled by the user, make the map centered around downtown Tiohtià:ke
        } else {
          //If user doesn't let geolocation, set their location to downtown Tiohtià:ke (should be last signed in user?)
          latitude = 45.508888;
          longitude = -73.561668;

          //Create the map using the latitude and longitude previously calculated
          let mapProp= {
          //center on montreal
          center:new google.maps.LatLng(latitude, longitude),
          zoom:8,
          };
          map = new google.maps.Map(document.getElementById("map"),mapProp);
        }// iF NO GEO

        //every time a marker is added, a path is traced from this one to the last one
      function addMarker(latitude, longitude){
        let m1Pos = {lat: latitude, lng: longitude};
        let marker = new google.maps.Marker({position: m1Pos});
        //Can make it bounce as such:
        //	let marker = new google.maps.Marker({position: m1Pos, animation:google.maps.Animation.BOUNCE});
        marker.setMap(map);

        let newUser ={lat: latitude,lng: longitude};
        let lastUser ={lat: 45.4042, lng: -71.8929};
        let line = [newUser, lastUser, newUser];

        let webPath = new google.maps.Polyline({
          path:line,
          strokeColor:"#0000FF",
          strokeOpacity:0.8,
          strokeWeight:2
        });
        webPath.setMap(map);
      }
    }
  });
  </script>
</head>
<style>
  /* Always set the map height explicitly to define the size of the div
   * element that contains the map. */
  #wrapper{
    height: 600px;
    width:85%;
    background:rgba(149, 0, 153,0.55);
    margin-left:8%;
    margin-top:10px;
    padding-top:50px;
  }
  #map{

    height:550px;
  }
  h1{
    margin-left:25%;
    margin-top:5%;
    color:rgba(149, 0, 153,0.55);
    font-family: Verdana;
  }
  #show-map{
      margin-left:25%;
  }
  /* Optional: Makes the sample page fill the window. */
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }
</style>
<body>

<h1>Initial Google Map Example</h1>
<button id="show-map">Show Map</button>
<div id = "wrapper">
<div id="map"></div>
</div>
</body>
</html>
