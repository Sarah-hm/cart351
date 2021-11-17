<?php
//only run in if when page loads
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['getAjaxOnLoad']))
{
  //echo("here");
  //get the data
   //exit;
   $theFile = fopen("files/dataUserInput.txt", "r") or die("Unable to open file!");

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
  <title> Community board</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <style>
  body{
    margin:0;
    padding:0;
  }
  /* CAN ADD CSS  - > just like any other HTML element */
  canvas{
    background:black;
    position:absolute;
    top:0%;
    left:0%;

}
  </style>
<!DOCTYPE html>
<html>
<head>
  <title> Examples </title>
  <style>
  body{
    margin:0;
    padding:0;
  }
  canvas{
    background:black;
}
  </style>

  <script>

  //userShape CLASS
class userShape{


  constructor(x,y,color, size, speed){

  }
}

  //ON LOAD
  window.onload = function(){

//directly here we get the data ...
//get the data
$.ajax({
  url: "landing.php",
  type: "get", //send it through get method
  data: {getAjaxOnLoad: "fread"}, //parameter (no form data)
  success: function(response) {
  //Do Something
  //console.log("responded" +response);
  //use the JSON .parse function to convert the JSON string into a Javascript object
  let parsedJSON = JSON.parse(response);
//  console.log(parsedJSON[0].size)
},
error: function() {
  console.log("error occurred");
}
});


// get the canvas
let canvas = document.getElementById("testCanvas");
//Make canvas the size of the window
canvas.width = window.innerWidth;
canvas.height = window.innerHeight ;
//Make canvas resize to the window
window.addEventListener('resize', function(event) {
//console.log("resize");
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;
});
//get the context
let context = canvas.getContext("2d");
//would usually be black

//console.log(parsedJSON[0].color)

context.fillStyle = "#8ED6FF"; // change the color we are using
let xPos = canvas.width/3;
let yPos = canvas.height/2;
let radius  = 40;
let startAngle = 0;
let endAngle = Math.PI * 2 //full rotation
//context.strokeStyle = "#FF0000"; // change the color we are using
context.arc(xPos,yPos,radius,startAngle,endAngle, true);
context.fill(); // set the fill
context.lineWidth=2; //change stroke
context.stroke(0);//set the stroke

}//ONLOAD
  </script>
</head>
<body>
<canvas id = "testCanvas" width = 500 height =500></canvas>
</body>
</html>
