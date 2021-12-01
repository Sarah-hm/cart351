<?php
// this is the file to setup your quereis ... and results are returned to the client.js ////

require('dbScripts/openDB.php');
require ('helperArrays.php');
//check if there has been something posted to the server to be processed
/*********** TO COMPLETE FOR EXERCISE 4 **********************************
** 1: you need to implement the correct sqlite queries (three->six) as per the exercise description.
** 2: please use the following if statements to implement (1). DO NOT CHANGE the if construct -
** only assign the correct query to the appropriate $selectedQuery var.
** 3: you DO NOT NEED to change the manner in which one iterates through the result,
*** packages it and sends back to the javascript success callback...
** 4: you MAY append helper arrays (from helperArrays.php (you can add)) to the end of the
**  outArray if you wish. See the  examples for suggested implementation
**/
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["select-query"]))
{

  $outArr = array();
  try {

    $selectedQuery = "";

  if($_GET["select-query"] =="onload"){
    $selectedQuery="SELECT * FROM dataStuff";
  }

  if($_GET["select-query"] =="default"){
    //sAbINe implemented :)
    $selectedQuery="SELECT * FROM dataStuff";
  }

    if($_GET["select-query"]==="one"){
      //sAbINe implemented :)
      $selectedQuery = "SELECT * FROM dataStuff ORDER BY after_mood";

    }

    if($_GET["select-query"]==="two"){
      //sAbINe implemented :)
      $selectedQuery = "SELECT * FROM dataStuff,events WHERE dataStuff.eID = events.eventID ORDER BY weather";

    }

    if($_GET["select-query"]==="three"){
      //TO IMPLEMENT: all entries where after mood is positive
     $selectedQuery = "SELECT * FROM dataStuff WHERE after_mood IN ('happy','neutral','calm','serene','well')";
    }

    if($_GET["select-query"]==="four"){
      //TO IMPLEMENT: Select all entries and order by event
        $selectedQuery = "SELECT * FROM dataStuff, events WHERE dataStuff.eID = events.eventID ORDER BY eventName";
      }

    if($_GET["select-query"]==="five"){
      //TO IMPLEMENT: Select all entries on monday/tuesday and order by event_affect_strength
      $selectedQuery = "SELECT * FROM dataStuff WHERE day = 'Monday'OR day = 'Tuesday' ORDER BY event_affect_strength";
    }

    else if($_GET["select-query"]==="six"){
      //TO IMPLEMENT: Both start and after mood are negative, ordered by weather
      $selectedQuery = "SELECT * FROM dataStuff WHERE start_mood IN ('sad','angry','neutral','calm', 'anxious','moody','hurt') AND after_mood IN ('sad','angry','neutral','calm', 'anxious','moody','hurt') ORDER BY weather";
  }

  if($selectedQuery!==""){
      $result = $file_db->query($selectedQuery);
      if (!$result) die("Cannot execute query.");

      //go through every row (as an associative array and append to the array)
      while($row = $result->fetch(PDO::FETCH_ASSOC))
      {
        $outArr[] = $row;
      }//end while

      //new :: let's add our helper array of days for the default query AT THE END::
      if(($_GET["select-query"] =="onload") ||($_GET["select-query"] =="default")) {
        $outArr[]= $days;
      }
      // if it is the first query we add on moods at end of outArr
      if($_GET["select-query"] =="one"){
        $outArr[]= $moods;
      }
      // if it is the second query add on events at end of outArr
      if($_GET["select-query"] =="two"){
        $outArr[]= $events;
      }

      echo(json_encode($outArr));
  } //the query is not empty string

  else{
      echo(json_encode("QUERY HAS NOT BEEN IMPLEMENTED..."));
  }

    exit;
  }//try

  catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();

  }
} //get



?>
