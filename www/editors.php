<?php include("header.php"); ?>

<?
$result = mysqli_query($conn, "SELECT t1.editor_id, t1.name from editor t1");

while ($row = mysqli_fetch_array($result)) {
  $name = $row["name"];
  echo("<a href='editor.php?name=" . $name . "'>" . $name . "</a><br/>");
}
?>

<?php include("footer.php"); ?>
