<?php include("header.php"); ?>

<?
  if (isset($_POST['page'])) {
    if (!isset($_POST['dt'])) {
      echo("<h2>Sorry, you need to enter a start date</h2>");
    }
    else {
      if ($_POST['dt']=="YYYY-MM-DD") {
        echo("<h2>Please enter a valid date</h2>");
      }
      else {
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
    <td>First major edit date (english):</td>
    <td><input name='dt' value='YYYY-MM-DD' /></td>
  </tr>
  <tr>
    <td>[not working yet]First major edit date (German):</td>
    <td><input name='dt_de' value='YYYY-MM-DD' /></td>
  </tr>
  <tr>
    <td>[not working yet]First major edit date (French):</td>
    <td><input name='dt_fr' value='YYYY-MM-DD' /></td>
  </tr>
  <tr>
    <td colspan=2 align=right><input type='submit' value='Add' /></td>
  </tr>
</table>
</form>

<?php include("footer.php"); ?>
