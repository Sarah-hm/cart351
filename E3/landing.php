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
//console.log(parsedJSON[0].color)

//draw the canvas and do shtuff in it

// get the canvas
let canvas = document.getElementById("communityBoard");
//Make canvas the size of the window
canvas.width = window.innerWidth;
canvas.height = window.innerHeight ;

//get the context
let context = canvas.getContext("2d");
//would usually be black

//draw users based on data retrieved
drawUser();

//Make canvas resize to the window
window.addEventListener('resize', function(event) {
//console.log("resize");
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;
//redraw users over resized canvas
drawUser();
});

//function for drawing the user (according to their user Input Data)
function drawUser(){
  //How many circles are going to be used to create a dim effect
  let NUM_DIM = 20;
  //Set the ratio to which the radius (size) should change everytime a new circle gets drawn
  let RATIO_RADIUS = 3
  //Set the ratio to which the alpha should change everytime a new circle gets drawn
  //In order to get from full opacity to 0 -> divide value of full opacity by the number of circles drawn to fake the dim effect
  let RATIO_ALPHA = 1/NUM_DIM


//convert HEX color from dataUserInput file to RGB value (to access Alpha value);
//Code found here :  https://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
function hexToRgb(hex) {
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}

//Set the color from the file into the hex converter function
let color = hexToRgb(parsedJSON[0].color);
//Set original alpha to full opacity
let alpha = 1;

//Set original values;
let xPos = canvas.width/3;
let yPos = canvas.height/2;
let radius  = parseInt(parsedJSON[0].size);
let startAngle = 0;
let endAngle = Math.PI * 2 //full rotation

//Draw circles (NUM_DIM) that get bigger but less opaque to fake a dim light effect around every user "aura";
for(let i=0; i<NUM_DIM; i++){
console.log(alpha);

context.fillStyle = "rgba("+color.r+", "+color.g+", "+color.b+", "+alpha+")"; // change the color we are using
  context.arc(xPos,yPos,radius,startAngle,endAngle, true);
  context.fill(); // set the fill
  // context.lineWidth=2; //change stroke
  // context.stroke();//set the stroke

  // //add to radius while decreasing the alpha (to fake dim effect)
  radius = radius + 3;
  alpha = alpha - 0.05;
}//FOR

}//DRAWUSER

},//SUCCESS
error: function() {
  console.log("error occurred");
}
});


}//ONLOAD
  </script>
</head>
<body>
<canvas id = "communityBoard" width = 500 height =500></canvas>
</body>
</html>
