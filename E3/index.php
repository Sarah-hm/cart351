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
 <script>

//Set the color to whichever color was picked by user
$(function(){
  //let colorPicker = $('#colorPicker');
  document.getElementById("colorPicker").onchange = function() {
  objectColor = this.value;
}
});

 //Set the slider for chosing the size of the object, and set the selected value to the object's size var
  $( function() {
  let sizeSlider =  $( "#sizeSlider" ).slider({
      min:10,
      max:100,
    change: function(event, ui) {
      let objectSize = ui.value;
 }
    });
  } );

//Set the slider for chosing the speed at which the object 'breaths' (blinks?) and set it to the object's speed var
  $( function() {
    $( "#speedSlider" ).slider({
      min:10,
      max:100,
      change: function(event, ui) {
        let objectSpeed = ui.value;
      }
    });
  } );


  </script>
  </head>
  <body>
    <div class="container">
      <div id="question">
        <h2>How have you been feeling today?</h2>
      </div>
      <!-- Where the user will preview the object they are creating (dynamically changed via the script)-->
      <div id="preview">

      </div>
      <div id="userInput">
        <div class="picker">
            <h3>Color</h3>
          <input type="color" id="colorPicker" name="color"
                 value="#e66465">
                  <!--<label for="color"> Color </label>-->
        </div>
        <div class="picker">
        <h3>Size</h3>
          <div  class="slider" id="sizeSlider"></div>
        </div>
        <div class="picker">
        <h3>Speed</h3>
          <div  class="slider" id="speedSlider"></div>
        </div>

      </div>
      <div id="formSubmission">
          <input type = "submit" name = "submit" value = "Submit" id =buttonS />
      </div>

    </div>
  </body>
</html>
