<?php

//check if there has been something posted to the server to be processed
if($_SERVER['REQUEST_METHOD'] == 'POST')
{

// need to process
 $latitude = $_POST['latitude'];
 $longitude = $_POST['longitude'];
 //run if there is a files array

   //package the data and echo back
    /* make  a new php object*/
    $myPackagedData=new stdClass();
    $myPackagedData->latitude = $latitude ;
    $myPackagedData->longitude = $longitude ;

     /* Now we want to JSON encode these values as a JSON string ..
     to send them to $.ajax success  call back function... */
    $myJSONObj = json_encode($myPackagedData); //if it's not encoded in JSON, javascript won't be able to read it (doesnt read php)
    // echo $myJSONObj;

    $theFile = fopen("files/geolocationData.txt", "a") or die("Unable to open file!");

    fwrite($theFile, $myJSONObj);
    //write a new line after!
    fwrite($theFile,"\n");
    fclose($theFile);
    // echo $myJSONObj;
    exit;
}//POST


//only run in if when page loads (when we got the position)
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['getAjaxOnLoad']))
{
  //echo("here");
  //get the data
   //exit;
   $theFile = fopen("files/geolocationData.txt", "r") or die("Unable to open file!");

   //$i=0;
   $outArr = array();
   //$NUM_PROPS = 3;
    //echo("test");
      while(!feof($theFile)) {   //read until eof
        //create an object to send back
        //get the string
          $str = fgets($theFile);
          $outArr[]= json_decode($str);
        }

      fclose($theFile);
        // var_dump($outArr);
        // Now we want to JSON encode these values to send them to $.ajax success.
      $myJSONObj = json_encode($outArr);
      echo $myJSONObj;
      exit;

} //if
 ?>


<!DOCTYPE html>
<html>
<head>
  <title> Native Web </title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTBA5GXAPNgRhVIvAxsgsuZpn2ezBGeCY&map_ids=4436878dd8c920c0"></script>
    <script>

    // we listen for the window load event ...
    $(document).ready(function(){

      let map = null;
      let mapID ="4436878dd8c920c0";
      let zoomLvl = 13;
      let latitude = null;
      let longitude = null;

      $('#show-map').on('click',loadAndRun);

      function loadAndRun(){
        console.log("clicked");
      //Get longitude/latitude based if the user enabled geolocation or not
        if(navigator.geolocation) {

      //If user enabled geolocation, set lat/long to their current position, send data to database, and send to tracing Path function
          navigator.geolocation.getCurrentPosition(function(position) {
          latitude = position.coords.latitude;
          longitude = position.coords.longitude;

          //Create the map using the latitude and longitude previously calculated
          let mapProp= {
          //center on montreal
          center:new google.maps.LatLng(latitude, longitude),
          zoom:zoomLvl,
          mapId: mapID,
          disableDefaultUI: true,
        //  streetViewControl: false,
          };
          map = new google.maps.Map(document.getElementById("map"),mapProp);
          //only adds marker if geolocation was enabled, hence new user and new location
          addMarker(latitude, longitude);
          //traces the path of all users, adding this last one as the most recent entry
          tracingWebPath(latitude, longitude);
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
          zoom:zoomLvl,
          mapId: mapID,
          };
          map = new google.maps.Map(document.getElementById("map"),mapProp);
          //traces the path of all users
          tracingWebPath(latitude, longitude);
        }// iF NO GEO

        //every time a marker is added, its position (and therefor the user's) is sent to the database
      function addMarker(latitude, longitude){
        let m1Pos = {lat: latitude, lng: longitude};
        let marker = new google.maps.Marker({position: m1Pos});
        //Can make it bounce as such:
        //	let marker = new google.maps.Marker({position: m1Pos, animation:google.maps.Animation.BOUNCE});
      //  marker.setMap(map);

        //Send data to database
          $.ajax({ //ajax is the most general function, we can be more specific with get, etc... Also very similar to other event handlers
            type: "POST",
            url: "index.php",
            data: {latitude: latitude, longitude: longitude},
            cache: false,
            timeout: 600000,
            success: function (response) {
              //console.log(latitude);
              //console.log(response);
            },
            error:function(){
              //This is where we end up if we have an error
              console.log("error occurred");
            }
          });
        };

      function tracingWebPath(latitude, longitude){
        //Get all the data from the database;
        //get the data
        $.ajax({
          url: "index.php",
          type: "get", //send it through get method
          data: {getAjaxOnLoad: "fread"}, //parameter (no form data)
          success: function(response) {
            //use the JSON .parse function to convert the JSON string into a Javascript object
            let parsedJSON = JSON.parse(response);

            //set the empty line array that is going to create the path
            let line = []
            console.log(parsedJSON);
            for (let i = 0; i < parsedJSON.length -1; i++){
              let lati = parseFloat(parsedJSON[i].latitude);
              let long = parseFloat(parsedJSON[i].longitude);

             let coords = {lat:lati, lng: long};
             line.push(coords);
            }
            let webPath = new google.maps.Polyline({
              path:line,
              strokeColor:"#F2F2F2",
              strokeOpacity:0.8,
              strokeWeight:2
            });
            webPath.setMap(map);
          },//SUCCESS
          error: function() {
            console.log("error occurred");
          }
        });
      }//tracing web path
    }// LOAD AND RUN
  }); // DOC READY
  </script>
</head>
<style>
  /* Always set the map height explicitly to define the size of the div
   * element that contains the map. */
  #container{
    height: 100%;
    width:100%;
    background:rgba(149, 0, 153,0.55);
    padding-top:50px;
  }
  #map{
    display:flex;
    height:80vh;
    justify-content:center;
  }
  h1{
    margin-left:25%;
    margin-top:5%;
    color:rgba(0, 0, 0,0.55);
    font-family: Verdana;
  }

  #show-map{

  }
  /* Optional: Makes the sample page fill the window. */
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }
</style>
<body>



<div id = "container">
  <h1>The name of the project</h1>
<div id="map">
  <div class="TerAck">
    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
  </div>
  <button id="show-map">Show Map</button>
</div>
</div>
</body>
</html>
