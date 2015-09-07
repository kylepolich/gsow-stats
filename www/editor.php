<?php include("header.php"); ?>

<?
$result = mysqli_query($conn, "SELECT t2.* from editor t1 join contributions t2 on t1.editor_id = t2.editor_id where t1.name='" . $_GET['name'] . "'");

echo("<table border=1>");
while ($row = mysqli_fetch_array($result)) {
  echo("<tr>");
  echo("<td><a href='page.php?pageid=" . $row['pageid'] . "'>" . $row['title'] . "</a></td>");
  echo("<td>" . $row['timestamp'] . "</td>");
  echo("<td>" . $row['comment'] . "</td>");
  echo("</tr>");
}
echo("</table>");
?>

<?php include("footer.php"); ?>
