<?php include("header.php"); ?>

<?
$result = mysqli_query($conn, "SELECT * FROM page_views where pageid=" . $_GET['pageid'] . " order by dt");

echo("<table border=1>");
while ($row = mysqli_fetch_array($result)) {
  echo("<tr>");
  $dt = $row['dt'];
  $views = $row['views'];
  echo("<td>" . $dt . "</td>");
  echo("<td>" . $views . "</td>");
  echo("</tr>");
}
echo("</table>");
?>

<?php include("footer.php"); ?>
