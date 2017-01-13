<?php
  include('../../funcs.php');
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1);
  $result = mysqli_query($conn, "SELECT * FROM page_views where pageid=" . $_GET['pageid'] . " and dt <> '0000-00-00 00:00:00' order by dt");
  echo("date\tviews\n");
  while ($row = mysqli_fetch_array($result)) {
    echo($row['dt'] . "\t");
    echo($row['views'] . "\n");
  }
?>
