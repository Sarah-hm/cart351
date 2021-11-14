<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exercice 3</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
 <link rel="stylesheet" href="css/exerciceStyle.css">
 <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
 <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  </head>
<?php
//check if there has been something posted to the server to be processed
if($_SERVER['REQUEST_METHOD'] == 'GET')
{
  //check if there is a new color (hence, new size and speed too); if so, append them to file;
  if(isset($_GET['color'])){

   $color = $_GET['color'];
   $size = $_GET ['size'];
   $speed = $_GET['speed'];
  // echo($color);
   //If you use fopen() on a file that does not exist, it will create it,
   //given that the file is opened for writing (w) or appending (a).
   $theFile = fopen("files/dataUserInput.txt", "a") or die("Unable to open file!");

  fwrite($theFile, "COLOR:".$color."\n");
  fwrite($theFile, "SIZE:".$size."\n");
  // fwrite($theFile, $color); // no newline...
  fwrite($theFile,  "SPEED:".$speed."\n");

  fclose($theFile);
//  echo("WE HAVE SUCCESSFULLY read the vars AND saved to the file ... ");
   // you must exit
exit;
} //IF COLOR SET

}//IF 'GET'
 ?>


  <body>
    <div class="container">
      <div id="question">
        <h2>How have you been feeling today?</h2>
      </div>
      <!-- Where the user will preview the object they are creating (dynamically changed via the script)-->
      <div id="preview">

      </div>
      <form id="userInputForm">
      <label for="color">Color</label><input type = "color" id="colorPicker" name="color" value="#e66465"required>
      <label for="size">Size</label><input type="range" id="sizeSlider" name="size" min="10" max="100">
      <label for="speed">Speed</label>  <input type="range" id="speedSlider" name="speed" min="1" max="20">
      <input type = "submit" name = "submit" value = "send" id =buttonS />
      </form>
    </div>
  </body>

  <script>
  $(document).ready (function(){
      $("#userInputForm").submit(function(event) {
         //stop submit the form, we will post it manually. PREVENT THE DEFAULT behaviour ...
        event.preventDefault();
       //console.log("button clicked");
       let data =$('#userInputForm').serializeArray(); //serializeArray : puts form data and puts it into an array;
       /*for console log */
       for (let valuePairs of data.entries()) {
        // console.log(valuePairs);
       }

       // P3
           $.ajax({
             type: "GET",
              url: "index.php",
              data: data,
              dataType: "text", /*response will be text */
              cache: false,
              timeout: 600000,
              success: function (response) {
                //reponse is a STRING (not a JavaScript object -> so we need to convert)
                //   console.log("we had success!");
                //   console.log(response);
                   //reset the form
                   $('#userInputForm')[0].reset();
                   //go to landing page
                   window.location.replace("landing.php")
             },
             error:function(){
            console.log("error occurred");
          }
        });
    });
 });
   </script>
</html>
