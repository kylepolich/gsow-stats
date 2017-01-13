<?php include("header.php"); ?>

<?php
$result = mysqli_query($conn, "SELECT pageid, title, count(*) as edits from contributions t1 group by pageid, title");

while ($row = mysqli_fetch_array($result)) {
  $pageid = $row['pageid'];
  $title = $row["title"];
  $edits = $row['edits'];
  echo("<a href='page.php?pageid=" . $pageid . "'>" . $title . " (" . $edits . " edits)" . "</a><br/>");
}
?>

<?php include("footer.php"); ?>
