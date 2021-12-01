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
  <title> Let you = a_land</title>
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
      let territoryColors = ['#A4BB35','#8C9639','#294324','#363B1E'];


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
        // streetViewControl: false,
          };
          map = new google.maps.Map(document.getElementById("map"),mapProp);
          //only adds marker if geolocation was enabled, hence new user and new location
          addMarker(latitude, longitude);

          //load JSON file of Native lands (territories) and display them on the map;
          loadAndRunNativeLand();

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

      function loadAndRunNativeLand(){
// https://native-land.ca/api/index.php?maps=territories

        $.get(
      "https://native-land.ca/api/index.php?maps=territories",
      function (data) {
        //success
        //step 1: console.log the result
        //console.log(data.length);
        //set boolean to true
        //loaded = true;

        // parse data into object

  //Run through data and divide the polygons (puts it in temp) data.length
        for (let i = 0; i < data.length ; i ++){
          // console.log(i);
          //console.log(data[i]);
          let link = data[i].properties.description
          console.log(link);
        // console.log(data[i].geometry.coordinates)
         let temp = data[i].geometry.coordinates;
        //  let geomArray = data[i].geometry.coordinates[0];

        //Puts all polygon lines (in temp) into their own arrays (geomArray)
        for(let j = 0; j< temp.length; j++){
          //console.log (temp[j]);
          let geomArray = temp[j];

          //set the empty line array that is going to create the path
          let line = []
          //Parse all the lines' coordinates (latitude, longitude) and push them into the array
          for (let k = 0; k < geomArray.length ; k ++){
          //  console.log(geomArray[k])
            let coordinates = geomArray[k]
            let long = parseFloat(coordinates[0]);
            let lati = parseFloat(coordinates[1]);
            let coords = {lat:lati, lng: long};
            line.push(coords);
            // console.log(lati);
            // console.log(long);
          }//FOR GEOMARRAY (coordinates)

          let territoryFillColorIndex = Math.floor(Math.random()*territoryColors.length);
          let terrFillColor = territoryColors[territoryFillColorIndex];

          let territory = new google.maps.Polygon({
            path:line,
            strokeColor:"#F2F2F2",
            strokeOpacity:0.8,
            strokeWeight:0,
            fillColor:terrFillColor,
            fillOpacity:0.1

          });
          territory.setMap(map);
          addListenersOnPolygon(territory, link);
              console.log("NEW");
        }// FOR temp (lines)

       }// FOR data (polygons)



      }// GET function
    )//GET
      //fail
      .fail(function () {
        console.log("error");
      });

      }

      let addListenersOnPolygon = function(polygon, link) {
  google.maps.event.addListener(polygon, 'click', function (event) {
    window.open(link, '_blank').focus();
  });
}


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
          //  console.log(parsedJSON);
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
    min-height: 100%;
    max-width: 100vw;
    background-color:#DCDCDC;
    padding:0;
  }
  #map{
    display:flex;
    height:80vh;
    justify-content:center;
    flex-wrap:wrap;
  }

  header {
    padding-left: 3%;
    padding-top:1%;
    margin:0;
    font-size: 3vh;
    color:rgba(0, 0, 0,0.55);
    font-family: Verdana;
  }

  #show-map{
    height: 6vh;
    width:100%;
    font-size:2vh;
  }

  .textBox{
    padding-bottom:3%;
  }

  .terAck {
    font-family:sans-serif;
    padding-bottom:1%;
    padding-left:3%;
    padding-right:3%;
  }
  /* Optional: Makes the sample page fill the window. */
  html, body {
    min-height: 100%;
    margin: 0;
    background-color:  #DCDCDC;
  }

/* box shadow code from : https://shadows.brumm.af/*/
  button {
    margin-bottom: 5%;
    background-color:#D7D7D7;
    border:none;
    box-shadow:
  2.8px 2.8px 2.2px rgba(0, 0, 0, 0.02),
  6.7px 6.7px 5.3px rgba(0, 0, 0, 0.028),
  12.5px 12.5px 10px rgba(0, 0, 0, 0.035),
  22.3px 22.3px 17.9px rgba(0, 0, 0, 0.042),
  41.8px 41.8px 33.4px rgba(0, 0, 0, 0.05),
  100px 100px 80px rgba(0, 0, 0, 0.07)
;
transition: all 2s;
  }

  button:hover{
  background-color:#353535;
  color:white;
  }

