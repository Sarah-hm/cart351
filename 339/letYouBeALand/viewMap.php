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
   <link rel="shortcut icon" type="image/jpg" href="https://img.icons8.com/dotty/80/000000/globe-earth.png"/>
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

       loadAndRun();
       // $('#show-map').on('click',loadAndRun);

       function loadAndRun(){
         console.log("clicked");
       //Get longitude/latitude based if the user enabled geolocation or not

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


           //load JSON file of Native lands (territories) and display them on the map;
           loadAndRunNativeLand();

           //traces the path of all users, adding this last one as the most recent entry
           tracingWebPath(latitude, longitude);
         }); // IF GEO



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
     width:100vw;
     margin-bottom: 5%;
     background-color:#8C9639;
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

 #sideNote{
   font-size:80%;
 }
 </style>
 <body>
 <div id = "container">
   <header>
     <h1>Let you = a_land</h1>
   </header>


 <div id="map">

   <button id="show-map">Access Map</button>

 </div>
 </div>
 </body>
 </html>
