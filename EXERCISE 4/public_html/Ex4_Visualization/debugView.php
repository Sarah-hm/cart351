<?php
require('dbScripts/openDB.php');
try {

$myStr="SELECT * FROM dataStuff";
      //TO IMPLEMENT: all entries where after mood is positive
$myStrTwo="SELECT * FROM dataStuff WHERE start_mood IN ('sad','angry','neutral','calm', 'anxious','moody','hurt') AND after_mood IN ('sad','angry','neutral','calm', 'anxious','moody','hurt') ORDER BY weather";
$result = $file_db->query($myStrTwo);

if (!$result) die("Cannot execute query.");
while($row = $result->fetch(PDO::FETCH_ASSOC))
{
  var_dump($row);

foreach ($row as $key=>$entry)
{

 echo "<p>".$key." :: ".$entry."</p>";
}

}//end while


}

catch(PDOException $e) {
  // Print PDOException message
  echo $e->getMessage();

}
exit;
  ?>
