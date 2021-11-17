<?php

//check if there has been something posted to the server to be processed
if($_SERVER['REQUEST_METHOD'] == 'POST')
{

// need to process
 $color = $_POST['color'];
 $size = $_POST['size'];
 $speed = $_POST['speed'];
 //run if there is a files array

   //package the data and echo back
    /* make  a new php object*/
    $myPackagedData=new stdClass();
    $myPackagedData->color = $color ;
    $myPackagedData->size = $size ;
    $myPackagedData->speed = $speed ;

     /* Now we want to JSON encode these values as a JSON string ..
     to send them to $.ajax success  call back function... */
    $myJSONObj = json_encode($myPackagedData); //if it's not encoded in JSON, javascript won't be able to read it (doesnt read php)
    // echo $myJSONObj;

    $theFile = fopen("files/dataUserInput.txt", "a") or die("Unable to open file!");

    fwrite($theFile, $myJSONObj);
    //write a new line after!
    fwrite($theFile,"\n");
    fclose($theFile);

    // echo $myJSONObj;
    exit;
}//POST
 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exercice 3</title>

 <link rel="stylesheet" href="css/exerciceStyle.css">
 <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
 <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  </head>

  <body>
    <div class="container">
      <div id="question">
        <h2>How have you been feeling today?</h2>
      </div>
      <!-- Where the user will preview the object they are creating (dynamically changed via the script)-->
      <div id="preview">

      </div>
      <form id="userInputForm" action = "" enctype ="multipart/form-data">

      <label for="color">General Mood</label><input type = "color" id="colorPicker" name="color" value="#8698b8"required>
      <label for="size">Confidence Level</label><input type="range" id="sizeSlider" name="size" min="10" max="200">
      <label for="speed">Anxiety Level</label>  <input type="range" id="speedSlider" name="speed" min="0" max="1">
      <input type = "submit" name = "submit" value = "send" id = "buttonS" />

      </form>
    </div>
  <script>
  $(document).ready (function(){
      $("#userInputForm").submit(function(event) {
         //stop submit the form, we will post it manually. PREVENT THE DEFAULT behaviour ...
        event.preventDefault();
    //   console.log("button clicked");
       let form = $('#userInputForm')[0];
     let dataForSending = new FormData(form);
     /*console.log to inspect the data */
    // for (let pair of dataForSending.entries()) {
    //   console.log(pair[0]+ ', ' + pair[1]);
    //   }

     $.ajax({ //ajax is the most general function, we can be more specific with get, etc... Also very similar to other event handlers
            type: "POST",
            enctype: 'multipart/form-data',
            url: "index.php",
            data: dataForSending, //both can be called "data", this is just to make it clearer. 1st data : necessary parameter; 2nd data(ForSending): variable that comes from higher
            processData: false,//prevents from converting into a query string
            contentType: false, //contentType is the type of data you're sending,i.e.application/json; charset=utf-8
            cache: false,
            timeout: 600000,
            success: function (response) {
              //  console.log("we had success!");
          //  console.log(response);

            //go to landing page
            window.location.replace("landing.php")
           },
           error:function(){
           //This is where we end up if we have an error
       console.log("error occurred");
        }
    });
    });
 });
   </script>
  </body>
</html>
