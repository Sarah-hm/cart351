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



//NEW:: make a helper array to hold the new values for each element (xPos,yPos, and sinAngle)
let positionsArray=[];
for(let i =0; i<parsedJSON.length-1; i++){
  //Set a random position for every user every time we reload the page
  let xPos = Math.random() * (canvas.width)
  let yPos = Math.random() * (canvas.height)
  positionsArray.push({x:xPos,y:yPos,sinAngle:0});

}


requestAnimationFrame(run);
//Every frame, the auras will pulsate and redraw themselves (with the dim effect) according to those pulsating radius values;


//Make canvas resize to the window
window.addEventListener('resize', function(event) {
//console.log("resize");
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;
//redraw users over resized canvas
//drawUser();
});



//our main draw loop....
function run()
{
  //clear after drawing all ...
  context.clearRect(0,0,canvas.width,canvas.height);

  //Run through all the users data and create an object based on their entries;
  for (let i=0; i<parsedJSON.length-1;i++){

    //set pulsating angle


  //draw users based on data retrieved
  //and the xPos, yPos and its own sin angle
  drawUser(parsedJSON[i].color, parsedJSON[i].size, parsedJSON[i].speed, positionsArray[i].x,positionsArray[i].y,positionsArray[i].sinAngle);

  //change after each frame the sin angle
  positionsArray[i].sinAngle +=0.001*parsedJSON[i].speed;
}



//function for drawing the user (according to their user Input Data)
function drawUser(color, size, speed, xPos, yPos,sinAngle){
  console.log(sinAngle)
//How many circles are going to be used to create a dim effect
let NUM_DIM = 20;
//console.log(color);
//Set the color from the file into the hex converter function
let convertedColor = hexToRgb(color);

//Set original values;

let radius  = parseInt(size); //55
let startAngle = 0;
let endAngle = Math.PI * 2 //full rotation


//go draw circle "auras" every frame (while animating)
//sinAngle +=0.005
//set pulsion as the original size of the object with a sin movement
let  pulsion =  parseInt(radius) - (Math.sin(sinAngle)*50);

//Draw circles (NUM_DIM) that get bigger but less opaque to fake a dim light effect around every user "aura";
for (let j=0; j<NUM_DIM; j++){
//overwrite new size on top of original size (with pulsion) to create a dim effect
newSize = pulsion + (j*7);
//set alpha to 0.1 to every circle create a more opaque layer on top of eachother
let alpha = 0.05
context.fillStyle = "rgba("+convertedColor.r+", "+convertedColor.g+", "+convertedColor.b+", "+alpha+")"; // change the color we are using
//context.arc(xPos,yPos,newSize,startAngle,endAngle, true);
context.fillRect(xPos-newSize/2,yPos-newSize/2,newSize,newSize);
//console.log(size);
context.fill(); // set the fill
}//FOR (DIM)

  }//drawUser
  requestAnimationFrame(run);
}////run

},//SUCCESS
error: function() {
  console.log("error occurred");
}
});

 //MOVE DOWN
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


}//ONLOAD
  </script>
</head>
<body>
<canvas id = "Moodboard" width = 500 height =500></canvas>
</body>
</html>
