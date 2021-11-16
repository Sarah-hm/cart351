<?php
if($_SERVER['REQUEST_METHOD'] == 'GET')
{
  echo("here");
  //get the data
   //exit;
   $theFile = fopen("files/dataUserInput.txt", "r") or die("Unable to open file!");

   //$i=0;
   $outArr = array();
   $NUM_PROPS = 3;
    //echo("test");
      while(!feof($theFile)) {   //read until eof
        //create an object to send back
        $packObj=new stdClass();
        for($j=0;$j<$NUM_PROPS;$j++){
          $str = fgets($theFile);
          //split and return an array ...
          $splitArr = explode(":",$str);
          $key = $splitArr[0];
          $val = $splitArr[1];
          //append the key value pair
          $packObj->$key = trim($val);
        }
        $outArr[]=$packObj;
      }

      fclose($theFile);
        // var_dump($outArr);
        // Now we want to JSON encode these values to send them to $.ajax success.
      $myJSONObj = json_encode($outArr);
      echo $myJSONObj;
} ?>
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
console.log(<?php echo$myJSONObj ?>);
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
context.fillStyle = "#8ED6FF"; // change the color we are using
// can use properties of the canvas object -> width, height ...
//console.log(canvas);
// note how we can use variables to generalize...
let lineLength = 100;
let x1 = canvas.width/2;
let y1 =canvas.height/2;
let x2 = x1+lineLength;
let y2 = canvas.height/2;
let x3 = x1+(lineLength/2);
let y3 = y1-lineLength;

// lets draw a triangle:
//The lineTo() method adds a new point and creates a line
//TO that point FROM the last specified point in the canvas
//(this method does not draw the line) -rather the stroke/fill does.
context.beginPath(); //start a path
context.moveTo(x1,y1); //where to start drawing
context.lineTo(x2,y2); //lineTo(where to go from last...)
context.lineTo(x3,y3);
context.lineTo(x1,y1);
context.fill();
context.strokeStyle = "#FFFFFF"; // change the color we are using
context.lineWidth =2;
context.stroke();
context.closePath(); //end a path ...
}//ONLOAD
  </script>
</head>
<body>
<canvas id = "testCanvas" width = 500 height =500></canvas>
</body>
</html>