.inTextLink {
  color:#000000;
}

.inTextLink:hover {
  color:#000000;
  font-style: italic;
}
</style>
<body>
<div id = "container">
  <header>
    <h1>Let you = a_land</h1>
  </header>


<div id="map">
  <div class="TextBox">
  <div class="terAck">
    <h2>By Sarah Hontoy-Major</h2>
    <h3>Presented to Miranda Joy Smitheram as part of DART 339 : second skin and softwear</h3>
    <h4>Territorial Acknowledgement</h4>
  <p>Given that this project was created as part of an academic assignement in the Fine Arts Faculty of Concordia University in Tiohtià:ke/Montréal,
    I would like to begin by acknowledging that Concordia University is located on unceded Indigenous lands.
     The Kanien’kehá:ka Nation is recognized as the custodians of the lands and waters on which the servers of this website resides.
     Tiohtià:ke/Montréal is historically known as a gathering place for many First Nations. Today, it is home to a diverse population of
     Indigenous and other peoples. We respect the continued connections with the past, present and future in our ongoing relationships with
     Indigenous and other peoples within the Montreal community.
 </p>
 <p>Learn more about Concordia's territorial acknoweledgement <a class = "inTextLink"href="https://www.concordia.ca/indigenous/resources/territorial-acknowledgement.html">right here </a></p>
  <p> By clicking on the following button to engage with this project and website, you are acknowledging the unceded Indigenous lands of the Kanien’kehá:ka Nation, as well as
      the lands on which you stand --wherever that might be, and vow to deepen your understanding and knowledge of those lands.</p>

      <h4> Project Explanation</h4>
<p>Let you = a_land aims at facilitating its users' visualization, interpretation and knowledge of the lands on which they stand, as well as the interconnectedness between them based on
  their own situatedness. The main interface is composed of three layers, two of them using API keys from both <a class = "inTextLink"href="https://developers.google.com/maps">Google Maps</a> and <a class = "inTextLink"href="https://native-land.ca/resources/api-docs/">Native-land Digital</a></p>
<p>If you are using the internet in 2021, API keys are all around you everyday. They provide access to some of the world's largest databases and apps, and hence tonnes of terabytes of raw knowledge.
   Applications like Google, Spotify, New York Times and even NASA each use API keys to make their data available to anyone* wanting access to it.</p>
<p>Hence, the first layer of [] was built using the Google Maps API, making the user able to navigate on the most used <a class = "inTextLink"href="https://pro.arcgis.com/en/pro-app/2.8/help/mapping/properties/mercator.htm">Mercator projection </a>
  map on the internet.This layer was greyed out, because as much as it is necessary for the user to realistically situated themself geographically,
  it was not the intention to put the main focus on this layer.</p>
  <p>The second layer was made with <a class = "inTextLink"href="https://native-land.ca/resources/api-docs/">Native-land Digital</a> API, which gives free public access to virtually all the data on their own website. While recreating Indigenous territorial maps on top of Google Maps API,
    they aim at creating and fostering "conversations about the history of colonialism, Indigenous ways of knowing, and settler-Indigenous relations, through educational resources such as [their] map". Their
     map is in continuous development and does not claim to be an official source of knowledge.
     <p>
The third layer is how the user is called to interact with the interface. The path that traces back users to users in chronological order of log in was meant to create an intertwined thread of some kind, a sort of second-skin weaved fabric placed on top of our lands, connecting our own situatedness with that of other users of the interface.
</p>
     <h4>How does it work?</h4>
     <p>The goal of this interface is to create a visual circuit and living organism between the users, so long as they keep connecting to the website. This is done when users click on the button at the bottom of this page,
       given they grant their browser access to their geolocation (you will be redirected to downtown Tiohtià:ke/Montréal if you do not grant access). </p>
       <p>The three layered map will appear, centering your geolocation on the screen. A path will be
        drawn from yourself to the last user who connected to the website. You can now explore your surroundings, gain a greater awareness of your situatedness on the land you stand on, and your interconnectedness with other
        users of this land. A non extensive number of Indigenous territories have been drawn on top of the map you are already familiar with, and clicking on any of them will bring you to a description and more links to further
        your research about the land on which you stand and the communities that have lived on it and protected it.</p>
  </div>
  <button id="show-map">I acknowledge the unceded lands on which I stand</button>
    </div>
</div>
</div>
</body>
</html>
