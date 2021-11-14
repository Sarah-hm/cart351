<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <?php
  if($_SERVER['REQUEST_METHOD'] == 'GET')
  {
    // echo("here");
    //get the data
     //exit;
     $theFile = fopen("files/dataUserInput.txt", "r") or die("Unable to open file!");
     //read until eof
     //$i=0;
     $outArr = array();
     $NUM_PROPS = 3;
      //echo("test");
        while(!feof($theFile)) {
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


        exit;
  } ?>
  <body>

hello
  </body>
</html>
