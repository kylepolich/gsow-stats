<?php include("header.php"); ?>

<?
  if (isset($_POST['page'])) {
    $page = $_POST['page'];
    $dt = $_POST['dt'];
    $query = "INSERT INTO edits (page, start) VALUES ('$page', '$dt');";
    $result = mysqli_query($conn, $query);
    $rows = mysqli_affected_rows($conn);
    if ($rows != 1) {
      echo("<h2>Sorry, there was an error adding that page</h2>");
    }
    else {
      echo("<p>Page added successfully. Please allow a few minutes for the data to be downloaded.</p>");
    }
  }
?>
<form action='admin.php' method=POST>
<h1>Add page</h1>
<table>
  <tr>
    <td>Page name:</td>
    <td><input name='page' /></td>
  </tr>
  <tr>
    <td>First major edit date:</td>
    <td><input name='dt' value='YYYY-MM-DD' /></td>
  </tr>
  <tr>
    <td colspan=2 align=right><input type='submit' value='Add' /></td>
  </tr>
</table>
</form>

<?php include("footer.php"); ?>
